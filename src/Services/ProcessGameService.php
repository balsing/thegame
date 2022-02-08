<?php

namespace App\Services;

use App\Entity\Room;
use App\Entity\RoomStatus;

class ProcessGameService
{
    public function process(Room $room)
    {
        $status = $room->getStatus()->getCode();
        switch ($status) {
            case RoomStatus::RUNNING_STATUS:
                $this->running($room);
                break;
            case RoomStatus::ACTION_TIME_STATUS:
                $this->action($room);
                break;
            case RoomStatus::EVALUATE_TIME_STATUS:
                $this->evaluate($room);
                break;
        }
    }

    private function running(Room $room)
    {
        // Раздаём всем по 6 карт и даём ознакомиться
    }

    private function action(Room $room)
    {
        // Отправляем случайное задание и ждём ответов
    }

    private function evaluate(Room $room)
    {
        // Оценка результатов. Показываем результаты и ждём ответы о оценке.
    }
}