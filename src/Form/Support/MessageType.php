<?php

namespace App\Form\Support;

use App\Entity\SupportMessage;
use App\Entity\SupportTicket;
use App\Entity\User;
use App\Services\User\UserServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * Форма для проверки создания сообщения в поддержку
 *
 * @package App\Form
 */
class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportMessage::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
