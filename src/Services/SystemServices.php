<?php

namespace App\Services;


use App\Repository\CitiesRepository;
use App\Repository\MetroStationsRepository;
use App\Repository\ServicesRepository;

class SystemServices
{
    private MetroStationsRepository $metroStationsRepository;
    private CitiesRepository $citiesRepository;
    private ServicesRepository $servicesRepository;

    public function __construct(MetroStationsRepository $metroStationsRepository, CitiesRepository $citiesRepository, ServicesRepository $servicesRepository)
    {
        $this->metroStationsRepository = $metroStationsRepository;
        $this->citiesRepository = $citiesRepository;
        $this->servicesRepository = $servicesRepository;
    }

    /**
     * Возвращаем массив городов по региону
     *
     * @param int $region_id
     * @return array
     */
    public function getCities(int $region_id): array
    {
        $result = [];
        $cities = $this->citiesRepository->findBy(['region' => $region_id]);

        foreach($cities as $item) {
            $result[] = [
                'id' => $item->getId(),
                'name' => $item->getName()
            ];
        }

        return $result;
    }

    /**
     * Возвращаем массив станций метро
     *
     * @param int $region_id
     * @return array
     */
    public function getMetroStations(int $region_id): array
    {
        $result = [];
        $stations = $this->metroStationsRepository->findBy(['region' => $region_id], ['station' => 'ASC']);

        foreach($stations as $item) {
            $result[] = [
                'id' => $item->getId(),
                'name' => $item->getStation(),
                'line' => $item->getLine(),
                'color' => $item->getLineColor()
            ];
        }

        return $result;
    }

    /**
     * Возвращаем массив услуг
     *
     * @return array
     */
    public function getServiceList(): array
    {
        $result = [];
        $services = $this->servicesRepository->findAll();

        foreach($services as $item) {
            $result[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'icon' => $item->getIcon(),
                'sort' => $item->getSort()
            ];
        }

        return $result;
    }
}
