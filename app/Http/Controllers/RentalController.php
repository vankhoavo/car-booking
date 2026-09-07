<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalRequest;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RentalController extends Controller
{
    public function create(Request $request): Response
    {
        $startDate = $request->string('date')->toString();
        $days = max(1, (int) $request->input('days', 1));
        $endDate = '';

        if ($startDate !== '') {
            try {
                $endDate = Carbon::parse($startDate)->addDays($days - 1)->toDateString();
            } catch (\Throwable) {
                $startDate = '';
            }
        }

        return Inertia::render('rental/Index', [
            'vehicles' => Vehicle::query()->where('status', 'available')->orderBy('name')->get(['id', 'name', 'brand', 'model', 'type', 'seats', 'description', 'image', 'price', 'status']),
            'initial' => [
                'pickup_location' => $request->string('pickup')->toString(),
                'return_location' => $request->string('destination')->toString(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'passengers' => max(1, (int) $request->input('passengers', 1)),
            ],
        ]);
    }

    public function store(StoreRentalRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            $vehicle = Vehicle::query()->whereKey($data['vehicle_id'])->lockForUpdate()->first();

            if (! $vehicle || $vehicle->status !== 'available') {
                abort(422, 'Xe hiện không khả dụng. Vui lòng chọn xe khác.');
            }

            if ($data['passengers'] > $vehicle->seats) {
                abort(422, "Xe {$vehicle->name} chỉ có {$vehicle->seats} chỗ.");
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
                abort(422, 'Xe đã có lịch trong khoảng thời gian bạn chọn. Vui lòng chọn xe khác hoặc ngày khác.');
            }

            Rental::create($data + ['status' => 'pending']);
        });

        return back()->with('success', 'Yêu cầu thuê xe đã được gửi thành công. Chúng tôi sẽ liên hệ để xác nhận.');
    }
}
