<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtTokenService
{
    public function createTokensForUser(User $user): array
    {
        $sessionStart = now()->timestamp;

        // Access token (short TTL)
       $accessToken = JWTAuth::fromUser($user);
        $accessExpireIn = JWTAuth::factory()->getTTL() * 60;

        // Refresh token (long TTL, with session_start claim)
        JWTAuth::factory()->setTTL(config('jwt.refresh_ttl'));
        $refreshToken = JWTAuth::claims([
            'type'          => 'refresh',
            'session_start' => $sessionStart,
        ])->fromUser($user);

        return [
            'access_token'             => $accessToken,
            'refresh_token'            => $refreshToken,
            'access_token_expires_in'  => $accessExpireIn,
            'refresh_token_expires_in' => config('jwt.refresh_ttl') * 60,
        ];
    }

    public function refreshTokens(string $refreshToken): array
    {
        try {
            JWTAuth::setToken($refreshToken);
            $payload = JWTAuth::getPayload();
            $user = JWTAuth::authenticate();

            if (! $user) {
                throw new AuthenticationException('Unauthenticated.');
            }

            $sessionStart = $payload->get('session_start');
            $maxSessionAge = config('jwt.refresh_ttl') * 60; // seconds

            if (now()->timestamp - $sessionStart > $maxSessionAge) {
                JWTAuth::invalidate(true);
                throw new AuthenticationException('Session expired. Please login again.');
            }

            JWTAuth::invalidate(true);
        } catch (TokenBlacklistedException|TokenInvalidException|TokenExpiredException|JWTException $e) {
            throw new AuthenticationException('Refresh token is invalid, expired, or already used.');
        }

        // New access token
        JWTAuth::factory()->setTTL((int) config('jwt.ttl'));
        $accessToken = JWTAuth::fromUser($user);
        $accessExpireIn = (int) config('jwt.ttl') * 60;

        // New refresh token (keep same session_start)
        JWTAuth::factory()->setTTL((int) config('jwt.refresh_ttl'));
        $newRefreshToken = JWTAuth::claims([
            'type'          => 'refresh',
            'session_start' => $sessionStart,
        ])->fromUser($user);

        return [
            'access_token'             => $accessToken,
            'refresh_token'            => $newRefreshToken,
            'access_token_expires_in'  => $accessExpireIn,
            'refresh_token_expires_in' => $maxSessionAge - (now()->timestamp - $sessionStart),
        ];
    }

    public function invalidateCurrentToken(): void
    {
        Auth::guard('api')->logout();
    }

   public function getUserFromToken(): ?User
{
    try {
        return Auth::guard('api')->user();
    } catch (
        TokenExpiredException |
        TokenInvalidException |
        TokenBlacklistedException |
        JWTException $e
    ) {
        throw new AuthenticationException('Token is invalid or expired.');
    }
}
}
