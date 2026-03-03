<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
class AuthService
{
    public function __construct(
        private readonly JwtTokenService $tokenService
    ) {}

    public function login(string $email, string $password): array
    {
        if (! $token = Auth::guard('api')->attempt(['email' => $email, 'password' => $password])) {
            throw new AuthenticationException('Invalid credentials.');
        }
    
        $user = Auth::guard('api')->user();
    
        // Access token TTL (seconds)
        $accessExpireIn = JWTAuth::factory()->getTTL() * 60;
        $sessionStart = now()->timestamp;
        // Configure long TTL for refresh token, then issue token as string
        JWTAuth::factory()->setTTL(config('jwt.refresh_ttl'));
        $refreshToken = JWTAuth::claims(['type' => 'refresh','session_start'=>$sessionStart])
        ->fromUser($user);
    
    
    
        return [
            'access_token'             => $token,
            'refresh_token'            => $refreshToken,
            'access_token_expires_in'  => $accessExpireIn,
            'refresh_token_expires_in' => config('jwt.refresh_ttl') * 60,
        ];
    }

    public function logout(): void
    {
        $this->tokenService->invalidateCurrentToken();
    }

  public function refresh(string $refreshToken): array
{
    try {
        JWTAuth::setToken($refreshToken);
        $payload = JWTAuth::getPayload();
        $user    = JWTAuth::authenticate();

        if (! $user) {
            throw new AuthenticationException('Unauthenticated.');
        }

        // Check absolute session expiry
        $sessionStart  = $payload->get('session_start');
        $maxSessionAge = config('jwt.refresh_ttl') * 60; // in seconds

        if (now()->timestamp - $sessionStart > $maxSessionAge) {
            JWTAuth::invalidate(true);
            throw new AuthenticationException('Session expired. Please login again.');
        }

        JWTAuth::invalidate(true);

    } catch (TokenBlacklistedException|TokenInvalidException|TokenExpiredException|JWTException $e) {
        throw new AuthenticationException('Refresh token is invalid, expired, or already used.');
    }

    
    JWTAuth::factory()->setTTL((int) config('jwt.ttl'));
    $accessToken = Auth::guard('api')->login($user);

    
    JWTAuth::factory()->setTTL((int) config('jwt.refresh_ttl'));
    $newRefreshToken = JWTAuth::claims([
        'typ'           => 'refresh',
        'session_start' => $sessionStart, 
    ])->fromUser($user);

    return [
        'access_token'             => $accessToken,
        'refresh_token'            => $newRefreshToken,
        'access_token_expires_in'  => (int) config('jwt.ttl') * 60,
        'refresh_token_expires_in' => $maxSessionAge - (now()->timestamp - $sessionStart),
    ];
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