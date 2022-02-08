<?php

namespace App\Dto\Game;

use App\Entity\Player;

class GameStartRequest
{
    protected Player $player;
    protected string $room;

    public function __construct(Player $player, string $room)
    {
        $this->player = $player;
        $this->room = $room;
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

    /**
     * @return string
     */
    public function getRoom(): string
    {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function setRoom(string $room): void
    {
        $this->room = $room;
    }


}