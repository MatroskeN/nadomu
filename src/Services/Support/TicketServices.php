<?php

namespace App\Services\Support;


use App\Entity\SupportTicket;
use App\Entity\User;
use App\Form\Support\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class TicketServices
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
    }

    /**
     * Создание тикета в поддержку
     *
     * @param User $user
     * @param array $data
     * @return FormInterface
     */
    public function createTicketForm(SupportTicket $supportTicket, array $data): FormInterface
    {
        $form = $this->formFactory->create(TicketType::class, $supportTicket);
        $form->submit($data);

        return $form;
    }

}
