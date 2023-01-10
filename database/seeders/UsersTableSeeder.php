<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'first_name' => 'Anton',
                'last_name' => 'Bohachuk',
                'birthdate' => '2003-03-16',
                'type' => User::LISTENER,
                'country' => 'Ukraine',
                'email' => 'anton@groupbwt.com',
                'phone_number' => '+380991124364',
                'country_phone_code' => 'UA',
                'password' => Hash::make('12345678'),
            ],
            [
                'first_name' => 'Bohdan',
                'last_name' => 'Bohachuk',
                'birthdate' => '2007-05-13',
                'type' => User::ANNOUNCER,
                'country' => 'Ukraine',
                'email' => 'bohdan@groupbwt.com',
                'phone_number' => '+380662037939',
                'country_phone_code' => 'UA',
                'password' => Hash::make('12345678'),
            ],
            [
                'first_name' => 'Liza',
                'last_name' => 'Bohachuk',
                'birthdate' => '2004-04-28',
                'type' => User::ANNOUNCER,
                'country' => 'Ukraine',
                'email' => 'liza@groupbwt.com',
                'phone_number' => '+380998888888',
                'country_phone_code' => 'UA',
                'password' => Hash::make('12345678'),
            ],
        ];

        User::insert($users);
    }
}
