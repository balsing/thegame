<?php

namespace App\Repository;

use App\Entity\UsersToRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsersToRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersToRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersToRoom[]    findAll()
 * @method UsersToRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersToRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersToRoom::class);
    }

    // /**
    //  * @return UsersToRoom[] Returns an array of UsersToRoom objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsersToRoom
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
