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
class ServicePriceType extends AbstractType
{
    private ServicesRepository $servicesRepository;

    public function __construct(ServicesRepository $servicesRepository)
    {
        $this->servicesRepository = $servicesRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service_id', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите услугу'
                    ]),
                    new Callback([$this, 'validateServiceId'])
                ]
            ])
            ->add('price', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите стоимость услуги'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 100000,
                        'message' => 'Максимальная стоимость услуги 100 000 рублей'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 5,
                        'message' => 'Минимальная стоимость услуги 10 рублей'
                    ]),
                ]
            ]);
    }

    /**
     * Валидация существования идентификатора услуги
     *
     * @param $value
     * @param ExecutionContextInterface $context
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validateServiceId($value, ExecutionContextInterface $context)
    {
        $service = $this->servicesRepository->findById($value);

        if (empty($service))
            $context
                ->buildViolation('Услуга не найдена')
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
