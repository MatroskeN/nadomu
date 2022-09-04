<?php

namespace App\Controller\Api;

use App\Controller\Base\BaseApiController;
use App\Entity\SupportTicket;
use App\Repository\AuthTokenRepository;
use App\Repository\FilesRepository;
use App\Repository\UserRepository;
use App\Services\File\FileServices;
use App\Services\Support\TicketServices;
use App\Services\User\TokenServices;
use App\Services\User\UserServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Security\Core\Security as CoreSecurity;


class SupportController extends BaseApiController
{

    /**
     * Создание запроса в поддержку
     *
     * @Route("/api/support", name="api_support_create_ticket", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="title", type="string", description="Тема сообщения", example="Тестовый запрос в поддержку"),
     *       @OA\Property(property="message", type="string", description="Текст сообщения", example="Какой-то текст сообщения"),
     *       @OA\Property(property="file_ids",
     *          type="array",
     *          description="Идентификаторы файлов",
     *          example="[1,2,3]",
     *          @OA\Items(type="integer", format="int32")
     *       ),
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Авторизационный токен получен")
     * @OA\Response(response=401, description="Необходима авторизация")
     *
     * @OA\Tag(name="support")
     * @Security(name="Bearer")
     */
    public function createTicket_action(Request $request, TicketServices $ticketServices,
                                        FileServices $fileServices, CoreSecurity $security): Response
    {
        $title = (string)$this->getJson($request, 'title');
        $message = (string)$this->getJson($request, 'message');
        $file_ids = (array)$this->getJson($request, 'file_ids') ?? [];

        $form = $ticketServices->createTicketForm(new SupportTicket(), [
            'title' => $title,
            'message' => $message,
            'file_ids' => $fileServices->filterBrokenIds($security->getUser(), $file_ids)
        ]);

        if ($form->isValid()) {
            return $this->jsonSuccess();
        } else {
            $errors = $this->getErrorMessages($form);

            return $this->jsonError($errors);
        }
    }
}
