<?php

namespace App\Services;

use GameData\Question\Question;
use Symfony\Component\Yaml\Yaml;

class QuestionService
{
    protected array $questions = [];

    public function __construct(string $baseDir)
    {
        $value = Yaml::parseFile($baseDir . '/src/GameData/Questions/questions.yaml');

        foreach ($value['questions'] as $item){
            $this->questions[$item['id']] = new Question($item['id'], $item['text']);
        }
    }

    /**
     * @param int $count
     * @return Question[]
     */
    public function getRandom(int $count = 10): array
    {
        $question = $this->questions;
        shuffle($question);
        return $question;
    }

    public function getById(int $id): Question
    {
        return $this->questions[$id];
    }
}