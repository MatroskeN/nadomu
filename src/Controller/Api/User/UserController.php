<?php

namespace App\Controller\Api\User;

use App\Controller\Base\BaseApiController;
use App\Entity\Notifications;
use App\Form\InitUserDataType;
use App\Form\UpdateUserDataType;
use App\Repository\PromocodesRepository;
use App\Services\Marketing\PromocodeServices;
use App\Services\SMSServices;
use App\Services\User\EmailConfirmationServices;
use App\Services\MailServices;
use App\Services\Notification;
use App\Services\User\TokenServices;
use App\Services\User\UserServices;
use App\Services\Marketing\UTMServices;
use App\Services\User\NotificationsServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use App\Entity\User;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\Notifier\Notification\Notification as NotificationNotification;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \Symfony\Component\Security\Core\Security as CoreSecurity;
use Symfony\Component\Validator\Constraints\DateTime;


class UserController extends BaseApiController
{
    /**
     * Получение текущей информации о пользователе
     *
     * @Route("/api/user", name="api_userinfo", methods={"GET"})
     *
     * @OA\Response(response=200, description="Информация предоставлена")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="user")
     * @Security(name="Bearer")
     */
    public function userInfo_action(CoreSecurity $security, UserServices $userServices): Response
    {
        $user = $security->getUser();
        $result = $userServices->getInformation($user);

        return $this->jsonSuccess(['user' => $result]);
    }

    /**
     * Обновление роли пользователя на специалиста
     *
     * @Route("/api/user/role", name="api_user_role", methods={"PATCH"})
     *
     * @OA\Response(response=200, description="Успешно сменили роль")
     * @OA\Response(response=401, description="Необходима авторизация")
     * @OA\Response(response=403, description="Пользователь уже является специалистом")
     *
     * @OA\Tag(name="user")
     * @Security(name="Bearer")
     */
    public function userRole_action(CoreSecurity $security, UserServices $userServices): Response
    {
        $user = $security->getUser();

        if ($user->getIsSpecialist())
            return $this->jsonError(['role' => 'Пользователь уже является специалистом!'], 403);

        $userServices->setSpecialistRole($user);

        return $this->jsonSuccess(['role' => $user->getRoles()]);
    }

    /**
     * Установка информации о пользователе после регистрации
     *
     * @Route("/api/user/init", name="api_set_init_data", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="email", type="string", description="Email", example="ivanov@mail.ru"),
     *       @OA\Property(property="last_name", type="string", description="Фамилия", example="Иванов"),
     *       @OA\Property(property="first_name", type="string", description="Имя", example="Иван"),
     *       @OA\Property(property="patronymic_name", type="string", description="Отчество", example="Иванович"),
     *       @OA\Property(property="promo", type="string", description="Промокод (для специалистов)", example="")
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Информация обновлена")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="user")
     * @Security(name="Bearer")
     */
    public function setInitData_action(
        Request $request,
        CoreSecurity $security,
        EmailConfirmationServices $emailConfirmationServices,
        MailServices $mailServices,
        UserServices $userServices,
        PromocodesRepository $promocodesRepository
    ): Response {
        $user = $security->getUser();
        $old_email = $user->getEmail();
        $user_data = [];
        $final_promo = null;

        $email = (string)$this->getJson($request, 'email');
        $promo = trim($this->getJson($request, 'promo'));
        $user_data['patronymic_name'] = (string)$this->getJson($request, 'patronymic_name');
        $user_data['first_name'] = (string)$this->getJson($request, 'first_name');
        $user_data['last_name'] = (string)$this->getJson($request, 'last_name');
        $user_data['email'] = $email;

        //если пользователь специалист, то проверяем промокод
        if ($user->getIsSpecialist()) {
            //в случае, если введен промокод ручонками, он имеет приоритет над куками
            if (!empty($promo)) {
                $final_promo = $promocodesRepository->findByCode($promo);

                if (!$final_promo)
                    return $this->jsonError(['promo' => 'Промокод не найден!'], 404);
            }

            //в случае, если нет никаких промокодов, но на этот номер кто-то отправлял приглашение, то мы крепим за ним этого юзера
            if (empty($final_promo))
                $final_promo = $promocodesRepository->findByPhone($user->getPhone());
        }

        $form = $this->createFormByArray(InitUserDataType::class, $user_data);

        if ($form->isValid()) {
            $userServices->setInitData($user, $form, $final_promo);

            if ($email != $old_email) {
                $confirmation = $emailConfirmationServices->createCode($user);

                $mailServices->sendTemplateEmail($email, 'Подтверждение почты на сервисе', '/mail/user/registration/code.html.twig', [
                    'code' => $confirmation->getCode(),
                    'user_id' => $user->getId()
                ]);
            }
        } else
            return $this->formValidationError($form);

        return $this->jsonSuccess();
    }

    /**
     * Отправка приглашения пользователю
     *
     * @Route("/api/user/promo", name="api_user_promo", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="phone", type="integer", description="Номер телефона", example="+79000001000")
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Промокод отправлен")
     * @OA\Response(response=400, description="Ошибка при отправке SMS")
     * @OA\Response(response=403, description="Не удалось отправить приглашение")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="promo")
     * @Security(name="Bearer")
     */
    public function sendpromo_action(
        Request $request,
        CoreSecurity $security,
        SMSServices $SMSServices,
        UserServices $userServices,
        PromocodeServices $promocodeServices,
        KernelInterface $kernel
    ): Response {
        $user = $security->getUser();
        $phone = (string)$this->getJson($request, 'phone');

        $formatted_phone = $SMSServices->phoneFormat($phone);

        if (!$formatted_phone)
            return $this->jsonError(['phone' => 'Введите корректный номер телефона'], 400);

        if ($promocodeServices->userInvited($formatted_phone))
            return $this->jsonError(['phone' => 'Пользователь уже приглашен'], 403);

        if ($formatted_phone == $user->getPhone())
            return $this->jsonError(['phone' => 'Вы уже зарегистрированы в системе :)'], 403);

        if ($userServices->phoneExists($formatted_phone))
            return $this->jsonError(['phone' => 'Данный номер уже зарегистрирован в программе'], 403);

        $code = $promocodeServices->generateCode();
        $message = 'Вас приглашает ' . $user->getFirstName() . ' ' . $user->getLastName() . ' на онлайн-сервис оказания медицинских услуг на дому.
По промокоду ' . $code . ' получите 3 месяца обслуживания бесплатно! 
https://надому.рф/p/' . $code;

        $status = $kernel->getEnvironment() == 'dev' ? true : $SMSServices->sendSMS($formatted_phone, trim($message));

        if ($status) {
            $promocodeServices->createInviteCode($user, $formatted_phone, $code);

            return $this->jsonSuccess(['sms_status' => $status]);
        } else
            return $this->jsonError(['sms_status' => $status]);
    }

    /**
     * Обновление данных пользователя
     *
     * @Route("/api/user", name="api_user_update_data", methods={"PATCH"}))
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="first_name", type="string", description="Имя", example="Иван"),
     *       @OA\Property(property="last_name", type="string", description="Фамилия", example="Иванов"),
     *       @OA\Property(property="patronymic_name", type="string", description="Отчество", example="Иванович"),
     *       @OA\Property(property="phone", type="string", description="Номер телефона", example="+78800553535"),
     *       @OA\Property(property="email", type="string", description="Email", example="ivanov@mail.ru"),
     *       @OA\Property(property="birthday", type="string", description="Дата рождения", example="1980-10-30"),
     *       @OA\Property(property="avatar_id", type="integer", description="ID изображения", example=""),
     *       @OA\Property(property="email_expert_answers", type="boolean", description="Ответы специалистов Email", example="true"),
     *       @OA\Property(property="email_new_requests", type="boolean", description="Новые заявки Email", example="true"),
     *       @OA\Property(property="email_users_response", type="boolean", description="Ответы пользователей Email", example="true"),
     *       @OA\Property(property="sms_expert_answers", type="boolean", description="Ответы специалистов СМС", example="true"),
     *       @OA\Property(property="sms_new_requests", type="boolean", description="Новые заявки СМС", example="true"),
     *       @OA\Property(property="sms_users_response", type="boolean", description="Ответы пользователей СМС", example="true")
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Информация обновлена")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="user")
     * @Security(name="Bearer")
     */
    public function userUpdate_action(
        Request $request,
        CoreSecurity $security,
        EmailConfirmationServices $emailConfirmationServices,
        MailServices $mailServices,
        NotificationsServices $notificationsServices,
        UserServices $userServices,
        SMSServices $SMSServices
    ): Response {
        $user = $security->getUser();
        $old_email = $user->getEmail();
        $user_data = [];

        // Получение данных для User
        $email = (string)$this->getJson($request, 'email');
        $user_data['first_name'] = (string)$this->getJson($request, 'first_name');
        $user_data['last_name'] = (string)$this->getJson($request, 'last_name');
        $user_data['patronymic_name'] = (string)$this->getJson($request, 'patronymic_name');
        $user_data['phone'] = $SMSServices->phoneFormat((string)$this->getJson($request, 'phone'));
        $user_data['email'] = $email;
        $user_data['birthday'] = (string)$this->getJson($request, 'birthday');
        $user_data['avatar'] = $this->getJson($request, 'avatar_id');

        // Получение данных для Notifications для пользователя
        $notifications_data['email_expert_answers'] = (bool)$this->getJson($request, 'email_expert_answers');
        $notifications_data['sms_expert_answers'] = (bool)$this->getJson($request, 'sms_expert_answers');

         // Получение данных для Notifications для специалиста
        $notifications_data['email_new_requests'] = (bool)$this->getJson($request, 'email_new_requests');
        $notifications_data['email_users_response'] = (bool)$this->getJson($request, 'email_users_response');
        $notifications_data['sms_new_requests'] = (bool)$this->getJson($request, 'sms_new_requests');
        $notifications_data['sms_users_response'] = (bool)$this->getJson($request, 'sms_users_response');

        if ($user_data['phone'] != $user->getPhone()) {
            return $this->jsonError(['phone' => 'Изменить номер в профиле нельзя.'], 403);
        }

        $form = $this->createFormByArray(UpdateUserDataType::class, $user_data);

        // Проверка существования notifications у пользователя
        $notifications = $notificationsServices->updateNotifications($user, $notifications_data);

        if ($form->isValid()) {
            if ($email != $old_email) {
                $confirmation = $emailConfirmationServices->createCode($user);
                $mailServices->sendTemplateEmail($user_data['email'], 'Подтверждение почты на сервисе', '/mail/user/registration/code.html.twig', [
                    'code' => $confirmation->getCode(),
                    'user_id' => $user->getId()
                ]);
            }
            //Сохранение данных
            $userServices->setUpdateData($user, $notifications, $form);
        } else
            return $this->formValidationError($form);

        return $this->jsonSuccess();
    }
}
