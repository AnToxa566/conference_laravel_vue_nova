<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

use App\Models\Plan;
use App\Models\User;


class PlanController extends Controller
{
    public function loadPlans(): JsonResponse
    {
        Plan::truncate();

        $allPlans = Cashier::stripe()->products->all(['active' => true]);

        foreach ($allPlans as $plan) {
            Plan::create([
                'slug' => $plan->name,
                'stripe_price' => $plan->default_price,
                'price' => $plan->metadata->price,
                'joins' => $plan->metadata->joins,
                'description' => $plan->description,
            ]);
        }

        return response()->json(null, 204);
    }


    public function fetchPlans(): JsonResponse
    {
        return response()->json(Plan::get());
    }


    public function fetchDetail(Plan $plan): JsonResponse
    {
        return response()->json($plan);
    }


    public function fetchCurrentPlan(): JsonResponse
    {
        $subscription = auth('sanctum')->user()->subscriptions()->active()->firstOrFail();

        return response()->json(Plan::where('slug', $subscription->name)->firstOrFail());
    }


    public function subscribeBasicPlan(User $user): JsonResponse
    {
        $plan = Plan::where('slug', Plan::BASIC_PLAN)->firstOrFail();

        $user->newSubscription($plan->slug, $plan->stripe_price)->create();

        $user->joins_left = $plan->joins;

        return response()->json(null, 204);
    }


    public function updateSubscription(Request $request): JsonResponse
    {
        $plan = Plan::where('slug', $request->get('plan_slug'))->firstOrFail();

        $request->user()->newSubscription($plan->slug, $plan->stripe_price)->create($request->get('payment'));

        $request->user()->joins_left = $plan->joins;
        $request->user()->save();

        $subscription = auth('sanctum')->user()->subscriptions()->active()->where('stripe_price', '!=', $plan->stripe_price)->firstOrFail();
        $subscription->cancelNow();

        return response()->json([
            'subscription_updated' => true
        ]);
    }
}
