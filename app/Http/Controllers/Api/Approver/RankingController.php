<?php
namespace App\Http\Controllers\Api\Approver;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $ranking = User::where('company_id', $companyId)
            ->where('role', 'traveler')
            ->withSum('bookings as total_company_savings', 'company_savings')
            ->withSum('bookings as total_onhappy_coins_earned', 'onhappy_coins_amount')
            ->withCount('bookings as total_bookings')
            ->orderByDesc('total_company_savings')
            ->get()
            ->values()
            ->map(fn($u, $i) => [
                'position'              => $i + 1,
                'user_id'               => $u->id,
                'name'                  => $u->name,
                'position_title'        => $u->position,
                'department'            => $u->department,
                'total_company_savings' => round((float) ($u->total_company_savings ?? 0), 2),
                'total_onhappy_coins_earned' => round((float) ($u->total_onhappy_coins_earned ?? 0), 2),
                'total_bookings'        => (int) ($u->total_bookings ?? 0),
            ]);

        return response()->json(['ranking' => $ranking]);
    }
}
