<?php
namespace App\WebSocketMessages;

use App\Entity\Question;

class NewQuestionMessage
{
    const ACTION = 'question';

    protected string $action = self::ACTION;
    private Question $question;

    public function __construct(Question $question)
    {
        $this->question = $question;
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
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }
}