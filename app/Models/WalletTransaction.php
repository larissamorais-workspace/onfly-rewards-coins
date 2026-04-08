<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    public $timestamps = false;
    public $updatedAt = false;
    const CREATED_AT = 'created_at';

    protected $fillable = ['wallet_id', 'booking_id', 'type', 'amount', 'description'];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'type' => 'string',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
