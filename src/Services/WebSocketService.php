<?php

namespace App\Services;

use App\Entity\Card;
use App\Entity\Room;
use App\Entity\User;
use App\WebSocketMessages\AddCardMessage;
use App\WebSocketMessages\NewAnswerMessage;
use App\WebSocketMessages\NewQuestionMessage;
use App\Entity\Question;
use App\WebSocketMessages\NewUserToRoomMessage;
use phpcent\Client;
use Symfony\Component\Serializer\SerializerInterface;

class WebSocketService
{
    public const START_GAME_COMMAND = 'start_game';
    public const VOTE_COMMAND = 'vote';
    public const REMOVE_CARD_COMMAND = 'remove_card';
    public const UPDATE_SCORE_FOR_USER_COMMAND = 'update_score_for_user';
    public const TIMER_COMMAND = 'start_timer';
    public const NOTIFY_COMMAND = 'message';
    private Client $client;
    private SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer
    )
    {
        $this->client = new Client("http://centrifugo:8000/api", "my_api_key", "my_secret");
        $this->serializer = $serializer;
    }

    public function generateToken(User $user): string
    {
        $this->client->subscribe($user->getId(), $user->getId());

        return $this->client->generateConnectionToken($user->getId());
    }

    public function sendMessageToUser(User $user, $message, $context = [])
    {
        return $this->client->publish('user_'.$user->getId(), ['action' => $message, 'context' => $context]);
    }

    public function sendCardToUser(User $user, Card $card)
    {
        $message = new AddCardMessage($card);

        return $this->client->publish('user_'.$user->getId(), json_decode($this->serializer->serialize($message, 'json'), true));
    }

    public function sendMessageToRoom(Room $room, $message, $context = [])
    {
        return $this->client->publish('room_'.$room->getId(), ['action' => $message, 'context' => $context]);
    }

    public function sendQuestionToRoom(Room $room, Question $question)
    {
        $message = new NewQuestionMessage($question);

        return $this->client->publish('room_'.$room->getId(), json_decode($this->serializer->serialize($message, 'json'), true));
    }

    public function sendNewAnswerMessage(User $user, Card $card, bool $isAuto = false)
    {
        $message = new NewAnswerMessage($card, $isAuto);

        return $this->client->publish('user_'.$user->getId(), json_decode($this->serializer->serialize($message, 'json'), true));
    }

    public function sendNewUserToRoom(Room $room, User $user)
    {
        $message = new NewUserToRoomMessage($user);

        return $this->client->publish('room_'.$room->getId(), json_decode($this->serializer->serialize($message, 'json'), true));
    }

    public function sendTimer(Room $room, int $seconds)
    {
        return $this->sendMessageToRoom($room, WebSocketService::TIMER_COMMAND, ['seconds' => $seconds]);
    }
}