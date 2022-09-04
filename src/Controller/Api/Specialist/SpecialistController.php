<?php

namespace App\Controller\Api\Specialist;

use App\Controller\Base\BaseApiController;
use App\Form\UpdateSpecialistDataType;
use App\Services\Specialist\SpecialistServices;
use App\Services\SMSServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use \Symfony\Component\Security\Core\Security as CoreSecurity;
use App\Services\User\UserServices;
use App\Entity\User;
use App\Repository\UserRepository;

class SpecialistController extends BaseApiController
{
    /**
     * Обновление данных специалиста
     *
     * @Route("/api/specialist", name="api_specialist_update_data", methods={"PATCH"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(type="object",
     *       @OA\Property(property="gender", type="string", description="Пол - male или female", example="male"),
     *       @OA\Property(property="region_id", type="integer", description="Идентификатор региона (1 - Москва)", example="1"),
     *       @OA\Property(property="stations_id",
     *          type="array",
     *          description="Идентификаторы станций",
     *          example="[1,2]",
     *          @OA\Items(type="integer", format="int32")
     *       ),
     *       @OA\Property(property="cities_id",
     *          type="array",
     *          description="Идентификаторы городов",
     *          example="[1,2]",
     *          @OA\Items(type="integer", format="int32")
     *       ),
     *       @OA\Property(property="callback_phone", type="string", description="Номер для обратной связи", example="+78800553535"),
     *       @OA\Property(property="services",
     *          type="array",
     *          description="Идентификаторы услуг и стоимость",
     *          example={{
     *                  "service_id": 1,
     *                  "price": 1000
     *                }, {
     *                  "service_id": 2,
     *                  "price": 1500
     *                }},
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
     *       @OA\Property(property="time_range", type="boolean", description="Разрешить искать +/- 1 час", example="true"),
     *       @OA\Property(property="public_photo",
     *          type="array",
     *          description="Идентификаторы файлов",
     *          example="[1,2]",
     *          @OA\Items(type="integer", format="int32")
     *       ),
     *       @OA\Property(property="public_docs",
     *          type="array",
     *          description="Идентификаторы файлов",
     *          example="[1,2]",
     *          @OA\Items(type="integer", format="int32")
     *       ),
     *       @OA\Property(property="private_docs",
     *          type="array",
     *          description="Идентификаторы файлов",
     *          example="[1,2]",
     *          @OA\Items(type="integer", format="int32")
     *       ),
     *       @OA\Property(property="experience", type="integer", description="Опыт, лет", example="10"),
     *       @OA\Property(property="education",
     *          type="array",
     *          description="Данные учебных заведений",
     *          example={{
     *                  "university": "Название учебного заведения",
     *                  "from": 2000,
     *                  "to": 2005
     *                }, {
     *                  "university": "Название учебного заведения 2",
     *                  "from": 2007,
     *                  "to": 2012
     *                }},
     *          @OA\Items(
     *              @OA\Property(
     *                 property="university",
     *                 type="string",
     *                 example="Название учебного заведения"
     *              ),
     *              @OA\Property(
     *                 property="from",
     *                 type="integer",
     *                 example="2000"
     *              ),
     *              @OA\Property(
     *                 property="to",
     *                 type="integer",
     *                 example="2005"
     *              ),
     *          ),
     *       ),
     *       @OA\Property(property="about", type="text", description="О себе", example="Доктор")
     *     )
     *   )
     * )
     * @OA\Response(response=200, description="Информация обновлена")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="specialist")
     * @Security(name="Bearer")
     */
    public function specialistUpdate_action(
        Request $request,
        CoreSecurity $security,
        SpecialistServices $specialistServices,
        SMSServices $SMSServices
    ): Response {
        $user = $security->getUser();
        if (!$user->getIsSpecialist())
            return $this->jsonError(['role' => 'Пользователь не является специалистом!'], 403);

        $specialist_data = [];
        // Получение данных
        $specialist_data['gender'] = (string)$this->getJson($request, 'gender');
        $specialist_data['region_id'] = (string)$this->getJson($request, 'region_id');
        $specialist_data['cities'] = (array)$this->getJson($request, 'cities_id');
        $specialist_data['metro_stations'] = (array)$this->getJson($request, 'stations_id');
        $specialist_data['callback_phone'] = (string)$this->getJson($request, 'callback_phone');
        $specialist_data['services'] = (array)$this->getJson($request, 'services');
        $specialist_data['worktime'] = (array)$this->getJson($request, 'worktime');
        $specialist_data['time_range'] = (bool)$this->getJson($request, 'time_range');
        $specialist_data['public_photo'] = (array)$this->getJson($request, 'public_photo');
        $specialist_data['public_docs'] = (array)$this->getJson($request, 'public_docs');
        $specialist_data['private_docs'] = (array)$this->getJson($request, 'private_docs');
        $specialist_data['experience'] = (string)$this->getJson($request, 'experience');
        $specialist_data['education'] = (array)$this->getJson($request, 'education');
        $specialist_data['about'] = (string)$this->getJson($request, 'about');

        $formatted_phone = $SMSServices->phoneFormat($specialist_data['callback_phone']);
        $specialist_data['phone'] = $formatted_phone;

        $form = $this->createFormByArray(UpdateSpecialistDataType::class, $specialist_data);

        if ($form->isValid()) {
            if (!$formatted_phone)
                return $this->jsonError(['phone' => 'Введите корректный номер телефона'], 400);
            //Сохранение данных
            $specialistServices->setUpdateData($user, $form);
        } else
            return $this->formValidationError($form);

        return $this->jsonSuccess();
    }

    /**
     * Получение информации о специалисте
     *
     * @Route("/api/specialist/{slug}", name="api_get_specialistinfo", methods={"GET"}, requirements={"user_id"="\d+"})
     * 
     * @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     description="Указывается либо ID пользователя либо SLUG",
     *     @OA\Schema(type="string", example="10-test-test")
     * )
     *
     * @OA\Response(response=200, description="Информация предоставлена")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="specialist")
     * @Security(name="Bearer")
     */
    public function getSpecialistPublicInfo_action(SpecialistServices $specialistServices, UserRepository $userRepository, string $slug): Response
    {
        if (!is_numeric($slug)) {
            $user = $userRepository->findBySlug($slug);
        } else {
            $user = $userRepository->findById($slug);
        }

        if (!$user) {
            return $this->jsonError(['user_id' => 'Пользователь не найден!'], 404);
        }

        if (!$user->getIsSpecialist()) {
            return $this->jsonError(['user_id' => 'Пользователь не специалист!'], 404);
        }

        $result = $specialistServices->getPublicInfo($user);

        return $this->jsonSuccess(['result' => $result]);
    }

    /**
     * Фильтр для специалиста
     *
     * @Route("/api/filter/specialist", name="api_specialist_filter_data", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="gender", type="string", description="Пол", example="male"),
     *       @OA\Property(property="page", type="integer", description="Страница", example="1"),
     *       @OA\Property(property="experience", type="string", description="Опыт", example="from5to10"),
     *       @OA\Property(property="price_min", type="integer", description="Минимальная цена услуги", example="1000"),
     *       @OA\Property(property="price_max", type="integer", description="Максимальная цена услуги", example="2000"),
     *       @OA\Property(property="city_id", type="integer", description="ID города", example="1"),
     *       @OA\Property(property="metro_id", type="integer", description="ID метро", example="1"),
     *       @OA\Property(property="sort", type="string", description="Сортировать по", example="price_min"),
     *       @OA\Property(property="rating", type="intger", description="Рейтинг", example="1"),
     *       @OA\Property(property="service_id", type="intger", description="Услуга", example="1"),
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
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Информация обновлена")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="filter")
     * @Security(name="Bearer")
     */
    public function specialistFilter_action(
        Request $request,
        SpecialistServices $specialistServices
    ): Response {
        $filter['city_id'] = (int)$this->getJson($request, 'city_id');
        $filter['metro_id'] = (int)$this->getJson($request, 'metro_id');
        $filter['service_id'] = (int)$this->getJson($request, 'service_id');
        $filter['sort'] = (string)$this->getJson($request, 'sort');
        $filter['price_min'] = (int)$this->getJson($request, 'price_min');
        $filter['price_max'] = (int)$this->getJson($request, 'price_max');
        $filter['rating'] = (string)$this->getJson($request, 'rating');
        $filter['worktime'] = (array)$this->getJson($request, 'worktime');
        $filter['gender'] =  strtolower(strval($this->getJson($request, 'gender')));
        $filter['experience'] = (string)$this->getJson($request, 'experience');
        $filter['page'] = (int)$this->getJson($request, 'page');

        $result = $specialistServices->getByFilter($filter);

        return $this->jsonSuccess($result);
    }
}
