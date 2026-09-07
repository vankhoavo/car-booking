<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminBookingController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->string('status')->toString();
        $bookings = Booking::query()
            ->with('vehicle:id,name,brand,model,seats')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        return Inertia::render('admin/Bookings', ['bookings' => $bookings, 'filters' => ['status' => $status]]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled', 'completed'])],
        ]);

        $result = DB::transaction(function () use ($booking, $data): string {
            $lockedBooking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $next = $data['status'];
            $current = $lockedBooking->status;
            $allowed = match ($current) {
                'pending' => ['confirmed', 'cancelled'],
                'confirmed' => ['completed', 'cancelled'],
                'cancelled', 'completed' => [],
                default => [],
            };

            if ($current === $next) {
                return 'unchanged';
            }

            if (! in_array($next, $allowed, true)) {
                return 'invalid_transition';
            }

            if ($next !== 'confirmed') {
                $lockedBooking->update(['status' => $next]);

                return 'updated';
            }

            $vehicle = $lockedBooking->vehicle()->lockForUpdate()->first();

            if (! $vehicle || $vehicle->status !== 'available') {
                return 'unavailable';
            }

            if ($lockedBooking->passengers > $vehicle->seats) {
                return 'capacity';
            }

            $bookingConflict = Booking::query()
                ->where('id', '!=', $lockedBooking->id)
                ->where('vehicle_id', $vehicle->id)
                ->whereDate('travel_date', $lockedBooking->travel_date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            $rentalConflict = Rental::query()
                ->where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('start_date', '<=', $lockedBooking->travel_date)
                ->whereDate('end_date', '>=', $lockedBooking->travel_date)
                ->exists();

            if ($bookingConflict || $rentalConflict) {
                return 'conflict';
            }

            $lockedBooking->update(['status' => 'confirmed']);

            return 'confirmed';
        });

        return match ($result) {
            'confirmed' => back()->with('success', 'Đã xác nhận đơn đặt xe.'),
            'updated', 'unchanged' => back()->with('success', 'Đã cập nhật trạng thái đơn đặt xe.'),
            'unavailable' => back()->withErrors(['status' => 'Không thể xác nhận: xe hiện không khả dụng.']),
            'capacity' => back()->withErrors(['status' => 'Không thể xác nhận: số hành khách vượt quá số chỗ của xe.']),
            'conflict' => back()->withErrors(['status' => 'Không thể xác nhận: xe đã có lịch trùng ngày.']),
            default => back()->withErrors(['status' => 'Không thể chuyển đơn sang trạng thái này.']),
        };
    }
}
