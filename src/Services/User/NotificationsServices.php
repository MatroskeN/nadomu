<?php

namespace App\Services\User;

use App\Entity\Notifications;
use App\Repository\NotificationsRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Notifier\Notification\Notification;

class NotificationsServices
{
    public function __construct(
        EntityManagerInterface $em,
        NotificationsRepository $notificationsRepository
    ) {
        $this->em = $em;
        $this->notificationsRepository = $notificationsRepository;
    }

    /**
     * @param User $user
     * @param array $notifications
     *
     * @return Notifications
     */
    public function updateNotifications(User $user, array $notifications): Notifications
    {
        $notification = $user->getNotification();
        if (!$notification) {
            $notification = new Notifications();

            $this->em->persist($notification);

            $user->setNotification($notification);
            $this->em->persist($user);
        }

        $notification
            ->setEmailExpertAnswers($notifications['email_expert_answers'])
            ->setEmailNewRequests($notifications['email_new_requests'])
            ->setEmailUsersResponse($notifications['email_users_response'])
            ->setSmsExpertAnswers($notifications['sms_expert_answers'])
            ->setSmsNewRequests($notifications['sms_new_requests'])
            ->setSmsUsersResponse($notifications['sms_users_response'])
            ->setUser($user);

        $this->em->persist($notification);
        $this->em->flush();

        return $notification;
    }
}
