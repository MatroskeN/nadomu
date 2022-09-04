<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\ServicesRepository;
use App\Repository\UserRepository;
use App\Services\User\UserServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security as CoreSecurity;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Форма для проверки обновления профиля пользователя
 *
 * @package App\Form
 */
class ServiceWorkTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('day', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите день'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 1,
                        'message' => 'День не может быть меньше 1'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 7,
                        'message' => 'День не может быть больше 7'
                    ]),
                ]
            ])
            ->add('hour', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Введите час'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Час не может быть меньше 0'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 23,
                        'message' => 'Час не может быть больше 23'
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
