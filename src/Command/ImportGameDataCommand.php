<?php

namespace App\Command;

use App\Entity\Card;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ImportGameDataCommand extends Command
{
    protected static $defaultName = 'import:gamedata';
    protected static $defaultDescription = 'Add a short description for your command';
    private const PATH_TO_QUESTIONS_FILE = '/public/images/questions_big_base.yaml';
    private const PATH_TO_CARDS_FILE = '/public/images/answers_big_base.yaml';
    private EntityManagerInterface $entityManager;
    private string $baseDir;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $baseDir,
        string $name = null
    )
    {
        $this->baseDir = $baseDir;
        $this->entityManager = $entityManager;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $value = Yaml::parseFile($this->baseDir . self::PATH_TO_QUESTIONS_FILE);

        foreach ($value['questions'] as $item){
            $question = new Question();
            $question->setText($item['text']);
            $this->entityManager->persist($question);
        }

        $value = Yaml::parseFile($this->baseDir . self::PATH_TO_CARDS_FILE);

        foreach ($value['answers'] as $item){
            $card = new Card();
            $card->setTitle($item['text']);
            $card->setFile($item['file']);
            $this->entityManager->persist($card);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
