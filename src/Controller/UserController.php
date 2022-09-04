<?php

namespace App\Controller;

use App\Controller\Base\BaseApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseApiController
{
    /**
     * Профиль пользователя
     *
     * @Route("/profile/user", name="profile_user", methods={"GET"})
     */
    public function profileUser_action(): Response
    {
        return $this->render('/pages/user/profile.html.twig', [
            'title' => 'Профиль пользователя'
        ]);
    }
}