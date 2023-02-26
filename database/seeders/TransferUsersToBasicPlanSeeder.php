<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Plan;
use App\Http\Controllers\API\PlanController;


class TransferUsersToBasicPlanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $planController = new PlanController();

        foreach ($users as $user) {
            if ($user->type !== User::ADMIN && !($user->subscribed(Plan::BASIC_PLAN))) {
                $planController->subscribeBasicPlan($user);
            }
        }
    }
}
