<?php

namespace Tests\Feature;

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
            );

        $this->assertDatabaseCount('vehicles', 6);

        foreach ([
            'Toyota Vios',
            'Toyota Corolla Altis',
            'Toyota Innova',
            'Ford Everest',
            'Kia Carnival',
            'VinFast VF 9',
        ] as $name) {
            $this->assertDatabaseHas('vehicles', [
                'name' => $name,
                'status' => 'available',
            ]);
        }
    }
}
