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
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $response = User::create($validated);

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response->{'auth_token'} = $response->createToken('auth_token')->plainTextToken;

        return response()->json($response);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $response = User::where('email', $validated['email'])->first();

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response->{'auth_token'} = $response->createToken('auth_token')->plainTextToken;
        return response()->json($response);
    }


    public function update(UpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $response = tap(User::find($validated['id']))->update($validated);

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return response()->json($response);
    }


    public function logout(): JsonResponse
    {
        $response = User::find(auth('sanctum')->id());

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response->tokens()->delete();

        return response()->json($response);
    }
}
