<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Cashier\Cashier;

use App\Models\Plan;


class LoadPlansSeeder extends Seeder
{
    public function run(): void
    {
        Plan::truncate();

        $allPlans = Cashier::stripe()->products->all(['active' => true]);

        foreach ($allPlans as $plan) {
            Plan::create([
                'slug' => $plan->name,
                'stripe_price' => $plan->default_price,
                'price' => $plan->metadata->price,
                'joins' => $plan->metadata->joins,
                'description' => $plan->description,
            ]);
        }
    }
}
