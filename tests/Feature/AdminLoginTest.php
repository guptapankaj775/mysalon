<?php

use App\Models\User;

test('super admin login page is accessible at /superadmin/login and /admin/login', function () {
    $response1 = $this->get('/superadmin/login');
    $response1->assertStatus(200);
    $response1->assertSee('System Admin Portal');

    $response2 = $this->get('/admin/login');
    $response2->assertStatus(200);
    $response2->assertSee('System Admin Portal');
});

test('super admin admin@salonjc.com can authenticate via /superadmin/login', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin@salonjc.com'],
        [
            'name' => 'Super Admin',
            'role' => 'admin',
            'is_verified' => true,
            'password' => bcrypt('password123'),
        ]
    );
    $admin->update([
        'is_verified' => true,
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/superadmin/login', [
        'email' => 'admin@salonjc.com',
        'password' => 'password123',
    ]);

    $this->assertAuthenticatedAs($admin);
    $response->assertRedirect('/dashboard');
});

test('non super admin user cannot authenticate via /superadmin/login', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'email' => 'regularuser@example.com',
        'is_verified' => true,
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/superadmin/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/superadmin/login');
    $response->assertSessionHasErrors(['email']);
});

test('super admin cannot log in via standard /login portal', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin@salonjc.com'],
        [
            'name' => 'Super Admin',
            'role' => 'admin',
            'is_verified' => true,
            'password' => bcrypt('password123'),
        ]
    );

    $response = $this->post('/login', [
        'email' => 'admin@salonjc.com',
        'password' => 'password123',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/superadmin/login');
    $response->assertSessionHasErrors(['email']);
});

test('super admin logout redirects to root domain /', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin@salonjc.com'],
        [
            'name' => 'Super Admin',
            'role' => 'admin',
            'is_verified' => true,
            'password' => bcrypt('password123'),
        ]
    );

    $response = $this->actingAs($admin)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
