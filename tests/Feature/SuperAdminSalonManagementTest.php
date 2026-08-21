<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::firstOrCreate(
        ['email' => 'admin@salonjc.com'],
        [
            'name' => 'Super Admin',
            'role' => 'admin',
            'is_verified' => true,
            'password' => bcrypt('password123'),
        ]
    );
});

test('super admin can view all salons and users list', function () {
    $salon = User::factory()->create([
        'salon_name' => 'Royal Touch Salon',
        'slug' => 'royal-touch-salon',
        'role' => 'user',
    ]);

    $response = $this->actingAs($this->superAdmin)->get(route('admin.users.index'));

    $response->assertStatus(200);
    $response->assertSee('Royal Touch Salon');
});

test('super admin can create a new salon with full details', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('admin.users.store'), [
        'name' => 'John Merchant',
        'email' => 'john.merchant@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'user',
        'salon_name' => 'Glow Beauty Studio',
        'slug' => 'glow-beauty-studio',
        'phone' => '+919988776655',
        'salon_type' => 'Beauty Parlour',
        'address' => '123 Beauty Lane',
        'city' => 'Mumbai',
        'state' => 'Maharashtra',
        'zip' => '400001',
        'is_verified' => 1,
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'john.merchant@example.com',
        'salon_name' => 'Glow Beauty Studio',
        'slug' => 'glow-beauty-studio',
        'phone' => '+919988776655',
    ]);
});

test('super admin can edit an existing salon', function () {
    $salon = User::factory()->create([
        'salon_name' => 'Old Salon Name',
        'slug' => 'old-salon-name',
        'role' => 'user',
    ]);

    $response = $this->actingAs($this->superAdmin)->put(route('admin.users.update', $salon->id), [
        'name' => $salon->name,
        'email' => $salon->email,
        'role' => 'user',
        'salon_name' => 'Updated Premium Salon',
        'slug' => 'updated-premium-salon',
        'phone' => '+911122334455',
        'salon_type' => 'Spa & Wellness',
        'is_verified' => 1,
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', [
        'id' => $salon->id,
        'salon_name' => 'Updated Premium Salon',
        'slug' => 'updated-premium-salon',
    ]);
});

test('super admin can toggle salon verification status', function () {
    $salon = User::factory()->create([
        'role' => 'user',
        'is_verified' => false,
    ]);

    $response = $this->actingAs($this->superAdmin)->patch(route('admin.users.toggle-verification', $salon->id));

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', [
        'id' => $salon->id,
        'is_verified' => true,
    ]);
});

test('super admin can delete a salon', function () {
    $salon = User::factory()->create([
        'salon_name' => 'Salon To Delete',
        'role' => 'user',
    ]);

    $response = $this->actingAs($this->superAdmin)->delete(route('admin.users.destroy', $salon->id));

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseMissing('users', [
        'id' => $salon->id,
    ]);
});
