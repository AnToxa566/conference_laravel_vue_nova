<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Plan',
                'slug' => 'free',
                'stripe_plan' => 'price_1MUByXIJ9AlRaPQ5LXUKg51b',
                'price' => 0,
                'description' => 'Free plan includes 1 conference join per month.'
            ],
            [
                'name' => 'Basic Plan',
                'slug' => 'basic',
                'stripe_plan' => 'price_1MUByXIJ9AlRaPQ5ZrGhS9ag',
                'price' => 15,
                'description' => 'The Basic Plan includes 5 conference joins per month - $15 per month.'
            ],
            [
                'name' => 'Profession Plan',
                'slug' => 'profession',
                'stripe_plan' => 'price_1MUByXIJ9AlRaPQ559icH0wU',
                'price' => 25,
                'description' => 'The Profession Plan includes 50 conference joins per month - $25 per month.'
            ],
            [
                'name' => 'Unlimited Plan',
                'slug' => 'unlimited',
                'stripe_plan' => 'price_1MUByXIJ9AlRaPQ5eU9OamKc',
                'price' => 100,
                'description' => 'The Unlimited plan has no limits on conferences joins - $100 per month.'
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
