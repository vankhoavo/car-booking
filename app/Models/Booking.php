<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'vehicle_id', 'customer_name', 'phone', 'email', 'pickup_location', 'destination',
        'travel_date', 'pickup_time', 'passengers', 'notes', 'status',
    ];

    protected $casts = ['travel_date' => 'date'];

    public function vehicle(): BelongsTo
    {
        /** @var BelongsTo<Vehicle, Booking> $relation */
        $relation = $this->belongsTo(Vehicle::class);
        return $relation;
    }
}
