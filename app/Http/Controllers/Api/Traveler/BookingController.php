<?php

namespace App\Http\Controllers\Api\Traveler;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TravelPolicy;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'modal'              => 'required|in:hotel,flight,bus,car',
            'provider_name'      => 'required|string|max:255',
            'destination_city'   => 'required|string|max:255',
            'destination_state'  => 'required|string|max:2',
            'paid_price'         => 'required|numeric|min:0',
            'original_price'     => 'required|numeric|min:0',
            'check_in'           => 'required|date',
            'check_out'          => 'nullable|date',
            'travel_policy_id'   => 'nullable|integer|exists:travel_policies,id',
        ]);

        $user = $request->user()->load('wallet');

        // Get policy ceiling for this modal
        $policy = $data['travel_policy_id']
            ? TravelPolicy::find($data['travel_policy_id'])
            : null;

        $teto = match($data['modal']) {
            'hotel'  => $policy?->max_daily_hotel ?? 750.00,
            'flight' => $policy?->max_flight,
            default  => null,
        };

        $checkIn  = $data['check_in'];
        $checkOut = $data['check_out'] ?? $data['check_in'];
        $nights   = max(1, \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)));

        // Calculate split
        $savings = ($teto && $data['paid_price'] < $teto)
            ? round(($teto - $data['paid_price']) * $nights, 2)
            : 0;
        $onhappyCoins  = round($savings * 0.5, 2);
        $companySavings = round($savings * 0.5, 2);

        // Create booking
        $booking = Booking::create([
            'user_id'           => $user->id,
            'travel_policy_id'  => $data['travel_policy_id'] ?? null,
            'modal'             => $data['modal'],
            'destination_city'  => $data['destination_city'],
            'destination_state' => $data['destination_state'],
            'provider_name'     => $data['provider_name'],
            'original_price'    => $data['original_price'],
            'paid_price'        => $data['paid_price'],
            'savings_total'     => $savings,
            'onhappy_coins_amount' => $onhappyCoins,
            'company_savings'   => $companySavings,
            'check_in'          => $data['check_in'],
            'check_out'         => $data['check_out'] ?? null,
            'status'            => 'confirmed',
        ]);

        // Credit wallet if onhappyCoins > 0
        if ($onhappyCoins > 0 && $user->wallet) {
            WalletTransaction::create([
                'wallet_id'   => $user->wallet->id,
                'booking_id'  => $booking->id,
                'type'        => 'credit',
                'amount'      => $onhappyCoins,
                'description' => 'Onhappy Coins: ' . $booking->provider_name,
            ]);

            $user->wallet->increment('balance', $onhappyCoins);
        }

        $newBalance = $user->wallet?->fresh()?->balance ?? 0;

        return response()->json([
            'booking'              => $booking,
            'onhappy_coins_earned' => $onhappyCoins,
            'new_wallet_balance'   => $newBalance,
        ], 201);
    }

    public function history(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->with('travelPolicy')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($b) => [
                'id'              => $b->id,
                'modal'           => $b->modal,
                'provider_name'   => $b->provider_name,
                'destination'     => $b->destination_city . ' - ' . $b->destination_state,
                'paid_price'      => $b->paid_price,
                'onhappy_coins_amount' => $b->onhappy_coins_amount,
                'company_savings' => $b->company_savings,
                'savings_total'   => $b->savings_total,
                'check_in'        => $b->check_in?->format('d/m/Y'),
                'check_out'       => $b->check_out?->format('d/m/Y'),
                'status'          => $b->status,
                'created_at'      => $b->created_at->format('d/m/Y'),
                'has_onhappy_coins' => $b->onhappy_coins_amount > 0,
            ]);

        return response()->json(['bookings' => $bookings]);
    }
}
