<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;

use App\Http\Controllers\API\PlanController;


class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'    => fake()->firstName(),
            'last_name'     => fake()->lastName(),

            'type'          => User::LISTENER,
            'country'       => fake()->countryCode(),
            'birthdate'     => now(),

            'phone_number'          => fake()->phoneNumber(),
            'country_phone_code'    => fake()->countryCode(),

            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),

            'password'          => Hash::make(User::FACTORY_PASSWORD),
            'remember_token'    => Str::random(10),
        ];
    }


    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            if ($user->type !== User::ADMIN) {
                (new PlanController())->subscribeBasicPlan($user);
            }
        });
    }


    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }


    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => User::ADMIN,
        ]);
    }


    public function announcer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => User::ANNOUNCER,
        ]);
    }


    public function listener(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => User::LISTENER,
        ]);
    }
}
