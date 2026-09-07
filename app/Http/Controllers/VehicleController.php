<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Vehicle::query()
                ->where('status', 'available')
                ->orderBy('name')
                ->get(['id', 'name', 'brand', 'model', 'type', 'seats', 'description', 'image', 'price']),
        );
    }

    public function show(Vehicle $vehicle): JsonResponse
    {
        abort_unless($vehicle->status === 'available', 404);

        return response()->json($vehicle->only([
            'id', 'name', 'brand', 'model', 'type', 'seats', 'description', 'image', 'price', 'status',
        ]));
    }

    public function availability(Request $request, Vehicle $vehicle): JsonResponse
    {
        $data = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $overlap = Rental::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('start_date', '<=', $data['end_date'])
            ->whereDate('end_date', '>=', $data['start_date'])
            ->exists();

        return response()->json([
            'available' => $vehicle->status === 'available' && ! $overlap,
        ]);
    }
}
