<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;


class LectureFactory extends Factory
{
    public function definition(): array
    {
        $presentetion = UploadedFile::fake()->create('presentetion.pptx');

        return [
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
