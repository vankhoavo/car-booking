<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'vehicle_id', 'customer_name', 'phone', 'email', 'pickup_location', 'destination',
        'travel_date', 'pickup_time', 'passengers', 'notes', 'status',
    ];

    protected $casts = [
        'travel_date' => 'date',
    ];

    /** @return BelongsTo<Vehicle, Booking> */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
