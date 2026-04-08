<?php
namespace Database\Seeders;

use App\Models\Booking;
use App\Models\TravelPolicy;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate in correct order (FK constraints)
        DB::table('wallet_transactions')->truncate();
        DB::table('bookings')->truncate();
        // Reset wallet balances
        DB::table('wallets')->update(['balance' => 0.00]);

        $travelers = User::where('role', 'traveler')->orderBy('id')->get();
        $policies  = TravelPolicy::all()->keyBy(fn($p) => $p->destination_city . '/' . $p->destination_state);

        $bookingsData = [
            // Isabela Carvalho — 4 bookings
            ['user' => 0, 'modal' => 'hotel',  'city' => 'São Paulo',      'state' => 'SP', 'provider' => 'Bristol São Paulo Business', 'price' => 390.00, 'check_in' => '-45 days', 'check_out' => '-43 days'],
            ['user' => 0, 'modal' => 'hotel',  'city' => 'Rio de Janeiro', 'state' => 'RJ', 'provider' => 'Ibis Rio de Janeiro Centro', 'price' => 289.00, 'check_in' => '-30 days', 'check_out' => '-28 days'],
            ['user' => 0, 'modal' => 'hotel',  'city' => 'São Paulo',      'state' => 'SP', 'provider' => 'Slaviero Essential São Paulo', 'price' => 445.00, 'check_in' => '-15 days', 'check_out' => '-13 days'],
            ['user' => 0, 'modal' => 'flight', 'city' => 'São Paulo',      'state' => 'SP', 'provider' => 'LATAM — GRU → Direto',         'price' => 1380.00, 'check_in' => '-10 days', 'check_out' => null],

            // Rafael Mendonça — 4 bookings
            ['user' => 1, 'modal' => 'hotel',  'city' => 'Curitiba',       'state' => 'PR', 'provider' => 'Ibis Curitiba Centro',          'price' => 289.00, 'check_in' => '-40 days', 'check_out' => '-38 days'],
            ['user' => 1, 'modal' => 'hotel',  'city' => 'Curitiba',       'state' => 'PR', 'provider' => 'Comfort Hotel Curitiba',         'price' => 320.00, 'check_in' => '-20 days', 'check_out' => '-18 days'],
            ['user' => 1, 'modal' => 'flight', 'city' => 'Curitiba',       'state' => 'PR', 'provider' => 'Gol — GRU → CWB Direto',        'price' => 760.00, 'check_in' => '-12 days', 'check_out' => null],
            ['user' => 1, 'modal' => 'bus',    'city' => 'Curitiba',       'state' => 'PR', 'provider' => 'Catarinense — Executivo',        'price' => 79.00,  'check_in' => '-5 days',  'check_out' => null],

            // Priscila Souza — 3 bookings
            ['user' => 2, 'modal' => 'hotel',  'city' => 'Belo Horizonte', 'state' => 'MG', 'provider' => 'Bristol Belo Horizonte Business','price' => 390.00, 'check_in' => '-35 days', 'check_out' => '-33 days'],
            ['user' => 2, 'modal' => 'hotel',  'city' => 'Belo Horizonte', 'state' => 'MG', 'provider' => 'Ibis Belo Horizonte Centro',     'price' => 289.00, 'check_in' => '-18 days', 'check_out' => '-16 days'],
            ['user' => 2, 'modal' => 'car',    'city' => 'Belo Horizonte', 'state' => 'MG', 'provider' => 'Localiza — Econômico',           'price' => 129.00, 'check_in' => '-8 days',  'check_out' => '-6 days'],

            // Lucas Ferreira — 3 bookings
            ['user' => 3, 'modal' => 'hotel',  'city' => 'São Paulo',      'state' => 'SP', 'provider' => 'Comfort Hotel São Paulo',        'price' => 320.00, 'check_in' => '-25 days', 'check_out' => '-23 days'],
            ['user' => 3, 'modal' => 'hotel',  'city' => 'São Paulo',      'state' => 'SP', 'provider' => 'Golden Tulip São Paulo Plaza',   'price' => 790.00, 'check_in' => '-14 days', 'check_out' => '-12 days'],
            ['user' => 3, 'modal' => 'flight', 'city' => 'Rio de Janeiro', 'state' => 'RJ', 'provider' => 'Azul — VCP → SDU',               'price' => 890.00, 'check_in' => '-7 days',  'check_out' => null],

            // Mariana Oliveira — 2 bookings
            ['user' => 4, 'modal' => 'hotel',  'city' => 'Curitiba',       'state' => 'PR', 'provider' => 'Mercure Curitiba Executive',     'price' => 520.00, 'check_in' => '-22 days', 'check_out' => '-20 days'],
            ['user' => 4, 'modal' => 'flight', 'city' => 'São Paulo',      'state' => 'SP', 'provider' => 'LATAM — GRU → Business',         'price' => 1380.00, 'check_in' => '-6 days', 'check_out' => null],
        ];

        foreach ($bookingsData as $data) {
            $user   = $travelers[$data['user']];
            $wallet = Wallet::where('user_id', $user->id)->first();

            $policy = $policies[$data['city'] . '/' . $data['state']] ?? null;

            $teto = match($data['modal']) {
                'hotel'  => $policy?->max_daily_hotel,
                'flight' => $policy?->max_flight,
                default  => null,
            };

            $savings        = ($teto && $data['price'] < $teto) ? round($teto - $data['price'], 2) : 0;
            $onhappyCoins   = round($savings * 0.5, 2);
            $companySavings = round($savings * 0.5, 2);

            $booking = Booking::create([
                'user_id'           => $user->id,
                'travel_policy_id'  => $policy?->id,
                'modal'             => $data['modal'],
                'destination_city'  => $data['city'],
                'destination_state' => $data['state'],
                'provider_name'     => $data['provider'],
                'original_price'    => $data['price'],
                'paid_price'        => $data['price'],
                'savings_total'     => $savings,
                'onhappy_coins_amount' => $onhappyCoins,
                'company_savings'   => $companySavings,
                'check_in'          => now()->modify($data['check_in'])->format('Y-m-d'),
                'check_out'         => $data['check_out'] ? now()->modify($data['check_out'])->format('Y-m-d') : null,
                'status'            => 'confirmed',
            ]);

            if ($onhappyCoins > 0 && $wallet) {
                WalletTransaction::create([
                    'wallet_id'   => $wallet->id,
                    'booking_id'  => $booking->id,
                    'type'        => 'credit',
                    'amount'      => $onhappyCoins,
                    'description' => 'Onhappy Coins: ' . $data['provider'],
                ]);
                $wallet->increment('balance', $onhappyCoins);
            }
        }
    }
}
