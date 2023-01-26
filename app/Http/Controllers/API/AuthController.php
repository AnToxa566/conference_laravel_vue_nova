<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;

use App\Models\Plan;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * Subscribes the user to the basic plan.
     *
     * @param User $user The user who should Subscribe the basic plan.
     * @return void
     */
    protected function subscribeBasicPlan(User $user): void
    {
        $user->newSubscription(
            Plan::BASIC_PLAN,
            Plan::where('slug', Plan::BASIC_PLAN)->firstOrFail()->stripe_price
        )->create();
    }

    /**
     * Registers a new user to the application.
     *
     * @param RegisterRequest $request The request data from the user.
     * @return Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $createdUser = User::create($validated);

        if (!$createdUser) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->subscribeBasicPlan($createdUser);

        $createdUser->{'auth_token'} = $createdUser->createToken('auth_token')->plainTextToken;
        return response()->json($createdUser);
    }

    /**
     * Authorizes the user to the application.
     *
     * @param LoginRequest $request The request data from the user.
     * @return Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $authorizedUser = User::where('email', $validated['email'])->firstOrFail();

        $authorizedUser->{'auth_token'} = $authorizedUser->createToken('auth_token')->plainTextToken;
        return response()->json($authorizedUser);
    }

    /**
     * Updates user data.
     *
     * @param UpdateRequest $request The request data from the user.
     * @return Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        return response()->json(
            tap(User::findOrFail($validated['id']))->update($validated)
        );
    }

    /**
     * Checks if the user is logged into the application.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function checkAuth(): JsonResponse
    {
        return response()->json(auth('sanctum')->check());
    }

    /**
     * Deletes all tokens of the current user.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        User::findOrFail(auth('sanctum')->id())->tokens()->delete();

        return response()->json(null, 204);
    }
}
