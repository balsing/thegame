<?php

namespace App\Entity;

use App\Repository\RoomStatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomStatusRepository::class)
 */
class RoomStatus
{
    /**
     * Игра в ожидании игроков
     */
    public const NEW_STATUS = 'new';
    /**
     * Начало игры
     */
    public const RUNNING_STATUS = 'running';
    /**
     * Время для выбора карточки
     */
    public const ACTION_TIME_STATUS = 'action_time';
    /**
     * Время для оценки участников
     */
    public const EVALUATE_TIME_STATUS = 'evaluate_time';
    /**
     * Игра была брошена
     */
    public const THROWN_STATUS = 'thrown';
    /**
     * Игра была закрыта
     */
    public const CLOSE_STATUS = 'thrown';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"comment":"Идентификатор"})
     */
    private int $id = 0;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Символьный код статуса"})
     */
    private string $code;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Название статуса"})
     */
    private string $title;

    public function __construct($code, $title)
    {
        $this->code = $code;
        $this->title = $title;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
