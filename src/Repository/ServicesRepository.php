<?php

namespace App\Repository;

use App\Entity\Services;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Services|null find($id, $lockMode = null, $lockVersion = null)
 * @method Services|null findOneBy(array $criteria, array $orderBy = null)
 * @method Services[]    findAll()
 * @method Services[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Services::class);
    }

    /**
     * Производим поиск по идентификатору услуги
     *
     * @param int $service_id
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(int $service_id)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id = :id')
            ->setParameter('id', $service_id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Производим поиск по нескольким идентификаторам услуг
     *
     * @param array $service_ids
     * 
     * @return [type]
     */
    public function findByIds(array $service_ids)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id IN (:ids)')
            ->setParameter('ids', $service_ids)
            ->getQuery()
           ->getResult();
    }
}
