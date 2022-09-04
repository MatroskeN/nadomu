<?php

namespace App\Controller\Api\User;

use App\Controller\Base\BaseApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\Security\Core\Security as CoreSecurity;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use OpenApi\Annotations as OA;
use App\Entity\User;
use App\Form\Feedback\FeedbackCreateType;
use App\Services\User\FeedbackServices;

class FeedbackController extends BaseApiController
{
    /**
     * Создание заявки обратной связи
     *
     * @Route("/api/user/feedback", name="api_user_create_feedback", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="title", type="string", description="Заголовок", example="Какие документы требуются для модерации?"),
     *       @OA\Property(property="message", type="text", description="Сообщение", example="Предоставил паспорт, но модерацию не прошел"),
     *       @OA\Property(property="files",
     *          type="array",
     *          description="Идентификаторы файлов",
     *          example="[1,2,4]",
     *          @OA\Items(type="integer", format="int32")
     *       ),
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Обращение успешно создано")
     * @OA\Response(response=400, description="Ошибка")
     *
     * @OA\Tag(name="feedback")
     * @Security(name="Bearer")
     */
    public function createUserFeedback_action(CoreSecurity $security, Request $request, FeedbackServices $feedbackServices): Response
    {
        $user = $security->getUser();

        $feedback['title'] = (string)$this->getJson($request, 'title');
        $feedback['message'] = (string)$this->getJson($request, 'message');
        $feedback['files'] = (array)$this->getJson($request, 'files');

        $form = $this->createFormByArray(FeedbackCreateType::class, $feedback);
        if ($form->isValid()) {
            $feedbackServices->saveFeedback($user, $form);
        } else {
            $errors = $this->getErrorMessages($form);
            return $this->jsonError($errors);
        }

        return $this->jsonSuccess(['result' => true]);
    }

    /**
     * Отправить сообщение
     *
     * @Route("/api/user/feedback/send", name="api_user_create_feedback_send", methods={"POST"})
     *
     * @OA\RequestBody(
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *       type="object",
     *       @OA\Property(property="chat_id", type="integer", description="ID чата", example="39"),
     *       @OA\Property(property="message", type="text", description="Сообщение", example="Предоставил паспорт, но модерацию не прошел"),
     *       @OA\Property(property="files",
     *          type="array",
     *          description="Идентификаторы файлов",
     *          example="[1,2,4]",
     *          @OA\Items(type="integer", format="int32")
     *       ),
     *     )
     *   )
     * )
     *
     * @OA\Response(response=200, description="Сообщение успешно отправлено")
     * @OA\Response(response=400, description="Ошибка")
     *
     * @OA\Tag(name="feedback")
     * @Security(name="Bearer")
     */
    public function sendUserFeedback_action(CoreSecurity $security, Request $request, FeedbackServices $feedbackServices): Response
    {
        $user = $security->getUser();

        $feedback['chat_id'] = (string)$this->getJson($request, 'chat_id');
        $feedback['message'] = (string)$this->getJson($request, 'message');
        $feedback['files'] = (array)$this->getJson($request, 'files');
        $form = $this->createFormByArray(FeedbackCreateType::class, $feedback);
        if ($form->isValid()) {
            $feedbackServices->saveFeedback($user, $form);
        } else {
            $errors = $this->getErrorMessages($form);
            return $this->jsonError($errors);
        }
        return $this->jsonSuccess(['result' => true]);
    }

    /**
     * Получение информации о заявке обратной связи
     *
     * @Route("/api/user/feedback/{feedback_id}", name="api_get_feedback", methods={"GET"}, requirements={"user_id"="\d+"})
     * 
     * @OA\Parameter(
     *     name="feedback_id",
     *     in="path",
     *     description="Указывается feedback_id",
     *     @OA\Schema(type="integer", example="30")
     * )
     *
     * @OA\Response(response=200, description="Информация предоставлена")
     * @OA\Response(response=400, description="Пустой id feedback")
     *
     * @OA\Tag(name="feedback")
     * @Security(name="Bearer")
     */
    public function getUserFeedback_action(CoreSecurity $security, Request $request, FeedbackServices $feedbackServices, int $feedback_id): Response
    {
        $user = $security->getUser();
        if (empty($feedback_id)) {
            return $this->jsonError(['feedback_id' => 'feedback_id пустой'], 400);
        }

        $result = $feedbackServices->getFeedback($user, $feedback_id);
        return $this->jsonSuccess(['result' => $result]);
    }

    /**
     * Список обращений на обратную связь у пользователя и специалиста
     * 
     * @Route(path="/api/user/requests/feedback/", name="api_user_feedback_requests", methods={"GET"})
     *
     * @OA\Get(path="/api/user/requests/feedback?", operationId="getFeedbackRequests"),
     *
     * @OA\Parameter(in="query", name="page", schema={"type"="integer", "example"=1}, description="Номер страницы. По умолчанию = 1"),
     * @OA\Parameter(in="query", name="closed", schema={"type"="boolean", "example"=false}, description="Статус обращения. Можно не указывать, по умолчанию false")
     *
     * @OA\Response(response=200, description="Заявки получены}")
     * @OA\Response(response=400, description="Ошибка валидации")
     * @OA\Response(response=401, description="Необходима авторизация")
     * 
     * @OA\Tag(name="feedback")
     * @Security(name="Bearer")
     */
    public function getFeedbackRequests(CoreSecurity $security, Request $request, FeedbackServices $feedbackServices)
    {
        $user = $security->getUser();
        $result = [];
        $page = $request->query->get('page') ?? 1;
        $closed = intval(filter_var($request->query->get('closed'), FILTER_VALIDATE_BOOLEAN));

        $result = $feedbackServices->getFeedbackRequests($user, $closed, $page);

        return $this->jsonSuccess(['result' => $result['result'], 'resultTotalCount' => $result['result_total_count']]);
    }
}
