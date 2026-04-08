<?php

namespace App\Http\Controllers\Api\Traveler;

use App\Http\Controllers\Controller;
use App\Models\TravelPolicy;
use App\Services\MockDataGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'destination_city'  => 'required|string|max:255',
            'destination_state' => 'required|string|max:2',
            'check_in'          => 'required|date',
            'check_out'         => 'nullable|date',
        ]);

        $user = $request->user()->load('company');

        $policy = TravelPolicy::where('company_id', $user->company_id)
            ->whereRaw('LOWER(destination_city) = ?', [strtolower($data['destination_city'])])
            ->where('destination_state', strtoupper($data['destination_state']))
            ->first();

        $policyMax = $policy?->max_daily_hotel ?? 750.00;

        $raw = app(MockDataGenerator::class)->generate('hotel', $data['destination_city'], $policyMax);

        $checkIn  = $data['check_in'];
        $checkOut = $data['check_out'] ?? $data['check_in'];
        $nights   = max(1, \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)));

        $results = collect($raw)->map(function ($r) use ($policyMax, $nights) {
            $savings      = $r['price'] < $policyMax
                ? round(($policyMax - $r['price']) * $nights, 2)
                : 0;
            $onhappyCoins = round($savings * 0.5, 2);

            return array_merge($r, [
                'nights'               => $nights,
                'original_price'       => $r['price'],
                'has_onhappy_coins'    => $onhappyCoins >= 1.0,
                'onhappy_coins_amount' => $onhappyCoins,
                'savings_total'        => $savings,
                'company_savings'      => round($savings * 0.5, 2),
            ]);
        })
        ->sortByDesc(fn($r) => $r['has_onhappy_coins'] ? (1000000 + $r['onhappy_coins_amount']) : (0 - $r['price']))
        ->values();

        return response()->json([
            'results'     => $results,
            'policy'      => $policy ? [
                'id'              => $policy->id,
                'max_daily_hotel' => $policy->max_daily_hotel,
            ] : null,
            'destination' => $data['destination_city'] . ' - ' . strtoupper($data['destination_state']),
        ]);
    }
}
