<?php

namespace App\Entity;

use App\Repository\ServicePriceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServicePriceRepository::class)
 */
class ServicePrice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Services::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceInfo::class, inversedBy="services")
     */
    private $serviceInfo;

    public function getId(): ?int
    {
        return $this->id;
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
