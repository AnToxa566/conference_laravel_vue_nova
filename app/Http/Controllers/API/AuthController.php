<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $createdUser = User::create($validated);

        if (!$createdUser) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        (new PlanController())->subscribeBasicPlan($createdUser);

        return response()->json([
            'user' => $createdUser,
            'auth_token' => $createdUser->createToken('auth_token')->plainTextToken,
        ]);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        $authorizedUser = User::where('email', $request->validated()['email'])->firstOrFail();

        return response()->json([
            'user' => $authorizedUser,
            'auth_token' => $authorizedUser->createToken('auth_token')->plainTextToken,
        ]);
    }


    public function update(UpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        return response()->json(
            tap(User::findOrFail($request->user()->id))->update($validated)
        );
    }


    public function checkAuth(): JsonResponse
    {
        return response()->json(auth('sanctum')->check());
    }


    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(null, 200);
    }
}
