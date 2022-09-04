<?php

namespace App\Repository;

use App\Entity\Requests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Requests|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requests|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requests[]    findAll()
 * @method Requests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestsRepository extends ServiceEntityRepository
{
    const STATUS = ['review', 'discussion', 'closed_by_user', 'closed_by_specialist', 'closed_before_discussion'];
    const STATUS_REVIEW = 'review';
    const STATUS_DISCUSSION = 'discussion';
    const STATUS_CLOSED_BY_USER = 'closed_by_user';
    const STATUS_CLOSED_BY_SPECIALIST = 'closed_by_specialist';
    const STATUS_CLOSED_BEFORE_DISCUSSION = 'closed_before_discussion';
    //пагинация страниц
    const PAGE_OFFSET = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requests::class);
    }

    // /**
    //  * @return Requests[] Returns an array of Requests objects
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
    public function findOneBySomeField($value): ?Requests
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
