<?php

namespace App\Form;

use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
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
use App\Repository\CitiesRepository;
use App\Repository\MetroStationsRepository;
use App\Repository\ServicesRepository;
use App\Repository\ServicePriceRepository;
use App\Repository\ServiceWorkTimeRepository;
use App\Services\FormServices;

/**
 * Форма для создания заявки на услугу специалисту
 *
 * @package App\Form
 */
class RequestsSingleDataType extends AbstractType
{

    public function __construct(
        UserRepository $userRepository,
        CoreSecurity $security,
        CitiesRepository $citiesRepository,
        MetroStationsRepository $metroStationsRepository,
        ServicesRepository $servicesRepository,
        ServicePriceRepository $servicePriceRepository,
        ServiceWorkTimeRepository $serviceWorkTimeRepository,
        FormServices $formServices
    ) {
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->citiesRepository = $citiesRepository;
        $this->metroStationsRepository = $metroStationsRepository;
        $this->servicesRepository = $servicesRepository;
        $this->servicePriceRepository = $servicePriceRepository;
        $this->serviceWorkTimeRepository = $serviceWorkTimeRepository;
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
            ->add('service_price_id', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Callback([$this, 'validateService'])
                ],
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
            ->add('specialist_id', IntegerType::class, [
                'constraints' => [
                    new Callback([$this, 'validateSpecialist'])
                ],
            ])
            ->add('request_type');
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
    public function validateSpecialist($value, ExecutionContextInterface $context)
    {

        if ($value == 0) {
            return $context
                ->buildViolation('ID специалиста отсутствует')
                ->addViolation();
        }

        $specialist_entity = $this->userRepository->findById($value);
        if (!$specialist_entity) {
            return $context
                ->buildViolation('Такого пользователя не существует')
                ->addViolation();
        }

        if (!$specialist_entity->getIsSpecialist()) {
            return $context
                ->buildViolation('Пользователь не является специалистом!')
                ->addViolation();
        }

        if ($specialist_entity->getId() == $this->security->getUser()->getId()) {
            return $context
                ->buildViolation('Нельзя создать заявку на себя!')
                ->addViolation();
        }
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
            $worktime = $form->get('worktime')->getData();
            $date = $form->get('date')->getData();
            if (empty($worktime) && empty($date)) {
                return $context
                    ->buildViolation('Нужно указать дату, либо время оказания услуги!')
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
    public function validateWorkTime($value, ExecutionContextInterface $context)
    {
        $this->formServices->validateWorkTime($value, $context);

        $form = $context->getObject()->getParent();
        $convenient_time = $form->get('convenient_time')->getData();
        $date = $form->get('date')->getData();
        $specialist_id = $form->get('specialist_id')->getData();

        if ($convenient_time == "specified" && !empty($date)) {
            $week_day = date("w", mktime(0, 0, 0, $date->format('m'), $date->format('d'), $date->format('Y')));
            $specialist = $this->userRepository->find($specialist_id);
            if ($specialist->getServiceInfo()) {
                foreach ($value as $time) {
                    if (!empty($date)) {
                        if ($time['day'] != $week_day) {
                            return  $context
                                ->buildViolation('День в дате не совпадает с выбранным!')
                                ->atPath('[date]')
                                ->addViolation();
                        }
                    }
                    $work_time = $this->serviceWorkTimeRepository->findOneBy(['serviceInfo' => $specialist->getServiceInfo()->getId(), 'day' => $time['day'], 'hour' => $time['hour']]);
                    if (!$work_time) {
                        return $context
                            ->buildViolation('Специалист не работает в указанное время!')
                            ->atPath('worktime')
                            ->addViolation();
                    }
                }
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
        $form = $context->getObject()->getParent();
        $specialist_id = $form->get('specialist_id')->getData();
        $specialist = $this->userRepository->find($specialist_id);
        if (!$specialist->getServiceInfo()) {
            return $context
                ->buildViolation('У пользователя нет услуг!')
                ->addViolation();
        }
        $item_services = $specialist->getServiceInfo()->getServices();

        $serv_ids = [];
        foreach ($item_services as $item) {
            $serv_ids[] = $item->getId();
        }

        if (count($value) < 1) {
            return $context
                ->buildViolation('Нужно указать минимум одну услугу!')
                ->addViolation();
        }

        foreach ($value as $service) {
            if (!in_array($service, $serv_ids)) {
                return $context
                    ->buildViolation("Услуга у пользователя отсутствует")
                    ->addViolation();
            }

            $find = $this->servicePriceRepository->findById($service);
            if (!$find) {
                return $context
                    ->buildViolation("Услуга с ID $service отсутствует")
                    ->addViolation();
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
