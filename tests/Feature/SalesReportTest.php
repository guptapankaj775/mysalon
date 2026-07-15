<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\RolePermission;
use App\Models\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    private function setupMerchant(array $permissions = [])
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

        foreach ($permissions as $perm) {
            RolePermission::firstOrCreate([
                'role' => 'user',
                'permission' => $perm,
            ]);
        }

        return $merchant;
    }

    public function test_guests_cannot_access_sales_reports()
    {
        $response = $this->get(route('admin.reports.sales'));
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_staff_cannot_access_sales_reports()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('admin.reports.sales'));
        $response->assertStatus(403);
    }

    public function test_merchant_with_active_plan_can_access_sales_reports()
    {
        $merchant = $this->setupMerchant(['view_sales_reports']);

        $response = $this->actingAs($merchant)->get(route('admin.reports.sales'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.sales');
    }

    public function test_sales_report_scopes_bookings_correctly_to_salon()
    {
        $merchant1 = $this->setupMerchant(['view_sales_reports']);
        $merchant2 = $this->setupMerchant(['view_sales_reports']);

        // Create category and service for merchant 1
        $cat1 = ServiceCategory::create(['name' => 'Hair', 'user_id' => $merchant1->id]);
        $service1 = Service::create([
            'name' => 'Cut 1',
            'category_id' => $cat1->id,
            'user_id' => $merchant1->id,
            'price' => 500,
            'duration' => 30
        ]);

        // Create category and service for merchant 2
        $cat2 = ServiceCategory::create(['name' => 'Nails', 'user_id' => $merchant2->id]);
        $service2 = Service::create([
            'name' => 'Cut 2',
            'category_id' => $cat2->id,
            'user_id' => $merchant2->id,
            'price' => 600,
            'duration' => 30
        ]);

        // Create bookings
        $booking1 = Booking::create([
            'full_name' => 'Customer A',
            'phone' => '1234567890',
            'email' => 'a@test.com',
            'service_category_id' => $cat1->id,
            'service_id' => $service1->id,
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:00:00',
            'stylist_id' => $merchant1->id,
            'base_price' => 500,
            'addons_price' => 0,
            'total_price' => 500,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $booking2 = Booking::create([
            'full_name' => 'Customer B',
            'phone' => '1234567890',
            'email' => 'b@test.com',
            'service_category_id' => $cat2->id,
            'service_id' => $service2->id,
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '11:00:00',
            'stylist_id' => $merchant2->id,
            'base_price' => 600,
            'addons_price' => 0,
            'total_price' => 600,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        // Merchant 1 should only see Booking 1
        $response1 = $this->actingAs($merchant1)->get(route('admin.reports.sales'));
        $response1->assertStatus(200);
        $response1->assertSee('Customer A');
        $response1->assertDontSee('Customer B');

        // Merchant 2 should only see Booking 2
        $response2 = $this->actingAs($merchant2)->get(route('admin.reports.sales'));
        $response2->assertStatus(200);
        $response2->assertSee('Customer B');
        $response2->assertDontSee('Customer A');
    }

    public function test_sales_report_date_staff_and_service_filters_work()
    {
        $merchant = $this->setupMerchant(['view_sales_reports']);

        $cat = ServiceCategory::create(['name' => 'Salon Services', 'user_id' => $merchant->id]);
        
        $serviceA = Service::create([
            'name' => 'Hair Cut',
            'category_id' => $cat->id,
            'user_id' => $merchant->id,
            'price' => 500,
            'duration' => 30
        ]);

        $serviceB = Service::create([
            'name' => 'Facial',
            'category_id' => $cat->id,
            'user_id' => $merchant->id,
            'price' => 1000,
            'duration' => 45
        ]);

        $staff1 = User::factory()->create(['role' => 'staff', 'created_by' => $merchant->id]);
        $staff2 = User::factory()->create(['role' => 'staff', 'created_by' => $merchant->id]);

        // Booking 1: Hair Cut by Staff 1, Today
        $booking1 = Booking::create([
            'full_name' => 'John Doe',
            'phone' => '1234567890',
            'email' => 'john@test.com',
            'service_category_id' => $cat->id,
            'service_id' => $serviceA->id,
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:00:00',
            'stylist_id' => $staff1->id,
            'base_price' => 500,
            'addons_price' => 0,
            'total_price' => 500,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        // Booking 2: Facial by Staff 2, Yesterday
        $booking2 = Booking::create([
            'full_name' => 'Jane Smith',
            'phone' => '0987654321',
            'email' => 'jane@test.com',
            'service_category_id' => $cat->id,
            'service_id' => $serviceB->id,
            'appointment_date' => now()->subDay()->format('Y-m-d'),
            'appointment_time' => '14:00:00',
            'stylist_id' => $staff2->id,
            'base_price' => 1000,
            'addons_price' => 0,
            'total_price' => 1000,
            'status' => 'closed',
            'payment_status' => 'paid',
        ]);

        // 1. Filter by Staff 1
        $response = $this->actingAs($merchant)->get(route('admin.reports.sales', ['staff_id' => $staff1->id]));
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');

        // 2. Filter by Service B (Facial)
        $response = $this->actingAs($merchant)->get(route('admin.reports.sales', ['service_id' => $serviceB->id]));
        $response->assertSee('Jane Smith');
        $response->assertDontSee('John Doe');

        // 3. Filter by Date range (Today only)
        $response = $this->actingAs($merchant)->get(route('admin.reports.sales', [
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }
}
