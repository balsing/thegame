<?php

namespace App\Repository;

use App\Entity\StageResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StageResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method StageResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method StageResult[]    findAll()
 * @method StageResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StageResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StageResult::class);
    }
}
