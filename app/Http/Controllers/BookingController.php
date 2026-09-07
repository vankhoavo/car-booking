<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('booking/Index', [
            'vehicles' => Vehicle::query()
                ->where('status', 'available')
                ->orderBy('name')
                ->get(['id', 'name', 'brand', 'model', 'type', 'seats', 'description', 'image', 'price']),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        Booking::create($request->validated() + ['status' => 'pending']);

        return back()->with('success', 'Đặt xe thành công. Chúng tôi sẽ liên hệ để xác nhận chuyến đi.');
    }
}
