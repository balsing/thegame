<?php

namespace App\Services;

use App\Entity\Player;
use phpcent\Client;

class WebSocketService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client("http://centrifugo:8000/api", "my_api_key", "my_secret");
    }

    public function generateToken(Player $player): string
    {
        $this->client->subscribe($player->getUuid(), $player->getUuid());
        return $this->client->generateConnectionToken($player->getUuid());
    }

    public function sendMessage(Player $player, $message)
    {
        return $this->client->publish($player->getUuid(), $message);

    }
}