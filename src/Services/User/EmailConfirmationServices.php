<?php

namespace App\Services\User;

use App\Entity\EmailConfirmation;
use App\Entity\User;
use App\Repository\EmailConfirmationRepository;
use App\Services\RandomizeServices;
use Doctrine\ORM\EntityManagerInterface;

class EmailConfirmationServices
{
    private EntityManagerInterface $em;
    private EmailConfirmationRepository $emailConfirmationRepository;

    public function __construct(EntityManagerInterface $em, EmailConfirmationRepository $emailConfirmationRepository)
    {
        $this->em = $em;
        $this->emailConfirmationRepository = $emailConfirmationRepository;
    }

    /**
     * Возвращаем информацию о крайнем запросе
     *
     * @param User $user
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastTimeSend(User $user): ?EmailConfirmation
    {
        return $this->emailConfirmationRepository->findByUserId($user->getId());
    }

    /**
     * Формируем код подтверждения для аккаунта
     * @param User $user
     * @return EmailConfirmation
     */
    public function createCode(User $user)
    {
        $confirmation = new EmailConfirmation();
        $confirmation
            ->setCode(md5(RandomizeServices::generateString(32)))
            ->setUser($user)
            ->setCreateTime(time())
            ->setIsConfirmed(false)
            ->setConfirmTime(0);

        $this->em->persist($confirmation);
        $this->em->flush();

        return $confirmation;
    }

    /**
     * Подтверждение пользователя, запись в базу
     *
     * @param EmailConfirmation $emailConfirmation
     */
    public function userConfirm(EmailConfirmation $emailConfirmation)
    {
        $user = $emailConfirmation->getUser();
        $user->setIsConfirmed(true);

        $emailConfirmation->setIsConfirmed(true)
            ->setConfirmTime(time());

        $this->em->persist($user);
        $this->em->persist($emailConfirmation);

        $this->em->flush();
    }

    /**
     * Проверка кода активации
     *
     * @param int $user_id
     * @param $code
     * @return EmailConfirmation|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkCode(int $user_id, $code): ?EmailConfirmation
    {
        return $this->emailConfirmationRepository->findByUserIdAndCode($user_id, $code);
    }
}
