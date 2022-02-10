<?php
namespace App\WebSocketMessages;


use App\Entity\Card;

class AddCardMessage
{
    const ACTION = 'add_card';

    protected string $action = self::ACTION;
    protected Card $card;

    public function __construct(Card $card)
    {
        $this->card = $card;
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
}