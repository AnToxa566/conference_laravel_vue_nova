<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Conference;


class LectureFactory extends Factory
{
    public function definition(): array
    {
        $presentetion = UploadedFile::fake()->create('presentetion.pptx');

        return [
            'user_id'           => (User::factory()->announcer()->create())->id,
            'conference_id'     => (Conference::factory()->create())->id,

            'title'             => ucfirst(fake()->words(2, true)),

            'date_time_start'   => now(),
            'date_time_end'     => now()->addHour(),

            'description'       => fake()->text(),

            'presentation_path' => Storage::fake('local')->put('presentations', $presentetion),
            'presentation_name' => $presentetion->name,

            'is_online'         => fake()->boolean(),
        ];
    }
}
