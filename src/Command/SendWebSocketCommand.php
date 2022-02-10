<?php

namespace App\Command;

use App\Entity\Player;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Services\CardService;
use App\Services\QuestionService;
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
    private UserRepository $userRepository;
    private CardService $answerService;
    private RoomRepository $roomRepository;
    private QuestionService $questionService;

    public function __construct(
        CardService      $answerService,
        UserRepository   $userRepository,
        QuestionService $questionService,
        RoomRepository $roomRepository,
        WebSocketService $service,
        string           $name = null
    )
    {
        $this->roomRepository = $roomRepository;
        $this->questionService = $questionService;
        $this->answerService = $answerService;
        $this->userRepository = $userRepository;
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
        //$user = $this->userRepository->find(3);

        //$answer = $this->answerService->getById(3);

        $room = $this->roomRepository->find(45);
        $question = $this->questionService->getNext($room);


        $token = $this->service->sendQuestionToRoom($room, $question);
        dd($token);

        return Command::SUCCESS;
    }
}
