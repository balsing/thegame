<?php

namespace App\WebSocketMessages;

use App\Entity\Question;
use App\Entity\User;

class NewUserToRoomMessage
{
    const ACTION = 'new_player';

    protected string $action = self::ACTION;
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}