<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminRentalController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->string('status')->toString();

        $rentals = Rental::query()
            ->with('vehicle:id,name,brand,model,seats')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        return Inertia::render('admin/Rentals', [
            'rentals' => $rentals,
            'filters' => ['status' => $status],
        ]);
    }

    public function update(Request $request, Rental $rental): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled', 'completed'])],
        ]);

        if ($data['status'] === 'confirmed') {
            if (! $rental->vehicle || $rental->vehicle->status !== 'available') {
                return back()->withErrors(['status' => 'Không thể xác nhận: xe hiện không khả dụng.']);
            }

            if ($rental->passengers > $rental->vehicle->seats) {
                return back()->withErrors(['status' => 'Không thể xác nhận: số hành khách vượt quá số chỗ của xe.']);
            }

            $rentalConflict = Rental::query()
                ->where('id', '!=', $rental->id)
                ->where('vehicle_id', $rental->vehicle_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('start_date', '<=', $rental->end_date)
                ->whereDate('end_date', '>=', $rental->start_date)
                ->exists();

            $bookingConflict = Booking::query()
                ->where('vehicle_id', $rental->vehicle_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereBetween('travel_date', [$rental->start_date, $rental->end_date])
                ->exists();

            if ($rentalConflict || $bookingConflict) {
                return back()->withErrors(['status' => 'Không thể xác nhận: xe đã có lịch trùng khoảng thời gian.']);
            }
        }

        $rental->update(['status' => $data['status']]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn thuê xe.');
    }
}
