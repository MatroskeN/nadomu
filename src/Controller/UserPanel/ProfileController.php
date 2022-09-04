<?php

namespace App\Controller\UserPanel;

use App\Controller\Base\BaseApiController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends BaseApiController
{
    /**
     * Заполнение общей информации после регистрации
     *
     * @Route("/profile/init", name="panel_init", methods={"GET"})
     */
    public function profileInit_action(): Response
    {
        return $this->render('/pages/panel/user/init.html.twig', [
            'title' => 'Заполнение информации профиля'
        ]);
    }
}
