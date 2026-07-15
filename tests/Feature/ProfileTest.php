<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'location' => 'New York, USA',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'salon_name' => 'Test Salon Name',
            'salon_type' => 'Hair Studio',
            'salon_model' => 'Franchisee',
            'franchisee_name' => 'Tony & Guy',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertSame('New York, USA', $user->location);
    $this->assertEquals(40.7128, $user->latitude);
    $this->assertEquals(-74.0060, $user->longitude);
    $this->assertSame('Test Salon Name', $user->salon_name);
    $this->assertSame('Hair Studio', $user->salon_type);
    $this->assertSame('Franchisee', $user->salon_model);
    $this->assertSame('Tony & Guy', $user->franchisee_name);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
            'salon_name' => 'Test Salon',
            'salon_type' => 'Hair Studio',
            'salon_model' => 'Self Owned',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('password can be updated optionally during profile update', function () {
    $user = User::factory()->create([
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
            'salon_name' => 'Test Salon',
            'salon_type' => 'Hair Studio',
            'salon_model' => 'Self Owned',
            'current_password' => 'password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-password123', $user->refresh()->password));
});

test('admin users cannot access profile settings', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/profile');
    $response->assertStatus(403);

    $response = $this->actingAs($admin)->patch('/profile', [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'salon_name' => 'Admin Salon',
        'salon_type' => 'Hair Studio',
        'salon_model' => 'Self Owned',
    ]);
    $response->assertStatus(403);
});

