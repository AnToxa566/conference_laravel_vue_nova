<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\Plan\UpdateSubscriptionRequest;

use App\Models\Plan;
use App\Models\User;


class PlanController extends Controller
{
    public function fetchPlans(): JsonResponse
    {
        return response()->json(Plan::get());
    }


    public function fetchDetail(Plan $plan): JsonResponse
    {
        return response()->json($plan);
    }


    public function fetchCurrentPlan(Request $request): JsonResponse
    {
        $subscription = $request->user()->subscriptions()->active()->firstOrFail();

        return response()->json(Plan::where('slug', $subscription->name)->firstOrFail());
    }


    public function subscribeBasicPlan(User $user): JsonResponse
    {
        $plan = Plan::where('slug', Plan::BASIC_PLAN)->firstOrFail();

        $user->newSubscription($plan->slug, $plan->stripe_price)->create();

        $user->joins_left = $plan->joins;
        $user->save();

        return response()->json(null, 200);
    }


    public function updateSubscription(UpdateSubscriptionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $plan = Plan::where('slug', $validated['plan_slug'])->firstOrFail();

        $user->newSubscription($plan->slug, $plan->stripe_price)->create($validated['payment_method']);

        $user->joins_left = $plan->joins;
        $user->save();

        $subscription = $user->subscriptions()->active()->where('stripe_price', '!=', $plan->stripe_price)->firstOrFail();
        $subscription->cancelNow();

        return response()->json(null, 200);
    }


    public function cancelSubscription(Request $request): JsonResponse
    {
        $user = $request->user();

        $subscription = $user->subscriptions()->active()->firstOrFail();
        $subscription->cancelNow();

        $this->subscribeBasicPlan($user);

        return response()->json(null, 200);
    }
}
