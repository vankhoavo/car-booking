<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('booking/Index', [
            'vehicles' => Vehicle::query()
                ->where('status', 'available')
                ->orderBy('name')
                ->get(['id', 'name', 'brand', 'model', 'type', 'seats', 'description', 'image', 'price', 'status']),
            'initial' => [
                'pickup_location' => $request->string('pickup')->toString(),
                'destination' => $request->string('destination')->toString(),
                'travel_date' => $request->string('date')->toString(),
                'pickup_time' => $request->string('time')->toString() ?: '09:00',
                'passengers' => max(1, (int) $request->input('passengers', 1)),
            ],
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['vehicle_id'])) {
            Booking::create($data + ['status' => 'pending']);

            return back()->with('success', 'Đặt xe thành công. Chúng tôi sẽ liên hệ để xác nhận chuyến đi.');
        }

        $conflict = DB::transaction(function () use ($data): bool {
            $vehicle = Vehicle::query()
                ->whereKey($data['vehicle_id'])
                ->lockForUpdate()
                ->first();

            if (! $vehicle || $vehicle->status !== 'available') {
                return true;
            }

            if ($data['passengers'] > $vehicle->seats) {
                return true;
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
                return true;
            }

            Booking::create($data + ['status' => 'pending']);

            return false;
        });

        if ($conflict) {
            $vehicle = Vehicle::query()->find($data['vehicle_id']);

            if (! $vehicle || $vehicle->status !== 'available') {
                return back()->withErrors(['vehicle_id' => 'Xe hiện không khả dụng. Vui lòng chọn xe khác.'])->withInput();
            }

            if ($data['passengers'] > $vehicle->seats) {
                return back()->withErrors(['passengers' => "Xe {$vehicle->name} chỉ có {$vehicle->seats} chỗ."])->withInput();
            }

            return back()->withErrors(['vehicle_id' => 'Xe đã có lịch trong ngày bạn chọn. Vui lòng chọn xe khác hoặc ngày khác.'])->withInput();
        }

        return back()->with('success', 'Đặt xe thành công. Chúng tôi sẽ liên hệ để xác nhận chuyến đi.');
    }
}
