<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(
        private readonly JwtTokenService $tokenService
    ) {}

    public function login(string $email, string $password): array
    {
        if (! Auth::guard('api')->attempt(['email' => $email, 'password' => $password])) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $user = Auth::guard('api')->user();

        return $this->tokenService->createTokensForUser($user);
    }

    public function refresh(string $refreshToken): array
    {
        return $this->tokenService->refreshTokens($refreshToken);
    }

    public function logout(): void
    {
        $this->tokenService->invalidateCurrentToken();
    }

    public function me(): User
    {
        $user = $this->tokenService->getUserFromToken();

        if (! $user) {
            throw new AuthenticationException('Unauthenticated.');
        }

        return $user;
    }
}
