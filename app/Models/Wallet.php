<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get the user that owns this wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all wallet transactions for this wallet.
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
