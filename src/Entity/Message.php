<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Chat::class, inversedBy="message")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\Column(type="integer")
     */
    private $create_time;

    /**
     * @ORM\Column(type="integer")
     */
    private $update_time;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="message")
     */
    private $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    public function setChat(?Chat $chat): self
    {
        $this->chat = $chat;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

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

    public function getUpdateTime(): ?int
    {
        return $this->update_time;
    }

    public function setUpdateTime(int $update_time): self
    {
        $this->update_time = $update_time;

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
            $file->setMessage($this);
        }

        return $this;
    }

    public function removeFile(Files $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getMessage() === $this) {
                $file->setMessage(null);
            }
        }

        return $this;
    }
}
