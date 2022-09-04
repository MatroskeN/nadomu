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
use App\Services\Specialist\FavoriteSpecialistServices;

class FavoriteSpecialistController extends BaseApiController
{
    /**
     * Добавление специалиста в избранное
     *
     * @Route("/api/specialist/favorite", name="api_specialist_favorite_add", methods={"POST"})
     * 
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="specialist_id", type="integer", description="ID специалиста", example="1"),
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Успешно добавлен в избранное")
     * @OA\Response(response=403, description="Запрещено, нет прав")
     *
     * @OA\Tag(name="favorite")
     * @Security(name="Bearer")
     */
    public function specialistFavoriteAdd_action(Request $request, CoreSecurity $security, FavoriteSpecialistServices $favoriteSpecialistServices, UserRepository $userRepository): Response
    {
        $user = $security->getUser();
        $specialist_id = (int)$this->getJson($request, 'specialist_id');
        $specialist = $userRepository->findById($specialist_id);

        if (!$specialist || !$specialist->getIsSpecialist()) {
            return $this->jsonError(['user_id' => 'Пользователь не специалист'], 403);
        }

        if ($user->getId() == $specialist_id) {
            return $this->jsonError(['user_id' => 'Вы не можете добавить себя в избранное!'], 403);
        }

        if ($favoriteSpecialistServices->isFavoriteSpecialistAdded($user, $specialist)) {
            return $this->jsonError(['user_id' => 'Этот специалист уже добавлен'], 403);
        }

        $favoriteSpecialistServices->saveFavoriteSpecialist($user, $specialist);

        return $this->jsonSuccess();
    }

    /**
     * Удаление специалиста из избранного
     *
     * @Route("/api/specialist/favorite", name="api_specialist_favorite_remove", methods={"DELETE"})
     * 
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="specialist_id", type="integer", description="ID специалиста", example="1"),
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Успешно удален из избранного")
     * @OA\Response(response=403, description="Запрещено, нет прав")
     *
     * @OA\Tag(name="favorite")
     * @Security(name="Bearer")
     */
    public function specialistFavoriteRemove_action(Request $request, CoreSecurity $security, FavoriteSpecialistServices $favoriteSpecialistServices, UserRepository $userRepository): Response
    {
        $user = $security->getUser();
        $specialist_id = (int)$this->getJson($request, 'specialist_id');
        $specialist = $userRepository->findById($specialist_id);

        if (!$specialist || !$specialist->getIsSpecialist()) {
            return $this->jsonError(['user_id' => 'Пользователь не специалист'], 403);
        }

        if (!$favoriteSpecialistServices->removeFavoriteSpecialist($user, $specialist)) {
            return $this->jsonError(['user_id' => 'Такой специалист не добавлен в избранное'], 403);
        }

        return $this->jsonSuccess();
    }

    /**
     * Получение списка специалистов в избранном
     *
     * @Route("/api/specialist/favorite", name="api_get_specialist_favorite", methods={"GET"})
     * 
     *
     * @OA\Response(response=200, description="Информация получена")
     *
     * @OA\Tag(name="favorite")
     * @Security(name="Bearer")
     */
    public function getSpecialistFavorite_action(Request $request, CoreSecurity $security, FavoriteSpecialistServices $favoriteSpecialistServices, UserRepository $userRepository): Response
    {
        $user = $security->getUser();

        $specialists = $favoriteSpecialistServices->getSpecialistsByUser($user);

        return $this->jsonSuccess(['result' => $specialists]);
    }
}
