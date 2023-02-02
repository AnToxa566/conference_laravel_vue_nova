<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;


class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(2)->listener()->create();
        User::factory(2)->announcer()->create();
    }
}
