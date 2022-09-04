<?php

namespace App\Services\Marketing;

use App\Entity\Promocodes;
use App\Entity\User;
use App\Repository\PromocodesRepository;
use App\Services\RandomizeServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function PHPUnit\Framework\throwException;

class PromocodeServices
{
    const INVITE_CODE = 'invite';

    private EntityManagerInterface $em;
    private PromocodesRepository $promocodesRepository;

    public function __construct(EntityManagerInterface $em, PromocodesRepository $promocodesRepository)
    {
        $this->em = $em;
        $this->promocodesRepository = $promocodesRepository;
    }

    /**
     * Создание запроса на инвайт пользователя
     *
     * @param User $user
     * @param string $phone
     * @param string $code
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createInviteCode(User $user, string $phone, string $code)
    {
        $item = new Promocodes();
        $item->setOwner($user)
            ->setAction(self::INVITE_CODE)
            ->setPhone($phone)
            ->setCode($code);

        $this->em->persist($item);
        $this->em->flush();
    }

    /**
     * Пригласили пользователя уже или нет
     *
     * @param string $phone
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function userInvited(string $phone)
    {
        return !empty($this->getPromoByPhone($phone));
    }


    /**
     * Возвращаем промокод по номеру телефона
     *
     * @param string $phone
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPromoByPhone(string $phone)
    {
        return $this->promocodesRepository->findByPhone($phone);
    }

    /**
     * Возвращаем промокоды пользователя
     *
     * @param User $user
     * @return array
     */
    public function getUserCodes(User $user, array $invited_users)
    {
        $result = [];
        $registered = $this->getRegisteredByInvite($invited_users);


        foreach ($user->getPromocodes() as $val) {
            $result[] = [
                'phone' => $val->getPhone(),
                'code' => $val->getCode(),
                'action' => $val->getAction(),
                'result' => $registered[$val->getPhone()] ?? false
            ];
        }

        return $result;
    }

    /**
     * Статистика по приглашениям
     *
     * @param array $promo_users
     * @return int[]
     */
    public function statInvites(array $promo_users): array
    {
        $result = [
            'invited' => 0,
            'registered' => 0,
            'filled' => 0,
        ];

        foreach($promo_users as $item) {
            $result['invited']++;

            if (!empty($item['result']))
                $result['registered']++;

            if (!empty($item['result']['status']))
                $result['filled']++;
        }

        return $result;
    }

    /**
     * Возвращаем свободный случайны промокод, которого нет в базе
     *
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function generateCode(): string
    {
        //лимитируем количество попыток для повторной генерации
        for ($i = 0; $i < 50; $i++) {
            $random = RandomizeServices::generateString(4, '0123456789abcdefghijklmnopqrstuvwxyz');
            $exists = $this->promocodesRepository->findByCode($random);

            if (!$exists)
                return $random;
        }

        throw new \Exception('Проблема с генерацией промокодов. Свободные промокоды отсутствуют!');
    }

    /**
     * Возвращаем зарегистрированных пользователей по приглашению
     *
     * @param array $invited_users
     * @return array
     */
    private function getRegisteredByInvite(array $invited_users): array
    {
        $registered = array_map(function ($v) {
            return array_merge([
                'phone' => $v->getPhone()
            ], $v->userInviteStatus());
        }, $invited_users);

        return array_combine(array_column($registered, 'phone'), $registered);
    }
}
