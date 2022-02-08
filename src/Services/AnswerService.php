<?php

namespace App\Services;

use GameData\Answers\Answers;
use Symfony\Component\Yaml\Yaml;

class AnswerService
{
    protected array $answers = [];

    public function __construct(string $baseDir)
    {
        $value = Yaml::parseFile($baseDir . '/src/GameData/Answers/answers.yaml');

        foreach ($value['answers'] as $item){
            $this->answers[$item['id']] = new Answers($item['id'], $item['text'], $item['file']);
        }
    }

    /**
     * @return Answers[]
     */
    public function getRandom(): array
    {
        $answers = $this->answers;
        shuffle($answers);

        return $answers;
    }

    public function getById(int $id): Answers
    {
        return $this->answers[$id];
    }
}