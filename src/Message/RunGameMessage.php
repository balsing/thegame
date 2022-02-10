<?php

namespace App\Message;

class RunGameMessage implements MessageInterface
{
    protected int $roomId;

    public function __construct(int $roomId)
    {
        $this->roomId = $roomId;
    }

    /**
     * @return int
     */
    public function getRoomId(): int
    {
        return $this->roomId;
    }

    /**
     * @param int $roomId
     */
    public function setRoomId(int $roomId): void
    {
        $this->roomId = $roomId;
    }
}