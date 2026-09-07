<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return Inertia::render('admin/Bookings', [
            'bookings' => $bookings,
            'filters' => ['status' => $status],
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled', 'completed'])],
        ]);

        if ($data['status'] === 'confirmed') {
            if (! $booking->vehicle || $booking->vehicle->status !== 'available') {
                return back()->withErrors(['status' => 'Không thể xác nhận: xe hiện không khả dụng.']);
            }

            if ($booking->passengers > $booking->vehicle->seats) {
                return back()->withErrors(['status' => 'Không thể xác nhận: số hành khách vượt quá số chỗ của xe.']);
            }

            $bookingConflict = Booking::query()
                ->whereKeyNot($booking->id)
                ->where('vehicle_id', $booking->vehicle_id)
                ->whereDate('travel_date', $booking->travel_date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            $rentalConflict = Rental::query()
                ->where('vehicle_id', $booking->vehicle_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('start_date', '<=', $booking->travel_date)
                ->whereDate('end_date', '>=', $booking->travel_date)
                ->exists();

            if ($bookingConflict || $rentalConflict) {
                return back()->withErrors(['status' => 'Không thể xác nhận: xe đã có lịch trùng ngày.']);
            }
        }

        $booking->update(['status' => $data['status']]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn đặt xe.');
    }
}
