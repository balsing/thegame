<?php

namespace App\Repository;

use App\Entity\RoomStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RoomStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomStatus[]    findAll()
 * @method RoomStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomStatus::class);
    }
}
