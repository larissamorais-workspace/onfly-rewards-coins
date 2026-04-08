<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'travel_policy_id', 'modal', 'destination_city', 'destination_state',
        'provider_name', 'original_price', 'paid_price', 'savings_total',
        'onhappy_coins_amount', 'company_savings', 'check_in', 'check_out', 'status'
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'paid_price' => 'decimal:2',
        'savings_total' => 'decimal:2',
        'onhappy_coins_amount' => 'decimal:2',
        'company_savings' => 'decimal:2',
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function travelPolicy()
    {
        return $this->belongsTo(TravelPolicy::class);
    }

    public function walletTransaction()
    {
        return $this->hasOne(WalletTransaction::class);
    }
}
