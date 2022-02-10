<?php

namespace App\Services;

use App\Entity\Card;
use App\Entity\Room;
use App\Repository\CardRepository;

class CardService
{
    private CardRepository $repository;

    public function __construct(
        CardRepository $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * @return Card[]
     */
    public function getNotUsedCards(Room $room): array
    {
        return $this->repository->findNotUsedCards($room);
    }

    public function getById(int $cardId): Card
    {
        return $this->repository->find($cardId);
    }
}