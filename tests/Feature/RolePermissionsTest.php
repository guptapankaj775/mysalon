<?php

use App\Models\User;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Gate;

test('guests cannot access roles & permissions settings', function () {
    $response = $this->get('/admin/roles-permissions');
    $response->assertRedirect('/login');
});

test('regular users cannot access roles & permissions settings', function () {
    $user = User::factory()->create(['role' => 'user']);
    $response = $this->actingAs($user)->get('/admin/roles-permissions');
    $response->assertStatus(403);
});

test('staff users cannot access roles & permissions settings', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $response = $this->actingAs($staff)->get('/admin/roles-permissions');
    $response->assertStatus(403);
});

test('admins can access roles & permissions settings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get('/admin/roles-permissions');
    $response->assertOk();
    $response->assertViewHas('roles');
    $response->assertViewHas('permissions');
});

test('admins can update roles & permissions matrix', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/admin/roles-permissions', [
        'permissions' => [
            'staff' => [
                'manage_inventory' => '1',
                'manage_bookings' => '1',
            ],
            'user' => [
                'create_bookings' => '1',
            ]
        ]
    ]);

    $response->assertRedirect('/admin/roles-permissions');
    $response->assertSessionHas('success');

    // Verify it updated the database mapping
    $this->assertDatabaseHas('role_permissions', [
        'role' => 'staff',
        'permission' => 'manage_inventory',
    ]);
    $this->assertDatabaseHas('role_permissions', [
        'role' => 'staff',
        'permission' => 'manage_bookings',
    ]);
    $this->assertDatabaseMissing('role_permissions', [
        'role' => 'staff',
        'permission' => 'manage_users',
    ]);
});

test('gates authorize capabilities dynamically based on role permissions', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    
    // Clear mappings
    RolePermission::query()->delete();

    // Verify staff doesn't have inventory permission initially
    $this->assertFalse($staff->hasPermission('manage_inventory'));
    $this->assertFalse(Gate::forUser($staff)->allows('manage_inventory'));

    // Seed staff with inventory permission
    RolePermission::create([
        'role' => 'staff',
        'permission' => 'manage_inventory'
    ]);

    // Clear cache/instance resolver of gate for freshness
    $this->assertTrue($staff->hasPermission('manage_inventory'));
    $this->assertTrue(Gate::forUser($staff)->allows('manage_inventory'));
    $this->assertFalse(Gate::forUser($staff)->allows('manage_users'));
});
