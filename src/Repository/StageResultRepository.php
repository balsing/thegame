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

    // /**
    //  * @return StageResult[] Returns an array of StageResult objects
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
    public function findOneBySomeField($value): ?StageResult
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
