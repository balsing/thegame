<?php

namespace App\MessageHandler;

use App\Entity\RoomStatus;
use App\Message\RunGameMessage;
use App\Repository\RoomRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RunGameHandler implements MessageHandlerInterface
{
    private RoomRepository $roomRepository;

    public function __construct(
        RoomRepository $roomRepository
    )
    {
        $this->roomRepository = $roomRepository;
    }

    public function __invoke(RunGameMessage $message)
    {

        $room = $this->roomRepository->find($message->getRoomId());

        if ($room->getStatus()->getCode() !== RoomStatus::RUNNING_STATUS) {
            return;
        }

        exec('bin/console game:run '.$message->getRoomId());
    }
}