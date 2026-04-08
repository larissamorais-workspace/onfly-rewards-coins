<?php

namespace App\Http\Controllers\Api\Approver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $travelers = User::where('company_id', $companyId)
            ->where('role', 'traveler')
            ->get();

        $userIds  = $travelers->pluck('id');
        $bookings = Booking::whereIn('user_id', $userIds)->with('user')->get();

        // KPIs
        $totalCompanySavings = round($bookings->sum('company_savings'), 2);
        $totalCoinsDistributed = round($bookings->sum('onhappy_coins_amount'), 2);
        $totalSavingsBruto = round($totalCompanySavings + $totalCoinsDistributed, 2);
        $bookingsWithSavings = $bookings->where('savings_total', '>', 0)->count();
        $activeTravelers = $bookings->pluck('user_id')->unique()->count();

        // ROI
        $roiPercentage = $totalCoinsDistributed > 0
            ? round(($totalCompanySavings / $totalCoinsDistributed) * 100, 1)
            : 0;
        $roiPerReal = $totalCoinsDistributed > 0
            ? round($totalCompanySavings / $totalCoinsDistributed, 2)
            : 0;

        // By department
        $byDepartment = $bookings->groupBy(fn($b) => $b->user?->department ?? 'Sem departamento')
            ->map(fn($group, $dept) => [
                'department'      => $dept,
                'bookings_count'  => $group->count(),
                'total_savings'   => round($group->sum('company_savings'), 2),
                'travelers_count' => $group->pluck('user_id')->unique()->count(),
            ])
            ->sortByDesc('total_savings')
            ->values();

        // Bookings report (for audit table)
        $bookingsReport = $bookings->sortByDesc('created_at')->values()->map(fn($b) => [
            'id'                   => $b->id,
            'traveler_name'        => $b->user?->name ?? '—',
            'department'           => $b->user?->department ?? '—',
            'provider_name'        => $b->provider_name,
            'destination'          => $b->destination_city . ' - ' . $b->destination_state,
            'paid_price'           => $b->paid_price,
            'savings_total'        => $b->savings_total,
            'onhappy_coins_amount' => $b->onhappy_coins_amount,
            'company_savings'      => $b->company_savings,
            'check_in'             => $b->check_in?->format('d/m/Y'),
            'status'               => $b->status,
        ]);

        return response()->json([
            'total_company_savings'          => $totalCompanySavings,
            'total_onhappy_coins_distributed' => $totalCoinsDistributed,
            'total_savings_bruto'            => $totalSavingsBruto,
            'total_bookings_with_savings'    => $bookingsWithSavings,
            'active_travelers'               => $activeTravelers,
            'roi_percentage'                 => $roiPercentage,
            'roi_per_real'                   => $roiPerReal,
            'by_department'                  => $byDepartment,
            'bookings_report'                => $bookingsReport,
        ]);
    }
}
