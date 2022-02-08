<?php

namespace App\Command;

use App\Entity\Player;
use App\Services\WebSocketService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetTokenCommand extends Command
{
    protected static $defaultName = 'app:get-token';
    protected static $defaultDescription = 'Add a short description for your command';
    private WebSocketService $service;

    public function __construct(
        WebSocketService $service,
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
