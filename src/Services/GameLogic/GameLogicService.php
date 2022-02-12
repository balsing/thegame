<?php

namespace App\Services\GameLogic;

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
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Message\RunGameMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class GameLogicService
{

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
        ;
        $characters = GameLogicSettings::CODE_CHARACTERS;
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < GameLogicSettings::CODE_LENGTH; $i++) {
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
        $this->entityManager->refresh($room);
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
            $this->entityManager->refresh($player);
            $needCards = GameLogicSettings::CARD_PLAYER_COUNT - $player->getCards()->count();
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
        $stage = $room->getLastStage();

        if($stage === null){
            return;
        }

        $stage->setStatus('closed');
        $room->setLastStage(null);
        $this->entityManager->persist($stage);
        $this->entityManager->persist($room);

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

        $room->setStatus($this->getStatus(RoomStatus::ACTION_TIME_STATUS));
        $room->setLastStage($stage);
        $this->entityManager->persist($room);
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

    public function choice(Room $room, User $user, int $cardId, bool $isAuto = false): bool
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
            $this->socketService->sendNewAnswerMessage($room->getOwner(), $card, $isAuto);

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
        return $this->getStageResult($stage,$currentPlayer) === null;
    }

    private function getStageResult(Stage $stage, UsersToRoom $currentPlayer): ?StageResult
    {
        return $this->stageResultRepository->findOneBy([
                'stage' => $stage,
                'player' => $currentPlayer
            ]);
    }

    /**
     * @param Stage $stage
     * @return StageResult[]
     */
    private function getStageResults(Stage $stage): array
    {
        return $this->stageResultRepository->findBy([
            'stage' => $stage,
        ]);
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

    public function getQuestion(Room $room)
    {
        $lastStage = $room->getLastStage();
        if($lastStage !== null){
            return $lastStage->getQuestion()->getText();
        }

        return 'Сейчас здесь будет вопрос...';
    }

    public function voteAction(Room $room)
    {
        $room->setStatus($this->getStatus(RoomStatus::EVALUATE_TIME_STATUS));
        $this->answerAFKPlayers($room);
        $this->sendVoteCommand($room);
    }

    private function answerAFKPlayers(Room $room)
    {
        $stage = $room->getLastStage();
        // Мы проверяем что все игроки ответили, если кто-то не ответил, мы должны ответить за него
        $results = $this->stageResultRepository->findBy(['stage' => $stage]);


        $hasAnswerPlayers = [];
        foreach ($results as $result){
            $hasAnswerPlayers[$result->getPlayer()->getId()] = $result->getPlayer();
        }


        $players = $room->getUsersToRooms();


        foreach ($players as $player){
            if (!array_key_exists($player->getId(), $hasAnswerPlayers)){
                $cards = $player->getCards()->toArray();

                shuffle($cards);
                $card = reset($cards);

                $this->choice($room, $player->getPlayer(), $card->getId(), true);
                $this->sendRemoveCard($player->getPlayer(), $card->getId());
            }
        }
    }

    private function sendVoteCommand(Room $room)
    {

        $results = $this->getStageResults($room->getLastStage());
        foreach ($results as $result){
            $users[] = [
                'id' => $result->getPlayer()->getId(),
                'title' => $result->getPlayer()->getPlayer()->getNickname(),
                'image' => $result->getCard()->getFile(),
            ];
        }
        $this->socketService->sendMessageToRoom($room, WebSocketService::VOTE_COMMAND, [
            'users' => $users,
        ]);
    }

    private function sendRemoveCard(User $user, $cardId)
    {
        $this->socketService->sendMessageToUser($user, WebSocketService::REMOVE_CARD_COMMAND, ['card' => $cardId]);
    }

    public function vote(Room $room, ?User $user, int $userId)
    {
        $currentPlayer = null;
        $targetPlayer = null;

        foreach ($room->getUsersToRooms() as $player) {
            if ($player->getPlayer() === $user) {
                $currentPlayer = $player;
            }

            if ($player->getId() === $userId) {
                $targetPlayer = $player;
            }
        }

        if(GameLogicSettings::CHECK_SELF_VOTE){
            if ($targetPlayer->getId() === $currentPlayer->getId()) {
                if ($room->getUsersToRooms()->count() > 1) {
                    return false;
                } elseif (GameLogicSettings::CHECK_SELF_VOTE_ONE_PLAYER) {
                    return false;
                }
            }
        }

        $stage = $this->getCurrentStage($room);

        $result = $this->getStageResult($stage,$currentPlayer);

        if($result === null){
            return false;
        }

        if($result->getIsVoted() === true){
            return false;
        }

        $result->setIsVoted(true);

        $score = $targetPlayer->getScore();
        $targetPlayer->setScore(++$score);

        $this->entityManager->persist($result);
        $this->entityManager->persist($targetPlayer);
        $this->entityManager->flush();

        $this->socketService->sendMessageToUser($room->getOwner(), WebSocketService::UPDATE_SCORE_FOR_USER_COMMAND, ['user' => $targetPlayer->getPlayer()->getId(), 'score' => $targetPlayer->getScore()]);
        return true;
    }

    public function start(Room $room)
    {
        $message = new RunGameMessage($room->getId());
        $this->bus->dispatch($message);

        $room->setStatus($this->getStatus(RoomStatus::RUNNING_STATUS));

        $this->entityManager->persist($room);
        $this->entityManager->flush();
    }
}