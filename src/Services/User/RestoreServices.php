<?php

namespace App\Services\User;

use App\Entity\AuthCode;
use App\Entity\AuthToken;
use App\Entity\ChangeUserPhone;
use App\Entity\RefreshToken;
use App\Entity\ResetPasswordCode;
use App\Entity\User;
use App\Repository\ChangeUserPhoneRepository;
use App\Repository\ResetPasswordCodeRepository;
use App\Services\RandomizeServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RestoreServices
{
    //задержка между повторными запросами смены номера
    const EMAIL_LIFE_TIME = 300;

    private EntityManagerInterface $em;
    private ChangeUserPhoneRepository $changeUserPhoneRepository;

    public function __construct(EntityManagerInterface $em, ChangeUserPhoneRepository $changeUserPhoneRepository)
    {
        $this->em = $em;
        $this->changeUserPhoneRepository = $changeUserPhoneRepository;
    }

    /**
     * Создание запроса на смену пароля
     *
     * @param User $user
     * @return ChangeUserPhone
     */
    public function createRequest(User $user)
    {
        $reset = new ChangeUserPhone();
        $reset
            ->setCode(md5(RandomizeServices::generateString(32)))
            ->setUser($user)
            ->setConfirmTime(0)
            ->setCreateTime(time())
            ->setIsConfirmed(false);

        $this->em->persist($reset);
        $this->em->flush();

        return $reset;
    }

    /**
     * Проверка кода активации
     *
     * @param int $user_id
     * @param $code
     * @return ChangeUserPhone|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkCode(int $user_id, $code): ?ChangeUserPhone
    {
        return $this->changeUserPhoneRepository->findByUserIdAndCode($user_id, $code);
    }

    /**
     * Возвращаем информацию о крайнем запросе
     *
     * @param User $user
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastTimeSend(User $user): ?ChangeUserPhone
    {
        return $this->changeUserPhoneRepository->findByUserId($user->getId());
    }

    /**
     * Обновление номера телефона
     *
     * @param ChangeUserPhone $restore
     * @param AuthCode $authCode
     * @return int|mixed|string|null
     */
    public function updatePhone(ChangeUserPhone $restore, AuthCode $authCode)
    {
        $restore->setIsConfirmed(true)
            ->setConfirmTime(time());
        
        $authCode->setIsCompleted(true);

        $user = $restore->getUser();
        $user->setPhone($authCode->getPhone());

        $this->em->persist($restore);
        $this->em->persist($authCode);
        $this->em->persist($user);
        $this->em->flush();
    }
}
