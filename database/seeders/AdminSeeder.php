<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;


class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create(['email' => 'admin@groupbwt.com']);
    }
}
