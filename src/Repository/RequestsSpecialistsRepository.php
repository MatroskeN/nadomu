<?php

namespace App\Repository;

use App\Entity\RequestsSpecialists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Requests;
use App\Repository\RequestsRepository;

/**
 * @method RequestsSpecialists|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestsSpecialists|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestsSpecialists[]    findAll()
 * @method RequestsSpecialists[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestsSpecialistsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestsSpecialists::class);
    }

    /**
     * @param mixed $specialist_id
     * @param mixed $closed
     * @param null $limit
     * @param null $offset
     * 
     * @return [type]
     */
    public function findRequestPagination($specialist_id, $closed, $status, $limit = null, $offset = null, $count = false)
    {
        $qb = $this->createQueryBuilder('r');

        if ($count) {
            $qb->select('count(r.id)');
        } else {
            $qb->select('r');
        }

        $qb->leftJoin(Requests::class, 'req', 'WITH', 'req.id = r.request')
            ->where('r.specialist = :specialist_id')
            ->andWhere('req.flag_closed = :closed')
            ->andWhere('r.status IN (:status)');

        if ($status == RequestsRepository::STATUS_REVIEW || $status == RequestsRepository::STATUS_DISCUSSION) {
            $status = [$status];
        }

        if ($status == "closed") {
            $status = [
                RequestsRepository::STATUS_CLOSED_BY_USER,
                RequestsRepository::STATUS_CLOSED_BY_SPECIALIST,
                RequestsRepository::STATUS_CLOSED_BEFORE_DISCUSSION
            ];
        }

        $qb->setParameter('specialist_id', $specialist_id)
            ->setParameter('status', $status)
            ->setParameter('closed', $closed);

        if (!is_null($offset)) {
            $qb->setFirstResult($offset);
        }

        if (!is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        $qb->orderBy('r.id', 'desc');
        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        }
        return $qb->getQuery()->getResult();
    }


    /**
     * @param mixed $request_id
     * @param mixed $closed
     * @param mixed $status
     * @param null $limit
     * @param null $offset
     * 
     * @return [type]
     */
    public function findRequestPaginationQuiz($request_id, $closed, $status, $limit = null, $offset = null, $count = false)
    {

        $qb = $this->createQueryBuilder('r');

        if ($count) {
            $qb->select('count(r.id)');
        } else {
            $qb->select('r');
        }

        $qb->leftJoin(Requests::class, 'req', 'WITH', 'req.id = r.request')
            ->where('r.request = :request_id')
            ->andWhere('req.flag_closed = :closed');
        if (!empty($status)) {
            if (is_array($status)) {
                $qb->andWhere('r.status IN (:status)');
            } else {
                $qb->andWhere('r.status = :status');
            }
        }

        $qb->setParameter('request_id', $request_id)
            ->setParameter('closed', $closed)
            ->setParameter('status', $status);

        if (!is_null($offset)) {
            $qb->setFirstResult($offset);
        }

        if (!is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        $qb->orderBy('r.id', 'desc');

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * @param mixed $specialist_id
     * @param mixed $closed
     * @param null $limit
     * @param null $offset
     * 
     * @return [type]
     */
    public function findRequestUser($user_id, $requests_specialists_id)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->leftJoin(Requests::class, 'req', 'WITH', 'req.id = r.request')
            ->where('r.id = :requests_specialists_id')
            ->andWhere('req.user = :user_id')
            ->setParameter('requests_specialists_id', $requests_specialists_id)
            ->setParameter('user_id', $user_id);

        $qb->orderBy('r.id', 'desc');
        return $qb->getQuery()->getOneOrNullResult();
    }
}
