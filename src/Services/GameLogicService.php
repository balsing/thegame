<?php

namespace App\Services;

use App\Dto\Game\GameStartRequest;
use App\Entity\Player;
use App\Entity\Room;
use App\Entity\RoomStatus;
use App\Entity\User;
use App\Entity\UsersToRoom;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Component\Security\Core\User\UserInterface;

class GameLogicService
{
    private EntityManagerInterface $entityManager;
    private QuestionService $questionService;
    private AnswerService $answerService;

    public function __construct(
        EntityManagerInterface $entityManager,
        QuestionService $questionService,
        AnswerService $answerService
    )
    {
        $this->entityManager = $entityManager;
        $this->questionService = $questionService;
        $this->answerService = $answerService;
    }

    public function createNewGame(User $user): Room
    {
        $room = new Room();
        $room->setCode($this->generateRandomString());
        $room->setStatus($this->getStatus(RoomStatus::NEW_STATUS));
        $this->entityManager->persist($room);
        $this->entityManager->flush();

        $player = new UsersToRoom();
        $player->setPlayer($user);
        $player->setRoom($room);
        $player->setIsOwner(true);

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $room;
    }

    public function enterToGame(User $user, string $code): Room
    {
        $room = $this->findRoom($code);

        $player = new UsersToRoom();
        $player->setPlayer($user);
        $player->setRoom($room);

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $room;
    }

    function generateRandomString() {
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
        if($status instanceof RoomStatus){
            return $status;
        }

        throw new Exception(sprintf('Статус с кодом %s не был найден', $statusCode));
    }

    private function findRoom(string $code): Room
    {
        $room = $this->entityManager->getRepository(Room::class)->findOneBy(['code' => $code]);
        if($room instanceof Room){
            return $room;
        }

        throw new Exception(sprintf('Комната с кодом %s не была найдена', $code));
    }

    public function startGame(GameStartRequest $gameStartRequest)
    {
        $room = $this->entityManager->getRepository(Room::class)->findOneBy(['code' => $code]);
        if(!$room instanceof Room){
            throw new Exception(sprintf('Комната с кодом %s не была найдена', $code));
        }

        $room->setStatus($this->getStatus(RoomStatus::RUNNING_STATUS));

        // Находим задание и раздаём по 4 карты каждому участнику
        // Мы можем сгенерировать здесь 10 вопросов и сразу сохранить их в задачу

        $questions = $this->questionService->getRandom();
        $room->setQuestions(array_map(fn ($q) => $q['id'], $questions));

        $answers = $this->answerService->getRandom();
        $room->setCards(array_map(fn ($a) => $a['id'], $answers));

        ///// нужно реализовать колоду, в которую буду добавляться карты и раздаваться
        /// вопрос куда мы будем результаты скидывать за раунд и общие
        /// Всё в массиве хранить - плохо
        /// Отдельно хранить - тоже плохо (но почему? боимся за БД ?) :/
        /// Да и игроков может быть есть смысл хранить в БД, пускай будут, тогда
        /// запросы можно сделать легче и авторизацию нормальную
        foreach ($room->getPlayers() as $player){
            $player->setCard();
        }

    }
}