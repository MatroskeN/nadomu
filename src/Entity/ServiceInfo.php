<?php

namespace App\Entity;

use App\Repository\ServiceInfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceInfoRepository::class)
 */
class ServiceInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="serviceInfo", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Regions::class)
     */
    private $region;

    /**
     * @ORM\ManyToMany(targetEntity=MetroStations::class)
     */
    private $metro_stations;

    /**
     * @ORM\ManyToMany(targetEntity=Cities::class)
     */
    private $cities;

    /**
     * @ORM\OneToMany(targetEntity=ServicePrice::class, mappedBy="serviceInfo")
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity=ServiceWorkTime::class, mappedBy="serviceInfo")
     */
    private $workTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $experience;

    /**
     * @ORM\OneToMany(targetEntity=ServiceEducation::class, mappedBy="serviceInfo")
     */
    private $education;

    /**
     * @ORM\OneToOne(targetEntity=ServiceImages::class, cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $time_range;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $callback_phone;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $rating;

    public function __construct()
    {
        $this->metro_stations = new ArrayCollection();
        $this->cities = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->workTime = new ArrayCollection();
        $this->education = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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
        }

        return $this;
    }

    public function removeMetroStation(MetroStations $metro_stations): self
    {
        $this->metro_stations->removeElement($metro_stations);

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
        }

        return $this;
    }

    public function removeCity(Cities $city): self
    {
        $this->cities->removeElement($city);

        return $this;
    }

    /**
     * @return Collection|ServicePrice[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(ServicePrice $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setServiceInfo($this);
        }

        return $this;
    }

    public function removeService(ServicePrice $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getServiceInfo() === $this) {
                $service->setServiceInfo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ServiceWorkTime[]
     */
    public function getWorkTime(): Collection
    {
        return $this->workTime;
    }

    public function addWorkTime(ServiceWorkTime $workTime): self
    {
        if (!$this->workTime->contains($workTime)) {
            $this->workTime[] = $workTime;
            $workTime->setServiceInfo($this);
        }

        return $this;
    }

    public function removeWorkTime(ServiceWorkTime $workTime): self
    {
        if ($this->workTime->removeElement($workTime)) {
            // set the owning side to null (unless already changed)
            if ($workTime->getServiceInfo() === $this) {
                $workTime->setServiceInfo(null);
            }
        }

        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(?int $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    /**
     * @return Collection|ServiceEducation[]
     */
    public function getEducation(): Collection
    {
        return $this->education;
    }

    public function addEducation(ServiceEducation $education): self
    {
        if (!$this->education->contains($education)) {
            $this->education[] = $education;
            $education->setServiceInfo($this);
        }

        return $this;
    }

    public function removeEducation(ServiceEducation $education): self
    {
        if ($this->education->removeElement($education)) {
            // set the owning side to null (unless already changed)
            if ($education->getServiceInfo() === $this) {
                $education->setServiceInfo(null);
            }
        }

        return $this;
    }

    public function getImages(): ?ServiceImages
    {
        return $this->images;
    }

    public function setImages(?ServiceImages $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getTimeRange(): ?bool
    {
        return $this->time_range;
    }

    public function setTimeRange(bool $time_range): self
    {
        $this->time_range = $time_range;

        return $this;
    }

    public function getCallbackPhone(): ?string
    {
        return $this->callback_phone;
    }

    public function setCallbackPhone(string $callback_phone): self
    {
        $this->callback_phone = $callback_phone;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
