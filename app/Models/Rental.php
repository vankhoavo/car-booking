<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    protected $fillable = [
        'vehicle_id', 'customer_name', 'phone', 'email', 'start_date', 'end_date',
        'pickup_location', 'return_location', 'passengers', 'notes', 'status',
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function vehicle(): BelongsTo
    {
        /** @var BelongsTo<Vehicle, Rental> $relation */
        $relation = $this->belongsTo(Vehicle::class);
        return $relation;
    }
}
