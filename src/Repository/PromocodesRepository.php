<?php

namespace App\Repository;

use App\Entity\Promocodes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Promocodes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promocodes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promocodes[]    findAll()
 * @method Promocodes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromocodesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promocodes::class);
    }

    /**
     * Поиск по записи промокода
     *
     * @param $code
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByCode($code)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.code = :val')
            ->setParameter('val', $code)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Поиск по номеру телефона
     *
     * @param string $phone
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByPhone(string $phone)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.phone = :val')
            ->setParameter('val', $phone)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
