<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentMethodRequest;
use App\Http\Requests\Payment\RemovePaymentMethodRequest;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class PaymentController extends Controller
{
    public function getSetupIntent(Request $request): JsonResponse
    {
        return response()->json($request->user()->createSetupIntent());
    }


    public function storePaymentMethod(StorePaymentMethodRequest $request): JsonResponse
    {
        $user = $request->user();
        $paymentMethodID = $request->validated()['payment_method'];

        if ($user->stripe_id === null) {
            $user->createAsStripeCustomer();
        }

        $user->addPaymentMethod($paymentMethodID);
        $user->updateDefaultPaymentMethod($paymentMethodID);

        return response()->json(null, 204);
    }


    public function getPaymentMethods(Request $request): JsonResponse
    {
        $user = $request->user();
        $methods = [];

        if ($user->hasPaymentMethod()) {
            foreach($user->paymentMethods() as $method) {
                array_push($methods, [
                    'id' => $method->id,
                    'brand' => $method->card->brand,
                    'last_four' => $method->card->last4,
                    'exp_month' => $method->card->exp_month,
                    'exp_year' => $method->card->exp_year,
                ]);
            }
        }

        return response()->json($methods);
    }


    public function removePaymentMethod(RemovePaymentMethodRequest $request): JsonResponse
    {
        $user = $request->user();
        $paymentMethodID = $request->validated()['payment_method'];

        $paymentMethods = $user->paymentMethods();

        foreach($paymentMethods as $method) {
            if ($method->id === $paymentMethodID) {
                $method->delete();
                break;
            }
        }

        return response()->json(null, 204);
    }
}
