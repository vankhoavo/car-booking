<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Rental;
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
                ->get(['id', 'name', 'brand', 'model', 'type', 'seats', 'description', 'image', 'price', 'status']),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['vehicle_id'])) {
            $vehicle = Vehicle::query()->whereKey($data['vehicle_id'])->where('status', 'available')->first();

            if (! $vehicle) {
                return back()->withErrors(['vehicle_id' => 'Xe hiện không khả dụng. Vui lòng chọn xe khác.'])->withInput();
            }

            if ($data['passengers'] > $vehicle->seats) {
                return back()->withErrors(['passengers' => "Xe {$vehicle->name} chỉ có {$vehicle->seats} chỗ."])->withInput();
            }

            $bookingConflict = Booking::query()
                ->where('vehicle_id', $vehicle->id)
                ->whereDate('travel_date', $data['travel_date'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            $rentalConflict = Rental::query()
                ->where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('start_date', '<=', $data['travel_date'])
                ->whereDate('end_date', '>=', $data['travel_date'])
                ->exists();

            if ($bookingConflict || $rentalConflict) {
                return back()->withErrors(['vehicle_id' => 'Xe đã có lịch trong ngày bạn chọn. Vui lòng chọn xe khác hoặc ngày khác.'])->withInput();
            }
        }

        Booking::create($data + ['status' => 'pending']);

        return back()->with('success', 'Đặt xe thành công. Chúng tôi sẽ liên hệ để xác nhận chuyến đi.');
    }
}
