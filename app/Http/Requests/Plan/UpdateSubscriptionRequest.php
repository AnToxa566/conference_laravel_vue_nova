<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan;

use Stripe\StripeClient;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;


class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method'    => ['required', 'string'],
            'plan_slug'         => ['required', 'string', 'exists:plans,slug'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        try {
            (new StripeClient(config('app.stripe_secret')))->paymentMethods->retrieve($validator->getData()['payment_method']);
        }
        catch (\Stripe\Exception\ApiErrorException $e) {
            $validator->after(function ($validator) {
                $validator->errors()->add('payment_method', 'Payment method is invalid!');
            });
        }
    }
}
