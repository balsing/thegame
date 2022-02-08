<?php

namespace App\Services;


class UserService
{
    private JwtTokenService $tokenService;

    public function __construct(
        JwtTokenService $tokenService
    )
    {
        $this->tokenService = $tokenService;
    }

    public function create(string $user)
    {
        $this->tokenService->create([
            'user' => $user
        ]);
    }
}