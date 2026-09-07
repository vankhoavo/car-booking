<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rental;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $data = $this->validated($request);
        Vehicle::create($data);

        return back()->with('success', 'Đã thêm xe mới.');
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($this->validated($request));

        return back()->with('success', 'Đã cập nhật thông tin xe.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $hasOrders = Booking::query()->where('vehicle_id', $vehicle->id)->exists()
            || Rental::query()->where('vehicle_id', $vehicle->id)->exists();

        if ($hasOrders) {
            return back()->withErrors([
                'vehicle' => 'Không thể xóa xe đã có đơn đặt hoặc đơn thuê. Hãy chuyển xe sang trạng thái "Ngừng sử dụng" thay vì xóa.',
            ]);
        }

        $vehicle->delete();

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
