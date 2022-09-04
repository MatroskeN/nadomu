<?php

namespace App\Controller\Api;

use App\Controller\Base\BaseApiController;
use App\Entity\AuthCode;
use App\Entity\User;
use App\Repository\AuthCodeRepository;
use App\Repository\PromocodesRepository;
use App\Repository\UserRepository;
use App\Services\MailServices;
use App\Services\SMSServices;
use App\Services\User\TokenServices;
use App\Services\User\UserServices;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;


class SMSController extends BaseApiController
{
    /**
     * Отправка кода для авторизации пользователя
     *
     * @Route("/api/sms", name="api_code_send", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="phone", type="string", description="Номер телефона пользователя", example="+7 (900) 000-00-00"),
     *       @OA\Property(property="role", type="string", description="Роль пользователя (user | specialist)", example="user")
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Код успешно отправлен")
     * @OA\Response(response=400, description="Ошибка при отправке")
     *
     * @OA\Tag(name="SMS")
     */
    public function send_action(KernelInterface $kernel, Request $request, SMSServices $SMSServices): Response
    {
        $phone = (string)$this->getJson($request, 'phone');
        $role = trim($this->getJson($request, 'role'));

        $formatted_phone = $SMSServices->phoneFormat($phone);
        $ip = $request->getClientIp();

        if (!$formatted_phone)
            return $this->jsonError(['phone' => 'Введите корректный номер!']);

        if (!isset(User::AVAILABLE_ROLES[$role]))
            return $this->jsonError(['role' => 'Введите корректную роль!']);

        $is_available = $SMSServices->checkAvailability($ip, $formatted_phone);

        if (!$is_available)
            return $this->jsonError(['phone' => 'Слишком большое количество отправок SMS, подождите!'], 403);

        $authCode = $SMSServices->createCode($formatted_phone, $ip, $role);

        $result = [
            'id' => $authCode->getId(),
            'sms_status' => false,
            'call_status' => false
        ];

        //для демонстрации, без отправки СМС, сразу показываем пароль на фронте
        if ($kernel->getEnvironment() == 'dev')
            $result['code'] = $authCode->getCode();
        else {
            $result['sms_status'] = $SMSServices->sendSMS($formatted_phone, 'Код авторизации на сервисе НАДОМУ.РФ - ' . $authCode->getCode());

            if (!$result['sms_status'])
                $result['call_status'] = $SMSServices->sendCall($formatted_phone, $authCode->getCode());
        }


        return $this->jsonSuccess($result);
    }

    /**
     * Прием ввода кода пользователя
     *
     * @Route("/api/sms", name="api_code_validate", methods={"PATCH"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="code_id", type="string", description="Идентификатор запроса", example="100"),
     *       @OA\Property(property="value", type="string", description="Код из SMS", example="101010")
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Пользователь успешно создан")
     * @OA\Response(response=400, description="Ошибка при создании")
     *
     * @OA\Tag(name="SMS")
     */
    public function check_action(Request $request, SMSServices $SMSServices, TokenServices $tokenServices,
                                 UserRepository $userRepository, UserServices $userServices, ParameterBagInterface $params, MailServices $mailServices): Response
    {
        $code_id = (int)$this->getJson($request, 'code_id');
        $value = trim($this->getJson($request, 'value'));

        $auth_code = $SMSServices->getCodeItem($code_id);

        $validate = $SMSServices->codeValidation($auth_code);
        if ($validate !== null)
            return $this->jsonError($validate['error'], $validate['status']);

        if ($auth_code->getCode() != $value) {
            $SMSServices->increaseAttempts($auth_code);
            return $this->jsonError(['code_id' => 'Код введен неверно!'], 403);
        }

        $SMSServices->markCompleted($auth_code);

        $user = $userRepository->findByPhone($auth_code->getPhone());

        if (!$user) {
            $user = $userServices->createUser($auth_code);

            $notifications = $params->get('mail.notifications');

            if (!empty($notifications)) {
                foreach ($notifications as $notify_mail)
                    $mailServices->sendTemplateEmail($notify_mail, 'Регистрация нового пользователя на сервисе', '/mail/admin/registration.html.twig', [
                        'phone' => $auth_code->getPhone(),
                        'id' => $user->getId(),
                        'role' => $auth_code->getRole()
                    ]);
            }

        }

        $result = $tokenServices->createPair($user);
        //если пользователь не специалист, но авторизация отправлена с формы специалиста
        $result['is_change_role'] = $user->getIsSpecialist() == false && $auth_code->getRole() == User::ROLE_SPECIALIST;
        $result['is_specialist'] = $user->getIsSpecialist();
        $result['is_empty_data'] = empty($user->getFirstName());

        return $this->jsonSuccess($result);
    }

    /**
     * Отправка кода для авторизации пользователя путем звонка
     *
     * @Route("/api/dial", name="api_dial_send", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="code_id", type="string", description="Идентификатор авторизации", example="1")
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Код успешно отправлен")
     * @OA\Response(response=400, description="Ошибка при совершении звонка")
     * @OA\Response(response=404, description="Идентификатор авторизации не найден")
     * @OA\Response(response=403, description="Проблема с кодом")
     *
     * @OA\Tag(name="SMS")
     */
    public function dial_action(KernelInterface $kernel, Request $request, SMSServices $SMSServices): Response
    {
        $code_id = (int)$this->getJson($request, 'code_id');
        $auth_code = $SMSServices->getCodeItem($code_id);

        $validate = $SMSServices->codeValidation($auth_code);
        if ($validate !== null)
            return $this->jsonError($validate['error'], $validate['status']);

        if ($auth_code->getIsDialed())
            return $this->jsonError(['code_id' => 'Звонок уже совершен'], 403);

        if ($kernel->getEnvironment() == 'dev')
            $status = true;
        else
            $status = $SMSServices->sendCall($auth_code->getPhone(), $auth_code->getCode());

        if ($status)
            $SMSServices->setDialed($auth_code);

        return $status ? $this->jsonSuccess() : $this->jsonError(['code_id' => 'Ошибка при совершении звонка']);
    }
}
