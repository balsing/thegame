<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Room;
use App\Entity\Stage;
use App\Entity\UsersToRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }
    public function getNextQuestion(Room $room): Question
    {
        $expr = $this->_em->getExpressionBuilder();

        $sub = $this->_em->createQueryBuilder()
            ->select('a')
            ->from(Stage::class, 'a')
            ->where('a.room = :room')
            ->andWhere('a.question = u.id');

        $qb = $this->createQueryBuilder('u')
            ->where($expr->not($expr->exists($sub->getDQL())))
            ->setMaxResults(1)
            ->orderBy('RANDOM()')
            ->setParameter('room', $room);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
