<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Conference;


class ConferencesTableSeeder extends Seeder
{
    public function run(): void
    {
        Conference::factory(50)->create();
    }
}
