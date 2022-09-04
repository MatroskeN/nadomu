<?php

namespace App\Controller\Api;

use App\Controller\Base\BaseApiController;
use App\Repository\AuthTokenRepository;
use App\Repository\UserRepository;
use App\Services\SystemServices;
use App\Services\User\TokenServices;
use App\Services\User\UserServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;


class SystemController extends BaseApiController
{
    /**
     * Получение общих системных данных (города, метро, услуги, ...)
     *
     * @Route("/api/system/data", name="api_system_data", methods={"GET"})
     *
     * @OA\Response(response=200, description="Информация предоставлена")
     *
     * @OA\Tag(name="system")
     */
    public function systemData_action(SystemServices $systemServices): Response
    {
        //захардкодили регион, пока в системе только он один
        $region_id = 1;

        return $this->jsonSuccess([
            'cities' => $systemServices->getCities($region_id),
            'stations' => $systemServices->getMetroStations($region_id),
            'services' => $systemServices->getServiceList()
        ]);
    }
}
