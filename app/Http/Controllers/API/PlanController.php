<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Plan;


class PlanController extends Controller
{
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
        $user = $request->user();
        $planID = $request->get('plan');
        $paymentID = $request->get('payment');

        if ($user->subscribed(Plan::STRIPE_PRODUCT)) {
            $user->subscription(Plan::STRIPE_PRODUCT)->swap($planID);
        }
        else {
            $user->newSubscription(Plan::STRIPE_PRODUCT, $planID)->create($paymentID);
        }

        return response()->json([
            'subscription_updated' => true
        ]);
    }
}
