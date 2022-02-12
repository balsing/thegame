<?php

namespace App\Services\GameLogic;

class GameLogicSettings
{
    // Задержка после вопроса
    public const SLEEP_AFTER_QUESTION = 20;
    // Задержка после начала голосования
    public const SLEEP_AFTER_VOTE = 40;
    // Задержка перед началом игры
    public const WAIT_FOR_CONNECTION = 5;
    // Задержка после начала игры до первого вопроса
    public const WAIT_BEFORE_START = 5;
    // Количество раундов
    public const ROUNDS_COUNT = 20;
    // Количество карточек, которые раздаются игроку
    public const CARD_PLAYER_COUNT = 6;
    // Набор символов, из которых генерируется код комнаты
    public const CODE_CHARACTERS = '0123456789'; //'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // Длина кода
    public const CODE_LENGTH = 3;
    // Проверять голосования за себя
    public const CHECK_SELF_VOTE = true;
    // Проверять голосования за себя, если только 1 игрок
    public const CHECK_SELF_VOTE_ONE_PLAYER = false;
}