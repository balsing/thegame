<?php

namespace App\Command;

use App\Entity\Room;
use App\Entity\RoomStatus;
use App\Entity\StageResult;
use App\Message\RunGameMessage;
use App\Repository\RoomRepository;
use App\Repository\StageResultRepository;
use App\Services\GameLogic\GameLogicService;
use App\Services\GameLogic\GameLogicSettings;
use App\Services\WebSocketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GameRunCommand extends Command
{
    protected static $defaultName = 'game:run';
    protected static $defaultDescription = 'Add a short description for your command';
    private EntityManagerInterface $entityManager;
    private GameLogicService $gameLogicService;
    private RoomRepository $roomRepository;
    private WebSocketService $socketService;

    protected function configure(): void
    {
        $this
            ->addArgument('roomId', InputArgument::REQUIRED, 'Argument description')
        ;
    }

    public function __construct(
        EntityManagerInterface $entityManager,
        GameLogicService $gameLogicService,
        RoomRepository $roomRepository,
        WebSocketService $socketService,
        $name = null
    )
    {
        $this->entityManager = $entityManager;
        $this->gameLogicService = $gameLogicService;
        $this->roomRepository = $roomRepository;
        $this->socketService = $socketService;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $roomId = $input->getArgument('roomId');
        $room = $this->roomRepository->find($roomId);

        if ($room->getStatus()->getCode() !== RoomStatus::RUNNING_STATUS) {
            return Command::INVALID;
        }


        $this->wait(GameLogicSettings::WAIT_FOR_CONNECTION, $room);
        $this->sendWaitMessage($room, GameLogicSettings::WAIT_FOR_CONNECTION);
        $this->sendMessage($room, sprintf('Игра начнётся через %s секунд',GameLogicSettings::WAIT_BEFORE_START));
        $this->wait(GameLogicSettings::WAIT_BEFORE_START, $room);

        $round = 1;
        while ($round < GameLogicSettings::ROUNDS_COUNT){
            $this->gameLogicService->nextQuestion($room);
            $this->sendWaitMessage($room, GameLogicSettings::SLEEP_AFTER_QUESTION);
            $this->wait(GameLogicSettings::SLEEP_AFTER_QUESTION, $room);


            $this->gameLogicService->voteAction($room);
            $this->sendWaitMessage($room, GameLogicSettings::SLEEP_AFTER_VOTE);
            $this->wait(GameLogicSettings::SLEEP_AFTER_VOTE, $room);

            $round++;
        }

        $this->sendMessage($room, 'Игра окончена');

        return Command::SUCCESS;
    }

    protected function wait(int $sec = 5, ?Room $room = null){
        // Тут должна быть очень сложная логика по которой мы осознаём что у нас все готово
        while ($sec > 0){
            sleep(1);
            if ($room !== null){
                $userCount = 0;
                foreach ($room->getUsersToRooms() as $usersToRoom) {
                    if ($usersToRoom->getIsActive()) {
                        $userCount++;
                    }
                }

                if ($userCount === 0){
                    return $this->closeAction();
                }

                if ($room->getStatus()->getCode() === RoomStatus::EVALUATE_TIME_STATUS) {
                    // Мы можем проверить все ли ответили
                    /** @var StageResultRepository $repo */
                    $repo = $this->entityManager->getRepository(StageResult::class);
                    $results = $repo->findBy([
                        'stage' => $room->getLastStage(),
                        'isVoted' => true,
                    ]);
                    if(count($results) === $userCount){
                        dump("Окончили досрочно: количество оценивших равно кол-ву пользователей");
                        break;
                    }
                }
                if ($room->getStatus()->getCode() === RoomStatus::ACTION_TIME_STATUS) {
                    // мы должны проверить что все выбрали карточки
                    /** @var StageResultRepository $repo */
                    $repo = $this->entityManager->getRepository(StageResult::class);
                    $userCount = $room->getUsersToRooms()->count();
                    $results = $repo->findBy([
                        'stage' => $room->getLastStage(),
                    ]);
                    if(count($results) === $userCount){
                        dump("Окончили досрочно: количество выбравших карточки равно кол-ву пользователей");
                        break;
                    }
                }
            }
            $sec--;
        }
        dump("Окончили ждать {$sec}");
    }

    protected function sendWaitMessage(Room $room, int $sec = 5){
        $this->socketService->sendTimer($room, $sec);
    }

    protected function sendMessage(Room $room, string $message){
        $this->socketService->sendMessageToRoom($room, WebSocketService::NOTIFY_COMMAND, $message);
    }

    private function closeAction()
    {
        throw new \Exception('Игра должна быть остановлена, т.к. нет подключённых клиентов');
    }
}
