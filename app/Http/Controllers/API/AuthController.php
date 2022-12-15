<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Http\Requests\Auth\AuthUpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $response = User::create($validated);

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        Auth::login($response, true);

        return response()->json($response);
    }


    public function login(AuthLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $response = User::where('email', $validated['email'])->first();

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        if (!Hash::check($validated['password'], $response->password)) {
            return response()->json(['message' => 'Password doesn\'t match.'], 500);
        }

        Auth::login($response, true);

        return response()->json($response);
    }


    public function update(AuthUpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $response = User::find($validated['id'])->update($validated);

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        Auth::login($response, true);

        return response()->json($response);
    }


    public function logout(): void
    {
        Auth::logout();
    }
}
