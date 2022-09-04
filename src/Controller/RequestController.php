<?php

namespace App\Controller;

use App\Controller\Base\BaseApiController;
use App\Services\User\RestoreServices;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\Security\Core\Security as CoreSecurity;
use App\Repository\RequestsRepository;
use App\Repository\RequestsSpecialistsRepository;

class RequestController extends BaseApiController
{
    /**
     * Просмотр заявок
     *
     * @Route("/profile/requests", name="profile_requests", methods={"GET"})
     */
    public function requests_action(CoreSecurity $security): Response
    {
        $user = $security->getUser();
        if ($user->getIsSpecialist()) {
            return $this->render('/pages/specialist/requests.html.twig', [
                'title' => 'Просмотр заявок'
            ]);
        }
        return $this->render('/pages/user/requests.html.twig', [
            'title' => 'Просмотр заявок'
        ]);
    }

    /**
     * Просмотр заявок квиз пользователя
     *
     * @Route("/profile/requests/quiz/{id}", name="profile_requests_quiz",  requirements={"id"="\d+"}, methods={"GET"})
     */
    public function requestsQuiz_action(CoreSecurity $security, RequestsRepository $requestsRepository, int $id): Response
    {
        $user = $security->getUser();
        $request = $requestsRepository->findOneBy(['user' => $user->getId(), 'id' => $id]);
        if (!$request) {
            return $this->redirectToRoute('index');
        }
        return $this->render('/pages/user/request.quiz.html.twig', [
            'title' => 'Просмотр заявки квиз'
        ]);
    }

    /**
     * Просмотр заявки
     *
     * @Route("/profile/request/{requests_specialists_id}", name="profile_requests_quiz",  requirements={"id"="\d+"}, methods={"GET"})
     * 
     */
    public function request_action(CoreSecurity $security, RequestsRepository $requestsRepository, RequestsSpecialistsRepository $requestsSpecialistsRepository, int $requests_specialists_id): Response
    {
        $user = $security->getUser();
        if ($user->getIsSpecialist()) {
            $request = $requestsSpecialistsRepository->findOneBy(['specialist' => $user->getId(), 'id' => $requests_specialists_id]);
            if (!$request) {
                return $this->redirectToRoute('index');
            }

            if ($request->getStatus() === $requestsRepository::STATUS_DISCUSSION) {
                return $this->render('/pages/specialist/request.discussion.html.twig', [
                    'title' => 'Просмотр заявки'
                ]);
            } else {
                return $this->render('/pages/user/request.html.twig', [
                    'title' => 'Просмотр заявки'
                ]);
            }
        }


        $request = $requestsSpecialistsRepository->findRequestUser($user->getId(), $requests_specialists_id);
        if (!$request) {
            return $this->redirectToRoute('index');
        }
        return $this->render('/pages/user/request.html.twig', [
            'title' => 'Просмотр заявки'
        ]);
    }
}
