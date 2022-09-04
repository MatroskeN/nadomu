<?php

namespace App\Controller;

use App\Controller\Base\BaseApiController;
use App\Entity\Cities;
use App\Entity\Regions;
use App\Repository\AuthTokenRepository;
use App\Repository\RegionsRepository;
use App\Repository\ServicesRepository;
use App\Repository\UserRepository;
use App\Services\User\TokenServices;
use App\Services\User\UserServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Security\Core\Security as CoreSecurity;


class IndexController extends BaseApiController
{
    /**
     * Главная страница
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index_action(): Response
    {
        return $this->render('/pages/index.html.twig', [
            'title' => 'Сервис для медсестер по оказанию услуг на дому'
        ]);
    }

    /**
     * Политика конфиденциальности
     *
     * @Route("/privacy/", name="privacy", methods={"GET"})
     */
    public function privacy_action(): Response
    {
        return $this->render('/pages/policy.html.twig', [
            'title' => 'Политика конфиденциальности'
        ]);
    }

    /**
     * Установка промокода
     *
     * @Route("/p/{code}", name="promo_redirect", methods={"GET"})
     */
    public function promo_action(string $code): Response
    {
        setcookie('promo', $code, time() + 180 * 86400, '/');

        return $this->redirect('/');
    }

    /**
     * Выход
     *
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout_action(): Response
    {
        setcookie('token', '', -1, '/');
        setcookie('PHPSESSID', '', -1);

        return $this->redirect('/');
    }
}
