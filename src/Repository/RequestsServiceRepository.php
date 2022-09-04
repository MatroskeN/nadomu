<?php

namespace App\Repository;

use App\Entity\RequestsService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RequestsService|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestsService|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestsService[]    findAll()
 * @method RequestsService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestsServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestsService::class);
    }

    // /**
    //  * @return RequestsService[] Returns an array of RequestsService objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RequestsService
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
