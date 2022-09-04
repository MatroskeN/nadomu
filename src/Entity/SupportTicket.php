<?php

namespace App\Entity;

use App\Repository\SupportTicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SupportTicketRepository::class)
 */
class SupportTicket
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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Заголовок не может быть пустым")
     * @Assert\Length(
     *     max = 128,
     *     maxMessage = "Максимальная длина 128 символов"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_closed;

    /**
     * @ORM\Column(type="integer")
     */
    private $create_time;

    /**
     * @ORM\Column(type="integer")
     */
    private $close_time;

    /**
     * @ORM\OneToMany(targetEntity=SupportMessage::class, mappedBy="ticket", orphanRemoval=true)
     */
    private $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getIsClosed(): ?bool
    {
        return $this->is_closed;
    }

    public function setIsClosed(bool $is_closed): self
    {
        $this->is_closed = $is_closed;

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

    public function getCloseTime(): ?int
    {
        return $this->close_time;
    }

    public function setCloseTime(int $close_time): self
    {
        $this->close_time = $close_time;

        return $this;
    }

    /**
     * @return Collection|SupportMessage[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(SupportMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setTicket($this);
        }

        return $this;
    }

    public function removeMessage(SupportMessage $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getTicket() === $this) {
                $message->setTicket(null);
            }
        }

        return $this;
    }
}
