<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('homepage exposes exactly six available vehicles', function () {
    $this->seed();

    $this->get('/')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('vehicles', 6)
            ->where('vehicles.0.name', 'Toyota Vios')
            ->where('vehicles.1.name', 'Toyota Corolla Altis')
            ->where('vehicles.2.name', 'Toyota Innova')
            ->where('vehicles.3.name', 'Ford Everest')
            ->where('vehicles.4.name', 'Kia Carnival')
            ->where('vehicles.5.name', 'VinFast VF 9')
        );
});
