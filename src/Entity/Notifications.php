<?php

namespace App\Entity;

use App\Repository\NotificationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationsRepository::class)
 */
class Notifications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $email_expert_answers = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private $email_new_requests = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sms_expert_answers = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sms_new_requests = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sms_users_response = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $email_users_response = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEmailExpertAnswers(): ?bool
    {
        return $this->email_expert_answers;
    }

    public function setEmailExpertAnswers(bool $email_expert_answers): self
    {
        $this->email_expert_answers = $email_expert_answers;

        return $this;
    }

    public function getEmailNewRequests(): ?bool
    {
        return $this->email_new_requests;
    }

    public function setEmailNewRequests(bool $email_new_requests): self
    {
        $this->email_new_requests = $email_new_requests;

        return $this;
    }

    public function getSmsExpertAnswers(): ?bool
    {
        return $this->sms_expert_answers;
    }

    public function setSmsExpertAnswers(bool $sms_expert_answers): self
    {
        $this->sms_expert_answers = $sms_expert_answers;

        return $this;
    }

    public function getSmsNewRequests(): ?bool
    {
        return $this->sms_new_requests;
    }

    public function setSmsNewRequests(bool $sms_new_requests): self
    {
        $this->sms_new_requests = $sms_new_requests;

        return $this;
    }

    public function getSmsUsersResponse(): ?bool
    {
        return $this->sms_users_response;
    }

    public function setSmsUsersResponse(bool $sms_users_response): self
    {
        $this->sms_users_response = $sms_users_response;

        return $this;
    }

    public function getEmailUsersResponse(): ?bool
    {
        return $this->email_users_response;
    }

    public function setEmailUsersResponse(bool $email_users_response): self
    {
        $this->email_users_response = $email_users_response;

        return $this;
    }
}
