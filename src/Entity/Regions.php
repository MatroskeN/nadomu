<?php

namespace App\Entity;

use App\Repository\RegionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RegionsRepository::class)
 */
class Regions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_main;

    /**
     * @ORM\OneToMany(targetEntity=Cities::class, mappedBy="region")
     */
    private $cities;

    /**
     * @ORM\OneToMany(targetEntity=MetroStations::class, mappedBy="region")
     */
    private $metro_stations;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
        $this->metro_stations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIsMain(): ?bool
    {
        return $this->is_main;
    }

    public function setIsMain(bool $is_main): self
    {
        $this->is_main = $is_main;

        return $this;
    }

    /**
     * @return Collection|Cities[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(Cities $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setRegion($this);
        }

        return $this;
    }

    public function removeCity(Cities $city): self
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getRegion() === $this) {
                $city->setRegion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MetroStations[]
     */
    public function getMetroStations(): Collection
    {
        return $this->metro_stations;
    }

    public function addMetroStation(MetroStations $metro_stations): self
    {
        if (!$this->metro_stations->contains($metro_stations)) {
            $this->metro_stations[] = $metro_stations;
            $metro_stations->setRegion($this);
        }

        return $this;
    }

    public function removeMetroStation(MetroStations $metro_stations): self
    {
        if ($this->metro_stations->removeElement($metro_stations)) {
            // set the owning side to null (unless already changed)
            if ($metro_stations->getRegion() === $this) {
                $metro_stations->setRegion(null);
            }
        }

        return $this;
    }
}
