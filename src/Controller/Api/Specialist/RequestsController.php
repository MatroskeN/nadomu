<?php

namespace App\Controller\Api\Specialist;

use App\Controller\Base\BaseApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use \Symfony\Component\Security\Core\Security as CoreSecurity;
use App\Services\User\UserServices;
use App\Repository\UserRepository;
use App\Services\Specialist\RequestsServices;
use App\Form\RequestsSingleDataType;
use App\Form\RequestsQuizDataType;
use Symfony\Component\Security\Core\Exception\LogicException;

class RequestsController extends BaseApiController
{
    /**
     * Создание заявки на услугу специалисту SINGLE
     *
     * @Route("/api/request/single", name="api_request_create_single", methods={"POST"})
     * 
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="specialist_id", type="integer", description="ID специалиста", example="10"),
     *       @OA\Property(property="city_id", type="integer", description="ID города", example="1"),
     *       @OA\Property(property="metro_id", type="integer", description="ID метро", example="2"),
     *       @OA\Property(property="address", type="string", description="Адрес оказания услуги", example="Ул. Пушкина дом 74 кв 26"),
     *       @OA\Property(property="service_price_id",
     *          type="array",
     *          description="ID услуги",
     *          example={1,2,3,4},
     *          @OA\Items(
     *                      @OA\Property(
     *                         property="service_id",
     *                         type="integer",
     *                         example="1"
     *                      ),
     *                      @OA\Property(
     *                         property="price",
     *                         type="integer",
     *                         example="1000"
     *                      )
     *                ),
     *       ),
     *       @OA\Property(property="convenient_time", type="string", description="Время оказания услуги", example="specified"),
     *       @OA\Property(property="date", type="string", description="Дата оказания услуги", example="2022-10-16"),
     *       @OA\Property(property="worktime",
     *          type="array",
     *          description="Дни недели (1-7) и время (0 - 23)",
     *          example={{
     *                  "day": 1,
     *                  "hour": 10
     *                }, {
     *                  "day": 1,
     *                  "hour": 11
     *                }, {
     *                  "day": 1,
     *                  "hour": 12
     *                }},
     *          @OA\Items(
     *                      @OA\Property(
     *                         property="day",
     *                         type="integer",
     *                         example="1"
     *                      ),
     *                      @OA\Property(
     *                         property="hour",
     *                         type="integer",
     *                         example="10"
     *                      )
     *                ),
     *       ),
     *       @OA\Property(property="additional_information", type="string", description="Доп. информация", example="Наберите перед приездом"),
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Заявка успешно создана")
     * @OA\Response(response=400, description="Ошибка валидации")
     * @OA\Response(response=404, description="Значение поля отсутствует")
     *
     * @OA\Tag(name="request")
     * @Security(name="Bearer")
     */
    public function serviceRequestCreateSingle_action(Request $request, CoreSecurity $security, RequestsServices $requestsServices): Response
    {
        $user = $security->getUser();
        $request_service['specialist_id'] = (int)$this->getJson($request, 'specialist_id');
        $request_service['city_id'] = (int)$this->getJson($request, 'city_id');
        $request_service['metro_id'] = (int)$this->getJson($request, 'metro_id');
        $request_service['address'] = (string)$this->getJson($request, 'address');
        $request_service['service_price_id'] = (array)$this->getJson($request, 'service_price_id');
        $request_service['convenient_time'] = (string)$this->getJson($request, 'convenient_time'); // any or specified
        $request_service['date'] = (string)$this->getJson($request, 'date');
        $request_service['worktime'] = (array)$this->getJson($request, 'worktime');
        $request_service['additional_information'] =  (string)$this->getJson($request, 'additional_information');
        $request_service['request_type'] = "single";

        $form = $this->createFormByArray(RequestsSingleDataType::class, $request_service);
        if ($form->isValid()) {
            // Save request
            try {
                $requestsServices->saveRequestSingle($user, $form);
            } catch (LogicException $e) {
                return $this->jsonError(['#' => $e->getMessage()]);
            }
        } else
            return $this->formValidationError($form);

        return $this->jsonSuccess();
    }

    /**
     * Создание заявки на услугу специалисту QUIZ
     *
     * @Route("/api/request/quiz", name="api_request_create_quiz", methods={"POST"})
     * 
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="gender", type="string", description="Пол", example="male"),
     *       @OA\Property(property="experience", type="string", description="Опыт", example="from3to5"), 
     *       @OA\Property(property="price_min", type="integer", description="Минимальная цена", example="100"),
     *       @OA\Property(property="price_max", type="integer", description="Максимальная цена", example="1000"),
     *       @OA\Property(property="city_id", type="integer", description="ID города", example="1"),
     *       @OA\Property(property="metro_id", type="integer", description="ID метро", example="2"),
     *       @OA\Property(property="address", type="string", description="Адрес оказания услуги", example="Ул. Пушкина дом 74 кв 26"),
     *       @OA\Property(property="service_id", type="integer", description="ID Услуги", example="2"),
     *       @OA\Property(property="convenient_time", type="string", description="Время оказания услуги", example="specified"),
     *       @OA\Property(property="date", type="string", description="Дата оказания услуги", example="2022-10-16"),
     *       @OA\Property(property="worktime",
     *          type="array",
     *          description="Дни недели (1-7) и время (0 - 23)",
     *          example={{
     *                  "day": 1,
     *                  "hour": 10
     *                }, {
     *                  "day": 1,
     *                  "hour": 11
     *                }, {
     *                  "day": 1,
     *                  "hour": 12
     *                }},
     *          @OA\Items(
     *                      @OA\Property(
     *                         property="day",
     *                         type="integer",
     *                         example="1"
     *                      ),
     *                      @OA\Property(
     *                         property="hour",
     *                         type="integer",
     *                         example="10"
     *                      )
     *                ),
     *       ),
     *       @OA\Property(property="additional_information", type="string", description="Доп. информация", example="Наберите перед приездом"),
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Заявка успешно создана")
     * @OA\Response(response=400, description="Ошибка валидации")
     * @OA\Response(response=404, description="Значение поля отсутствует")
     *
     * @OA\Tag(name="request")
     * @Security(name="Bearer")
     */
    public function serviceRequestCreateQuiz_action(Request $request, CoreSecurity $security, RequestsServices $requestsServices): Response
    {
        $user = $security->getUser();
        $request_service['city_id'] = (int)$this->getJson($request, 'city_id');
        $request_service['metro_id'] = (int)$this->getJson($request, 'metro_id');
        $request_service['address'] = (string)$this->getJson($request, 'address');
        $request_service['service_id'] = (int)$this->getJson($request, 'service_id');
        $request_service['convenient_time'] = (string)$this->getJson($request, 'convenient_time'); // any or specified
        $request_service['date'] = (string)$this->getJson($request, 'date');
        $request_service['worktime'] = (array)$this->getJson($request, 'worktime');
        $request_service['additional_information'] =  (string)$this->getJson($request, 'additional_information');
        $request_service['gender'] = (string)$this->getJson($request, 'gender');
        $request_service['experience'] = (string)$this->getJson($request, 'experience');
        $request_service['price_min'] = (string)$this->getJson($request, 'price_min');
        $request_service['price_max'] = (string)$this->getJson($request, 'price_max');
        $request_service['request_type'] = "quiz";

        $form = $this->createFormByArray(RequestsQuizDataType::class, $request_service);
        if ($form->isValid()) {
            // Save request
            try {
                $requestsServices->saveRequestQuiz($user, $form);
            } catch (LogicException $e) {
                return $this->jsonError(['#' => $e->getMessage()]);
            }
        } else
            return $this->formValidationError($form);

        return $this->jsonSuccess();
    }

    /**
     * Получение информации о заявке для специалиста
     *
     * @Route("/api/specialist/request/{id}", name="api_specialist_get_request", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Указываем request ID",
     *     @OA\Schema(type="integer", example=1)
     * )
     *
     * @OA\Response(response=200, description="Информация предоставлена")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="request")
     * @Security(name="Bearer")
     */
    public function getSpecialistRequest(Request $request, CoreSecurity $security, RequestsServices $requestsServices, int $id)
    {
        $specialist = $security->getUser();

        if (!$specialist->getIsSpecialist())
            return $this->jsonError(['role' => 'Пользователь не является специалистом!'], 403);
        try {
            $result = $requestsServices->getSpecialistRequest($specialist, $id);
        } catch (LogicException $e) {
            return $this->jsonError(['request_id' => $e->getMessage()]);
        }
        return $this->jsonSuccess(['result' => $result]);
    }

    /**
     * Получение информации о заявке для пользователя
     *
     * @Route("/api/user/request/{id}", name="api_user_get_request", methods={"GET"})
     * 
     * @OA\Get(path="/api/user/request/{id}", operationId="getUserRequest",),
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Указываем request specialist ID",
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\Response(response=200, description="Информация предоставлена")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="request")
     * @Security(name="Bearer")
     */
    public function getUserRequest(Request $request, CoreSecurity $security, RequestsServices $requestsServices, int $id)
    {
        $user = $security->getUser();

        try {
            $result = $requestsServices->getUserRequest($user, $id);
        } catch (LogicException $e) {
            return $this->jsonError(['request_specialist_id' => $e->getMessage()]);
        }
        return $this->jsonSuccess(['result' => $result]);
    }

    /**
     * Оставить сообщение в чате заявки
     *
     * @Route("/api/specialist/message/request", name="api_specialist_set_request_message", methods={"POST"})
     * 
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="message", type="string", description="Сообщение", example="Привет! Сколько стоит услуга, если я твой сосед?", description="Текст сообщения"),
     *       @OA\Property(property="requests_specialists_id", type="integer", description="ID заявки", example="1", description="ID specialist request")
     *     )
     *   )
     * )
     * 
     * @OA\Response(response=200, description="Сообщение отправлено")
     * @OA\Response(response=400, description="Ошибка валидации")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="request")
     * @Security(name="Bearer")
     */
    public function setRequestChatMessage(Request $request, CoreSecurity $security, RequestsServices $requestsServices)
    {
        $user = $security->getUser();

        $chat['message'] = trim((string)$this->getJson($request, 'message'));
        $chat['requests_specialists_id'] = (int)$this->getJson($request, 'requests_specialists_id');

        if (empty($chat['message'])) {
            return $this->jsonError(['message' => "Сообщение не может быть пустым!"], 400);
        }

        if (empty($chat['requests_specialists_id'])) {
            return $this->jsonError(['request_id' => "Нужно указать request specialist ID!"], 400);
        }

        try {
            $requestsServices->sendMessage($user, $chat);
        } catch (LogicException $e) {
            return $this->jsonError(['#' => $e->getMessage()]);
        }

        return $this->jsonSuccess(['result' => true]);
    }
    /**
     * Получение списка заявок для пользователя single / quiz
     * 
     * @Route(path="/api/user/requests", name="api_user_requests", methods={"GET"})
     *
     * @OA\Get(path="/api/user/requests?", operationId="getUserRequests"),
     *
     * @OA\Parameter(in="query", name="page", schema={"type"="integer", "example"=1}, description="Номер страницы"),
     * @OA\Parameter(in="query", name="closed", schema={"type"="boolean", "example"=false}, description="Статус заявки Открыта/Закрыта. Можно не указывать, по умолчанию false")
     *
     * @OA\Response(response=200, description="Заявки получены}")
     * @OA\Response(response=400, description="Ошибка валидации")
     * @OA\Response(response=401, description="Необходима авторизация")
     * 
     * @OA\Tag(name="request")
     * @Security(name="Bearer")
     */
    public function getUserRequests(CoreSecurity $security, Request $request, RequestsServices $requestsServices)
    {
        $user = $security->getUser();
        $result = [];
        $page = $request->query->get('page') ?? 1;
        $closed = intval(filter_var($request->query->get('closed'), FILTER_VALIDATE_BOOLEAN));

        try {
            $result = $requestsServices->getUserRequestsAll($user, $page, $closed);
        } catch (LogicException $e) {
            return $this->jsonError(['#' => $e->getMessage()]);
        }

        return $this->jsonSuccess(['result' => $result['result'], 'resultTotalCount' => $result['result_total_count']]);
    }

    /**
     * Получения списка специалистов для конкретной заявки quiz
     * 
     * @Route(path="/api/user/quiz/request/", name="api_user_request_quiz", methods={"GET"})
     *
     * @OA\Get(path="/api/user/quiz/request?", operationId="getUserQuizRequest",),
     * 
     * @OA\Parameter(in="query", name="page", schema={"type"="integer", "example"=1}, description="Номер страницы"),
     * @OA\Parameter(in="query", name="request_id", schema={"type"="integer", "example"=1}, description="Id заявки", required=true),
     * @OA\Parameter(in="query", name="active", schema={"type"="boolean", "example"=false}, description="Активна либо отклонена")
     *
     * @OA\Response(response=200, description="Заявки получены}")
     * @OA\Response(response=400, description="Ошибка валидации")
     * @OA\Response(response=401, description="Необходима авторизация")
     * 
     * @OA\Tag(name="request")
     * @Security(name="Bearer")
     */
    public function getUserQuizRequest(CoreSecurity $security, Request $request, RequestsServices $requestsServices)
    {
        $user = $security->getUser();
        $result = [];
        $page = $request->query->get('page') ?? 1;
        $request_id = $request->query->get('request_id');
        $active = intval(filter_var($request->query->get('active'), FILTER_VALIDATE_BOOLEAN));

        if (empty($request_id)) {
            return $this->jsonError(['request_id' => "Нужно указать параметры"], 400);
        }

        try {
            $result = $requestsServices->getUserQuizRequest($user, $page, $request_id, $active);
        } catch (LogicException $e) {
            return $this->jsonError(['#' => $e->getMessage()]);
        }

        return $this->jsonSuccess($result);
    }

    /**
     * @Route(path="/api/specialist/requests", name="api_user_get_requests", methods={"GET"})
     *
     * @OA\Get(path="/api/specialist/requests?", operationId="getSpecialistRequests",
     * ),
     * 
     *  @OA\Parameter(in="query", name="page", schema={"type"="integer", "example"=1}, description="Номер страницы", required=true),
     *  @OA\Parameter(in="query", name="status", schema={"type"="string", "example"="review"},  description="Статус заявки: На рассмотрении - review, В процессе - progress, закрыта - closed")
     * 
     * 
     *
     * @OA\Response(response=200, description="Заявки получены}")
     * @OA\Response(response=400, description="Ошибка валидации")
     * @OA\Response(response=401, description="Необходима авторизация")
     * 
     * @OA\Tag(name="request")
     * @Security(name="Bearer")
     */
    public function getSpecialistRequests(CoreSecurity $security, Request $request, RequestsServices $requestsServices)
    {
        $specialist = $security->getUser();
        if (!$specialist->getIsSpecialist()) {
            return $this->jsonError(['role' => 'Пользователь не является специалистом!']);
        }

        $result = [];
        $page = $request->query->get('page') ?? 1;
        $status = $request->query->get('status');

        if (empty($status)) {
            return $this->jsonError(['status' => "Нужно указать номер страницы и статус"]);
        }

        try {
            $result = $requestsServices->getSpecialistRequestsAll($specialist, $page, $status);
        } catch (LogicException $e) {
            return $this->jsonError(['#' => $e->getMessage()]);
        }

        return $this->jsonSuccess(['result' => $result['result'], 'resultTotalCount' => $result['result_total_count']]);
    }
}
