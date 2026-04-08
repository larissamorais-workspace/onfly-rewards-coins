<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            UserSeeder::class,
            TravelPolicySeeder::class,
            WalletSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
