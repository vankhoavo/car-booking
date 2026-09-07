<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Vehicle;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/Index', [
            'stats' => [
                'vehicles' => Vehicle::count(),
                'availableVehicles' => Vehicle::where('status', 'available')->count(),
                'pendingBookings' => Booking::where('status', 'pending')->count(),
                'pendingRentals' => Rental::where('status', 'pending')->count(),
                'blogPosts' => BlogPost::count(),
            ],
            'recentBookings' => Booking::query()
                ->with('vehicle:id,name')
                ->latest()
                ->limit(8)
                ->get(['id', 'customer_name', 'phone', 'destination', 'travel_date', 'passengers', 'status', 'vehicle_id']),
            'recentRentals' => Rental::query()
                ->with('vehicle:id,name')
                ->latest()
                ->limit(8)
                ->get(['id', 'customer_name', 'phone', 'start_date', 'end_date', 'passengers', 'status', 'vehicle_id']),
        ]);
    }
}
