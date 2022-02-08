<?php

namespace App\Command;

use App\Entity\Player;
use App\Services\QuestionService;
use App\Services\WebSocketService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugCommand extends Command
{
    protected static $defaultName = 'app:debug';
    protected static $defaultDescription = 'Add a short description for your command';
    private QuestionService $service;

    public function __construct(
        QuestionService $service,
        string $name = null
    )
    {
        $this->service = $service;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        /*$this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;*/
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $player = new Player('Денис');
        dump($player->getUuid());
        $token = $this->service->generateToken($player);
        dd($token);

        return Command::SUCCESS;
    }
}
