<?php

namespace App\Controller;

use App\Controller\Base\BaseApiController;
use App\Services\User\RestoreServices;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestoreController extends BaseApiController
{
    /**
     * Смена номера
     *
     * @Route("/restore", name="restore", methods={"GET"})
     */
    public function restore_action(): Response
    {
        return $this->render('/pages/restore/restore.html.twig', [
            'title' => 'Смена номера'
        ]);
    }

    /**
     * Актуальность данных для смены номера
     *
     * @Route("/restore/{user_id}/{code}", name="restore_confirmation", requirements={"user_id"="\d+"}, methods={"GET"})
     */
    public function restoreConfirmation_action(int $user_id, string $code, RestoreServices $restoreServices): Response
    {
        $status = $restoreServices->checkCode($user_id, $code);
        
        return $this->render('pages/restore/confirmation.html.twig', [
            'title' => "Подтверждение смены номера",
            'status' => $status,
            'user_id' => $user_id,
            'code' => $code
        ]);
    }
}
