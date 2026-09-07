<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Rental;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBookingRentalTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_a_booking_without_authentication(): void
    {
        $vehicle = Vehicle::create([
            'name' => 'Toyota Vios',
            'brand' => 'Toyota',
            'model' => 'Vios',
            'type' => 'Sedan',
            'seats' => 5,
            'price' => 650000,
            'status' => 'available',
        ]);

        $response = $this->post('/dat-xe', [
            'customer_name' => 'Nguyen Van A',
            'phone' => '0901234567',
            'email' => 'customer@example.com',
            'pickup_location' => 'Da Nang Airport',
            'destination' => 'Hoi An',
            'travel_date' => now()->addDay()->toDateString(),
            'pickup_time' => '09:00',
            'passengers' => 2,
            'vehicle_id' => $vehicle->id,
            'notes' => '2 vali',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'email' => 'customer@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_rejects_booking_when_passengers_exceed_vehicle_capacity(): void
    {
        $vehicle = Vehicle::create([
            'name' => 'Toyota Vios', 'brand' => 'Toyota', 'model' => 'Vios',
            'type' => 'Sedan', 'seats' => 5, 'price' => 650000, 'status' => 'available',
        ]);

        $response = $this->post('/dat-xe', [
            'customer_name' => 'Customer', 'phone' => '0901234567', 'email' => 'customer@example.com',
            'pickup_location' => 'Da Nang', 'destination' => 'Hoi An',
            'travel_date' => now()->addDay()->toDateString(), 'pickup_time' => '09:00',
            'passengers' => 6, 'vehicle_id' => $vehicle->id, 'notes' => null,
        ]);

        $response->assertSessionHasErrors('passengers');
        $this->assertDatabaseMissing('bookings', ['email' => 'customer@example.com']);
    }

    public function test_rejects_rental_when_passengers_exceed_vehicle_capacity(): void
    {
        $vehicle = Vehicle::create([
            'name' => 'Toyota Innova', 'brand' => 'Toyota', 'model' => 'Innova',
            'type' => 'MPV', 'seats' => 7, 'price' => 900000, 'status' => 'available',
        ]);

        $response = $this->post('/thue-xe', [
            'customer_name' => 'Customer', 'phone' => '0901234567', 'email' => 'rental@example.com',
            'vehicle_id' => $vehicle->id, 'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(), 'pickup_location' => 'Da Nang',
            'return_location' => 'Hoi An', 'passengers' => 8, 'notes' => null,
        ]);

        $response->assertSessionHasErrors('passengers');
        $this->assertDatabaseMissing('rentals', ['email' => 'rental@example.com']);
    }

    public function test_rejects_an_unavailable_rental_period(): void
    {
        $vehicle = Vehicle::create([
            'name' => 'Toyota Innova',
            'brand' => 'Toyota',
            'model' => 'Innova',
            'type' => 'MPV',
            'seats' => 7,
            'price' => 900000,
            'status' => 'available',
        ]);

        Rental::create([
            'vehicle_id' => $vehicle->id,
            'customer_name' => 'Existing Customer',
            'phone' => '0901234567',
            'email' => 'existing@example.com',
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'pickup_location' => 'Da Nang',
            'return_location' => 'Da Nang',
            'passengers' => 2,
            'status' => 'confirmed',
        ]);

        $response = $this->post('/thue-xe', [
            'customer_name' => 'New Customer',
            'phone' => '0901234567',
            'email' => 'new@example.com',
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'pickup_location' => 'Da Nang',
            'return_location' => 'Hoi An',
            'passengers' => 3,
            'notes' => null,
        ]);

        $response->assertSessionHasErrors('vehicle_id');
        $this->assertDatabaseMissing('rentals', ['email' => 'new@example.com']);
    }

    public function test_vehicle_availability_api_rejects_a_conflicting_booking(): void
    {
        $vehicle = Vehicle::create([
            'name' => 'Toyota Corolla Cross',
            'brand' => 'Toyota',
            'model' => 'Corolla Cross',
            'type' => 'SUV',
            'seats' => 5,
            'price' => 950000,
            'status' => 'available',
        ]);
        $date = now()->addDays(3)->toDateString();

        Booking::create([
            'vehicle_id' => $vehicle->id,
            'customer_name' => 'Existing Customer',
            'phone' => '0901234567',
            'email' => 'existing@example.com',
            'pickup_location' => 'Da Nang',
            'destination' => 'Hoi An',
            'travel_date' => $date,
            'pickup_time' => '09:00',
            'passengers' => 2,
            'status' => 'confirmed',
        ]);

        $response = $this->getJson("/api/vehicles/{$vehicle->id}/availability?start_date={$date}&end_date={$date}");

        $response->assertOk()->assertJson(['available' => false]);
    }

    public function test_vehicle_availability_api_accepts_a_free_period(): void
    {
        $vehicle = Vehicle::create([
            'name' => 'Kia Carnival',
            'brand' => 'Kia',
            'model' => 'Carnival',
            'type' => 'MPV',
            'seats' => 7,
            'price' => 1200000,
            'status' => 'available',
        ]);
        $date = now()->addDays(3)->toDateString();

        $response = $this->getJson("/api/vehicles/{$vehicle->id}/availability?start_date={$date}&end_date={$date}");

        $response->assertOk()->assertJson(['available' => true]);
    }
}
