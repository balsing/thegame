<?php

namespace App\Entity;


use GameData\Answers\Answers;
use JsonSerializable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

class Player implements JsonSerializable
{
    protected string $name;
    protected ?Room $room = null;
    protected bool $isOwner = false;
    protected int $score = 0;
    protected UuidV4 $uuid;
    /**
     * @var Answers[]
     */
    protected array $card = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->uuid = Uuid::v4();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Room|null
     */
    public function getRoom(): ?Room
    {
        return $this->room;
    }

    /**
     * @param Room|null $room
     */
    public function setRoom(?Room $room): void
    {
        $this->room = $room;
    }

    /**
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->isOwner;
    }

    /**
     * @param bool $isOwner
     */
    public function setIsOwner(bool $isOwner): void
    {
        $this->isOwner = $isOwner;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function jsonSerialize() {
        return [
            'name' => $this->getName(),
            'room' => $this->getRoom(),
            'score' => $this->getScore(),
            'isOwner' => $this->isOwner(),
            'uuid' => $this->getUuid(),
        ];
    }

    /**
     * @return UuidV4
     */
    public function getUuid(): UuidV4
    {
        return $this->uuid;
    }

    /**
     * @param UuidV4 $uuid
     */
    public function setUuid(UuidV4 $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return Answers[]
     */
    public function getCard(): array
    {
        return $this->card;
    }

    /**
     * @param Answers[] $card
     */
    public function setCard(array $card): void
    {
        $this->card = $card;
    }
}