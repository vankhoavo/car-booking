<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_confirm_booking_when_rental_overlaps(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $vehicle = Vehicle::create([
            'name' => 'Toyota Vios', 'brand' => 'Toyota', 'model' => 'Vios',
            'type' => 'Sedan', 'seats' => 5, 'price' => 650000, 'status' => 'available',
        ]);
        $booking = Booking::create([
            'vehicle_id' => $vehicle->id, 'customer_name' => 'Customer', 'phone' => '0901234567',
            'email' => 'customer@example.com', 'pickup_location' => 'Da Nang', 'destination' => 'Hoi An',
            'travel_date' => now()->addDays(3)->toDateString(), 'pickup_time' => '09:00',
            'passengers' => 2, 'status' => 'pending',
        ]);
        Rental::create([
            'vehicle_id' => $vehicle->id, 'customer_name' => 'Rental Customer', 'phone' => '0901234568',
            'email' => 'rental@example.com', 'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(), 'pickup_location' => 'Da Nang',
            'return_location' => 'Da Nang', 'passengers' => 2, 'status' => 'confirmed',
        ]);

        $this->actingAs($admin)
            ->put("/admin/bookings/{$booking->id}", ['status' => 'confirmed'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'pending']);
    }

    public function test_admin_cannot_confirm_rental_when_booking_overlaps(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $vehicle = Vehicle::create([
            'name' => 'Toyota Innova', 'brand' => 'Toyota', 'model' => 'Innova',
            'type' => 'MPV', 'seats' => 7, 'price' => 900000, 'status' => 'available',
        ]);
        $rental = Rental::create([
            'vehicle_id' => $vehicle->id, 'customer_name' => 'Rental Customer', 'phone' => '0901234567',
            'email' => 'rental@example.com', 'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(), 'pickup_location' => 'Da Nang',
            'return_location' => 'Hoi An', 'passengers' => 3, 'status' => 'pending',
        ]);
        Booking::create([
            'vehicle_id' => $vehicle->id, 'customer_name' => 'Booking Customer', 'phone' => '0901234568',
            'email' => 'booking@example.com', 'pickup_location' => 'Da Nang', 'destination' => 'Hoi An',
            'travel_date' => now()->addDays(4)->toDateString(), 'pickup_time' => '10:00',
            'passengers' => 3, 'status' => 'confirmed',
        ]);

        $this->actingAs($admin)
            ->put("/admin/rentals/{$rental->id}", ['status' => 'confirmed'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('rentals', ['id' => $rental->id, 'status' => 'pending']);
    }

    public function test_admin_cannot_skip_booking_states(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $vehicle = Vehicle::create([
            'name' => 'Toyota Camry', 'brand' => 'Toyota', 'model' => 'Camry',
            'type' => 'Sedan', 'seats' => 5, 'price' => 1100000, 'status' => 'available',
        ]);
        $booking = Booking::create([
            'vehicle_id' => $vehicle->id, 'customer_name' => 'Customer', 'phone' => '0901234567',
            'email' => 'customer@example.com', 'pickup_location' => 'Da Nang', 'destination' => 'Hue',
            'travel_date' => now()->addDays(3)->toDateString(), 'pickup_time' => '09:00',
            'passengers' => 2, 'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->put("/admin/bookings/{$booking->id}", ['status' => 'completed'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'pending']);
    }

    public function test_admin_cannot_reduce_vehicle_capacity_below_active_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $vehicle = Vehicle::create([
            'name' => 'Ford Everest', 'brand' => 'Ford', 'model' => 'Everest',
            'type' => 'SUV', 'seats' => 7, 'price' => 1300000, 'status' => 'available',
        ]);
        Booking::create([
            'vehicle_id' => $vehicle->id, 'customer_name' => 'Customer', 'phone' => '0901234567',
            'email' => 'customer@example.com', 'pickup_location' => 'Da Nang', 'destination' => 'Hoi An',
            'travel_date' => now()->addDays(3)->toDateString(), 'pickup_time' => '09:00',
            'passengers' => 6, 'status' => 'confirmed',
        ]);

        $this->actingAs($admin)
            ->put("/admin/vehicles/{$vehicle->id}", [
                'name' => $vehicle->name, 'brand' => $vehicle->brand, 'model' => $vehicle->model,
                'type' => $vehicle->type, 'seats' => 5, 'description' => $vehicle->description,
                'image' => $vehicle->image, 'price' => $vehicle->price, 'status' => $vehicle->status,
            ])
            ->assertSessionHasErrors('seats');

        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'seats' => 7]);
    }
}
