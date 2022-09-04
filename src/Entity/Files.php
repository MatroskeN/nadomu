<?php

namespace App\Entity;

use App\Repository\FilesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FilesRepository::class)
 */
class Files
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
     * @ORM\ManyToOne(targetEntity=SupportMessage::class, inversedBy="files")
     */
    private $support_message;

    /**
     * @ORM\Column(type="integer")
     */
    private $create_time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $file_path;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_deleted;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $delete_time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filetype;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceImages::class, inversedBy="private_docs")
     */
    private $private_docs;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceImages::class, inversedBy="public_docs")
     */
    private $public_docs;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceImages::class, inversedBy="public_photo")
     */
    private $public_photo;

    /**
     * @ORM\ManyToOne(targetEntity=Message::class, inversedBy="files")
     */
    private $message;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_image;


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

    public function getSupportMessage(): ?SupportMessage
    {
        return $this->support_message;
    }

    public function setSupportMessage(?SupportMessage $support_message): self
    {
        $this->support_message = $support_message;

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

    public function getFilePath(): ?string
    {
        return $this->file_path;
    }

    public function setFilePath(string $file_path): self
    {
        $this->file_path = $file_path;

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

    public function getDeleteTime(): ?int
    {
        return $this->delete_time;
    }

    public function setDeleteTime(?int $delete_time): self
    {
        $this->delete_time = $delete_time;

        return $this;
    }

    public function getFiletype(): ?string
    {
        return $this->filetype;
    }

    public function setFiletype(string $filetype): self
    {
        $this->filetype = $filetype;

        return $this;
    }

    public function getPublicDocs(): ?ServiceImages
    {
        return $this->public_docs;
    }

    public function setPublicDocs(?ServiceImages $serviceImages): self
    {
        $this->public_docs = $serviceImages;

        return $this;
    }

    public function getPrivateDocs(): ?ServiceImages
    {
        return $this->private_docs;
    }

    public function setPrivateDocs(?ServiceImages $serviceImages): self
    {
        $this->private_docs = $serviceImages;

        return $this;
    }

    public function getPublicPhoto(): ?ServiceImages
    {
        return $this->public_photo;
    }

    public function setPublicPhoto(?ServiceImages $public_photo): self
    {
        $this->public_photo = $public_photo;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getIsImage(): ?bool
    {
        return $this->is_image;
    }

    public function setIsImage(?bool $is_image): self
    {
        $this->is_image = $is_image;

        return $this;
    }

}
