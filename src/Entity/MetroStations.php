<?php

namespace App\Entity;

use App\Repository\MetroStationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MetroStationsRepository::class)
 */
class MetroStations
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $station;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $line;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adm_area;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $district;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $line_color;

    /**
     * @ORM\ManyToOne(targetEntity=Regions::class, inversedBy="metro_stations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStation(): ?string
    {
        return $this->station;
    }

    public function setStation(string $station): self
    {
        $this->station = $station;

        return $this;
    }

    public function getLine(): ?string
    {
        return $this->line;
    }

    public function setLine(string $line): self
    {
        $this->line = $line;

        return $this;
    }

    public function getAdmArea(): ?string
    {
        return $this->adm_area;
    }

    public function setAdmArea(string $adm_area): self
    {
        $this->adm_area = $adm_area;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(string $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getLineColor(): ?string
    {
        return $this->line_color;
    }

    public function setLineColor(?string $line_color): self
    {
        $this->line_color = $line_color;

        return $this;
    }

    public function getRegion(): ?Regions
    {
        return $this->region;
    }

    public function setRegion(?Regions $region): self
    {
        $this->region = $region;

        return $this;
    }
}
