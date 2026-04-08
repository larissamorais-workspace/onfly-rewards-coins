<?php

namespace App\Http\Controllers\Api\Traveler;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $wallet = $user->wallet()
            ->with(['transactions' => function ($q) {
                $q->orderBy('created_at', 'desc')->limit(20);
            }])
            ->first();

        $transactions = $wallet
            ? $wallet->transactions->map(fn($t) => [
                'id'          => $t->id,
                'type'        => $t->type,
                'amount'      => $t->amount,
                'description' => $t->description,
                'created_at'  => optional($t->created_at)->format('d/m/Y') ?? now()->format('d/m/Y'),
            ])
            : collect();

        $bookingRows = $user->bookings()->orderByDesc('created_at')->get();

        // Balance = sum of all coins earned across bookings (source of truth for display)
        $totalCoins = round($bookingRows->sum('onhappy_coins_amount'), 2);

        $bookings = $bookingRows->map(fn($b) => [
            'id'                   => $b->id,
            'provider_name'        => $b->provider_name,
            'destination'          => $b->destination_city . ' - ' . $b->destination_state,
            'paid_price'           => $b->paid_price,
            'onhappy_coins_amount' => $b->onhappy_coins_amount,
            'company_savings'      => $b->company_savings,
            'savings_total'        => $b->savings_total,
            'check_in'             => $b->check_in?->format('d/m/Y'),
            'check_out'            => $b->check_out?->format('d/m/Y'),
            'status'               => $b->status,
            'created_at'           => $b->created_at->format('d/m/Y'),
            'has_onhappy_coins'    => $b->onhappy_coins_amount > 0,
        ]);

        return response()->json([
            'wallet'       => [
                'id'      => $wallet?->id,
                'balance' => $totalCoins,
            ],
            'transactions' => $transactions,
            'bookings'     => $bookings,
        ]);
    }
}
