<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = ['name', 'brand', 'model', 'type', 'seats', 'description', 'image', 'price', 'status'];
    protected $casts = ['price' => 'decimal:2'];

    public function bookings(): HasMany
    {
        /** @var HasMany<Booking, Vehicle> $relation */
        $relation = $this->hasMany(Booking::class);
        return $relation;
    }

    public function rentals(): HasMany
    {
        /** @var HasMany<Rental, Vehicle> $relation */
        $relation = $this->hasMany(Rental::class);
        return $relation;
    }
}
