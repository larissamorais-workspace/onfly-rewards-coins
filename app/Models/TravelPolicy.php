<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelPolicy extends Model
{
    protected $fillable = ['company_id', 'destination_city', 'destination_state', 'max_daily_hotel', 'max_daily_food', 'max_flight'];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'max_daily_hotel' => 'decimal:2',
            'max_daily_food' => 'decimal:2',
            'max_flight' => 'decimal:2',
        ];
    }

    /**
     * Get the company that owns this travel policy.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all bookings associated with this travel policy.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
