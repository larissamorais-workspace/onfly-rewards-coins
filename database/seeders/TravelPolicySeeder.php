<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\TravelPolicy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TravelPolicySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('travel_policies')->truncate();

        $company = Company::first();

        $policies = [
            [
                'destination_city' => 'São Paulo',
                'destination_state' => 'SP',
                'max_daily_hotel' => 750.00,
                'max_daily_food' => 120.00,
                'max_flight' => 1200.00,
            ],
            [
                'destination_city' => 'Curitiba',
                'destination_state' => 'PR',
                'max_daily_hotel' => 500.00,
                'max_daily_food' => 90.00,
                'max_flight' => 800.00,
            ],
            [
                'destination_city' => 'Belo Horizonte',
                'destination_state' => 'MG',
                'max_daily_hotel' => 550.00,
                'max_daily_food' => 95.00,
                'max_flight' => 900.00,
            ],
            [
                'destination_city' => 'Rio de Janeiro',
                'destination_state' => 'RJ',
                'max_daily_hotel' => 700.00,
                'max_daily_food' => 110.00,
                'max_flight' => 1100.00,
            ],
        ];

        foreach ($policies as $policy) {
            TravelPolicy::create(array_merge($policy, ['company_id' => $company->id]));
        }
    }
}
