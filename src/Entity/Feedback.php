<?php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FeedbackRepository::class)
 */
class Feedback
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
    private $title;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Chat::class, mappedBy="feedback")
     */
    private $feedback;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="feedback")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->feedback = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?int
    {
        return $this->created_at;
    }

    public function setCreatedAt(int $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?int $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection|Chat[]
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Chat $feedback): self
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback[] = $feedback;
            $feedback->setFeedbackId($this);
        }

        return $this;
    }

    public function removeFeedback(Chat $feedback): self
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getFeedbackId() === $this) {
                $feedback->setFeedbackId(null);
            }
        }

        return $this;
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
}
