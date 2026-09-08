<?php

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('homepage exposes six available vehicles and the requested section navigation', function () {
    $this->seed();

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Toyota Vios');
    $response->assertSee('Toyota Corolla Altis');
    $response->assertSee('Toyota Innova');
    $response->assertSee('Ford Everest');
    $response->assertSee('Kia Carnival');
    $response->assertSee('VinFast VF 9');

    expect(Vehicle::where('status', 'available')->count())->toBeGreaterThanOrEqual(6);
    $response->assertSee('href="#dich-vu"', false);
    $response->assertSee('href="#doi-xe"', false);
    $response->assertSee('href="#quy-trinh"', false);
    $response->assertSee('href="#blog"', false);
    $response->assertSee('href="#lien-he"', false);
});
