<?php

namespace App\Services\Specialist;

use App\Entity\ServiceEducation;
use App\Entity\ServiceImages;
use App\Entity\ServiceInfo;
use App\Entity\ServicePrice;
use App\Entity\ServiceWorkTime;
use App\Entity\User;
use App\Form\Steps\StepOneType;
use App\Repository\CitiesRepository;
use App\Repository\FilesRepository;
use App\Repository\MetroStationsRepository;
use App\Repository\PromocodesRepository;
use App\Repository\RegionsRepository;
use App\Repository\ServicesRepository;
use App\Repository\UserRepository;
use App\Services\File\FileServices;
use App\Services\User\UserServices;
use App\Repository\SpecialistFavoriteRepository;
use App\Services\HelperServices;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use \Symfony\Component\Security\Core\Security as CoreSecurity;

class SpecialistServices
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $userPasswordHasher;
    private FormFactoryInterface $formFactory;
    private CitiesRepository $citiesRepository;
    private MetroStationsRepository $metroStationsRepository;
    private RegionsRepository $regionsRepository;
    private ServicesRepository $servicesRepository;
    private FilesRepository $filesRepository;
    private FileServices $fileServices;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $userPasswordHasher,
        FormFactoryInterface $formFactory,
        CitiesRepository $citiesRepository,
        MetroStationsRepository $metroStationsRepository,
        RegionsRepository $regionsRepository,
        ServicesRepository $servicesRepository,
        FilesRepository $filesRepository,
        FileServices $fileServices,
        UserRepository $userRepository,
        UserServices $userServices,
        SpecialistFavoriteRepository $specialistFavoriteRepository,
        CoreSecurity $security
    ) {
        $this->em = $em;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->formFactory = $formFactory;
        $this->citiesRepository = $citiesRepository;
        $this->metroStationsRepository = $metroStationsRepository;
        $this->regionsRepository = $regionsRepository;
        $this->servicesRepository = $servicesRepository;
        $this->filesRepository = $filesRepository;
        $this->fileServices = $fileServices;
        $this->userRepository = $userRepository;
        $this->userServices = $userServices;
        $this->specialistFavoriteRepository = $specialistFavoriteRepository;
        $this->security = $security;
    }

    /**
     * Формирование формы с заполнением данных для обновления профиля
     *
     * @param string $formType
     * @param array $data
     * @param null $entity
     * @return FormInterface
     */
    public function setUpdateData(UserInterface $user, FormInterface $form): User
    {
        // Сохранение пола
        $gender = $form->get('gender')->getData();
        $user->setGender($gender);
        $this->em->persist($user);
        $this->em->flush();

        $experience = $form->get('experience')->getData();
        $time_range = $form->get('time_range')->getData();
        $time_range = boolval($time_range);
        $callback_phone = $form->get('callback_phone')->getData();
        $about = $form->get('about')->getData();

        $service_info = $user->getServiceInfo() ?? new ServiceInfo();
        $service_info->setUser($user)
            ->setExperience($experience)
            ->setTimeRange($time_range)
            ->setAbout($about)
            ->setCallbackPhone($callback_phone);
        $this->em->persist($service_info);
        $this->em->persist($user);
        $this->em->flush();

        $this->updateRegion($user, $form);
        $this->updateServices($user, $form);
        $this->updateWorktime($user, $form);
        $this->updateFiles($user, $form);
        $this->updateEducation($user, $form);

        return $user;
    }

    /**
     * Обновляем информацию о пользователе на базе формы. Шаг 2
     *
     * @param User $user
     * @param FormInterface $form
     * @return User
     */
    public function updateRegion(UserInterface $user, FormInterface $form): User
    {
        $region_id = $form->get('region_id')->getData();
        $cities = $form->get('cities')->getData();
        $stations = $form->get('metro_stations')->getData();

        $service_info = $user->getServiceInfo() ?? new ServiceInfo();
        $service_info->setUser($user);

        $region = $this->regionsRepository->find($region_id);
        $cities = $this->citiesRepository->findBy([
            'id' => $cities
        ]);
        $stations = $this->metroStationsRepository->findBy([
            'id' => $stations
        ]);

        $service_info->setRegion($region);

        $service_info->getMetroStations()->clear();
        foreach ($stations as $item)
            $service_info->addMetroStation($item);

        $service_info->getCities()->clear();
        foreach ($cities as $item)
            $service_info->addCity($item);

        $this->em->persist($service_info);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }


    /**
     * Обновляем информацию о сервисах
     *
     * @param User $user
     * @param FormInterface $form
     * @return User
     */
    public function updateServices(UserInterface $user, FormInterface $form): User
    {
        $services = $form->get('services')->getData();
        $service_info = $user->getServiceInfo() ?? new ServiceInfo();
        $service_info->setUser($user);

        $current_service = $service_info->getServices();
        foreach ($current_service as $item) {
            $service_info->removeService($item);
            $this->em->remove($item); //чтобы запись из базы удалилась, иначе битые записи NULL
        }

        foreach ($services as $item) {
            $service = $this->servicesRepository->findById($item['service_id']);

            $service_price = new ServicePrice();
            $service_price
                ->setPrice($item['price'])
                ->setService($service);

            $this->em->persist($service_price);

            $service_info->addService($service_price);
        }

        $this->em->persist($service_info);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Обновляем информацию о пользователе на базе формы. Шаг 4
     *
     * @param User $user
     * @param FormInterface $form
     * @return User
     */
    public function updateWorktime(UserInterface $user, FormInterface $form): User
    {
        $worktime = $form->get('worktime')->getData();
        $service_info = $user->getServiceInfo() ?? new ServiceInfo();
        $service_info->setUser($user);
        $time_range = (int)$form->get('time_range')->getData();

        $current_worktime = $service_info->getWorkTime();
        foreach ($current_worktime as $item) {
            $service_info->removeWorkTime($item);
            $this->em->remove($item); //чтобы запись из базы удалилась, иначе битые записи NULL
        }

        $data = [];
        $new_worktime = [];
        if ($time_range) {
            foreach ($worktime as $item) {
                $data[$item['day']][] = $item['hour'];
            }
            foreach ($data as $day => $item) {
                foreach ($item as $hour) {
                    // Проверка на 0 и 23, что бы не добавить лишнего
                    $hour_max = $hour + 1;
                    $hour_min = $hour - 1;
                    if ($hour != 23 && !in_array($hour_max, $data[$day])) {
                        $new_worktime[] = [
                            'day' => $day,
                            'hour' => $hour_max,
                            'hidden' => true
                        ];
                    }
                    if ($hour != 0 && !in_array($hour_min, $data[$day])) {
                        $new_worktime[] = [
                            'day' => $day,
                            'hour' => $hour_min,
                            'hidden' => true,
                        ];
                    }

                    $new_worktime[] = [
                        'day' => $day,
                        'hour' => $hour,
                        'hidden' => false
                    ];
                }
            }
            //Удаляем дубликаты в случае появления
            $worktime = array_unique($new_worktime, SORT_REGULAR);
        }

        foreach ($worktime as $item) {
            if (!isset($item['hidden'])) {
                $item['hidden'] = 0;
            }
            $service_worktime = new ServiceWorkTime();
            $service_worktime
                ->setDay($item['day'])
                ->setHour($item['hour'])
                ->setHidden($item['hidden']);

            $this->em->persist($service_worktime);

            $service_info->addWorkTime($service_worktime);
        }

        $this->em->persist($service_info);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Обновляем информацию о пользователе о образовании
     *
     * @param User $user
     * @param FormInterface $form
     * @return User
     */
    public function updateEducation(UserInterface $user, FormInterface $form): User
    {
        $education = $form->get('education')->getData();
        $service_info = $user->getServiceInfo() ?? new ServiceInfo();

        $current_education = $service_info->getEducation();
        foreach ($current_education as $item) {
            $service_info->removeEducation($item);
            $this->em->remove($item); //чтобы запись из базы удалилась, иначе битые записи NULL
        }

        foreach ($education as $item) {
            $service_education = new ServiceEducation();
            $service_education
                ->setUniversity($item['university'])
                ->setYearFrom($item['from'])
                ->setYearTo($item['to']);

            $this->em->persist($service_education);

            $service_info->addEducation($service_education);
        }

        $this->em->persist($service_info);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }


    /**
     * Обновляем информацию о файлах
     *
     * @param User $user
     * @param FormInterface $form
     * @return User
     */
    public function updateFiles(UserInterface $user, FormInterface $form): User
    {
        $public_photo = $form->get('public_photo')->getData();
        $public_docs = $form->get('public_docs')->getData();
        $private_docs = $form->get('private_docs')->getData();

        $service_info = $user->getServiceInfo() ?? new ServiceInfo();
        $service_info->setUser($user);

        $images = $service_info->getImages() ?? new ServiceImages();

        //удаляем неактуальные картинки профиля
        $public_photo_id = [];
        if ($images->getPublicPhoto())
            foreach ($images->getPublicPhoto() as $item) {
                if (!in_array($item->getId(), $public_photo)) {
                    $images->removePublicPhoto($item);
                    $this->deleteFileById($item->getId());
                } else
                    $public_photo_id[] = $item->getId();
            }

        //перебираем картинки профиля и выставляем
        foreach ($public_photo as $item) {
            if (!in_array($item, $public_photo_id)) {
                $file = $this->filesRepository->find($item);
                $images->addPublicPhoto($file);
            }
        }

        //удаляем неактуальные картинки документов
        $public_docs_id = [];
        if ($images->getPublicDocs())
            foreach ($images->getPublicDocs() as $item) {
                if (!in_array($item->getId(), $public_docs)) {
                    $images->removePublicDoc($item);
                    $this->deleteFileById($item->getId());
                } else
                    $public_docs_id[] = $item->getId();
            }

        //перебираем картинки документов и выставляем
        foreach ($public_docs as $item) {
            if (!in_array($item, $public_docs_id)) {
                $file = $this->filesRepository->find($item);
                $images->addPublicDoc($file);
            }
        }

        //удаляем неактуальные картинки документов для модерации
        $private_docs_id = [];
        if ($images->getPrivateDocs())
            foreach ($images->getPrivateDocs() as $item) {
                if (!in_array($item->getId(), $private_docs)) {
                    $images->removePrivateDoc($item);
                    $this->deleteFileById($item->getId());
                } else
                    $private_docs_id[] = $item->getId();
            }

        //перебираем картинки документов модерации и выставляем
        foreach ($private_docs as $item) {
            if (!in_array($item, $private_docs_id)) {
                $file = $this->filesRepository->find($item);
                $images->addPrivateDoc($file);
            }
        }

        $service_info->setImages($images);

        $this->em->persist($service_info);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Удаляем файл по идентификатору
     *
     * @param int $file_id
     */
    private function deleteFileById(int $file_id)
    {
        $file = $this->filesRepository->find($file_id);

        $this->fileServices->deleteFile($file);
    }

    /**
     * Возвращаем информацию по пользователю публичную для аккаунта
     *
     * @param User $user
     * @return array[]
     */
    public function getPublicInfo(User $user): array
    {
        $result = [
            'id' => $user->getId(),
            'userinfo' => [
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'patronymic_name' => $user->getPatronymicName(),
                'gender' => $user->getGender(),
                'last_visit_time' => $this->userServices->getOnlineTime($user),
                'is_confirmed' => $user->getIsConfirmed(),
                'is_blocked' => $user->getIsBlocked(),
                'create_time' => $user->getCreateTime(),
                'callback_phone' => null,
                'about' => null,
                'slug'  => $user->getSlug(),
                'rating' => null,
                'time_range' => null,
            ],
            'comments' => [],
            'favorites_count' => 0, // hardcoded
            'favorite_added' => false,
            'location' => [
                'region' => null,
                'cities' => [],
                'metro' => [],
            ],
            'services' => [],
            'worktime' => [],
            'education' => [],
            'experience' => null,
            'images' => [
                'profile' => null,
                'public_docs' => [],
                'public_photo' => []
            ],
        ];
        if ($this->security->getUser()) {
            $favorite_added = $this->specialistFavoriteRepository->count(['specialist' => $user->getId(), 'user' => $this->security->getUser()->getId()]);
            if ($favorite_added) {
                $result['favorite_added'] = true;
            }
        }

        $favorite_count = $this->specialistFavoriteRepository->count(['specialist' => $user->getId()]);
        if ($favorite_count) {
            $result['favorites_count'] = $favorite_count;
        }

        if ($user->getServiceInfo()) {
            $result['userinfo']['callback_phone'] = $user->getServiceInfo()->getCallbackPhone();
            $result['userinfo']['about'] = $user->getServiceInfo()->getAbout();
            $result['userinfo']['rating'] = $user->getServiceInfo()->getRating();
            $result['userinfo']['time_range'] = $user->getServiceInfo()->getRating();
        }

        if ($user->getServiceInfo() && $user->getServiceInfo()->getRegion())
            $result['location']['region'] = [
                'id' => $user->getServiceInfo()->getRegion()->getId(),
                'name' => $user->getServiceInfo()->getRegion()->getName()
            ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getCities())
            foreach ($user->getServiceInfo()->getCities() as $item)
                $result['location']['cities'][] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getMetroStations())
            foreach ($user->getServiceInfo()->getMetroStations() as $item)
                $result['location']['metro'][] = [
                    'id' => $item->getId(),
                    'name' => $item->getStation(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getServices())
            foreach ($user->getServiceInfo()->getServices() as $item)
                $result['services'][] = [
                    'service' => [
                        'id' => $item->getService()->getId(),
                        'service_price_id' => $item->getId(),
                        'name' => $item->getService()->getName(),
                        'price' => $item->getPrice(),
                        'icon' => $item->getService()->getIcon(),
                    ],
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getWorkTime())
            foreach ($user->getServiceInfo()->getWorkTime() as $item)
                if (intval($item->getHidden()) != 1) {
                    $result['worktime'][] = [
                        'day' => $item->getDay(),
                        'hour' => $item->getHour(),
                    ];
                }
        if ($user->getServiceInfo() && $user->getServiceInfo()->getEducation())
            foreach ($user->getServiceInfo()->getEducation() as $item)
                $result['education'][] = [
                    'university' => $item->getUniversity(),
                    'from' => $item->getYearFrom(),
                    'to' => $item->getYearTo(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getExperience())
            $result['experience'] = $user->getServiceInfo()->getExperience();

        if ($user->getAvatar())
            $result['images']['profile'] = [
                'id' => $user->getAvatar()->getId(),
                'filepath' => $user->getAvatar()->getFilePath(),
                'type' => $user->getAvatar()->getFiletype(),
            ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getImages() && $user->getServiceInfo()->getImages()->getPublicDocs())
            foreach ($user->getServiceInfo()->getImages()->getPublicDocs() as $item)
                $result['images']['public_docs'][] = [
                    'id' => $item->getId(),
                    'filepath' => $item->getFilePath(),
                    'type' => $item->getFiletype(),
                    'is_image' => $item->getIsImage(),
                ];

        if ($user->getServiceInfo() && $user->getServiceInfo()->getImages() && $user->getServiceInfo()->getImages()->getPublicPhoto())
            foreach ($user->getServiceInfo()->getImages()->getPublicPhoto() as $item)
                $result['images']['public_photos'][] = [
                    'id' => $item->getId(),
                    'filepath' => $item->getFilePath(),
                    'type' => $item->getFiletype(),
                    'is_image' => $item->getIsImage(),
                ];

        if (!empty($result['worktime'])) {
            $result['schedule'] = ServiceWorkTime::getPublicWorkTime($result['worktime']);
        }

        return $result;
    }

    /**
     * Получаем информацию о специалистах по фильтру
     *
     * @param array|null $filter
     *
     * @return [type]
     */
    public function getByFilter(array $filter = null)
    {
        $search = $this->userRepository->filterSQL($filter);

        $users_ids = [];
        foreach ($search['result'] as $user) {
            $users_ids[] = $user['user_id'];
        }

        $users = $this->userRepository->getUsersById($users_ids);
        $result['resultTotalCount'] = $search['resultTotalCount'];
        $result = [];
        foreach ($users_ids as $id) {
            foreach ($users as $key => $user) {
                if ($id == $user->getId())
                    $result[$key] = $this->getPublicInfo($user);
            }
        }

        return ['result' => array_values($result), 'resultTotalCount' => $search['resultTotalCount']];
    }
}
