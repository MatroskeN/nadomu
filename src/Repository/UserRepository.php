<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\DBAL\Connection;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    //пагинация страниц
    const PAGE_OFFSET = 10;
    // Пол
    const GENDER_MAN = "male";
    const GENDER_WOMAN = 'female';
    const EXPERIENCE = ['exist', 'less1', 'from1to3', 'from3to5', 'from5to10', 'more10'];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Used to update phone.
     */
    public function updatePhone(PasswordAuthenticatedUserInterface $user, string $phone): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPhone($phone);
        $this->_em->persist($user);
        $this->_em->flush();
    }


    /**
     * Find by email user
     * @param $email
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByEmail($email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find by phone user
     *
     * @param $phone
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByPhone($phone)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.phone = :val')
            ->setParameter('val', $phone)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find by ID user
     *
     * @param $user_id
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(int $user_id)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $user_id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find by slug user
     *
     * @param $slug
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlug(string $slug)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Возвращаем приглашенных пользователем юзеров
     *
     * @param int $user_id
     * @return int|mixed[]|string
     */
    public function findInvitedByUserId(int $user_id)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.invited = :val')
            ->setParameter('val', $user_id)
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверка уникальности емайла, не используется ли кем-то другим
     * @param int $user_id
     * @param string $email
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkUniqueEmail(int $user_id, string $email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->andWhere('u.id <> :id')
            ->setParameter('val', $email)
            ->setParameter('id', $user_id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Возвращаем список пользователей с пагинацией
     *
     * @param $page
     * @return int|mixed|string
     */
    public function getUserByPages($page)
    {
        $offset = ($page - 1) * self::PAGE_OFFSET;

        return $this->createQueryBuilder('u')
            ->orderBy('u.create_time', 'DESC')
            ->setMaxResults(self::PAGE_OFFSET)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult(Query::HYDRATE_SIMPLEOBJECT);
    }

    /**
     * Возвращаем список пользователей по id
     *
     * @param array $ids
     *
     * @return [type]
     */
    public function getUsersById(array $ids)
    {
        return  $this->createQueryBuilder('u')
            ->where('u.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param mixed $filter
     *
     * @return [type]
     */
    public function filterSQL($filter)
    {
        $sql_where = "";
        $price = "";
        $city = "";
        $metro = "";
        $service = "";
        $worktime = "";
        $sort = "";
        $parameters = [];
        // Пол
        if ($filter['gender'] == $this::GENDER_MAN || $filter['gender'] == $this::GENDER_WOMAN) {
            $sql_where .= " and t.gender = :gender";
            $parameters['gender'] = $filter['gender'];
        }

        // Опыт
        switch ($filter['experience']) {
            case 'exist':
                $sql_where .= " and serv.experience IS NOT NULL ";
                break;
            case 'less1':
                $sql_where .= " and serv.experience < 1 ";
                break;
            case 'from1to3':
                $sql_where .= " and serv.experience < 3 ";
                break;
            case 'from3to5':
                $sql_where .= " and serv.experience BETWEEN 3 AND 5 ";
                break;
            case 'from5to10':
                $sql_where .= " and serv.experience BETWEEN 5 AND 10 ";
                break;
            case 'more10':
                $sql_where .= " and serv.experience > 10 ";
                break;
        }

        //Город
        if (!empty(intval($filter['city_id']))) {
            $city = " and service_info_cities.cities_id = :city_id";
            $sql_where .= " and t.city_name IS NOT NULL ";
            $parameters['city_id'] = $filter['city_id'];
        }

        //Метро
        if (!empty(intval($filter['metro_id']))) {
            $metro = " and service_info_metro_stations.metro_stations_id = :metro_id";
            $parameters['metro_id'] = $filter['metro_id'];
        }


        //Услуга
        if (!empty(intval($filter['service_id']))) {
            $service = " and services.id = :service_id ";
            $price .= ' and service_price.service_id = :service_id';
            $parameters['service_id'] = $filter['service_id'];
        }

        //Рейтинг
        if (!empty(intval($filter['rating']))) {
            $sql_where .= " and serv.rating = :rating  ";
            $parameters['rating'] = $filter['rating'];
        }

        // Сортировка
        switch ($filter['sort']) {
            case 'price_min':
                if (!empty($service)) {
                    $sort = " ORDER BY t.work_price DESC  ";
                }
                break;
            case 'price_max':
                if (!empty($service)) {
                    $sort = " ORDER BY t.work_price ASC ";
                }
                break;
            case 'rate_min':
                $sort = " ORDER BY `serv`.`rating` ASC ";
                break;
            case 'rate_max':
                $sort = " ORDER BY `serv`.`rating` DESC";
                break;
        }

        // Цена
        if (!empty(intval($filter['price_min'])) && !empty(intval($filter['price_max']))) {
            $price = " and service_price.price BETWEEN :price_min and :price_max ";
            $parameters['price_max'] = (int)$filter['price_max'];
            $parameters['price_min'] = (int)$filter['price_min'];
        }

        $sql_where .= " and t.work_price IS NOT NULL ";
        $sql_where .= " and t.service_name IS NOT NULL ";
        $sql_where .= " and t.metro_name IS NOT NULL ";
        $types = [];
        // Время работы
        if (!empty($filter['worktime'])) {
            $day = [];
            $hour = [];
            $types = ['hour' => Connection::PARAM_INT_ARRAY, 'day' => Connection::PARAM_INT_ARRAY];
            foreach ($filter['worktime'] as $value) {
                $day[] =  intval($value['day']);
                $hour[] =  intval($value['hour']);
            }
            $parameters['hour'] = $hour;
            $parameters['day'] = $day;
            $worktime .= " and service_work_time.day IN (:day) and service_work_time.hour IN (:hour)";
            $sql_where .= " and t.work_day IS NOT NULL ";
        }
        return $this->getFilterData($filter, $sql_where, $price, $city, $metro, $service, $worktime, $sort, $parameters, $types);
    }

    /**
     * Возвращаем список пользователей с пагинацией
     *
     * @param $page
     * @return int|mixed|string
     */
    private function getFilterData($filter, $sql_where, $price, $city, $metro, $service, $worktime, $sort, $parameters, $types = null)
    {
        $offset = (intval($filter['page'] - 1)) * self::PAGE_OFFSET;
        $limit = self::PAGE_OFFSET;
        if (intval($filter['page']) == 0) {
            $offset = 0;
            $limit = 999;
        }

        if (!empty($sql_where)) {
            if (str_starts_with($sql_where, ' and')) {
                $sql_where = substr($sql_where, 4);
            }
            $sql_where = " WHERE " . $sql_where;
        }

        $query = trim("SELECT * FROM ( SELECT u.id, u.first_name, u.last_name, u.patronymic_name, u.last_visit_time, u.slug, u.birthday, u.gender, ( SELECT CONCAT( ';', GROUP_CONCAT(name SEPARATOR ';'), ';' ) FROM cities WHERE id IN ( SELECT cities_id FROM service_info_cities WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $city ) ) as city_name, ( SELECT CONCAT( ';', GROUP_CONCAT(station SEPARATOR ';'), ';' ) FROM metro_stations WHERE id IN ( SELECT metro_stations_id FROM service_info_metro_stations WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $metro ) ) as metro_name, ( SELECT CONCAT( 'day:', GROUP_CONCAT( service_work_time.`day` SEPARATOR ',' ), 'hour:', GROUP_CONCAT( service_work_time.`hour` SEPARATOR ',' ) ) FROM `service_work_time` WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $worktime ) as work_day, ( SELECT CONCAT( ';', GROUP_CONCAT(price SEPARATOR ';'), ';' ) FROM `service_price` WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $price ) as work_price, ( SELECT CONCAT( ';', GROUP_CONCAT(name SEPARATOR ';'), ';' ) FROM services WHERE id IN ( SELECT service_id FROM service_price WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $service ) ) as service_name FROM user as u ) as t LEFT JOIN service_info as serv ON serv.user_id = t.id  $sql_where $sort LIMIT $limit OFFSET $offset");

        $query_count = trim("SELECT COUNT(*) as count FROM ( SELECT u.id, u.first_name, u.last_name, u.patronymic_name, u.last_visit_time, u.slug, u.birthday, u.gender, ( SELECT CONCAT( ';', GROUP_CONCAT(name SEPARATOR ';'), ';' ) FROM cities WHERE id IN ( SELECT cities_id FROM service_info_cities WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $city ) ) as city_name, ( SELECT CONCAT( ';', GROUP_CONCAT(station SEPARATOR ';'), ';' ) FROM metro_stations WHERE id IN ( SELECT metro_stations_id FROM service_info_metro_stations WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $metro ) ) as metro_name, ( SELECT CONCAT( 'day:', GROUP_CONCAT( service_work_time.`day` SEPARATOR ',' ), 'hour:', GROUP_CONCAT( service_work_time.`hour` SEPARATOR ',' ) ) FROM `service_work_time` WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $worktime ) as work_day, ( SELECT CONCAT( ';', GROUP_CONCAT(price SEPARATOR ';'), ';' ) FROM `service_price` WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $price ) as work_price, ( SELECT CONCAT( ';', GROUP_CONCAT(name SEPARATOR ';'), ';' ) FROM services WHERE id IN ( SELECT service_id FROM service_price WHERE service_info_id = ( SELECT id FROM service_info WHERE service_info.user_id = u.id ) $service ) ) as service_name FROM user as u ) as t LEFT JOIN service_info as serv ON serv.user_id = t.id  $sql_where $sort");

        $conn = $this->getEntityManager()->getConnection();

        $data['result'] = $conn->executeQuery($query, $parameters, $types)->fetchAll();
        $total_count = $conn->executeQuery($query_count, $parameters, $types)->fetch();
        $data['resultTotalCount'] = (int)$total_count['count'];

        return $data;
    }
}
