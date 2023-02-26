<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Http\Controllers\API\PlanController;


class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $planController = new PlanController();

        $users = [
            [
                'first_name' => 'Anton',
                'last_name' => 'Bohachuk',
                'birthdate' => '2003-03-16',
                'type' => User::LISTENER,
                'country' => 'UA',
                'email' => 'bohachuk_am@groupbwt.com',
                'phone_number' => '+380991124364',
                'country_phone_code' => 'UA',
                'password' => Hash::make('12345678'),
            ],
            [
                'first_name' => 'Bohdan',
                'last_name' => 'Bohachuk',
                'birthdate' => '2007-05-13',
                'type' => User::ANNOUNCER,
                'country' => 'UA',
                'email' => 'bogachuk566@gmail.com',
                'phone_number' => '+380662037939',
                'country_phone_code' => 'UA',
                'password' => Hash::make('12345678'),
            ],
            [
                'first_name' => 'Liza',
                'last_name' => 'Bohachuk',
                'birthdate' => '2004-04-28',
                'type' => User::ANNOUNCER,
                'country' => 'UA',
                'email' => 'trash.anton.bog@gmail.com',
                'phone_number' => '+380998888888',
                'country_phone_code' => 'UA',
                'password' => Hash::make('12345678'),
            ],
        ];

        foreach ($users as $user) {
            $createdUder = User::create($user);

            $planController->subscribeBasicPlan($createdUder);
        }
    }
}
