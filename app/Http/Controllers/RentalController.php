<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalRequest;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RentalController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('rental/Index', [
            'vehicles' => Vehicle::query()
                ->where('status', 'available')
                ->orderBy('name')
                ->get(['id', 'name', 'brand', 'model', 'type', 'seats', 'description', 'image', 'price', 'status']),
        ]);
    }

    public function store(StoreRentalRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $vehicle = Vehicle::query()
            ->whereKey($data['vehicle_id'])
            ->where('status', 'available')
            ->first();

        if (! $vehicle) {
            return back()->withErrors(['vehicle_id' => 'Xe hiện không khả dụng. Vui lòng chọn xe khác.'])->withInput();
        }

        if ($data['passengers'] > $vehicle->seats) {
            return back()->withErrors(['passengers' => "Xe {$vehicle->name} chỉ có {$vehicle->seats} chỗ."])->withInput();
        }

        $overlap = Rental::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('start_date', '<=', $data['end_date'])
            ->whereDate('end_date', '>=', $data['start_date'])
            ->exists();

        $bookingConflict = Booking::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('travel_date', [$data['start_date'], $data['end_date']])
            ->exists();

        if ($overlap || $bookingConflict) {
            return back()->withErrors(['vehicle_id' => 'Xe đã có lịch trong khoảng thời gian bạn chọn. Vui lòng chọn xe khác hoặc ngày khác.'])->withInput();
        }

        Rental::create($data + ['status' => 'pending']);

        return back()->with('success', 'Yêu cầu thuê xe đã được gửi thành công. Chúng tôi sẽ liên hệ để xác nhận.');
    }
}
