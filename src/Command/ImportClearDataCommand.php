<?php

namespace App\Command;

use App\Entity\Card;
use App\Entity\Question;
use App\Entity\Room;
use App\Entity\Stage;
use App\Entity\StageResult;
use App\Entity\UsersToRoom;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportClearDataCommand extends Command
{
    protected static $defaultName = 'import:clear';
    protected static $defaultDescription = 'Удаляет всю лишнюю информацию, в том числе вопросы и ответы';
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
        $this->delete(StageResult::class);
        $this->delete(Stage::class);
        $this->delete(UsersToRoom::class);
        $this->delete(Room::class);
        $this->delete(Question::class);
        $this->delete(Card::class);

        return Command::SUCCESS;
    }

    protected function delete(string $class){
        $result = $this->entityManager->getRepository($class)->findAll();
        foreach ($result as $item){
            $this->entityManager->remove($item);
        }

        $this->entityManager->flush();
    }
}
