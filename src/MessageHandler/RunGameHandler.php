<?php

namespace App\MessageHandler;

use App\Entity\Room;
use App\Entity\RoomStatus;
use App\Entity\StageResult;
use App\Message\RunGameMessage;
use App\Repository\RoomRepository;
use App\Repository\StageResultRepository;
use App\Services\GameLogic\GameLogicService;
use App\Services\WebSocketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RunGameHandler implements MessageHandlerInterface
{
    public const SLEEP_AFTER_QUESTION = 20;
    public const SLEEP_AFTER_VOTE = 40;
    private RoomRepository $roomRepository;
    private WebSocketService $socketService;
    private GameLogicService $gameLogicService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        GameLogicService $gameLogicService,
        RoomRepository $roomRepository,
        WebSocketService $socketService
    )
    {
        $this->entityManager = $entityManager;
        $this->gameLogicService = $gameLogicService;
        $this->roomRepository = $roomRepository;
        $this->socketService = $socketService;
    }

    public function __invoke(RunGameMessage $message)
    {

        exec('bin/console game:run '.$message->getRoomId());

        return;

        $room = $this->roomRepository->find($message->getRoomId());

        if ($room->getStatus()->getCode() !== RoomStatus::RUNNING_STATUS) {
            return;
        }

        $this->wait(5, $room);
        $this->sendWaitMessage($room, 5);
        $this->sendMessage($room, 'Игра начнётся через 15 секунд');
        $this->wait(5, $room);

        $round = 1;
        while ($round < 20){
            $this->gameLogicService->nextQuestion($room);
            $this->sendWaitMessage($room, self::SLEEP_AFTER_QUESTION);
            $this->wait(self::SLEEP_AFTER_QUESTION, $room);


            $this->gameLogicService->voteAction($room);
            $this->sendWaitMessage($room, self::SLEEP_AFTER_VOTE);
            $this->wait(self::SLEEP_AFTER_VOTE, $room);

            $round++;
        }

        $this->sendMessage($room, 'Игра окончена');
    }

    protected function wait(int $sec = 5, ?Room $room = null){
        // Тут должна быть очень сложная логика по которой мы осознаём что у нас все готово
        while ($sec > 0){
            sleep(1);
            if ($room !== null){
                if ($room->getStatus()->getCode() === RoomStatus::EVALUATE_TIME_STATUS) {
                    // Мы можем проверить все ли ответили
                    /** @var StageResultRepository $repo */
                    $repo = $this->entityManager->getRepository(StageResult::class);
                    $userCount = $room->getUsersToRooms()->count();
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
}