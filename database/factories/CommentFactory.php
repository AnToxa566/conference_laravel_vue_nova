<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Lecture;


class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => (User::factory()->create())->id,
            'lecture_id'    => (Lecture::factory()->create())->id,

            'description'   => fake()->text(),
        ];
    }
}
