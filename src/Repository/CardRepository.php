<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Room;
use App\Entity\UsersToRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    /**
     * @param Room $room
     * @return Card[]
     */
    public function findNotUsedCards(Room $room): array
    {
        $expr = $this->_em->getExpressionBuilder();

        $sub = $this->_em->createQueryBuilder()
            ->select('a')
            ->from(UsersToRoom::class, 'a')
            ->innerJoin('a.cards', 'g')
            ->where('a.room = :room')
            ->andWhere('g.id = u.id');

        $qb = $this->createQueryBuilder('u')
            ->where($expr->not($expr->exists($sub->getDQL())))
            ->setParameter('room', $room);

        return $qb->getQuery()->getResult();
    }
}
