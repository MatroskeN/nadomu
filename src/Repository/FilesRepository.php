<?php

namespace App\Repository;

use App\Entity\Files;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Files|null find($id, $lockMode = null, $lockVersion = null)
 * @method Files|null findOneBy(array $criteria, array $orderBy = null)
 * @method Files[]    findAll()
 * @method Files[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Files::class);
    }


    /**
     * Возвращаем результаты с данными выборки по пользователю и идентификаторам
     *
     * @param int $user_id
     * @param array $ids
     * @return int|mixed[]|string
     */
    public function findByUserIdAndFileIds(int $user_id, array $ids)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id IN (:file_ids)')
            ->andWhere('e.user = :user_id')
            ->andWhere('e.is_deleted = 0')
            ->setParameter('file_ids', $ids)
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Возвращаем результаты с данными выборки по пользователю и идентификаторам
     * 
     * @param int $user_id
     * @param int $file_id
     * 
     * @return Files|null
     */
    public function findByUserIdAndFileId(int $user_id, int $file_id): ?Files
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id = :file_id')
            ->andWhere('e.user = :user_id')
            ->andWhere('e.is_deleted = 0')
            ->setParameter('file_id', $file_id)
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
