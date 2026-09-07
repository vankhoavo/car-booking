<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = ['name', 'brand', 'model', 'type', 'seats', 'description', 'image', 'price', 'status'];
    protected $casts = ['price' => 'decimal:2'];

    /** @return HasMany<Booking, Vehicle> */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /** @return HasMany<Rental, Vehicle> */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
