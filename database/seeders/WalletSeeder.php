<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('wallets')->truncate();

        // Create wallet only for travelers (not approvers)
        User::where('role', 'traveler')->each(function (User $user) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00,
            ]);
        });
    }
}
