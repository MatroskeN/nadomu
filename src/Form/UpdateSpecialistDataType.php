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
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Repository\CitiesRepository;
use App\Repository\MetroStationsRepository;
use App\Repository\RegionsRepository;
use App\Services\FormServices;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * Форма для редактирования профиля
 *
 * @package App\Form
 */
class UpdateSpecialistDataType extends AbstractType
{
    private RegionsRepository $regionsRepository;
    private CitiesRepository $citiesRepository;
    private MetroStationsRepository $metroStationsRepository;
    private $cities = [];
    private $metro_stations = [];

    public function __construct(
        UserRepository $userRepository,
        CoreSecurity $security,
        RegionsRepository $regionsRepository,
        CitiesRepository $citiesRepository,
        MetroStationsRepository $metroStationsRepository,
        FormServices $formServices
    ) {
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->regionsRepository = $regionsRepository;
        $this->citiesRepository = $citiesRepository;
        $this->metroStationsRepository = $metroStationsRepository;
        $this->formServices = $formServices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('gender', ChoiceType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите свой пол'
                    ])
                ],
                'choices'  => [
                    'male' => 'male',
                    'female' => 'female',
                ],
                'invalid_message' => 'Ошибка параметра'
            ])
            ->add('region_id', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите регион'
                    ]),
                    new Callback([$this, 'validateRegion'])
                ]
            ])
            ->add('cities', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Callback([$this, 'validateCities'])
                ]
            ])
            ->add('metro_stations', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Callback([$this, 'validateMetro'])
                ]
            ])
            ->add('callback_phone', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите рабочий номер'
                    ])
                ]
            ])
            ->add('services', CollectionType::class, [
                'entry_type' => ServicePriceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'Выберите хотя бы одну оказываемую услугу'
                    ])
                ]
            ])
            ->add('worktime', CollectionType::class, [
                'entry_type' => ServiceWorkTimeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => 3,
                        'minMessage' => 'Выберите не менее 3 часов в неделю'
                    ]),
                    new Callback([$this, 'validateWorkTime'])
                ]
            ])
            ->add('time_range')
            ->add('public_photo', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'max' => 50,
                        'maxMessage' => 'Максимальное количество фото - 50',
                    ]),
                    new Callback([$this->formServices, 'validateFileId'], ['groups' => 'InitUser'])
                ]
            ])
            ->add('public_docs', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'max' => 50,
                        'maxMessage' => 'Максимальное количество документов - 50',
                    ]),
                    new Callback([$this->formServices, 'validateFileId'], ['groups' => 'InitUser'])
                ]
            ])
            ->add('private_docs', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'Загрузите документы для проверки',
                        'max' => 10,
                        'maxMessage' => 'Максимум 10 документов на проверку',
                    ]),
                    new Callback([$this->formServices, 'validateFileId'], ['groups' => 'InitUser'])
                ]
            ])
            ->add('experience', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Укажите количество лет опыта'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 1,
                        'message' => 'Опыт не может быть меньше 1 года'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 100,
                        'message' => 'Опыт не может быть больше 100 лет'
                    ]),
                ]
            ])
            ->add('education', CollectionType::class, [
                'entry_type' => ServiceEducationTimeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'max' => 100,
                        'minMessage' => 'Укажите хотя бы одно место обучения',
                        'maxMessage' => 'Слишком много тоже не нужно',
                    ])
                ]
            ])
            ->add('about');
    }

    /**
     * Валидация региона. Важно чтобы он шел первым, так как здесь выставляются города и станции метро при проверке
     *
     * @param $value
     * @param ExecutionContextInterface $context
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validateRegion($value, ExecutionContextInterface $context)
    {
        if (!empty($value)) {
            $region = $this->regionsRepository->findById($value);

            if (!$region)
                $context
                    ->buildViolation('Регион не найден')
                    ->addViolation();
            else {
                $this->cities = $region->getCities();
                $this->metro_stations = $region->getMetroStations();
            }
        } else
            $context
                ->buildViolation('Укажите регион')
                ->addViolation();
    }

    /**
     * Валидация корректности заполненности городов
     *
     * @param $value
     * @param ExecutionContextInterface $context
     */
    public function validateCities($value, ExecutionContextInterface $context)
    {
        $form = $context->getObject()->getParent();
        $region_id = $form->get('region_id')->getData();
        $cities = $form->get('cities')->getData();

        if (!empty($region_id)) {
            if (empty($cities)) {
                $context
                    ->buildViolation('Нужно указать город!')
                    ->addViolation();
            }
            $items = [];
            foreach ($this->cities as $item)
                $items[] = $item->getId();

            $diff = array_diff($value, $items);
            $diff = array_values($diff);

            if (!empty($diff))
                $context
                    ->buildViolation('Город с идентификатором ' . $diff[0] . ' не найден')
                    ->addViolation();
        }
    }

    /**
     * Валидация корректности заполненности станций метро. Важно, чтобы шел крайним,
     * так как в нем проверка итогового выбора по городам и станциям
     *
     * @param $value
     * @param ExecutionContextInterface $context
     */
    public function validateMetro($value, ExecutionContextInterface $context)
    {
        $form = $context->getObject()->getParent();
        $cities = $form->get('cities')->getData();
        $region_id = $form->get('region_id')->getData();
        if (!empty($region_id)) {
            $items = [];
            foreach ($this->metro_stations as $item)
                $items[] = $item->getId();

            $diff = array_diff($value, $items);
            $diff = array_values($diff);

            if (!empty($diff))
                $context
                    ->buildViolation('Станция с идентификатором ' . $diff[0] . ' не найдена')
                    ->addViolation();

            if ((count($cities) + count($value)) == 0)
                $context
                    ->buildViolation('Выберите как минимум один город или станцию')
                    ->addViolation();
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
