<?php

namespace App\Services\Specialist;

use App\Entity\Requests;
use App\Entity\RequestsSpecialists;
use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\RequestsService;

use App\Repository\CitiesRepository;
use App\Repository\FilesRepository;
use App\Repository\MetroStationsRepository;
use App\Repository\RegionsRepository;
use App\Repository\ServicesRepository;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use App\Repository\RequestsServiceRepository;
use App\Repository\RequestsSpecialistsRepository;
use App\Repository\ServicePriceRepository;
use App\Repository\RequestsRepository;
use App\Repository\ChatRepository;

use App\Services\File\FileServices;
use App\Services\User\UserServices;
use App\Services\Specialist\SpecialistServices;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\LogicException;
use App\Entity\ServiceWorkTime;
use Symfony\Component\BrowserKit\Request;
use LogicException as GlobalLogicException;

class RequestsServices
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
        ServicePriceRepository $servicePriceRepository,
        RequestsRepository $requestsRepository,
        RequestsSpecialistsRepository $requestsSpecialistsRepository,
        SpecialistServices $specialistServices,
        ChatRepository $chatRepository,
        MessageRepository $messageRepository,
        RequestsServiceRepository $requestsServiceRepository
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
        $this->servicePriceRepository = $servicePriceRepository;
        $this->requestsRepository = $requestsRepository;
        $this->requestsSpecialistsRepository = $requestsSpecialistsRepository;
        $this->chatRepository = $chatRepository;
        $this->specialistServices = $specialistServices;
        $this->messageRepository = $messageRepository;
        $this->requestsServiceRepository = $requestsServiceRepository;
    }

    /**
     * Сохранение формы заявки на услугу
     *
     * @param string $formType
     * @param array $data
     * @param null $entity
     * @return FormInterface
     */
    public function saveRequestSingle(UserInterface $user, FormInterface $form): Requests
    {
        $city_id = $form->get('city_id')->getData();
        $metro_id = $form->get('metro_id')->getData();
        $address = $form->get('address')->getData();
        $service_price_id = $form->get('service_price_id')->getData();
        $convenient_time = $form->get('convenient_time')->getData();
        $date = $form->get('date')->getData();
        $worktime = $form->get('worktime')->getData();
        $additional_information = $form->get('additional_information')->getData();
        $request_type = $form->get('request_type')->getData();
        $specialist_id = $form->get('specialist_id')->getData();
        $city = null;
        $metro = null;

        if ($city_id) {
            $city = $this->citiesRepository->find($city_id);
            if (!$city)
                throw new LogicException('Не найден город с идентификатором ' . $city_id);
        }

        if ($metro_id) {
            $metro = $this->metroStationsRepository->find($metro_id);
            if (!$metro)
                throw new LogicException('Не найдено метро с идентификатором ' . $metro_id);
        }

        $specialist_entity = $this->userRepository->findById($specialist_id);

        $service_request = new Requests();
        $service_request
            ->setUser($user)
            ->setCity($city)
            ->setMetro($metro)
            ->setAddress($address)
            ->setConvenientTime($convenient_time)
            ->setDate($date)
            ->setWorkTime($worktime)
            ->setAdditionalInformation($additional_information)
            ->setCreatedTime(time())
            ->setFlagClosed(false)
            ->setRequestType($request_type);

        $this->em->persist($service_request);
        $this->em->flush();

        $request_specialist = $this->saveRequestSingleSpecialist($service_request, $specialist_entity);
        $this->saveRequestServiceSingle($service_price_id, $request_specialist);
        return $service_request;
    }

    /**
     * Сохранение формы заявки на услугу
     *
     * @param string $formType
     * @param array $data
     * @param null $entity
     * @return FormInterface
     */
    public function saveRequestQuiz(UserInterface $user, FormInterface $form): Requests
    {
        $city_id = $form->get('city_id')->getData();
        $metro_id = $form->get('metro_id')->getData();
        $address = $form->get('address')->getData();
        $service_id = $form->get('service_id')->getData();
        $convenient_time = $form->get('convenient_time')->getData();
        $date = $form->get('date')->getData();
        $worktime = $form->get('worktime')->getData();
        $additional_information = $form->get('additional_information')->getData();
        $gender = $form->get('gender')->getData();
        $price_min = $form->get('price_min')->getData();
        $price_max = $form->get('price_max')->getData();
        $experience = $form->get('experience')->getData();

        $request_type = $form->get('request_type')->getData();
        $city = null;
        $metro = null;

        if ($convenient_time == "any") {
            $worktime = null;
            $date = null;
        }
        // Заполняем массив если если дата и день выбрана, но нет времени
        if ($convenient_time == "specified" && !empty($date) && empty($worktime)) {
            $i = 0;
            $week_day = date("w", mktime(0, 0, 0, $date->format('m'), $date->format('d'), $date->format('Y')));
            while ($i != 24) {
                $worktime[$i]['day'] = $week_day;
                $worktime[$i]['hour'] = $i;
                $i++;
            }
        }

        if ($city_id) {
            $city = $this->citiesRepository->find($city_id);
            if (!$city)
                throw new LogicException('Не найден город с идентификатором ' . $city_id);
        }

        if ($metro_id) {
            $metro = $this->metroStationsRepository->find($metro_id);
            if (!$metro)
                throw new LogicException('Не найдено метро с идентификатором ' . $metro_id);
        }

        if ($gender == "any") {
            $gender = null;
        }

        $filter['experience'] =  $experience;
        $filter['price_min'] = $price_min;
        $filter['price_max'] = $price_max;
        $filter['gender'] = $gender;
        $filter['worktime'] = $worktime;
        $filter['city_id'] = $city_id;
        $filter['metro_id'] = $metro_id;
        $filter['service_id'] = $service_id;
        $filter['rating'] = null;
        $filter['sort'] = null;
        $filter['page'] = null;

        $result = $this->userRepository->filterSQL($filter);

        if (!$result) {
            throw new LogicException('Подходящие специалисты не найдены');
        }

        $specialist_ids = [];
        foreach ($result['result'] as $specialist) {
            $specialist_ids[] = $specialist['user_id'];
        }

        $specialist_entity = $this->userRepository->getUsersById($specialist_ids);

        $service_request = new Requests();
        $service_request
            ->setUser($user)
            ->setCity($city)
            ->setMetro($metro)
            ->setAddress($address)
            ->setConvenientTime($convenient_time)
            ->setDate($date)
            ->setWorkTime($worktime)
            ->setAdditionalInformation($additional_information)
            ->setCreatedTime(time())
            ->setFlagClosed(false)
            ->setQuizFilter($filter)
            ->setRequestType($request_type);

        $this->em->persist($service_request);
        $this->em->flush();

        $this->saveRequestQuizSpecialist($service_request, $specialist_entity);
        $this->saveRequestServiceQuiz($service_request, $service_id, $specialist_entity);
        return $service_request;
    }

    /**
     * Сохранение специлиста для заявки
     * 
     * @param UserInterface $user
     * @param FormInterface $form
     * 
     * @return array
     */
    public function saveRequestSingleSpecialist($service_request, $specialist_entity): RequestsSpecialists
    {
        $seviceRequestSpecialists = new RequestsSpecialists();
        $chat = new Chat();
        $seviceRequestSpecialists
            ->setRequest($service_request)
            ->setSpecialist($specialist_entity)
            ->setStatus('review');

        $chat->setRequestSpecialist($seviceRequestSpecialists);

        $this->em->persist($seviceRequestSpecialists);
        $this->em->persist($chat);
        $this->em->flush();

        return $seviceRequestSpecialists;
    }

    /**
     * Сохранение услуги для заявки
     * 
     * @param UserInterface $user
     * @param FormInterface $form
     * 
     * @return array
     */
    public function saveRequestServiceSingle($services, $request_specialist)
    {
        foreach ($services as $service) {
            $item = $this->servicePriceRepository->find($service);
            if ($item) {
                $requestsService = new RequestsService();
                $requestsService
                    ->setRequestSpecialists($request_specialist)
                    ->setService($item->getService())
                    ->setPrice($item->getPrice());
                $this->em->persist($requestsService);
                $this->em->flush();
            }
        }
    }

    /**
     * Сохранение специлиста для заявки
     * 
     * @param UserInterface $user
     * @param FormInterface $form
     * 
     * @return array
     */
    public function saveRequestQuizSpecialist($service_request, $specialist_entity)
    {
        foreach ($specialist_entity as $specialist) {
            $chat = new Chat();
            $seviceRequestSpecialists = new RequestsSpecialists();
            $seviceRequestSpecialists
                ->setRequest($service_request)
                ->setSpecialist($specialist)
                ->setStatus('review');
            $chat->setRequestSpecialist($seviceRequestSpecialists);
            $this->em->persist($seviceRequestSpecialists);
            $this->em->persist($chat);
            $this->em->flush();
        }
    }

    /**
     * Сохранение услуги для заявки
     * 
     * @param UserInterface $user
     * @param FormInterface $form
     * 
     * @return array
     */
    public function saveRequestServiceQuiz($service_request, $service_id, $specialist_entity)
    {
        foreach ($specialist_entity as $item) {
            $item_services = $item->getServiceInfo()->getServices();

            $item_price = [];
            foreach ($item_services as $serv) {
                if ($serv->getService()->getId() == $service_id) {
                    $item_price[] = $serv;
                }
            }

            if (empty($item_price[0]))
                continue;

            $requests_specialists = $this->requestsSpecialistsRepository->findOneBy(['request' => $service_request->getId(), 'specialist' => $item->getId()]);

            $service_entity = $this->servicePriceRepository->find($item_price[0]);
            if ($service_entity) {
                $requestsService = new RequestsService();
                $requestsService
                    ->setRequestSpecialists($requests_specialists)
                    ->setService($service_entity->getService())
                    ->setPrice($service_entity->getPrice());
                $this->em->persist($requestsService);
                $this->em->flush();
            }
        }
    }

    /**
     * Специалист получает заявку по id
     * 
     * @param mixed $user
     * @param mixed $id
     * 
     * @return [type]
     */
    public function getSpecialistRequest(UserInterface $specialist, $request_id)
    {
        $request = $this->requestsSpecialistsRepository->findOneBy(['request' => $request_id, 'specialist' => $specialist->getId()]);

        if (!$request) {
            throw new LogicException('Заявка не существует');
        }

        $user_requested = $request->getRequest()->getUser();

        return $this->getRequestData($user_requested, $specialist, $request);
    }

    /**
     * Пользователь получает заявку по request_specialist_id
     * 
     * @param mixed $user
     * @param mixed $request_specialist_id
     * 
     * @return [type]
     */
    public function getUserRequest(UserInterface $user, $request_specialist_id)
    {
        $request = $this->requestsSpecialistsRepository->findRequestUser($user->getId(), $request_specialist_id);

        if (!$request) {
            throw new LogicException('Заявка не существует');
        }

        $specialist = $request->getSpecialist();

        return $this->getRequestData($user, $specialist, $request);
    }

    /**
     * Формирование заявки
     * 
     * @param UserInterface $user
     * @param UserInterface $specialist
     * 
     * @return [type]
     */
    public function getRequestData(UserInterface $user, UserInterface $specialist, $request, $closed = null)
    {
        $result = [
            'userInfo' => [
                'user_id' => $user->getId(),
                'user_img' => null,
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'slug' => $user->getSlug(),
                'last_visit_time' => $this->userServices->getOnlineTime($user),
            ],
            'specialistInfo' => [
                'specialist_id' => $specialist->getId(),
                'user_img' => null,
                'first_name' => $specialist->getFirstName(),
                'last_name' => $specialist->getLastName(),
                'slug' => $specialist->getSlug(),
                'last_visit_time' => $this->userServices->getOnlineTime($specialist),
            ],
            'requestInfo' => [
                'specialist_status' => $request->getStatus(),
                'request_id' => $request->getRequest()->getId(),
                'city' => null,
                'metro' => null,
                'address' => $request->getRequest()->getAddress(),
                'services' => null,
                'convenient_time' => $request->getRequest()->getConvenientTime() == "specified" ? "В выбранную дату и/или время" : "В любое время",
                'date' => $request->getRequest()->getDate()->format("Y-m-d"),
                'worktime' => null,
                'worktime_formatted' => null,
                'additional_information' => $request->getRequest()->getAdditionalInformation(),
                'created_time' => date("Y-m-d", $request->getRequest()->getCreatedTime()),
                'flag_closed' => $request->getRequest()->getFlagClosed(),
                'request_type' => $request->getRequest()->getRequestType(),
                'quiz_filter' => $request->getRequest()->getQuizFilter(),
            ],
            'chat' => []
        ];

        // Если заявка закрыта, ставим статус closed by user
        if ($closed) {
            if ($request->getStatus() === $this->requestsRepository::STATUS_DISCUSSION)
                $result['requestInfo']['specialist_status'] = $this->requestsRepository::STATUS_CLOSED_BY_USER;
        }

        if ($user->getAvatar()) {
            $result['userInfo']['user_img'] = $user->getAvatar()->getFilePath();
        }

        if ($specialist->getAvatar()) {
            $result['specialistInfo']['user_img'] = $specialist->getAvatar()->getFilePath();
        }

        if ($request->getRequest()->getMetro()) {
            $result['requestInfo']['metro'] = $request->getRequest()->getMetro()->getStation();
        }

        if ($request->getRequest()->getCity()) {
            $result['requestInfo']['city'] = $request->getRequest()->getCity()->getName();
        }

        if (!empty($request->getRequest()->getWorkTime())) {
            $result['requestInfo']['worktime'] = $request->getRequest()->getWorkTime();
            $result['requestInfo']['worktime_formatted'] = ServiceWorkTime::getPublicWorkTime($request->getRequest()->getWorkTime());
        }

        $services = $this->requestsServiceRepository->findBy(['requests_specialists' => $request->getId()]);
        if ($services) {
            foreach ($services as $service) {
                $result['requestInfo']['services'][] = [
                    'id' => $service->getService()->getId(),
                    'name' => $service->getService()->getName(),
                    'price' => $service->getPrice()
                ];
            }
        }


        if ($request->getChat()) {
            foreach ($request->getChat()->getMessage() as $item) {
                $result['chat'][] = [
                    'first_name' => $item->getUser()->getFirstName(),
                    'last_name' => $item->getUser()->getLastName(),
                    'message' => $item->getComment(),
                    'created_time' => date("Y-m-d H:i:s", $item->getCreateTime())
                ];
            }
        }

        return $result;
    }

    /**
     * Отправка сообщения в чат
     * 
     * @param mixed $user
     * @param mixed $id
     * 
     * @return [type]
     */
    public function sendMessage(UserInterface $user, $data): Message
    {
        $request = $this->requestsSpecialistsRepository->find($data['requests_specialists_id']);
        $specialist_id = null;
        $chat = null;

        if (!$request) {
            throw new LogicException('Заявка не существует');
        }

        $user_id = $request->getRequest()->getUser()->getId();
        $specialist_request = $this->requestsSpecialistsRepository->findOneBy(['id' => $data['requests_specialists_id'], 'specialist' => $user->getId()]);

        if ($specialist_request) {
            $specialist_id = $specialist_request->getSpecialist()->getId();
            $chat = $specialist_request->getChat();
        }

        if ($user->getId() != $user_id && $user->getId() != $specialist_id) {
            throw new LogicException('Вы не можете оставлять сообщение в этом чате');
        }

        if ($request->getRequest()->getFlagClosed() == 1) {
            throw new LogicException('Заявка закрыта. Оставлять сообщения нельзя.');
        }

        if ($request->getStatus() != $this->requestsRepository::STATUS_DISCUSSION) {
            throw new LogicException('Вы не можете оставлять сообщение в этом чате');
        }


        if (!$chat) {
            $chat = $this->chatRepository->findOneBy(['request_specialist' => $data['requests_specialists_id']]);
        }

        $message = new Message();
        $message->setUser($user)
            ->setChat($chat)
            ->setComment($data['message'])
            ->setCreateTime(time())
            ->setUpdateTime(time());
        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    /**
     * Получение списка заявок для пользователя single / quiz
     * 
     * @param UserInterface $user
     * @param string $status
     * @param int $page
     * 
     * @return [type]
     */
    public function getUserRequestsAll(UserInterface $user, int $page, $closed)
    {
        $offset = ($page - 1) * $this->requestsRepository::PAGE_OFFSET;
        $limit = $this->requestsRepository::PAGE_OFFSET;

        $result_total_count = $this->requestsRepository->count(['user' => $user, 'flag_closed' => $closed]);
        $requestsRepository = $this->requestsRepository->findBy(['user' => $user, 'flag_closed' => $closed], ['id' => 'DESC'], $limit, $offset);

        $result = [];
        if (!$requestsRepository) {
            return ['result' => $result, 'result_total_count' => 0];
        }

        foreach ($requestsRepository as $key => $request) {
            $result[$key] = [
                'requestInfo' => [
                    'request_specialist_id' => null,
                    'request_id' => $request->getId(),
                    'city' => null,
                    'metro' => null,
                    'created_time' => date("Y-m-d H:m:s", $request->getCreatedTime()),
                    'address' => $request->getAddress(),
                    'convenientTime' => $request->getConvenientTime(),
                    'convenientTimeFormatted' => ($request->getConvenientTime() == "any") ? "Время оказания услуги в любое время" : "Время оказания услуги в определенное время",
                    'date' => $request->getDate()->format("Y-m-d"),
                    'workTime' => null,
                    'workTimeFormatted' => null,
                    'additionalInformation' => $request->getAdditionalInformation() ?? "Не указана",
                    'requestType' => $request->getRequestType(),
                    'flag_closed' => $request->getFlagClosed()
                ]
            ];

            if ($request->getWorkTime()) {
                $result[$key]['requestInfo']['workTime'] = $request->getWorkTime();
                $result[$key]['requestInfo']['workTimeFormatted'] = ServiceWorkTime::getPublicWorkTime($request->getWorkTime());
            }

            if ($request->getCity()) {
                $result[$key]['requestInfo']['city'] = $request->getCity()->getName();
            }
            if ($request->getMetro()) {
                $result[$key]['requestInfo']['metro'] = $request->getMetro()->getStation();
            }

            if ($request->getRequestType() == 'quiz') {
                $quiz_data = $request->getQuizFilter();
                $result[$key]['quiz']['request_sended'] = count($this->requestsSpecialistsRepository->findBy(['request' => $request->getId()]));
                $result[$key]['quiz']['request_discussion'] = count($this->requestsSpecialistsRepository->findBy(['request' => $request->getId(), 'status' => 'discussion']));

                if (!empty($quiz_data['price_min']) && !empty($quiz_data['price_max'])) {
                    $result[$key]['quiz']['service_price'] = 'от ' . $quiz_data['price_min'] . ' руб. до ' . $quiz_data['price_max'] . ' руб.';
                }

                $result[$key]['quiz']['date'] = $quiz_data['date'] ?? null;
                $result[$key]['quiz']['rate'] = $quiz_data['rating'] ?? null;

                if (!empty($quiz_data['service_id'])) {
                    $service = $this->servicesRepository->find((int)$quiz_data['service_id']);
                    if ($service) {
                        $result[$key]['quiz']['service'] = $service->getName();
                    }
                }
                // Пол
                switch ($quiz_data['gender']) {
                    case 'male':
                        $result[$key]['quiz']['gender'] = "Мужской";
                        break;
                    case 'female':
                        $result[$key]['quiz']['gender'] = "Женский";
                        break;
                    case null:
                    default:
                        $result[$key]['quiz']['gender'] = "Любой";
                        break;
                }

                // Опыт
                switch ($quiz_data['experience']) {
                    case 'exist':
                        $result[$key]['quiz']['experience'] = 'C опытом';
                        break;
                    case 'less1':
                        $result[$key]['quiz']['experience'] = "Опыт меньше года";
                        break;
                    case 'from1to3':
                        $result[$key]['quiz']['experience'] = "Опыт от 1 до 3 лет";
                        break;
                    case 'from3to5':
                        $result[$key]['quiz']['experience'] = "Опыт от 3 до 5 лет";
                        break;
                    case 'from5to10':
                        $result[$key]['quiz']['experience'] = "Опыт от 5 до 10 лет";
                        break;
                    case 'more10':
                        $result[$key]['quiz']['experience'] = "Опыт больше 10 лет";
                        break;
                    default:
                        $result[$key]['quiz']['experience'] = "Без опыта";
                        break;
                }
            }

            if ($request->getRequestType() == 'single') {
                $specialist_repository = $this->requestsSpecialistsRepository->findOneBy(['request' => $request->getId()]);
                $specialist = $this->specialistServices->getPublicInfo($specialist_repository->getSpecialist());
                $result[$key]['specialistInfo'] = $specialist;
                $result[$key]['requestInfo']['request_specialist_id'] = $specialist_repository->getId();
                $service = $this->requestsServiceRepository->findOneBy(['requests_specialists' => $specialist_repository]);
                $result[$key]['requestInfo']['service'] = [
                    'service_name' => $service->getService()->getName(),
                    'price' => $service->getPrice(),
                ];
            }
        }

        return ['result' => $result, 'result_total_count' => $result_total_count];
    }

    /**
     * // Получения списка специалистов для конкретной заявке quiz
     * 
     * @param UserInterface $user
     * @param string $status
     * @param int $page
     * @param int $request_id
     * 
     * @return [type]
     */
    public function getUserQuizRequest(UserInterface $user, int $page, int $request_id, int $active)
    {
        $request = $this->requestsRepository->findOneBy(['user' => $user->getId(), 'id' => $request_id]);
        $result = [];

        $status = null;

        if (!$request) {
            throw new LogicException('Заявка не существует');
        }

        if ($request->getRequestType() != 'quiz') {
            throw new LogicException('Заявка single');
        }

        $closed = intval($request->getFlagClosed());

        if (!$closed) {
            if (!$active) {
                $status = [$this->requestsRepository::STATUS_CLOSED_BY_USER, $this->requestsRepository::STATUS_CLOSED_BY_SPECIALIST];
            } else {
                $status = $this->requestsRepository::STATUS_DISCUSSION;
            }
        } else {
            $status = [
                $this->requestsRepository::STATUS_CLOSED_BY_USER,
                $this->requestsRepository::STATUS_CLOSED_BY_SPECIALIST,
                $this->requestsRepository::STATUS_DISCUSSION
            ];
        }

        $offset = ($page - 1) * $this->requestsRepository::PAGE_OFFSET;
        $limit = $this->requestsRepository::PAGE_OFFSET;

        $result_total_count = intval($this->requestsSpecialistsRepository->findRequestPaginationQuiz($request_id, $closed, $status, null, null, true));
        $request_specialist = $this->requestsSpecialistsRepository->findRequestPaginationQuiz($request_id, $closed, $status, $limit, $offset);

        if (!$request_specialist) {
            return ['result' => $result, 'result_total_count' => 0];
        }

        foreach ($request_specialist as $key => $request) {
            $result[$key] = $this->getRequestData($request->getRequest()->getUser(), $request->getSpecialist(), $request, $closed);
        }

        return ['result' => $result, 'result_total_count' => $result_total_count];
    }

    /**
     * @param UserInterface $user
     * @param string $status
     * @param int $page
     * 
     * @return [type]
     */
    public function getSpecialistRequestsAll(UserInterface $specialist, int $page, string $status)
    {
        $offset = ($page - 1) * $this->requestsRepository::PAGE_OFFSET;
        $limit = $this->requestsRepository::PAGE_OFFSET;
        $closed = 0;
        if ($status === 'closed') {
            $closed = 1;
        } else {
            $exp_values = $this->requestsRepository::STATUS;
            if (in_array($status, $exp_values) === FALSE) {
                throw new LogicException('Такого статуса нет');
            }
        }

        $result_total_count = intval($this->requestsSpecialistsRepository->findRequestPagination($specialist->getId(), $closed, $status, null, null, true));
        $request_specialist = $this->requestsSpecialistsRepository->findRequestPagination($specialist->getId(), $closed, $status, $limit, $offset);

        $result = [];
        if (!$request_specialist) {
            return ['result' => [], 'result_total_count' => 0];
        }
        foreach ($request_specialist as $key => $request) {
            $result[$key] = [
                'userInfo' => [
                    'firstName' => $request->getRequest()->getUser()->getFirstName(),
                    'lastName' => $request->getRequest()->getUser()->getLastName(),
                    'gender' => $request->getRequest()->getUser()->getGender(),
                    'last_visit_time' => $this->userServices->getOnlineTime($request->getRequest()->getUser()),
                    'image' => [],
                ],
                'requestInfo' => [
                    'request_id' => $request->getRequest()->getId(),
                    'request_specialist_id' => $request->getId(),
                    'title' => '#' . $request->getRequest()->getId(),
                    'created_time' => date("Y-m-d H:m:s", $request->getRequest()->getCreatedTime()),
                    'address' => $request->getRequest()->getAddress(),
                    'convenientTime' => $request->getRequest()->getConvenientTime(),
                    'convenientTimeFormatted' => ($request->getRequest()->getConvenientTime() == "any") ? "Время оказания услуги в любое время" : "Время оказания услуги в определенное время",
                    'date' => $request->getRequest()->getDate()->format("Y-m-d"),
                    'workTime' => null,
                    'workTimeFormatted' => null,
                    'additionalInformation' => $request->getRequest()->getAdditionalInformation() ?? "Не указана",
                    'requestType' => $request->getRequest()->getRequestType(),
                    'flag_closed' => $request->getRequest()->getFlagClosed(),
                    'message_count' => 0,
                    'message_unread' => 0
                ]
            ];

            if ($request->getRequest()->getWorkTime()) {
                $result[$key]['requestInfo']['workTime'] = $request->getRequest()->getWorkTime();
                $result[$key]['requestInfo']['workTimeFormatted'] = ServiceWorkTime::getPublicWorkTime($request->getRequest()->getWorkTime());
            }

            if ($request->getRequest()->getUser()->getAvatar())
                $result[$key]['userInfo']['image'] = [
                    'id' => $request->getRequest()->getUser()->getAvatar()->getId(),
                    'filepath' => $request->getRequest()->getUser()->getAvatar()->getFilePath(),
                    'type' => $request->getRequest()->getUser()->getAvatar()->getFiletype(),
                    'is_image' => $request->getRequest()->getUser()->getAvatar()->getIsImage(),
                ];

            $chat = $this->chatRepository->findOneBy(['request_specialist' => $request->getId()]);
            if ($chat) {
                $messages = $this->messageRepository->findBy(['chat' => $chat->getId()]);
                if ($messages) {
                    $result[$key]['requestInfo']['message_count'] = count($messages);
                }
            }
        }

        return ['result' => $result, 'result_total_count' => $result_total_count];
    }
}
