<?php

namespace App\Entity;

use App\Repository\SupportMessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SupportMessageRepository::class)
 */
class SupportMessage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Сообщение не может быть пустым")
     * @Assert\Length(
     *     max = 100000,
     *     maxMessage = "Максимальная длина 100000 символов"
     * )
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_deleted;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_edited;

    /**
     * @ORM\Column(type="integer")
     */
    private $create_time;

    /**
     * @ORM\ManyToOne(targetEntity=SupportTicket::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ticket;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="support_message")
     */
    private $files;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_support;

    public function __construct()
    {
        $this->files = new ArrayCollection();
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): self
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }

    public function getIsEdited(): ?bool
    {
        return $this->is_edited;
    }

    public function setIsEdited(bool $is_edited): self
    {
        $this->is_edited = $is_edited;

        return $this;
    }

    public function getCreateTime(): ?int
    {
        return $this->create_time;
    }

    public function setCreateTime(int $create_time): self
    {
        $this->create_time = $create_time;

        return $this;
    }

    public function getTicket(): ?SupportTicket
    {
        return $this->ticket;
    }

    public function setTicket(?SupportTicket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(Files $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setSupportMessage($this);
        }

        return $this;
    }

    public function removeFile(Files $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getSupportMessage() === $this) {
                $file->setSupportMessage(null);
            }
        }

        return $this;
    }

    public function getIsSupport(): ?bool
    {
        return $this->is_support;
    }

    public function setIsSupport(bool $is_support): self
    {
        $this->is_support = $is_support;

        return $this;
    }
}
