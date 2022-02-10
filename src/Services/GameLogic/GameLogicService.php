<?php

namespace App\Services\GameLogic;

use App\Dto\Game\GameStartRequest;
use App\Entity\Player;
use App\Entity\Question;
use App\Entity\Room;
use App\Entity\RoomStatus;
use App\Entity\Stage;
use App\Entity\StageResult;
use App\Entity\User;
use App\Entity\UsersToRoom;
use App\Repository\StageRepository;
use App\Repository\StageResultRepository;
use App\Services\CardService;
use App\Services\QuestionService;
use App\Services\WebSocketService;
use App\WebSocketMessages\NewAnswerMessage;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Message\RunGameMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Security\Core\User\UserInterface;

class GameLogicService
{
    public const BASE_CARD_COUNT = 6;

    private EntityManagerInterface $entityManager;
    private QuestionService $questionService;
    private CardService $cardService;
    private MessageBusInterface $bus;
    private StageRepository $stageRepository;
    private WebSocketService $socketService;
    private StageResultRepository $stageResultRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        QuestionService        $questionService,
        CardService            $cardService,
        MessageBusInterface    $bus,
        StageRepository        $stageRepository,
        StageResultRepository  $stageResultRepository,
        WebSocketService       $socketService
    )
    {
        $this->entityManager = $entityManager;
        $this->questionService = $questionService;
        $this->cardService = $cardService;
        $this->bus = $bus;
        $this->stageRepository = $stageRepository;
        $this->stageResultRepository = $stageResultRepository;
        $this->socketService = $socketService;
    }

    public function createNewGame(User $user): Room
    {
        $room = new Room();
        $room->setCode($this->generateRandomString());
        $room->setStatus($this->getStatus(RoomStatus::NEW_STATUS));
        $room->setOwner($user);
        $this->entityManager->persist($room);
        $this->entityManager->flush();

        return $room;
    }

    public function enterToGame(User $user, string $code): Room
    {
        $room = $this->findRoom(mb_strtoupper($code));

        if (!$this->isUserAlreadyInRoom($room, $user)) {
            $room->getUsersToRooms()->removeElement($user);

            $player = new UsersToRoom();
            $player->setPlayer($user);
            $player->setRoom($room);

            $this->entityManager->persist($player);
            $this->entityManager->flush();

            $this->socketService->sendNewUserToRoom($room, $user);
        }

        return $room;
    }

    function generateRandomString()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 4; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function getStatus(string $statusCode): RoomStatus
    {
        $status = $this->entityManager->getRepository(RoomStatus::class)->findOneBy(['code' => $statusCode]);
        if ($status instanceof RoomStatus) {
            return $status;
        }

        throw new Exception(sprintf('Статус с кодом %s не был найден', $statusCode));
    }

    private function findRoom(string $code): Room
    {
        $room = $this->entityManager->getRepository(Room::class)->findOneBy(['code' => $code]);
        if ($room instanceof Room) {
            return $room;
        }

        throw new Exception(sprintf('Комната с кодом %s не была найдена', $code));
    }

    public function nextQuestion(Room $room)
    {
        $this->closeStage($room);
        $this->fillCards($room);
        $question = $this->getNextQuestion($room);
        $this->createStage($room, $question);
    }

    private function fillCards(Room $room)
    {
        // Получаем все неиспользуемые карты
        $allCards = $this->cardService->getNotUsedCards($room);
        if ($allCards < 1) {
            // Игра окончена
        }
        shuffle($allCards);


        foreach ($room->getUsersToRooms() as $player) {
            $needCards = self::BASE_CARD_COUNT - $player->getCards()->count();
            while ($needCards > 0) {
                $card = array_shift($allCards);
                $player->addCard($card);
                $this->socketService->sendCardToUser($player->getPlayer(), $card);
                $needCards--;
            }
        }

        $this->entityManager->persist($room);
        $this->entityManager->flush();
    }

    private function getNextQuestion(Room $room): Question
    {
        $question = $this->questionService->getNext($room);

        $this->socketService->sendQuestionToRoom($room, $question);

        return $question;
    }

    private function closeStage(Room $room)
    {
        // ToDo: мы должны пройтись по всем игрокам и посмотреть, ответили они или нет
        // ToDo: и если не ответили, то наказать их забрав любую карту

        $stages = $this->stageRepository->findBy([
            'room' => $room,
            'status' => 'open'
        ]);

        foreach ($stages as $stage) {
            $stage->setStatus('closed');
            $this->entityManager->persist($stage);
        }

        $this->entityManager->flush();
    }

    private function createStage(Room $room, Question $question)
    {
        $stage = new Stage();
        $stage->setStatus('open');
        $stage->setQuestion($question);
        $stage->setRoom($room);

        $this->entityManager->persist($stage);
        $this->entityManager->flush();
    }

    public function getCards(Room $room, User $user): array
    {
        foreach ($room->getUsersToRooms() as $player) {
            if ($player->getPlayer() === $user) {
                return $player->getCards()->toArray();
            }
        }
    }

    public function choice(Room $room, User $user, int $cardId): bool
    {
        $card = $this->cardService->getById($cardId);
        $currentPlayer = null;
        foreach ($room->getUsersToRooms() as $player) {
            if ($player->getPlayer() === $user) {
                $player->getCards()->removeElement($card);
                $currentPlayer = $player;
                break;
            }
        }

        $stage = $this->getCurrentStage($room);

        if ($this->checkIsNewChoice($stage, $currentPlayer)) {
            $stageResult = new StageResult();
            $stageResult->setPlayer($currentPlayer);
            $stageResult->setCard($card);
            $stageResult->setStage($stage);

            $this->entityManager->persist($stageResult);
            $this->entityManager->flush();

            $card->setTitle($user->getNickname());
            $this->socketService->sendNewAnswerMessage($room->getOwner(), $card);

            return true;
        } else {
            return false;
        }
    }

    private function getCurrentStage(Room $room): Stage
    {
        return $this->stageRepository->findOneBy([
            'room' => $room,
            'status' => 'open'
        ]);
    }

    private function checkIsNewChoice(Stage $stage, UsersToRoom $currentPlayer): bool
    {
        return $this->stageResultRepository->findOneBy([
                'stage' => $stage,
                'player' => $currentPlayer
            ]) === null;
    }

    private function isUserAlreadyInRoom(Room $room, User $user): bool
    {
        foreach ($room->getUsersToRooms() as $player) {
            if ($player->getPlayer() === $user){
                return true;
            }
        }

        return false;
    }
}