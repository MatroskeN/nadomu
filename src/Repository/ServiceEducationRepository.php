<?php

namespace App\Repository;

use App\Entity\ServiceEducation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceEducation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceEducation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceEducation[]    findAll()
 * @method ServiceEducation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceEducationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceEducation::class);
    }

    // /**
    //  * @return ServiceEducation[] Returns an array of ServiceEducation objects
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
    public function findOneBySomeField($value): ?ServiceEducation
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
