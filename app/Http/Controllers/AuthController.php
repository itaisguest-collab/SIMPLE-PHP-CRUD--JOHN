<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\AuthService;
use App\Http\Resources\AuthTokenResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        return (new AuthTokenResource($data))->response();
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->me();

        return (new UserResource($user))->response();
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(['message' => 'Logged out']);
    }

    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->input('refresh_token');

        $data = $this->authService->refresh($refreshToken);

        return (new AuthTokenResource($data))->response();
    }
}