<?php

namespace App\Form;

use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security as CoreSecurity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use App\Repository\CitiesRepository;
use App\Repository\MetroStationsRepository;
use App\Repository\ServicesRepository;
use App\Services\FormServices;

/**
 * Форма для создания заявки на услугу специалисту
 *
 * @package App\Form
 */
class RequestsQuizDataType extends AbstractType
{

    public function __construct(
        UserRepository $userRepository,
        CoreSecurity $security,
        CitiesRepository $citiesRepository,
        MetroStationsRepository $metroStationsRepository,
        ServicesRepository $servicesRepository,
        FormServices $formServices
    ) {
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->citiesRepository = $citiesRepository;
        $this->metroStationsRepository = $metroStationsRepository;
        $this->servicesRepository = $servicesRepository;
        $this->formServices = $formServices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city_id', IntegerType::class, [
                'constraints' => [
                    new Callback([$this, 'validateCity'])
                ],
            ])
            ->add('metro_id', IntegerType::class, [
                'constraints' => [
                    new Callback([$this, 'validateMetro'])
                ],
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите адрес оказания услуги'
                    ]),
                    new Callback([$this, 'validateAddress'])
                ]
            ])
            ->add('request_type', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите адрес оказания услуги'
                    ])
                ]
            ])
            ->add('service_id', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите услугу'
                    ]),
                    new Callback([$this, 'validateService'])
                ]
            ])
            ->add('convenient_time', ChoiceType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите время оказания услуги'
                    ]),
                    new Callback([$this, 'validateConvenientTime'])
                ],
                'choices'  => [
                    'specified' => 'specified',
                    'any' => 'any',
                ],
                'invalid_message' => 'Ошибка параметра'
            ])
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                "required" => true,
                'html5' => false,
                'constraints' => [
                    new Callback([$this, 'validateDate'])
                ],
            ))
            ->add('worktime', CollectionType::class, [
                'entry_type' => ServiceWorkTimeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'constraints' => [
                    new Callback([$this, 'validateWorkTime'])
                ],
            ])
            ->add('additional_information', TextType::class, [])
            ->add('gender', ChoiceType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите пол специалиста'
                    ])
                ],
                'choices'  => [
                    'male' => 'male',
                    'female' => 'female',
                    'any' => 'any'
                ],
                'invalid_message' => 'Ошибка параметра'
            ])
            ->add('experience', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Опыт'
                    ]),
                    new Callback([$this, 'validateExperience'])
                ]
            ])
            ->add('request_type')
            ->add('price_min', RangeType::class, [
                'constraints' => [
                    new Assert\Range([
                        'min' => 0,
                        'max' => 1000000,
                        'minMessage' => 'Минимальное значение 0',
                        'maxMessage' => 'Максимальное значение 1000000'
                    ]),
                    new Callback([$this, 'validatePrice'])
                ]
            ])
            ->add('price_max', RangeType::class, [
                'constraints' => [
                    new Assert\Range([
                        'min' => 0,
                        'max' => 1000000,
                        'minMessage' => 'Минимальное значение 0',
                        'maxMessage' => 'Максимальное значение 1000000'
                    ])
                ]
            ]);
    }


    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     * 
     * @return [type]
     */
    public function validateConvenientTime($value, ExecutionContextInterface $context)
    {
        if ($value == "specified") {
            $form = $context->getObject()->getParent();
            $date = $form->get('date')->getData();
            if (empty($date)) {
                return $context
                    ->buildViolation('Нужно указать дату оказания услуги!')
                    ->addViolation();
            }
        }
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     * 
     * @return [type]
     */
    public function validateService($value, ExecutionContextInterface $context)
    {
        if (empty($value)) {
            return $context
                ->buildViolation('Укажите услугу!')
                ->addViolation();
        }
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     * 
     * @return [type]
     */
    public function validateAddress($value, ExecutionContextInterface $context)
    {
        if (preg_match("/[^a-zа-яё0-9. ]/iu", $value)) {
            return $context
                ->buildViolation('В написании адреса только буквы латинского и русского алфавита!')
                ->addViolation();
        }
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     * 
     * @return [type]
     */
    public function validateExperience($value, ExecutionContextInterface $context)
    {
        $exp_values = $this->userRepository::EXPERIENCE;
        if (in_array($value, $exp_values) === FALSE) {
            return $context
                ->buildViolation('Опыт указан неверно!')
                ->addViolation();
        }
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     * 
     * @return [type]
     */
    public function validatePrice($value, ExecutionContextInterface $context)
    {
        $form = $context->getObject()->getParent();
        $price_min = $value;
        $price_max = $form->get('price_max')->getData();
        if ($price_min > $price_max) {
            return $context
                ->buildViolation('Минимальная цена больше максимальной!')
                ->addViolation();
        }
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     * 
     * @return [type]
     */
    public function validateCity($value, ExecutionContextInterface $context)
    {
        if (!$value) {
            return $context
                ->buildViolation('Укажите город!')
                ->addViolation();
        }
        if ($value) {
            $city = $this->citiesRepository->find($value);
            if (!$city) {
                return $context
                    ->buildViolation('Город не найден!')
                    ->addViolation();
            }
        }
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     * 
     * @return [type]
     */
    public function validateMetro($value, ExecutionContextInterface $context)
    {
        if ($value) {
            $metro = $this->metroStationsRepository->find($value);
            if (!$metro) {
                return $context
                    ->buildViolation('Метро не найдено!')
                    ->addViolation();
            }
        }
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     *
     * @return [type]
     */
    public function validateDate($value, ExecutionContextInterface $context)
    {
        if ($value) {
            if ($value->getTimestamp() + 24 * 60 * 60 < time()) {
                return $context
                    ->buildViolation('Нельзя устанавливать прошедшую дату!')
                    ->addViolation();
            }
        }
    }

    /**
     * @param mixed $value
     * @param ExecutionContextInterface $context
     * 
     * @return [type]
     */
    public function validateWorkTime($value, ExecutionContextInterface $context)
    {
        $this->formServices->validateWorkTime($value, $context);

        $form = $context->getObject()->getParent();
        $convenient_time = $form->get('convenient_time')->getData();
        $date = $form->get('date')->getData();

        if ($convenient_time == "specified" && !empty($date)) {
            $week_day = date("w", mktime(0, 0, 0, $date->format('m'), $date->format('d'), $date->format('Y')));
            foreach ($value as $time) {
                if (!empty($date)) {
                    if ($time['day'] != $week_day) {
                        return  $context
                            ->buildViolation('День в дате не совпадает с выбранным!')
                            ->atPath('[date]')
                            ->addViolation();
                    }
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
