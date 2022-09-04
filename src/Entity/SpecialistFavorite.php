<?php

namespace App\Entity;

use App\Repository\SpecialistFavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SpecialistFavoriteRepository::class)
 */
class SpecialistFavorite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="specialistFavorites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $specialist;

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

    public function getSpecialist(): ?User
    {
        return $this->specialist;
    }

    public function setSpecialist(?User $specialist): self
    {
        $this->specialist = $specialist;

        return $this;
    }
}
