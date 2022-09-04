<?php

namespace App\Controller;

use App\Controller\Base\BaseApiController;
use App\Services\Specialist\SpecialistServices;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security as CoreSecurity;

class CatalogController extends BaseApiController
{
    /**
     * Каталог
     *
     * @Route("/catalog/", name="catalog", methods={"GET"})
     */
    public function catalog_action(SpecialistServices $specialistServices): Response
    {
        $source_template = $this->getCardTemplate();

        $items = $specialistServices->getByFilter([
            'gender' => null,
            'experience' => null,
            'city_id' => null,
            'metro_id' => null,
            'service_id' => null,
            'rating' => null,
            'sort' => 'price_min',
            'price_min' => null,
            'price_max' => null,
            'page' => 1
        ]);

        return $this->render('/pages/catalog/catalog.html.twig', [
            'title' => 'Каталог',
            'source_template' => $source_template,
            'items' => $items
        ]);
    }

    /**
     * Возвращаем шаблон для VUE, с удаленными данными от Twig
     *
     * @return string
     */
    private function getCardTemplate(): string
    {
        $source_template = file_get_contents('../templates/pages/catalog/card.html.twig');
        $source_template = preg_replace('/\{%\s?+if\s?+SHOW_ONLY_TWIG\s?+==\s?+false\s?+%\}((?:(?!endif|if).)*)\{%\s?+endif\s?+%\}/ism', '$1', $source_template);
        $source_template = preg_replace('/\{%\s?+if\s?+SHOW_ONLY_TWIG\s?+==\s?+true\s?+%\}((?:(?!endif|if).)*)\{%\s?+endif\s?+%\}/ism', '', $source_template);


        return $source_template;
    }
}
