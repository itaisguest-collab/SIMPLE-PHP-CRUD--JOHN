<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtTokenService
{
    public function createTokenForUser(User $user): array
    {
        $token = Auth::guard('api')->login($user);

        return [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    public function invalidateCurrentToken(): void
    {
        Auth::guard('api')->logout();
    }

    public function refreshToken(): array
    {
        $token = JWTAuth::refresh();

        return [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    public function getUserFromToken(): ?User
    {
        return Auth::guard('api')->user();
    }
}