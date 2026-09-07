<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rental;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminVehicleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Vehicles', [
            'vehicles' => Vehicle::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Vehicle::create($this->validated($request));

        return back()->with('success', 'Đã thêm xe mới.');
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $data = $this->validated($request);

        $result = DB::transaction(function () use ($vehicle, $data): string {
            $lockedVehicle = Vehicle::query()->whereKey($vehicle->id)->lockForUpdate()->firstOrFail();
            $activePassengers = max(
                (int) Booking::query()
                    ->where('vehicle_id', $lockedVehicle->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->max('passengers'),
                (int) Rental::query()
                    ->where('vehicle_id', $lockedVehicle->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->max('passengers'),
            );

            if ((int) $data['seats'] < $activePassengers) {
                return 'capacity';
            }

            $lockedVehicle->update($data);

            return 'updated';
        });

        if ($result === 'capacity') {
            return back()->withErrors([
                'seats' => 'Số chỗ mới không thể nhỏ hơn số hành khách của đơn đang chờ hoặc đã xác nhận.',
            ])->withInput();
        }

        return back()->with('success', 'Đã cập nhật thông tin xe.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $deleted = DB::transaction(function () use ($vehicle): bool {
            $lockedVehicle = Vehicle::query()->whereKey($vehicle->id)->lockForUpdate()->firstOrFail();

            $hasOrders = Booking::query()->where('vehicle_id', $lockedVehicle->id)->exists()
                || Rental::query()->where('vehicle_id', $lockedVehicle->id)->exists();

            if ($hasOrders) {
                return false;
            }

            $lockedVehicle->delete();

            return true;
        });

        if (! $deleted) {
            return back()->withErrors([
                'vehicle' => 'Không thể xóa xe đã có đơn đặt hoặc đơn thuê. Hãy chuyển xe sang trạng thái "Ngừng sử dụng" thay vì xóa.',
            ]);
        }

        return back()->with('success', 'Đã xóa xe.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'brand' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:80'],
            'type' => ['nullable', 'string', 'max:80'],
            'seats' => ['required', 'integer', 'min:1', 'max:60'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'url', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'status' => ['required', 'in:available,maintenance,inactive'],
        ]);
    }
}
