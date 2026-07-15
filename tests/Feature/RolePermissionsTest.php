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

test('subdomain/merchant users with active plans can access roles & permissions settings', function () {
    $merchant = User::factory()->create(['role' => 'user']);
    
    $plan = \App\Models\SubscriptionPlan::firstOrCreate(
        ['slug' => 'premium-plan'],
        [
            'name' => 'Premium Plan',
            'price' => 10.00,
            'billing_cycle' => 'monthly',
            'is_trial' => false,
            'status' => true,
        ]
    );

    \App\Models\UserSubscription::create([
        'user_id' => $merchant->id,
        'plan_id' => $plan->id,
        'status' => 'active',
        'payment_status' => 'paid',
        'amount_paid' => 10.00,
        'starts_at' => now(),
        'expires_at' => now()->addMonth(),
    ]);

    $response = $this->actingAs($merchant)->get('/admin/roles-permissions');
    $response->assertOk();
    $response->assertViewHas('roles');
    $response->assertViewHas('permissions');
});

test('salon role permissions are isolated per tenant', function () {
    // Create Salon Owner A and Salon Owner B
    $ownerA = User::factory()->create(['role' => 'user']);
    $ownerB = User::factory()->create(['role' => 'user']);

    // Create staff for Salon A and Salon B
    $staffA = User::factory()->create(['role' => 'staff', 'created_by' => $ownerA->id]);
    $staffB = User::factory()->create(['role' => 'staff', 'created_by' => $ownerB->id]);

    // Clear mappings
    RolePermission::query()->delete();

    // Salon A configures staff role to have manage_inventory
    RolePermission::create([
        'role' => 'staff',
        'permission' => 'manage_inventory',
        'created_by' => $ownerA->id
    ]);

    // Salon B configures staff role to have manage_bookings, but NOT manage_inventory
    RolePermission::create([
        'role' => 'staff',
        'permission' => 'manage_bookings',
        'created_by' => $ownerB->id
    ]);

    // Assert Salon A staff has manage_inventory but not manage_bookings
    $this->assertTrue($staffA->hasPermission('manage_inventory'));
    $this->assertFalse($staffA->hasPermission('manage_bookings'));

    // Assert Salon B staff has manage_bookings but not manage_inventory
    $this->assertTrue($staffB->hasPermission('manage_bookings'));
    $this->assertFalse($staffB->hasPermission('manage_inventory'));
});

test('salon roles fallback to global default permissions when not customized', function () {
    // Create Salon Owner C and their staff
    $ownerC = User::factory()->create(['role' => 'user']);
    $staffC = User::factory()->create(['role' => 'staff', 'created_by' => $ownerC->id]);

    // Clear mappings
    RolePermission::query()->delete();

    // Setup global default permission for staff: manage_inventory (created_by is null)
    RolePermission::create([
        'role' => 'staff',
        'permission' => 'manage_inventory',
        'created_by' => null
    ]);

    // Staff C has not customized permissions, so they should fallback to global defaults
    $this->assertTrue($staffC->hasPermission('manage_inventory'));
    $this->assertFalse($staffC->hasPermission('manage_bookings'));
});


