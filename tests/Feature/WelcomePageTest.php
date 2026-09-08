<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_exposes_exactly_six_available_vehicles(): void
    {
        $this->seed();

        $this->get('/')
            ->assertSuccessful()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Welcome')
                ->has('vehicles', 6)
                ->where('vehicles.0.name', 'Toyota Corolla Altis')
                ->where('vehicles.1.name', 'Toyota Vios')
                ->where('vehicles.2.name', 'Toyota Innova')
                ->where('vehicles.3.name', 'Ford Everest')
                ->where('vehicles.4.name', 'Kia Carnival')
                ->where('vehicles.5.name', 'VinFast VF 9')
            );
    }
}
