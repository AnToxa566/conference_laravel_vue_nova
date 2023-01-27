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


    public function subscribeBasicPlan(User $user): JsonResponse
    {
        $plan = Plan::where('slug', Plan::BASIC_PLAN)->firstOrFail();

        $user->newSubscription($plan->slug, $plan->stripe_price)->create();

        $user->joins_left = $plan->joins;

        return response()->json(null, 204);
    }


    public function updateSubscription(Request $request): JsonResponse
    {
        $request->user()->newSubscription($request->get('plan_slug'), $request->get('stripe_price'))->create($request->get('payment'));

        // $request->user()->joins_left = $plan->joins;

        return response()->json([
            'subscription_updated' => true
        ]);
    }
}