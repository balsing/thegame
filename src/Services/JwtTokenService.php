<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenService
{
    private const KEY = 'example_key';

    public function create(array $payload): string
    {
        return JWT::encode($payload, self::KEY, 'HS256');
    }

    public function decode(string $jwt): object
    {
        return JWT::decode($jwt, new Key(self::KEY, 'HS256'));
    }
}