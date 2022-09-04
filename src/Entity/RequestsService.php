<?php

namespace App\Entity;

use App\Repository\RequestsServiceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RequestsServiceRepository::class)
 */
class RequestsService
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=RequestsSpecialists::class, inversedBy="service")
     * @ORM\JoinColumn(nullable=false)
     */
    private $requests_specialists;

    /**
     * @ORM\ManyToOne(targetEntity=Services::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestSpecialists(): ?RequestsSpecialists
    {
        return $this->requests_specialists;
    }

    public function setRequestSpecialists(?RequestsSpecialists $requests_specialists): self
    {
        $this->requests_specialists = $requests_specialists;

        return $this;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(?Services $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }
}
