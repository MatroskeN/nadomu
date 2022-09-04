<?php

namespace App\Controller\Api;

use App\Controller\Base\BaseApiController;
use App\Repository\AuthTokenRepository;
use App\Repository\UserRepository;
use App\Services\User\TokenServices;
use App\Services\User\UserServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;


class TokenController extends BaseApiController
{
    /**
     * Получение информации о токене
     *
     * @Route("/api/token", name="api_token_information", methods={"GET"})
     *
     * @OA\Response(response=200, description="Информация предоставлена")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="token")
     * @Security(name="Bearer")
     */
    public function tokenInformation_action(Request $request, TokenServices $tokenServices, AuthTokenRepository $authTokenRepository): Response
    {
        $token = $tokenServices->getRawToken($request);
        $token_data = $authTokenRepository->findByUserIdAndToken($token['user_id'], $token['token']);

        return $this->jsonSuccess([
            'expiration' => $token_data->getExpirationTime(),
            'time_left' => $token_data->getExpirationTime() - time()
        ]);
    }

    /**
     * Обновление авторизационного токена по рефреш токену
     *
     * @Route("/api/token", name="api_token_refresh", methods={"PATCH"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="refresh_token", type="string", description="Refresh token", example="1:xxxxxxxxxx")
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Авторизационный токен получен")
     * @OA\Response(response=403, description="Токен не валиден или отсутствует")
     * @OA\Response(response=404, description="Токен не найден")
     *
     * @OA\Tag(name="token")
     * @Security(name="Bearer")
     */
    public function tokenRefresh_action(Request $request, TokenServices $tokenServices): Response
    {
        $refresh_token = (string)$this->getJson($request, 'refresh_token');
        $refresh_data = $tokenServices->getDataRefreshToken($refresh_token);

        if (!$refresh_data)
            return $this->jsonError(['refresh_token' => 'Отсутствует refresh токен'], 403);

        $token = $tokenServices->checkRefreshToken($refresh_data['user_id'], $refresh_data['token']);

        if (!$token)
            return $this->jsonError(['refresh_token' => 'Refresh токен отсутствует или уже истек'], 404);

        $result_token = $tokenServices->createAuthByRefreshToken($token);

        return $this->jsonSuccess(['auth_token' => $result_token]);
    }
}
