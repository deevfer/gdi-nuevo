<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HomePageSeeder::class,
            PlatformPageSeeder::class,
            FinancingPageSeeder::class,
            InvestorsPageSeeder::class,
            StrategiesPageSeeder::class,
            InsightsPageSeeder::class,
            ContactPageSeeder::class,
            GlobalContentSeeder::class,
        ]);
    }
}
