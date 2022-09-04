<?php

namespace App\Services\Specialist;


use App\Entity\SpecialistFavorite;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SpecialistFavoriteRepository;
use App\Services\Specialist\SpecialistServices;
use App\Repository\UserRepository;

class FavoriteSpecialistServices
{

    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        SpecialistFavoriteRepository $specialistFavoriteRepository,
        SpecialistServices $specialistServices,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->specialistFavoriteRepository = $specialistFavoriteRepository;
        $this->specialistServices = $specialistServices;
        $this->userRepository = $userRepository;
    }

    /**
     * Добавление специалиста в избранное
     *
     * @param string $formType
     * @param array $data
     * @param null $entity
     * @return FormInterface
     */
    public function saveFavoriteSpecialist(UserInterface $user, UserInterface $specialist): User
    {
        $specialistFavorite = new SpecialistFavorite();
        $specialistFavorite->setUser($user)->setSpecialist($specialist);

        $this->em->persist($specialistFavorite);
        $this->em->flush();

        return $user;
    }

    /**
     * Добавление специалиста в избранное
     *
     * @param string $formType
     * @param array $data
     * @param null $entity
     * @return FormInterface
     */
    public function removeFavoriteSpecialist(UserInterface $user, UserInterface $specialist): bool
    {
        $specialistFavorite = $this->specialistFavoriteRepository->iSAddedSpecialist($user->getId(), $specialist->getId());

        if ($specialistFavorite) {
            $this->em->remove($specialistFavorite);
            $this->em->flush();
        } else {
            return false;
        }

        return true;
    }

    /**
     * Проверка добавлен ли уже в избранное
     *
     * @param UserInterface $user
     * @param UserInterface $specialist
     * 
     * @return bool
     */
    public function isFavoriteSpecialistAdded(UserInterface $user, UserInterface $specialist): bool
    {
        return boolval($this->specialistFavoriteRepository->iSAddedSpecialist($user->getId(), $specialist->getId()));
    }

    /**
     * Получаем список специалистов
     * 
     * @param UserInterface $user
     * 
     * @return [type]
     */
    public function getSpecialistsByUser(UserInterface $user): array
    {   
        $users_ids = [];
        if ($user->getSpecialistFavorites()) {
            foreach ($user->getSpecialistFavorites() as $specialist) {
                $users_ids[] = $specialist->getSpecialist()->getId();
            }
        }

        $users = $this->userRepository->getUsersById($users_ids);

        $result = [];
        foreach ($users as $key => $user) {
            $result[$key] = $this->specialistServices->getPublicInfo($user);
        }

        return $result;
    }
}
