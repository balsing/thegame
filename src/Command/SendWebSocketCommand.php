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
use Symfony\Component\Uid\UuidV4;

class SendWebSocketCommand extends Command
{
    protected static $defaultName = 'app:send-message';
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
        $player->setUuid(UuidV4::fromString('e1f0320e-cee5-4863-8d86-1c045eb51207'));
        $token = $this->service->sendMessage($player, 'Привет из сообщения');
        dd($token);

        return Command::SUCCESS;
    }
}
