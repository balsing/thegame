<?php

namespace App\Services;

use App\Entity\Question;
use App\Entity\Room;
use App\Repository\QuestionRepository;

class QuestionService
{
    protected array $questions = [];
    private QuestionRepository $repository;

    public function __construct(
        QuestionRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function getNext(Room $room): Question
    {
        return $this->repository->getNextQuestion($room);
    }
}