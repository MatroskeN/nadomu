<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatRepository::class)
 */
class Chat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=RequestsSpecialists::class, inversedBy="chat", cascade={"persist", "remove"})
     */
    private $request_specialist;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="chat")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=Feedback::class, inversedBy="feedback")
     */
    private $feedback;

    public function __construct()
    {
        $this->message = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestSpecialist(): ?RequestsSpecialists
    {
        return $this->request_specialist;
    }

    public function setRequestSpecialist(?RequestsSpecialists $request_specialist): self
    {
        $this->request_specialist = $request_specialist;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessage(): Collection
    {
        return $this->message;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->message->contains($message)) {
            $this->message[] = $message;
            $message->setChat($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->message->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChat() === $this) {
                $message->setChat(null);
            }
        }

        return $this;
    }

    public function getFeedback(): ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(?Feedback $feedback): self
    {
        $this->feedback = $feedback;

        return $this;
    }
}
