<?php

declare(strict_types=1);

namespace Tests\Feature;

use Stripe\StripeClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Plan;


class UpdateSubscriptionTest extends TestCase
{
    use RefreshDatabase;


    protected function getPaymentMethodId(): string
    {
        $stripe = new StripeClient(config('app.stripe_secret'));

        $paymentMethod = $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
              'number' => '4242424242424242',
              'exp_month' => 2,
              'exp_year' => 2024,
              'cvc' => '314',
            ],
        ]);

        return $paymentMethod->id;
    }


    protected function getDataToUpdateSubscription(string $planSlug, bool $isInvalidPaymentMethod = false): array
    {
        return [
            'payment_method'    => $isInvalidPaymentMethod ? 'invalid' : $this->getPaymentMethodId(),
            'plan_slug'         => $planSlug,
        ];
    }


    public function testSuccessfulUpdateSubscription()
    {
        $user = User::factory()->create();
        $plan = Plan::where('slug', '=', Plan::BEGINNER_PLAN)->firstOrFail();

        $this
            ->actingAs($user)
            ->putJson('/api/plans/subscription', $this->getDataToUpdateSubscription($plan->slug))
            ->assertSuccessful();

        $this->assertEquals($plan->joins, $user->joins_left);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'stripe_price' => $plan->stripe_price,
            'stripe_status' => 'active',
        ]);
    }


    public function testUnauthorizedTryingToUpdateSubscription()
    {
        $this->putJson('/api/plans/subscription')->assertUnauthorized();
    }


    public function testUpdateSubscriptionWithInvalidPlan()
    {
        $this
            ->actingAs(User::factory()->create())
            ->putJson('/api/plans/subscription', $this->getDataToUpdateSubscription('invalid_plan'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('plan_slug');
    }


    public function testUpdateSubscriptionWithInvalidPaymentMethod()
    {
        $this
            ->actingAs(User::factory()->create())
            ->putJson('/api/plans/subscription', $this->getDataToUpdateSubscription(Plan::BEGINNER_PLAN, true))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('payment_method');
    }
}
