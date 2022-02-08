<?php

namespace App\Dto\Lobby;

use App\Entity\Player;

class LobbyCreateRequest
{
    protected Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }
}