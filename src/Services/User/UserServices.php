<?php

namespace App\Services\User;

use App\Entity\AuthCode;
use App\Entity\Promocodes;
use App\Entity\User;
use App\Entity\Notifications;
use App\Repository\UserRepository;
use App\Services\Marketing\PromocodeServices;
use App\Services\RandomizeServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Services\HelperServices;
use App\Services\TwigServices;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\Security\Core\Security as CoreSecurity;

class UserServices
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $userPasswordHasher;
    private FormFactoryInterface $formFactory;
    private UserRepository $userRepository;
    private RandomizeServices $randomizeServices;
    private PromocodeServices $promocodeServices;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $userPasswordHasher,
        FormFactoryInterface $formFactory,
        UserRepository $userRepository,
        RandomizeServices $randomizeServices,
        PromocodeServices $promocodeServices,
        NotificationsServices $notificationsServices,
        CoreSecurity $security
    ) {
        $this->em = $em;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->randomizeServices = $randomizeServices;
        $this->promocodeServices = $promocodeServices;
        $this->notificationsServices = $notificationsServices;
        $this->security = $security;
    }


    /**
     * Создание аккаунта пользователя в базе
     *
     * @param User $user
     * @param AuthCode $authCode
     * @return User
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createUser(AuthCode $authCode): User
    {
        $phone = $authCode->getPhone();
        $promo = $authCode->getPromo();

        $user = new User();
        $user->setPhone($phone)
            ->setPassword($this->userPasswordHasher->hashPassword($user, $this->randomizeServices->generateString()))
            ->setIsBlocked(false)
            ->setIsConfirmed(false)
            ->setCreateTime(time())
            ->setLastVisitTime(0);

        if ($authCode->getRole() == User::ROLE_SPECIALIST)
            $user->setRoles([User::AVAILABLE_ROLES[User::ROLE_SPECIALIST]]);

        //если есть промокод, выставляем, кто пригласил
        if (!empty($promo) && empty($user->getInvited()))
            $user->setInvited($promo->getOwner());

        $this->em->persist($user);

        // Создаем уведомления
        $notifications = new Notifications();
        $notifications->setUser($user);
        $this->em->persist($notifications);

        $user->setNotification($notifications);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Выставляем время крайнего визита
     *
     * @param User $user
     */
    public function updateLastVisit(User $user)
    {
        //если с крайнего обновления меньше 5 минут, пропускаем
        if (time() - $user->getLastVisitTime() > 300) {
            $user->setLastVisitTime(time());

            $this->em->persist($user);
            $this->em->flush();
        }
    }

    /**
     * Проверка на наличие номера телефона
     *
     * @param string $phone
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function phoneExists(string $phone)
    {
        return !empty($this->userRepository->findByPhone($phone));
    }

    /**
     * Возвращаем приглашенных пользователей
     *
     * @param User $user
     * @return int|mixed[]|string
     */
    public function getInvitedUsers(User $user)
    {
        return $this->userRepository->findInvitedByUserId($user->getId());
    }


    /**
     * Возвращаем информацию по пользователю
     *
     * @param User $user
     * @return array[]
     */
    public function getInformation(User $user): array
    {
        $invited_users = $this->getInvitedUsers($user);
        $promo_users = $this->promocodeServices->getUserCodes($user, $invited_users);

        // Set birthday
        $birthday = $user->getBirthday();
        if ($birthday) {
            $birthday = $birthday->format('Y-m-d');
        } else {
            $birthday = null;
        }



        $result = [
            'id' => $user->getId(),
            'roles' => $user->getRoles(),
            'is_specialist' => $user->getIsSpecialist(),
            'userinfo' => [
                'phone' => $user->getPhone(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'patronymic_name' => $user->getPatronymicName(),
                'gender' => $user->getGender(),
                'slug'  => $user->getSlug(),
                'birthday' => $birthday,
                'notifications' => null,
                'invited' => null,
                'users' => [],
                'promocodes' => $promo_users,
                'promo_statistics' => $this->promocodeServices->statInvites($promo_users),
                'is_confirmed' => $user->getIsConfirmed(),
                'is_blocked' => $user->getIsBlocked(),
                'create_time' => $user->getCreateTime(),
                'last_visit_time' => $user->getLastVisitTime(),
            ],
            'specialistinfo' => [
                'time_range' => null,
                'about' => null,
                'experience' => null,
                'callback_phone' => null,
                'rating' => null,
                'location' => [
                    'region' => null,
                    'cities' => [],
                    'metro' => [],
                ],
                'services' => [],
                'worktime' => [],
                'education' => [],

                'images' => [
                    'profile' => null,
                    'private_docs' => [],
                    'public_docs' => [],
                ],
            ]
        ];

        $notifications = $user->getNotification();
        if (!$notifications)
            $notifications = new Notifications();

        if ($user->getServiceInfo()) {
            $result['specialistinfo']['callback_phone'] = $user->getServiceInfo()->getCallbackPhone();
            $result['specialistinfo']['about'] = $user->getServiceInfo()->getAbout();
            $result['specialistinfo']['rating'] = -$user->getServiceInfo()->getRating();
            $result['specialistinfo']['time_range'] = -$user->getServiceInfo()->getRating();
        }

        if ($user->getAvatar())
            $result['images']['profile'] = [
                'id' => $user->getAvatar()->getId(),
                'filepath' => $user->getAvatar()->getFilePath()
            ];

        $result['notifications']['email_expert_answers'] = $notifications->getEmailExpertAnswers();
        $result['notifications']['email_new_requests'] = $notifications->getEmailNewRequests();
        $result['notifications']['email_users_response'] = $notifications->getEmailUsersResponse();
        $result['notifications']['sms_expert_answers'] = $notifications->getSmsExpertAnswers();
        $result['notifications']['sms_new_requests'] = $notifications->getSmsNewRequests();
        $result['notifications']['sms_users_response'] = $notifications->getSmsUsersResponse();


        if ($user->getServiceInfo() && $user->getServiceInfo()->getRegion())
            $result['specialistinfo']['location']['region'] = [
                'id' => $user->getServiceInfo()->getRegion()->getId(),
                'name' => $user->getServiceInfo()->getRegion()->getName()
            ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getCities())
            foreach ($user->getServiceInfo()->getCities() as $item)
                $result['specialistinfo']['location']['cities'][] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getMetroStations())
            foreach ($user->getServiceInfo()->getMetroStations() as $item)
                $result['specialistinfo']['location']['metro'][] = [
                    'id' => $item->getId(),
                    'name' => $item->getStation(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getServices())
            foreach ($user->getServiceInfo()->getServices() as $item)
                $result['specialistinfo']['services'][] = [
                    'service' => [
                        'id' => $item->getService()->getId(),
                        'name' => $item->getService()->getName(),
                    ],
                    'price' => $item->getPrice(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getWorkTime())
            foreach ($user->getServiceInfo()->getWorkTime() as $item)
                $result['specialistinfo']['worktime'][] = [
                    'day' => $item->getDay(),
                    'hour' => $item->getHour(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getEducation())
            foreach ($user->getServiceInfo()->getEducation() as $item)
                $result['specialistinfo']['education'][] = [
                    'university' => $item->getUniversity(),
                    'from' => $item->getYearFrom(),
                    'to' => $item->getYearTo(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getExperience())
            $result['specialistinfo']['experience'] = $user->getServiceInfo()->getExperience();

        if ($user->getServiceInfo() && $user->getServiceInfo()->getImages() && $user->getServiceInfo()->getImages()->getProfile())
            $result['images']['profile'] = [
                'id' => $user->getServiceInfo()->getImages()->getProfile()->getId(),
                'filepath' => $user->getServiceInfo()->getImages()->getProfile()->getFilePath(),
                'type' => $user->getServiceInfo()->getImages()->getProfile()->getFiletype(),
                'is_image' => $user->getServiceInfo()->getImages()->getProfile()->getIsImage(),
            ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getImages() && $user->getServiceInfo()->getImages()->getPublicDocs())
            foreach ($user->getServiceInfo()->getImages()->getPublicDocs() as $item)
                $result['specialistinfo']['images']['public_docs'][] = [
                    'id' => $item->getId(),
                    'filepath' => $item->getFilePath(),
                    'type' => $item->getFiletype(),
                    'is_image' => $item->getIsImage(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getImages() && $user->getServiceInfo()->getImages()->getPrivateDocs())
            foreach ($user->getServiceInfo()->getImages()->getPrivateDocs() as $item)
                $result['specialistinfo']['images']['private_docs'][] = [
                    'id' => $item->getId(),
                    'filepath' => $item->getFilePath(),
                    'type' => $item->getFiletype(),
                    'is_image' => $item->getIsImage(),
                ];


        if ($user->getInvited())
            $result['invited'] = [
                'id' => $user->getInvited()->getId(),
                'first_name' => $user->getInvited()->getFirstName(),
                'last_name' => $user->getInvited()->getLastName(),
            ];

        if ($invited_users)
            foreach ($invited_users as $item)
                $result['users'][] = [
                    'id' => $item->getId(),
                    'phone' => $item->getPhone(),
                    'info' => $item->userInviteStatus()
                ];

        return $result;
    }

    /**
     * Выставляем роль специалиста для юзера
     *
     * @param User $user
     * @return User
     */
    public function setSpecialistRole(User $user): User
    {
        $specialist_code = User::AVAILABLE_ROLES[User::ROLE_SPECIALIST];

        $user->setRoles([$specialist_code]);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Выставляем базовую информацию о пользователе после регистрации
     *
     * @param UserInterface $user
     * @param FormInterface $form
     * @param Promocodes|null $promo
     * @return UserInterface
     */
    public function setInitData(UserInterface $user, FormInterface $form, ?Promocodes $promo): UserInterface
    {
        $patronymic_name = $form->get('patronymic_name')->getData();
        $first_name = $form->get('first_name')->getData();
        $last_name = $form->get('last_name')->getData();
        $email = $form->get('email')->getData();

        $user->setFirstName($first_name)
            ->setLastName($last_name)
            ->setPatronymicName($patronymic_name)
            ->setEmail($email);

        if (empty($slug)) {
            $slug = $this->getTranslitUrl($user);
            $user->setSlug($slug);
        }

        if (!empty($promo))
            $user->setInvited($promo->getOwner());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param UserInterface $user
     * @param FormInterface $form
     *
     * @return UserInterface
     */
    public function setUpdateData(UserInterface $user, Notifications $notifications, FormInterface $form): UserInterface
    {
        $patronymic_name = $form->get('patronymic_name')->getData();
        $first_name = $form->get('first_name')->getData();
        $last_name = $form->get('last_name')->getData();
        $phone = $form->get('phone')->getData();
        $email = $form->get('email')->getData();
        $birthday = $form->get('birthday')->getData();
        $avatar = $form->get('avatar')->getData();
        $slug = $user->getSlug();

        $user->setFirstName($first_name)
            ->setLastName($last_name)
            ->setPatronymicName($patronymic_name)
            ->setPhone($phone)
            ->setEmail($email)
            ->setBirthday($birthday)
            ->setNotification($notifications)
            ->setAvatar($avatar);

        if (empty($slug)) {
            $slug = $this->getTranslitUrl($user);

            $user->setSlug($slug);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return [type]
     */
    private function getTranslitUrl(UserInterface $user)
    {
        if (empty($user->getId()) || empty($user->getFirstName()) || empty($user->getLastName())) {
            return false;
        }

        $string = $user->getFirstName() . ' ' . $user->getLastName();
        return $user->getId() . '-' . HelperServices::transliteration($string);
    }

    /**
     * Возвращает в текстовом виде последний онлайн пользователя
     *
     * @param UserInterface $user
     *
     * @return [type]
     */
    public function getOnlineTime(UserInterface $user)
    {
        if (empty($user->getId())) {
            return false;
        }

        $last_visit = $user->getLastVisitTime();
        $was_online = ($user->getGender() == 'female') ? "Была в сети " : "Был в сети ";

        $diff = time() - $last_visit;
        if ($diff < 60 * 5) {
            return 'Онлайн';
        } elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 3600) return $was_online . TwigServices::plural(floor($diff / 60), ['минуту', 'минуты', 'минут'],) . ' назад';
                if ($diff < 7200) return $was_online . 'час назад';
                if ($diff < 86400) return $was_online . TwigServices::plural(floor($diff / 3600), ['час', 'часа', 'часов']) . ' назад';
            }
            if ($day_diff == 1) return $was_online . 'вчера в ' . date("H:i", $last_visit);
            if ($day_diff < 7) return $was_online . TwigServices::plural($day_diff, ['день', 'дня', 'дней']) . ' назад';
            if ($day_diff < 31) return $was_online . TwigServices::plural(ceil($day_diff / 7), ['неделю', 'недели', 'недель']) . ' назад';
            if ($day_diff < 60) return $was_online . 'больше месяца назад';
            return 'Пользователь давно не заходил';
        } else {
            return $was_online . gmdate("Y-m-d", $last_visit) . ' в ' . date("H:i", $last_visit);
        }
    }
}
