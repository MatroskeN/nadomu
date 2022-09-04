<?php

namespace App\Controller\Api\User;

use App\Controller\Base\BaseApiController;
use App\Repository\UserRepository;
use App\Services\MailServices;
use App\Services\RandomizeServices;
use App\Services\SMSServices;
use App\Form\Restore\RestorePhoneType;
use App\Services\User\RestoreServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use OpenApi\Annotations as OA;
use App\Entity\User;



class RestoreController extends BaseApiController
{
    /**
     * Запрос на смену номера
     *
     * @Route("/api/user/restore", name="api_restore_request", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="email", type="string", description="E-mail пользователя", example="email@test.ru"),
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Успешно отправлено")
     * @OA\Response(response=400, description="E-mail не корректный")
     * @OA\Response(response=403, description="Пользователь заблокирован")
     * @OA\Response(response=429, description="Необходимо подождать до следующей отправки")
     * @OA\Response(response=404, description="E-mail не найден")
     *
     * @OA\Tag(name="restore")
     */
    public function restoreRequest_action(
        Request $request,
        UserRepository $userRepository,
        RestoreServices $restoreServices,
        MailServices $mailServices
    ): Response {
        $email = (string)$this->getJson($request, 'email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return $this->jsonError(['email' => 'Введите корректный e-mail!']);

        $user = $userRepository->findByEmail($email);

        if ($user === null)
            return $this->jsonError(['email' => 'E-mail не найден!'], 404);

        if ($user->getIsBlocked())
            return $this->jsonError(['email' => 'Пользователь заблокирован'], 403);

        $last_request = $restoreServices->getLastTimeSend($user);

        if ($last_request && $last_request->getCreateTime() > time() - RestoreServices::EMAIL_LIFE_TIME)
            return $this->jsonError(['email' => 'Вы недавно отправляли запрос на смену номера. Пожалуйста, подождите'], 429);

        $restore = $restoreServices->createRequest($user);

        $mailServices->sendTemplateEmail($user->getEmail(), 'Запрос на смену пароля', '/mail/user/restore/code.html.twig', [
            'code' => $restore->getCode(),
            'user_id' => $user->getId(),
            'user_email' => $user->getEmail()
        ]);

        return $this->jsonSuccess();
    }

    /**
     * Запрос на проверку корректности номеров телефона
     *
     * @Route("/api/user/restore/phones", name="api_restore_validation_phones_request", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="phone", type="string", description="Номер телефона", example="+79000001000"),
     *       @OA\Property(property="phone_repeat", type="string", description="Повторный номер телефона", example="+79000001000"),
     *       @OA\Property(property="user_id", type="integer", description="ID пользователя", example="1"),
     *       @OA\Property(property="code", type="string", description="Код", example="0461b3df9c9ecadc464f54d0480d9cdf")
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Успешно отправлено")
     * @OA\Response(response=400, description="Ошибка номеров")
     *
     * @OA\Tag(name="restore")
     */
    public function restoreValidationPhones_action(
        Request $request,
        RestoreServices $restoreServices,
        SMSServices $SMSServices,
        KernelInterface $kernel
    ): Response {
        $user_data['phone'] = trim((string)$this->getJson($request, 'phone'));
        $user_data['phone_repeat'] = trim((string)$this->getJson($request, 'phone_repeat'));
        $user_data['user_id'] = (int)$this->getJson($request, 'user_id');
        $user_data['code'] = (string)$this->getJson($request, 'code');

        if (!$restoreServices->checkCode($user_data['user_id'], $user_data['code'])) {
            return $this->jsonError(['code' => 'Ошибка кода']);
        }

        $user_data['phone'] = $SMSServices->phoneFormat($user_data['phone']);
        $user_data['phone_repeat'] = $SMSServices->phoneFormat($user_data['phone_repeat']);

        if (!$user_data['phone'] || !$user_data['phone_repeat'])
            return $this->jsonError(['code' => 'Неверно указан номер']);

        $form = $this->createFormByArray(RestorePhoneType::class, $user_data);
        if ($form->isValid()) {
            $ip = $request->getClientIp();

            $authCode = $SMSServices->createCode($user_data['phone'], $ip, User::ROLE_USER);

            $result = [
                'id' => $authCode->getId(),
                'user_data' => $user_data,
                'sms_status' => false,
                'call_status' => false
            ];

            //для демонстрации, без отправки СМС, сразу показываем пароль на фронте
            if ($kernel->getEnvironment() == 'dev')
                $result['code'] = $authCode->getCode();
            else {
                $result['sms_status'] = $SMSServices->sendSMS($user_data['phone'], 'Код подтверждения для смены номера на сервисе НАДОМУ.РФ - ' . $authCode->getCode());

                if (!$result['sms_status'])
                    $result['call_status'] = $SMSServices->sendCall($user_data['phone'], $authCode->getCode());
            }
            return $this->jsonSuccess($result);
        } else {
            $errors = $this->getErrorMessages($form);
            return $this->jsonError($errors);
        }
    }

    /**
     * Запрос на проверку кода для смены номера телефона
     *
     * @Route("/api/user/restore/code", name="api_restore_validation_code_request", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="user_id", type="integer", description="ID пользователя", example="10"),
     *       @OA\Property(property="auth_id", type="integer", description="ID восстановления в auth_code", example="77"),
     *       @OA\Property(property="auth_code", type="integer", description="СМС код", example="1133"),
     *       @OA\Property(property="restore_code", type="string", description="Код", example="0461b3df9c9ecadc464f54d0480d9cdf")
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Успешно отправлено")
     * @OA\Response(response=400, description="Ошибка кода")
     *
     * @OA\Tag(name="restore")
     */
    public function restoreValidationCode_action(
        Request $request,
        RestoreServices $restoreServices,
        SMSServices $SMSServices
    ): Response {
        $user_data['user_id'] = (int)$this->getJson($request, 'user_id');
        $user_data['restore_code'] = (string)$this->getJson($request, 'restore_code');
        $user_data['auth_code'] = (string)$this->getJson($request, 'auth_code');
        $user_data['auth_id'] = (int)$this->getJson($request, 'auth_id');
        
        $check_code = $restoreServices->checkCode($user_data['user_id'], $user_data['restore_code']);
        
        if (!$check_code) {
            return $this->jsonError(['code' => 'Ошибка кода']);
        }

        $auth_code = $SMSServices->getCodeItem($user_data['auth_id']);

        $validate = $SMSServices->codeValidation($auth_code);
        if ($validate !== null)
            return $this->jsonError($validate['error'], $validate['status']);

        if ($auth_code->getCode() != $user_data['auth_code']) {
            $SMSServices->increaseAttempts($auth_code);
            return $this->jsonError(['code_id' => 'Код введен неверно!'], 403);
        }

        $restoreServices->updatePhone($check_code, $auth_code);

        return $this->jsonSuccess();
    }
}
