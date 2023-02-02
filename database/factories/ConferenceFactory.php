<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ConferenceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'             => ucfirst(fake()->words(2, true)),
            'date_time_event'   => fake()->dateTimeBetween('now', '+1 year'),

            'latitude'  => fake()->latitude(),
            'longitude' => fake()->longitude(),

            'country'   => fake()->countryCode(),
        ];
    }
}
