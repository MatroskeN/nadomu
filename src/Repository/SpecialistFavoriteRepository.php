<?php

namespace App\Repository;

use App\Entity\SpecialistFavorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SpecialistFavorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecialistFavorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecialistFavorite[]    findAll()
 * @method SpecialistFavorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecialistFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecialistFavorite::class);
    }

    /**
     * Есть ли у пользователя в избранном специалист
     * 
     * @param mixed $specialist_id
     * 
     * @return SpecialistFavorite|null
     */
    public function iSAddedSpecialist($user_id, $specialist_id): ?SpecialistFavorite
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user_id')
            ->andWhere('s.specialist = :specialist_id')
            ->setParameter('user_id', $user_id)
            ->setParameter('specialist_id', $specialist_id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
