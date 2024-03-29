<?php

namespace App\Repository;

use App\Entity\ServiceImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceImages[]    findAll()
 * @method ServiceImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceImages::class);
    }

    // /**
    //  * @return ServiceImages[] Returns an array of ServiceImages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ServiceImages
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
