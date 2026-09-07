<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVehicleManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_view_vehicle_management(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/vehicles')
            ->assertOk();
    }

    public function test_admin_can_create_a_vehicle(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/vehicles', [
                'name' => 'Toyota Vios',
                'brand' => 'Toyota',
                'model' => 'Vios',
                'type' => 'Sedan',
                'seats' => 5,
                'description' => 'Xe 5 chỗ.',
                'image' => null,
                'price' => 800000,
                'status' => 'available',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('vehicles', [
            'name' => 'Toyota Vios',
            'seats' => 5,
            'status' => 'available',
        ]);
    }

    public function test_non_admin_cannot_manage_vehicles(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get('/admin/vehicles')
            ->assertForbidden();
    }
}
