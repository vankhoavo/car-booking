<?php

use App\Models\Vehicle;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the public tour page instead of redirecting home', function () {
    $this->get('/tour')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page->component('tour/Index'));
});

it('carries booking search parameters into the booking page', function () {
    $vehicle = Vehicle::factory()->create(['status' => 'available', 'seats' => 4]);

    $this->get('/dat-xe?pickup=Da%20Nang&destination=Hoi%20An&date=2026-09-20&time=10%3A30&passengers=3')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('booking/Index')
            ->where('initial.pickup_location', 'Da Nang')
            ->where('initial.destination', 'Hoi An')
            ->where('initial.travel_date', '2026-09-20')
            ->where('initial.pickup_time', '10:30')
            ->where('initial.passengers', 3)
            ->has('vehicles', 1)
            ->where('vehicles.0.id', $vehicle->id));
});

it('carries rental search parameters and duration into the rental page', function () {
    $vehicle = Vehicle::factory()->create(['status' => 'available', 'seats' => 7]);

    $this->get('/thue-xe?pickup=Da%20Nang&destination=Hue&date=2026-09-20&days=3&passengers=5')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('rental/Index')
            ->where('initial.pickup_location', 'Da Nang')
            ->where('initial.return_location', 'Hue')
            ->where('initial.start_date', '2026-09-20')
            ->where('initial.end_date', '2026-09-22')
            ->where('initial.passengers', 5)
            ->has('vehicles', 1)
            ->where('vehicles.0.id', $vehicle->id));
});
