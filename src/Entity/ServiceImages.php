<?php

namespace App\Entity;

use App\Repository\ServiceImagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceImagesRepository::class)
 */
class ServiceImages
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Files::class, cascade={"persist", "remove"})
     */
    private $profile;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="public_docs")
     */
    private $public_docs;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="private_docs")
     */
    private $private_docs;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="public_photo")
     */
    private $public_photo;

    public function __construct()
    {
        $this->public_docs = new ArrayCollection();
        $this->private_docs = new ArrayCollection();
        $this->public_photo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?Files
    {
        return $this->profile;
    }

    public function setProfile(?Files $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getPublicDocs(): Collection
    {
        return $this->public_docs;
    }

    public function addPublicDoc(Files $publicDoc): self
    {
        if (!$this->public_docs->contains($publicDoc)) {
            $this->public_docs[] = $publicDoc;
            $publicDoc->setPublicDocs($this);
        }

        return $this;
    }

    public function removePublicDoc(Files $publicDoc): self
    {
        if ($this->public_docs->removeElement($publicDoc)) {
            // set the owning side to null (unless already changed)
            if ($publicDoc->getPublicDocs() === $this) {
                $publicDoc->setPublicDocs(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getPrivateDocs(): Collection
    {
        return $this->private_docs;
    }

    public function addPrivateDoc(Files $privateDoc): self
    {
        if (!$this->private_docs->contains($privateDoc)) {
            $this->private_docs[] = $privateDoc;
            $privateDoc->setPrivateDocs($this);
        }

        return $this;
    }

    public function removePrivateDoc(Files $privateDoc): self
    {
        if ($this->private_docs->removeElement($privateDoc)) {
            // set the owning side to null (unless already changed)
            if ($privateDoc->getPrivateDocs() === $this) {
                $privateDoc->setPrivateDocs(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getPublicPhoto(): Collection
    {
        return $this->public_photo;
    }

    public function addPublicPhoto(Files $publicPhoto): self
    {
        if (!$this->public_photo->contains($publicPhoto)) {
            $this->public_photo[] = $publicPhoto;
            $publicPhoto->setPublicPhoto($this);
        }

        return $this;
    }

    public function removePublicPhoto(Files $publicPhoto): self
    {
        if ($this->public_photo->removeElement($publicPhoto)) {
            // set the owning side to null (unless already changed)
            if ($publicPhoto->getPublicPhoto() === $this) {
                $publicPhoto->setPublicPhoto(null);
            }
        }

        return $this;
    }
}
