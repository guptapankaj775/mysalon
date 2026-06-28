<?php

use App\Models\User;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SalesInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = ServiceCategory::create([
        'name' => 'Hair Care',
        'status' => true,
    ]);

    $this->service = Service::create([
        'name' => 'Haircut',
        'price' => 500.00,
        'category_id' => $this->category->id,
        'status' => true,
        'duration' => 30,
    ]);
});

test('guest users cannot access invoice page', function () {
    $booking = Booking::create([
        'full_name' => 'John Doe',
        'phone' => '1234567890',
        'email' => 'john@example.com',
        'service_category_id' => $this->category->id,
        'service_id' => $this->service->id,
        'appointment_date' => '2026-07-01',
        'appointment_time' => '10:00',
        'base_price' => 500.00,
        'addons_price' => 15.00,
        'total_price' => 515.00,
        'status' => 'pending',
        'payment_status' => 'paid',
    ]);

    $response = $this->get(route('booking.invoice', $booking->id));

    $response->assertRedirect(route('login'));
});

test('authorized users can view invoice for their paid booking', function () {
    $user = User::factory()->create();
    
    $booking = Booking::create([
        'user_id' => $user->id,
        'full_name' => 'John Doe',
        'phone' => '1234567890',
        'email' => 'john@example.com',
        'service_category_id' => $this->category->id,
        'service_id' => $this->service->id,
        'appointment_date' => '2026-07-01',
        'appointment_time' => '10:00',
        'base_price' => 500.00,
        'addons_price' => 15.00,
        'total_price' => 515.00,
        'status' => 'pending',
        'payment_status' => 'paid',
        'payment_method' => 'credit_card',
        'transaction_id' => 'TXN_TEST_123',
    ]);

    $response = $this->actingAs($user)->get(route('booking.invoice', $booking->id));

    $response->assertStatus(200);
    $response->assertSee('INVOICE');
    $response->assertSee('INV-BOOK-' . $booking->id);
    $response->assertSee('SRV-' . $booking->service_id);
    $response->assertSee('500.00');
    $response->assertSee('15.00');
    $response->assertSee('515.00');
});

test('unauthorized users cannot view invoice of another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $booking = Booking::create([
        'user_id' => $user1->id,
        'full_name' => 'John Doe',
        'phone' => '1234567890',
        'email' => 'john@example.com',
        'service_category_id' => $this->category->id,
        'service_id' => $this->service->id,
        'appointment_date' => '2026-07-01',
        'appointment_time' => '10:00',
        'base_price' => 500.00,
        'addons_price' => 15.00,
        'total_price' => 515.00,
        'status' => 'pending',
        'payment_status' => 'paid',
        'payment_method' => 'credit_card',
        'transaction_id' => 'TXN_TEST_123',
    ]);

    $response = $this->actingAs($user2)->get(route('booking.invoice', $booking->id));

    $response->assertStatus(403);
});

test('invoice cannot be viewed for unpaid bookings', function () {
    $user = User::factory()->create();

    $booking = Booking::create([
        'user_id' => $user->id,
        'full_name' => 'John Doe',
        'phone' => '1234567890',
        'email' => 'john@example.com',
        'service_category_id' => $this->category->id,
        'service_id' => $this->service->id,
        'appointment_date' => '2026-07-01',
        'appointment_time' => '10:00',
        'base_price' => 500.00,
        'addons_price' => 15.00,
        'total_price' => 515.00,
        'status' => 'pending',
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($user)->get(route('booking.invoice', $booking->id));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error');
});
