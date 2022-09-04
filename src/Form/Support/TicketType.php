<?php

namespace App\Form\Support;

use App\Entity\SupportTicket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * Форма для проверки создания запроса в поддержку
 *
 * @package App\Form
 */
class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('message', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank([], 'Введите текст сообщения'),
                    new Assert\Length([
                        'max' => 100000,
                        'maxMessage' => 'Максимальная длина 100000 символов'
                    ])
                ],
            ])
            ->add('file_ids', CollectionType::class, [
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'max' => 50,
                        'maxMessage' => 'Максимальное количество файлов в сообщении 50'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportTicket::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
