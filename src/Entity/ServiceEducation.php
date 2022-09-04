<?php

namespace App\Entity;

use App\Repository\ServiceEducationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceEducationRepository::class)
 */
class ServiceEducation
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
    private $university;

    /**
     * @ORM\Column(type="integer")
     */
    private $year_from;

    /**
     * @ORM\Column(type="integer")
     */
    private $year_to;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceInfo::class, inversedBy="education")
     */
    private $serviceInfo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniversity(): ?string
    {
        return $this->university;
    }

    public function setUniversity(string $university): self
    {
        $this->university = $university;

        return $this;
    }

    public function getYearFrom(): ?int
    {
        return $this->year_from;
    }

    public function setYearFrom(int $year_from): self
    {
        $this->year_from = $year_from;

        return $this;
    }

    public function getYearTo(): ?int
    {
        return $this->year_to;
    }

    public function setYearTo(int $year_to): self
    {
        $this->year_to = $year_to;

        return $this;
    }

    public function getServiceInfo(): ?ServiceInfo
    {
        return $this->serviceInfo;
    }

    public function setServiceInfo(?ServiceInfo $serviceInfo): self
    {
        $this->serviceInfo = $serviceInfo;

        return $this;
    }
}
