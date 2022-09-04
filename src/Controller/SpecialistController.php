<?php

namespace App\Controller;

use App\Controller\Base\BaseApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as CoreSecurity;

class SpecialistController extends BaseApiController
{
    /**
     * Профиль пользователя
     *
     * @Route("/profile/specialist", name="profile_specialist", methods={"GET"})
     */
    public function profileSpecialist_action(CoreSecurity $security): Response
    {
        // Check user role
        $user = $security->getUser();
        if (!$user->getIsSpecialist()) {
            return $this->redirectToRoute('index');
        }

        return $this->render('/pages/specialist/profile.html.twig', [
            'title' => 'Профиль специалиста'
        ]);
    }
}
