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
class ServiceEducationTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('university', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Введите место обучения'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Название не может быть меньше 3 символов',
                        'maxMessage' => 'Название не может быть больше 255 символов',
                    ])
                ]
            ])
            ->add('from', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Введите год начала обучения'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 1900,
                        'message' => 'Минимальное значение 1900'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 2050,
                        'message' => 'Введите корректный год'
                    ]),
                ]
            ])
            ->add('to', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Введите год окончания обучения'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 1900,
                        'message' => 'Минимальное значение 1900'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 2050,
                        'message' => 'Введите корректный год'
                    ]),
                    new Callback([$this, 'validateYears'])
                ]
            ]);
    }

    /**
     * Валидация корректности заполненности годов
     *
     * @param $value
     * @param ExecutionContextInterface $context
     */
    public function validateYears($value, ExecutionContextInterface $context)
    {
        $form = $context->getObject()->getParent();
        $from = $form->get('from')->getData();
        $to   = $form->get('to')->getData();

        if ($to < $from)
            $context
                ->buildViolation('Год окончания не может быть раньше начала')
                ->addViolation();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
