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

    public function sendMessageToUser(User $user, $message)
    {
        return $this->client->publish('user_'.$user->getId(), ['action' => $message]);
    }

    public function sendCardToUser(User $user, Card $card)
    {
        $message = new AddCardMessage($card);

        return $this->client->publish('user_'.$user->getId(), json_decode($this->serializer->serialize($message, 'json'), true));
    }

    public function sendMessageToRoom(Room $room, $message)
    {
        return $this->client->publish('room_'.$room->getId(), ['action' => $message]);
    }

    public function sendQuestionToRoom(Room $room, Question $question)
    {
        $message = new NewQuestionMessage($question);

        return $this->client->publish('room_'.$room->getId(), json_decode($this->serializer->serialize($message, 'json'), true));
    }

    public function sendNewAnswerMessage(User $user, Card $card)
    {
        $message = new NewAnswerMessage($card);

        return $this->client->publish('user_'.$user->getId(), json_decode($this->serializer->serialize($message, 'json'), true));
    }

    public function sendNewUserToRoom(Room $room, User $user)
    {
        $message = new NewUserToRoomMessage($user);

        return $this->client->publish('room_'.$room->getId(), json_decode($this->serializer->serialize($message, 'json'), true));
    }
}