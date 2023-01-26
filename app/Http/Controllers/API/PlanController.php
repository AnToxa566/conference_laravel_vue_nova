<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

use App\Models\Plan;


class PlanController extends Controller
{
    /**
     * Loads all plans into the database from the Stripe API.
     *
     * @return Illuminate\Http\JsonResponse
     */
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

    /**
     * Gets an all of plans.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function fetchPlans(): JsonResponse
    {
        return response()->json(Plan::get());
    }

    /**
     * Gets a concrete plan's instance.
     *
     * @param Plan $plan The plan received in router via slug.
     * @return Illuminate\Http\JsonResponse
     */
    public function fetchDetail(Plan $plan): JsonResponse
    {
        return response()->json($plan);
    }

    /**
     * Updates a subscription for the current user.
     *
     * @param Request $request The request containing subscription update info.
     * @return Illuminate\Http\JsonResponse
     */
    public function updateSubscription(Request $request): JsonResponse
    {
        $request->user()->newSubscription($request->get('plan_slug'), $request->get('stripe_price'))->create($request->get('payment'));

        return response()->json([
            'subscription_updated' => true
        ]);
    }
}
