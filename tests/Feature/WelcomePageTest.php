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
                ->has('vehicles', fn (Assert $vehicles) => $vehicles
                    ->where('*.name', fn (array $names): bool => $names === [
                        'Toyota Corolla Altis',
                        'Toyota Vios',
                        'Toyota Innova',
                        'Ford Everest',
                        'Kia Carnival',
                        'VinFast VF 9',
                    ])
                )
            );
    }
}
