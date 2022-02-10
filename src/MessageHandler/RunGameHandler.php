<?php

namespace App\MessageHandler;

use App\Entity\Room;
use App\Message\RunGameMessage;
use App\Repository\RoomRepository;
use App\Services\GameLogic\GameLogicService;
use App\Services\WebSocketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RunGameHandler implements MessageHandlerInterface
{
    private RoomRepository $roomRepository;
    private WebSocketService $socketService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        RoomRepository $roomRepository,
        WebSocketService $socketService
    )
    {
        $this->entityManager = $entityManager;
        $this->roomRepository = $roomRepository;
        $this->socketService = $socketService;
    }

    public function __invoke(RunGameMessage $message)
    {
        $room = $this->roomRepository->find($message->getRoomId());

        // 1. Меняем статус на игра началась
        $this->setActiveStatus($room);
        // 2. раздаём 4 карты
        $this->sendStartHands($room);
        // 3. начинаем цикл по кругам
        $this->startGame($room);

        $this->stopGame($room);
    }

    private function setActiveStatus(Room $room)
    {
        // Пока покуй на статусы
    }

    private function sendStartHands(Room $room)
    {
        $cards = $room->getCards();

        foreach ($room->getUsersToRooms() as $usersToRoom){
            $user = $usersToRoom->getPlayer();
            $i = GameLogicService::BASE_CARD_COUNT;
            while ($i > 0) {
                $card = array_shift($cards);
                $this->socketService->sendCardToUser($user, $card);
                $i--;
            }
        }

        $room->setCards($cards);

        $this->saveEntity($room);
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function startGame(Room $room, int $int = 3)
    {
        while ($int > 0){
            // 4. Выдаём вопрос
            $this->sendQuestion($room);
            $this->wait(60);
            // 5. Ждём ответы 35 секунд, либо пока не ответят все (!)

            // 5. Показываем ответы
            $this->showAnswers($room);
            $this->wait(60);
            // 6. Подведение итогов? пока нет
            // 7. Убираем выбывшие карты, добавляем новые
            $this->prepareNextRound($room);

            $int--;
        }
    }

    private function stopGame(?Room $room)
    {
        // хз чё тут делать, вывести надпись как из ералаша - конец
    }

    private function sendQuestion(Room $room)
    {
        $questions = $room->getQuestions();
        $question = array_shift($questions);
        $this->socketService->sendQuestionToRoom($room, $question);

        $room->setQuestions($questions);
        $this->saveEntity($room);
    }

    protected function wait(int $sec = 5){
        // Тут должна быть очень сложная логика по которой мы осознаём что у нас все готово
        sleep($sec);
    }

    private function showAnswers(Room $room)
    {
        // Показываем ответы
    }

    private function prepareNextRound(Room $room)
    {
        // Убираем выбывшие карты, добавляем новые
    }
}