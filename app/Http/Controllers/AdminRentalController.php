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

class AdminRentalController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->string('status')->toString();
        $rentals = Rental::query()->with('vehicle:id,name,brand,model,seats')->when($status !== '', fn ($query) => $query->where('status', $status))->latest()->get();
        return Inertia::render('admin/Rentals', ['rentals' => $rentals, 'filters' => ['status' => $status]]);
    }

    public function update(Request $request, Rental $rental): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled', 'completed'])]]);
        $result = DB::transaction(function () use ($rental, $data): string {
            $lockedRental = Rental::query()->whereKey($rental->id)->lockForUpdate()->firstOrFail();
            $next = $data['status'];
            $current = $lockedRental->status;
            /** @var array<string, array<int, string>> $allowedTransitions */
            $allowedTransitions = ['pending' => ['confirmed', 'cancelled'], 'confirmed' => ['completed', 'cancelled'], 'cancelled' => [], 'completed' => []];
            $allowed = $allowedTransitions[$current] ?? [];
            if ($current === $next) return 'unchanged';
            if (! in_array($next, $allowed, true)) return 'invalid_transition';
            if ($next !== 'confirmed') { $lockedRental->update(['status' => $next]); return 'updated'; }

            $vehicle = $lockedRental->vehicle()->lockForUpdate()->first();
            if (! $vehicle || $vehicle->status !== 'available') return 'unavailable';
            if ($lockedRental->passengers > $vehicle->seats) return 'capacity';
            $rentalConflict = Rental::query()->where('id', '!=', $lockedRental->id)->where('vehicle_id', $vehicle->id)->whereIn('status', ['pending', 'confirmed'])->whereDate('start_date', '<=', $lockedRental->end_date)->whereDate('end_date', '>=', $lockedRental->start_date)->exists();
            $bookingConflict = Booking::query()->where('vehicle_id', $vehicle->id)->whereIn('status', ['pending', 'confirmed'])->whereBetween('travel_date', [$lockedRental->start_date, $lockedRental->end_date])->exists();
            if ($rentalConflict || $bookingConflict) return 'conflict';
            $lockedRental->update(['status' => 'confirmed']);
            return 'confirmed';
        });
        return match ($result) {
            'confirmed' => back()->with('success', 'Đã xác nhận đơn thuê xe.'),
            'updated', 'unchanged' => back()->with('success', 'Đã cập nhật trạng thái đơn thuê xe.'),
            'unavailable' => back()->withErrors(['status' => 'Không thể xác nhận: xe hiện không khả dụng.']),
            'capacity' => back()->withErrors(['status' => 'Không thể xác nhận: số hành khách vượt quá số chỗ của xe.']),
            'conflict' => back()->withErrors(['status' => 'Không thể xác nhận: xe đã có lịch trùng khoảng thời gian.']),
            default => back()->withErrors(['status' => 'Không thể chuyển đơn sang trạng thái này.']),
        };
    }
}
