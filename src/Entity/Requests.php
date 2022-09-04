<?php

namespace App\Entity;

use App\Repository\RequestsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RequestsRepository::class)
 */
class Requests
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="requests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Cities::class)
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=MetroStations::class)
     */
    private $metro;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $convenient_time;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $work_time = [];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $additional_information;

    /**
     * @ORM\Column(type="integer")
     */
    private $created_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $updated_time;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $flag_closed;

    /**
     * @ORM\OneToMany(targetEntity=RequestsSpecialists::class, mappedBy="request")
     */
    private $specialists;

    /**
     * @ORM\OneToMany(targetEntity=RequestsService::class, mappedBy="requests_specialists")
     */
    private $service;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $request_type;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $quiz_filter = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $specialist_selected;

    public function __construct()
    {
        $this->specialists = new ArrayCollection();
        $this->service = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCity(): ?Cities
    {
        return $this->city;
    }

    public function setCity(?Cities $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getMetro(): ?MetroStations
    {
        return $this->metro;
    }

    public function setMetro(?MetroStations $metro): self
    {
        $this->metro = $metro;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getConvenientTime(): ?string
    {
        return $this->convenient_time;
    }

    public function setConvenientTime(string $convenient_time): self
    {
        $this->convenient_time = $convenient_time;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getWorkTime(): ?array
    {
        return $this->work_time;
    }

    public function setWorkTime(?array $work_time): self
    {
        $this->work_time = $work_time;

        return $this;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->additional_information;
    }

    public function setAdditionalInformation(?string $additional_information): self
    {
        $this->additional_information = $additional_information;

        return $this;
    }

    public function getCreatedTime(): ?int
    {
        return $this->created_time;
    }

    public function setCreatedTime(int $created_time): self
    {
        $this->created_time = $created_time;

        return $this;
    }

    public function getUpdatedTime(): ?int
    {
        return $this->updated_time;
    }

    public function setUpdatedTime(?int $updated_time): self
    {
        $this->updated_time = $updated_time;

        return $this;
    }

    public function getFlagClosed(): ?bool
    {
        return $this->flag_closed;
    }

    public function setFlagClosed(bool $flag_closed): self
    {
        $this->flag_closed = $flag_closed;

        return $this;
    }

    /**
     * @return Collection|RequestsSpecialists[]
     */
    public function getSpecialists(): Collection
    {
        return $this->specialists;
    }

    public function addSpecialist(RequestsSpecialists $specialist): self
    {
        if (!$this->specialists->contains($specialist)) {
            $this->specialists[] = $specialist;
            $specialist->setRequest($this);
        }

        return $this;
    }

    public function removeSpecialist(RequestsSpecialists $specialist): self
    {
        if ($this->specialists->removeElement($specialist)) {
            // set the owning side to null (unless already changed)
            if ($specialist->getRequest() === $this) {
                $specialist->setRequest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RequestsService[]
     */
    public function getService(): Collection
    {
        return $this->service;
    }

    public function addService(RequestsService $service): self
    {
        if (!$this->service->contains($service)) {
            $this->service[] = $service;
            $service->setRequest($this);
        }

        return $this;
    }

    public function removeService(RequestsService $service): self
    {
        if ($this->service->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getRequest() === $this) {
                $service->setRequest(null);
            }
        }

        return $this;
    }

    public function getRequestType(): ?string
    {
        return $this->request_type;
    }

    public function setRequestType(string $request_type): self
    {
        $this->request_type = $request_type;

        return $this;
    }

    public function getQuizFilter(): ?array
    {
        return $this->quiz_filter;
    }

    public function setQuizFilter(?array $quiz_filter): self
    {
        $this->quiz_filter = $quiz_filter;

        return $this;
    }

    public function getSpecialistSelected(): ?User
    {
        return $this->specialist_selected;
    }

    public function setSpecialistSelected(?User $specialist_selected): self
    {
        $this->specialist_selected = $specialist_selected;

        return $this;
    }
}
