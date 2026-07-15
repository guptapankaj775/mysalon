<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\SalesInvoice;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiServiceBookingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    private function setupMerchantUser()
    {
        $merchant = User::factory()->create(['role' => 'user']);
        
        $plan = SubscriptionPlan::firstOrCreate(
            ['slug' => 'premium-plan'],
            [
                'name' => 'Premium Plan',
                'price' => 10.00,
                'billing_cycle' => 'monthly',
                'is_trial' => false,
                'status' => true,
            ]
        );

        UserSubscription::create([
            'user_id' => $merchant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'payment_status' => 'paid',
            'amount_paid' => 10.00,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        return $merchant;
    }

    public function test_salon_user_can_create_booking_with_multiple_services_and_staff_and_overlap_prevention_works()
    {
        $merchant = $this->setupMerchantUser();
        $staff1 = User::factory()->create(['role' => 'staff', 'created_by' => $merchant->id]);
        $staff2 = User::factory()->create(['role' => 'staff', 'created_by' => $merchant->id]);
        $customer = User::factory()->create(['role' => 'user', 'phone' => '9876543210']);

        $category = ServiceCategory::create(['name' => 'Haircut', 'user_id' => $merchant->id, 'status' => true]);
        $service1 = Service::create([
            'name' => 'Hair Cut',
            'category_id' => $category->id,
            'price' => 500,
            'duration' => 30,
            'user_id' => $merchant->id,
            'status' => true,
        ]);
        $service2 = Service::create([
            'name' => 'Hair Color',
            'category_id' => $category->id,
            'price' => 1200,
            'duration' => 60,
            'user_id' => $merchant->id,
            'status' => true,
        ]);

        // 1. Create a multi-service booking assigned to staff1
        $response = $this->actingAs($merchant)->post('/admin/bookings/store', [
            'customer_type' => 'existing',
            'customer_id' => $customer->id,
            'appointmentDate' => now()->addDay()->format('Y-m-d'),
            'appointmentTime' => '10:00',
            'staff_id' => $staff1->id,
            'services' => [
                ['service_id' => $service1->id],
                ['service_id' => $service2->id],
            ],
            'requirements' => 'Allergic to ammonia',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/bookings');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $customer->id,
            'base_price' => 1700,
            'total_price' => 1751,
            'status' => 'assigned',
            'stylist_id' => $staff1->id,
            'special_requirements' => 'Allergic to ammonia',
        ]);

        $booking = Booking::first();
        $this->assertCount(2, $booking->bookingServices);

        // 2. Test staff availability overlaps
        // Create another booking at 10:15 for staff1 -> should fail (since 10:00 session has duration 30 + 60 = 90 mins)
        $response = $this->actingAs($merchant)->post('/admin/bookings/store', [
            'customer_type' => 'existing',
            'customer_id' => $customer->id,
            'appointmentDate' => $booking->appointment_date,
            'appointmentTime' => '10:15',
            'staff_id' => $staff1->id,
            'services' => [
                ['service_id' => $service1->id],
            ],
        ]);

        $response->assertSessionHas('error');
        $response->assertStatus(302);
    }

    public function test_staff_can_view_assigned_work_and_execute_status_progression_to_auto_invoice_and_close()
    {
        $merchant = $this->setupMerchantUser();
        $staff1 = User::factory()->create(['role' => 'staff', 'created_by' => $merchant->id]);
        $customer = User::factory()->create(['role' => 'user', 'phone' => '9876543210']);

        $category = ServiceCategory::create(['name' => 'Haircut', 'user_id' => $merchant->id, 'status' => true]);
        $service1 = Service::create([
            'name' => 'Hair Cut',
            'category_id' => $category->id,
            'price' => 500,
            'duration' => 30,
            'user_id' => $merchant->id,
            'status' => true,
        ]);
        $service2 = Service::create([
            'name' => 'Hair Color',
            'category_id' => $category->id,
            'price' => 1200,
            'duration' => 60,
            'user_id' => $merchant->id,
            'status' => true,
        ]);

        $booking = Booking::create([
            'user_id' => $customer->id,
            'full_name' => $customer->name,
            'phone' => '9876543210',
            'email' => $customer->email,
            'service_category_id' => $category->id,
            'service_id' => $service1->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '14:00',
            'base_price' => 1700,
            'addons_price' => 51,
            'total_price' => 1751,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $bs1 = BookingService::create([
            'booking_id' => $booking->id,
            'service_id' => $service1->id,
            'staff_id' => $staff1->id,
            'status' => 'assigned',
        ]);

        $bs2 = BookingService::create([
            'booking_id' => $booking->id,
            'service_id' => $service2->id,
            'staff_id' => $staff1->id,
            'status' => 'assigned',
        ]);

        $booking->updateStatusFromServices();
        $this->assertEquals('assigned', $booking->fresh()->status);

        // 1. Staff 1 views dashboard - sees both assigned services
        $response = $this->actingAs($staff1)->get('/admin');
        $response->assertOk();
        $response->assertSee($service1->name);
        $response->assertSee($service2->name);

        // 2. Staff 1 starts all booking services
        $response = $this->actingAs($staff1)->post("/admin/bookings/{$booking->id}/services/start");
        $response->assertRedirect();
        $this->assertEquals('in_progress', $bs1->fresh()->status);
        $this->assertEquals('in_progress', $bs2->fresh()->status);
        $this->assertEquals('in_progress', $booking->fresh()->status);

        // 3. Staff 1 completes all booking services
        $response = $this->actingAs($staff1)->post("/admin/bookings/{$booking->id}/services/complete");
        $response->assertRedirect();
        $this->assertEquals('completed', $bs1->fresh()->status);
        $this->assertEquals('completed', $bs2->fresh()->status);

        $booking = $booking->fresh();
        $this->assertEquals('closed', $booking->status);
        $this->assertEquals('pending', $booking->payment_status);

        $this->assertDatabaseHas('sales_invoices', [
            'invoice_number' => 'INV-BOOK-' . $booking->id,
            'amount' => $booking->total_price,
            'status' => 'pending',
        ]);

        // 5. Admin updates payment status to paid
        $booking->update(['payment_status' => 'paid']);

        $this->assertDatabaseHas('sales_invoices', [
            'invoice_number' => 'INV-BOOK-' . $booking->id,
            'amount' => $booking->total_price,
            'status' => 'paid',
        ]);
    }

    public function test_available_slots_endpoint_returns_correct_time_slots()
    {
        $merchant = $this->setupMerchantUser();
        $staff1 = User::factory()->create(['role' => 'staff', 'created_by' => $merchant->id]);
        \App\Models\Specialist::create([
            'name' => $staff1->name,
            'email' => $staff1->email,
            'user_id' => $merchant->id,
            'status' => true,
        ]);
        $customer = User::factory()->create(['role' => 'user']);

        $category = ServiceCategory::create(['name' => 'Haircut', 'user_id' => $merchant->id, 'status' => true]);
        $service1 = Service::create([
            'name' => 'Hair Cut',
            'category_id' => $category->id,
            'price' => 500,
            'duration' => 30,
            'user_id' => $merchant->id,
            'status' => true,
        ]);

        // Get slots on tomorrow's date
        $date = now()->addDay()->format('Y-m-d');

        $response = $this->actingAs($merchant)->json('GET', '/admin/bookings/available-slots', [
            'date' => $date,
            'duration' => 60,
        ]);

        $response->assertOk();
        $response->assertSee('09:00');
        $response->assertSee('10:00');
        $response->assertSee('19:30');

        // Create a booking for staff1 at 10:00 for 60 minutes
        $booking = Booking::create([
            'user_id' => $customer->id,
            'full_name' => $customer->name,
            'phone' => '9876543210',
            'email' => $customer->email,
            'service_category_id' => $category->id,
            'service_id' => $service1->id,
            'appointment_date' => $date,
            'appointment_time' => '10:00',
            'base_price' => 500,
            'addons_price' => 15,
            'total_price' => 515,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        BookingService::create([
            'booking_id' => $booking->id,
            'service_id' => $service1->id,
            'staff_id' => $staff1->id,
            'status' => 'assigned',
        ]);

        $booking->updateStatusFromServices();

        // 10:00 slot should no longer be available for 60 minutes duration since staff1 is busy from 10:00 to 11:00 (60 mins booking)
        $response = $this->actingAs($merchant)->json('GET', '/admin/bookings/available-slots', [
            'date' => $date,
            'duration' => 60,
        ]);

        $response->assertOk();
        $response->assertDontSee('10:00');
        $response->assertSee('09:00');
        $response->assertSee('11:00');
    }
}
