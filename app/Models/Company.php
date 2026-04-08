<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'cnpj'];

    /**
     * Get all users that belong to the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all travel policies for the company.
     */
    public function travelPolicies()
    {
        return $this->hasMany(TravelPolicy::class);
    }
}
