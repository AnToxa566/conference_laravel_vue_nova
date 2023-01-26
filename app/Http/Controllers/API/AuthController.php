<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;


class AuthController extends Controller
{
    public function checkAuth(): JsonResponse
    {
        return response()->json(auth('sanctum')->check());
    }


    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $createdUser = User::create($validated);

        if (!$createdUser) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $createdUser->{'auth_token'} = $createdUser->createToken('auth_token')->plainTextToken;
        return response()->json($createdUser);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $authorizedUser = User::where('email', $validated['email'])->firstOrFail();

        $authorizedUser->{'auth_token'} = $authorizedUser->createToken('auth_token')->plainTextToken;
        return response()->json($authorizedUser);
    }


    public function update(UpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        return response()->json(
            tap(User::findOrFail($validated['id']))->update($validated)
        );
    }


    public function logout(): JsonResponse
    {
        User::findOrFail(auth('sanctum')->id())->tokens()->delete();

        return response()->json(null, 204);
    }
}
