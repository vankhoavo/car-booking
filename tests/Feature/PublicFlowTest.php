<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_the_public_tour_page_instead_of_redirecting_home(): void
    {
        $this->get('/tour')->assertSuccessful()->assertInertia(fn (Assert $page) => $page->component('tour/Index'));
    }

    public function test_carries_booking_search_parameters_into_the_booking_page(): void
    {
        $vehicle = Vehicle::create(['name' => 'Test Vehicle', 'brand' => 'Test', 'model' => 'One', 'type' => 'Sedan', 'seats' => 4, 'price' => 500000, 'status' => 'available']);
        $this->get('/dat-xe?pickup=Da%20Nang&destination=Hoi%20An&date=2026-09-20&time=10%3A30&passengers=3')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('booking/Index')->where('initial.pickup_location', 'Da Nang')->where('initial.destination', 'Hoi An')->where('initial.travel_date', '2026-09-20')->where('initial.pickup_time', '10:30')->where('initial.passengers', 3)->has('vehicles', 1)->where('vehicles.0.id', $vehicle->id));
    }

    public function test_carries_rental_search_parameters_and_duration_into_the_rental_page(): void
    {
        $vehicle = Vehicle::create(['name' => 'Test Rental Vehicle', 'brand' => 'Test', 'model' => 'Two', 'type' => 'MPV', 'seats' => 7, 'price' => 800000, 'status' => 'available']);
        $this->get('/thue-xe?pickup=Da%20Nang&destination=Hue&date=2026-09-20&days=3&passengers=5')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page->component('rental/Index')->where('initial.pickup_location', 'Da Nang')->where('initial.return_location', 'Hue')->where('initial.start_date', '2026-09-20')->where('initial.end_date', '2026-09-22')->where('initial.passengers', 5)->has('vehicles', 1)->where('vehicles.0.id', $vehicle->id));
    }
}
