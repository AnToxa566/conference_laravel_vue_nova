<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Events\WebhookHandled;

use App\Models\User;
use App\Models\Plan;

use App\Http\Controllers\API\PlanController;


class StripeWebhookController extends CashierController
{
    public function handleWebhook(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);
        $method = 'handle'.Str::studly(str_replace('.', '_', $payload['type']));

        WebhookReceived::dispatch($payload);

        if (method_exists($this, $method)) {
            $response = $this->{$method}($payload);

            WebhookHandled::dispatch($payload);

            return $response;
        }

        return $this->missingMethod($payload);
    }

    /*
        invoice.payment_succeeded
    */
    protected function handleInvoicePaymentSucceeded(array $payload): JsonResponse
    {
        $user = User::where('stripe_id', '=', $payload['data']['object']['customer'])->firstOrFail();
        $subscription = $user->subscriptions()->active()->where('user_id', '=', $user->id)->firstOrFail();
        $plan = Plan::where('slug', $subscription->name)->firstOrFail();

        $user->joins_left = $plan->joins;
        $user->save();

        return response()->json(null, 200);
    }

    /*
        invoice.payment_failed
    */
    protected function handleInvoicePaymentFailed(array $payload): JsonResponse
    {
        $user = User::where('stripe_id', '=', $payload['data']['object']['customer'])->firstOrFail();

        (new PlanController)->subscribeBasicPlan($user);

        return response()->json(null, 200);
    }
}
