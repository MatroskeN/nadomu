<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Services\StepServices;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("phone", message="Такой телефон уже существует")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    //обозначение роли пользователя
    CONST ROLE_USER = 'user';
    CONST ROLE_SPECIALIST = 'specialist';

    //доступные роли и их ассоциации
    CONST AVAILABLE_ROLES = [
        self::ROLE_USER => 'ROLE_USER',
        self::ROLE_SPECIALIST => 'ROLE_SPECIALIST'
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(
     *     message = "Телефон не может быть пустым", groups={"Default"}
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Assert\NotBlank(
     *     message = "E-mail не может быть пустым", groups={"InitUser", "Default"}
     * )
     * @Assert\Email(
     *     message = "Введите корректный e-mail", groups={"InitUser", "Default"}
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=AuthToken::class, mappedBy="user", orphanRemoval=true)
     * @Ignore()
     */
    private $authTokens;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_confirmed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_blocked;

    /**
     * @ORM\Column(type="integer")
     */
    private $create_time;

    /**
     * @ORM\Column(type="integer")
     */
    private $last_visit_time;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $invited;

    /**
     * @ORM\OneToMany(targetEntity=Promocodes::class, mappedBy="owner")
     */
    private $promocodes;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Assert\NotBlank(
     *     message = "Нужно указать имя", groups={"InitUser", "Default"}
     * )
     * @Assert\Length(
     *      min = 2,
     *      max = 128,
     *      minMessage = "Имя должно содержать минимум {{ limit }} символа",
     *      maxMessage = "Имя не должно быть длинее {{ limit }} символов",
     *      groups={"InitUser", "Default"}
     * )
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Assert\NotBlank(
     *     message = "Нужно указать фамилию", groups={"InitUser", "Default"}
     * )
     * @Assert\Length(
     *      min = 2,
     *      max = 128,
     *      minMessage = "Фамилия должна содержать минимум {{ limit }} символа",
     *      maxMessage = "Фамилия не должна быть длинее {{ limit }} символов",
     *      groups={"InitUser", "Default"}
     * )
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Assert\NotBlank(
     *     message = "Нужно указать отчество", groups={"InitUser", "Default"}
     * )
     * @Assert\Length(
     *      min = 2,
     *      max = 128,
     *      minMessage = "Отчество должно содержать минимум {{ limit }} символа",
     *      maxMessage = "Отчество не должно быть длинее {{ limit }} символов",
     *      groups={"InitUser", "Default"}
     * )
     */
    private $patronymic_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(
     *     message = "Нужно выбрать пол",
     *     groups={"Default"}
     * )
     * @Assert\Choice(choices={"male", "female"}, message="Выберите male или female", groups={"Default"})
     */
    private $gender;

    /**
     * @ORM\OneToOne(targetEntity=ServiceInfo::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $serviceInfo;

    /**
     * @ORM\OneToOne(targetEntity=Files::class, cascade={"persist", "remove"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\OneToOne(targetEntity=Notifications::class, cascade={"persist", "remove"})
     */
    private $notification;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=SpecialistFavorite::class, mappedBy="user")
     */
    private $specialistFavorites;


    /**
     * @ORM\OneToMany(targetEntity=Requests::class, mappedBy="user")
     */
    private $requests;

    /**
     * @ORM\OneToMany(targetEntity=Feedback::class, mappedBy="user")
     */
    private $feedback;


    public function __construct()
    {
        $this->authTokens = new ArrayCollection();
        $this->promocodes = new ArrayCollection();
        $this->specialistFavorites = new ArrayCollection();
        $this->seviceRequests = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->feedback = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->phone;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->phone;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|AuthToken[]
     */
    public function getAuthTokens(): Collection
    {
        return $this->authTokens;
    }

    public function addAuthToken(AuthToken $authToken): self
    {
        if (!$this->authTokens->contains($authToken)) {
            $this->authTokens[] = $authToken;
            $authToken->setUser($this);
        }

        return $this;
    }

    public function removeAuthToken(AuthToken $authToken): self
    {
        if ($this->authTokens->removeElement($authToken)) {
            // set the owning side to null (unless already changed)
            if ($authToken->getUser() === $this) {
                $authToken->setUser(null);
            }
        }

        return $this;
    }

    public function getIsConfirmed(): ?bool
    {
        return $this->is_confirmed;
    }

    public function setIsConfirmed(bool $is_confirmed): self
    {
        $this->is_confirmed = $is_confirmed;

        return $this;
    }

    public function getIsBlocked(): ?bool
    {
        return $this->is_blocked;
    }

    public function setIsBlocked(bool $is_blocked): self
    {
        $this->is_blocked = $is_blocked;

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

    public function getLastVisitTime(): ?int
    {
        return $this->last_visit_time;
    }

    public function setLastVisitTime(int $last_visit_time): self
    {
        $this->last_visit_time = $last_visit_time;

        return $this;
    }

    public function getInvited(): ?self
    {
        return $this->invited;
    }

    public function setInvited(?self $invited): self
    {
        $this->invited = $invited;

        return $this;
    }

    /**
     * @return Collection|Promocodes[]
     */
    public function getPromocodes(): Collection
    {
        return $this->promocodes;
    }

    public function addPromocode(Promocodes $promocode): self
    {
        if (!$this->promocodes->contains($promocode)) {
            $this->promocodes[] = $promocode;
            $promocode->setOwner($this);
        }

        return $this;
    }

    public function removePromocode(Promocodes $promocode): self
    {
        if ($this->promocodes->removeElement($promocode)) {
            // set the owning side to null (unless already changed)
            if ($promocode->getOwner() === $this) {
                $promocode->setOwner(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getPatronymicName(): ?string
    {
        return $this->patronymic_name;
    }

    public function setPatronymicName(?string $patronymic_name): self
    {
        $this->patronymic_name = $patronymic_name;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getServiceInfo(): ?ServiceInfo
    {
        return $this->serviceInfo;
    }

    public function setServiceInfo(ServiceInfo $serviceInfo): self
    {
        // set the owning side of the relation if necessary
        if ($serviceInfo->getUser() !== $this) {
            $serviceInfo->setUser($this);
        }

        $this->serviceInfo = $serviceInfo;

        return $this;
    }

    /**
     * Возвращаем, является ли пользователь специалистом или нет
     *
     * @return bool
     */
    public function getIsSpecialist(): bool
    {
        $specialist_code = self::AVAILABLE_ROLES[self::ROLE_SPECIALIST];

        return in_array($specialist_code, $this->roles);
    }

    /**
     * Информация о пользователе для инвайтов
     *
     * @return array
     */
    public function userInviteStatus(): array
    {
        $status = $this->isInviteActive();
        $reason = 'Пользователь активирован';

        if (!$this->is_confirmed)
            $reason = 'Необходимо подтвердить e-mail';

        if ($this->is_blocked)
            $reason = 'Пользователь заблокирован';

        return [
            'status' => $status,
            'reason' => $reason
        ];
    }

    /**
     * Возвращаем активен пользователь или нет
     *
     * @return bool
     */
    public function isInviteActive(): bool
    {
        return ($this->is_confirmed && !$this->is_blocked);
    }

    public function getAvatar(): ?Files
    {
        return $this->avatar;
    }

    public function setAvatar(?Files $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getNotification(): ?Notifications
    {
        return $this->notification;
    }

    public function setNotification(?Notifications $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|SpecialistFavorite[]
     */
    public function getSpecialistFavorites(): Collection
    {
        return $this->specialistFavorites;
    }

    public function addSpecialistFavorite(SpecialistFavorite $specialistFavorite): self
    {
        if (!$this->specialistFavorites->contains($specialistFavorite)) {
            $this->specialistFavorites[] = $specialistFavorite;
            $specialistFavorite->setUser($this);
        }

        return $this;
    }

    public function removeSpecialistFavorite(SpecialistFavorite $specialistFavorite): self
    {
        if ($this->specialistFavorites->removeElement($specialistFavorite)) {
            // set the owning side to null (unless already changed)
            if ($specialistFavorite->getUser() === $this) {
                $specialistFavorite->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Requests[]
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Requests $request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            $request->setUser($this);
        }

        return $this;
    }

    public function removeRequest(Requests $request): self
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getUser() === $this) {
                $request->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Feedback[]
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): self
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback[] = $feedback;
            $feedback->setUser($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): self
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getUser() === $this) {
                $feedback->setUser(null);
            }
        }

        return $this;
    }

}
