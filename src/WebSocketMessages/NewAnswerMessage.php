<?php
namespace App\WebSocketMessages;


use App\Entity\Card;

class NewAnswerMessage
{
    const ACTION = 'new_answer';

    protected string $action = self::ACTION;
    protected Card $card;
    protected bool $isAuto;

    public function __construct(Card $card, bool $isAuto)
    {
        $this->card = $card;
        $this->isAuto = $isAuto;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return Card
     */
    public function getCard(): Card
    {
        return $this->card;
    }

    /**
     * @param Card $card
     */
    public function setCard(Card $card): void
    {
        $this->card = $card;
    }

    /**
     * @return bool
     */
    public function isAuto(): bool
    {
        return $this->isAuto;
    }

    /**
     * @param bool $isAuto
     */
    public function setIsAuto(bool $isAuto): void
    {
        $this->isAuto = $isAuto;
    }
}