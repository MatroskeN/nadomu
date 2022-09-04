<?php

namespace App\Entity;

use App\Repository\RequestsSpecialistsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RequestsSpecialistsRepository::class)
 */
class RequestsSpecialists
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Requests::class, inversedBy="specialists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $request;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $specialist;

    /**
     * @ORM\OneToOne(targetEntity=Chat::class, mappedBy="request_specialist", cascade={"persist", "remove"})
     */
    private $chat;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequest(): ?Requests
    {
        return $this->request;
    }

    public function setRequest(?Requests $request): self
    {
        $this->request = $request;

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

    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    public function setChat(?Chat $chat): self
    {
        // unset the owning side of the relation if necessary
        if ($chat === null && $this->chat !== null) {
            $this->chat->setRequestSpecialist(null);
        }

        // set the owning side of the relation if necessary
        if ($chat !== null && $chat->getRequestSpecialist() !== $this) {
            $chat->setRequestSpecialist($this);
        }

        $this->chat = $chat;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
