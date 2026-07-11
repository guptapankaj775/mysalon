<?php

use App\Models\User;
use App\Models\Brand;
use App\Models\Vendor;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\InventoryCategory;
use App\Models\Inventory;
use App\Models\Specialist;
use App\Models\Booking;
use App\Models\RolePermission;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;

function setupMerchantUser(array $permissions = []) {
    // 1. Create a merchant (role = user)
    $merchant = User::factory()->create(['role' => 'user']);

    // 2. Create or find subscription plan
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

    // 3. Create active subscription
    UserSubscription::create([
        'user_id' => $merchant->id,
        'plan_id' => $plan->id,
        'status' => 'active',
        'payment_status' => 'paid',
        'amount_paid' => 10.00,
        'starts_at' => now(),
        'expires_at' => now()->addMonth(),
    ]);

    // 4. Map required permissions
    foreach ($permissions as $permission) {
        RolePermission::firstOrCreate([
            'role' => 'user',
            'permission' => $permission,
        ]);
    }

    return $merchant;
}

test('merchant can only list and manage their own brands', function () {
    $merchantA = setupMerchantUser(['manage_inventory']);
    $merchantB = setupMerchantUser(['manage_inventory']);

    // Create brands
    $brandA = Brand::create(['name' => 'Brand Merchant A', 'user_id' => $merchantA->id, 'status' => true]);
    $brandB = Brand::create(['name' => 'Brand Merchant B', 'user_id' => $merchantB->id, 'status' => true]);

    // Merchant A listing brands
    $response = $this->actingAs($merchantA)->get('/admin/brands');
    $response->assertOk();
    $response->assertSee('Brand Merchant A');
    $response->assertDontSee('Brand Merchant B');

    // Merchant A cannot edit Brand B
    $response = $this->actingAs($merchantA)->get("/admin/brands/{$brandB->id}/edit");
    $response->assertStatus(403);

    // Merchant A cannot update Brand B
    $response = $this->actingAs($merchantA)->put("/admin/brands/{$brandB->id}", [
        'name' => 'Attempted Update',
        'status' => '1',
    ]);
    $response->assertStatus(403);

    // Merchant A cannot delete Brand B
    $response = $this->actingAs($merchantA)->delete("/admin/brands/{$brandB->id}");
    $response->assertStatus(403);
});

test('merchant can only list and manage their own vendors', function () {
    $merchantA = setupMerchantUser(['manage_vendors']);
    $merchantB = setupMerchantUser(['manage_vendors']);

    // Create vendors
    $vendorA = Vendor::create(['name' => 'Vendor Merchant A', 'user_id' => $merchantA->id, 'status' => true]);
    $vendorB = Vendor::create(['name' => 'Vendor Merchant B', 'user_id' => $merchantB->id, 'status' => true]);

    // Merchant A listing vendors
    $response = $this->actingAs($merchantA)->get('/admin/vendors');
    $response->assertOk();
    $response->assertSee('Vendor Merchant A');
    $response->assertDontSee('Vendor Merchant B');

    // Merchant A cannot edit Vendor B
    $response = $this->actingAs($merchantA)->get("/admin/vendors/{$vendorB->id}/edit");
    $response->assertStatus(403);

    // Merchant A cannot update Vendor B
    $response = $this->actingAs($merchantA)->put("/admin/vendors/{$vendorB->id}", [
        'name' => 'Attempted Vendor Update',
        'status' => '1',
    ]);
    $response->assertStatus(403);

    // Merchant A cannot delete Vendor B
    $response = $this->actingAs($merchantA)->delete("/admin/vendors/{$vendorB->id}");
    $response->assertStatus(403);
});

test('merchant can only list and manage their own service categories', function () {
    $merchantA = setupMerchantUser(['manage_services']);
    $merchantB = setupMerchantUser(['manage_services']);

    // Create service categories
    $catA = ServiceCategory::create(['name' => 'Cat Merchant A', 'description' => 'Desc', 'user_id' => $merchantA->id, 'status' => true]);
    $catB = ServiceCategory::create(['name' => 'Cat Merchant B', 'description' => 'Desc', 'user_id' => $merchantB->id, 'status' => true]);

    // Merchant A listing categories
    $response = $this->actingAs($merchantA)->get('/admin/categories');
    $response->assertOk();
    $response->assertSee('Cat Merchant A');
    $response->assertDontSee('Cat Merchant B');

    // Merchant A cannot edit Cat B
    $response = $this->actingAs($merchantA)->get("/admin/categories/{$catB->id}/edit");
    $response->assertStatus(403);

    // Merchant A cannot update Cat B
    $response = $this->actingAs($merchantA)->put("/admin/categories/{$catB->id}", [
        'name' => 'Attempted Cat Update',
        'description' => 'New Desc',
        'status' => '1',
    ]);
    $response->assertStatus(403);

    // Merchant A cannot delete Cat B
    $response = $this->actingAs($merchantA)->delete("/admin/categories/{$catB->id}");
    $response->assertStatus(403);
});

test('merchant can only list and manage their own services', function () {
    $merchantA = setupMerchantUser(['manage_services']);
    $merchantB = setupMerchantUser(['manage_services']);

    $catA = ServiceCategory::create(['name' => 'Cat A', 'description' => 'D', 'user_id' => $merchantA->id]);
    $catB = ServiceCategory::create(['name' => 'Cat B', 'description' => 'D', 'user_id' => $merchantB->id]);

    // Create services
    $serviceA = Service::create([
        'name' => 'Service Merchant A',
        'description' => 'Desc',
        'price' => 50,
        'duration' => 30,
        'category_id' => $catA->id,
        'user_id' => $merchantA->id,
        'status' => true
    ]);
    $serviceA->icon()->create(['image_path' => 'icon.svg']);

    $serviceB = Service::create([
        'name' => 'Service Merchant B',
        'description' => 'Desc',
        'price' => 50,
        'duration' => 30,
        'category_id' => $catB->id,
        'user_id' => $merchantB->id,
        'status' => true
    ]);
    $serviceB->icon()->create(['image_path' => 'icon.svg']);

    // Merchant A listing services
    $response = $this->actingAs($merchantA)->get('/admin/services');
    $response->assertOk();
    $response->assertSee('Service Merchant A');
    $response->assertDontSee('Service Merchant B');

    // Merchant A cannot edit Service B
    $response = $this->actingAs($merchantA)->get("/admin/services/{$serviceB->id}/edit");
    $response->assertStatus(403);

    // Merchant A cannot delete Service B
    $response = $this->actingAs($merchantA)->delete("/admin/services/{$serviceB->id}");
    $response->assertStatus(403);
});

test('merchant can only list and manage their own inventory items', function () {
    $merchantA = setupMerchantUser(['manage_inventory']);
    $merchantB = setupMerchantUser(['manage_inventory']);

    // Create inventories
    $invA = Inventory::create([
        'item_name' => 'Inventory Merchant A',
        'user_id' => $merchantA->id,
        'price' => 10,
        'quantity' => 50,
        'min_quantity' => 5,
        'status' => true
    ]);
    $invB = Inventory::create([
        'item_name' => 'Inventory Merchant B',
        'user_id' => $merchantB->id,
        'price' => 10,
        'quantity' => 50,
        'min_quantity' => 5,
        'status' => true
    ]);

    // Merchant A listing inventory
    $response = $this->actingAs($merchantA)->get('/admin/inventory');
    $response->assertOk();
    $response->assertSee('Inventory Merchant A');
    $response->assertDontSee('Inventory Merchant B');

    // Merchant A cannot edit Inv B
    $response = $this->actingAs($merchantA)->get("/admin/inventory/{$invB->id}/edit");
    $response->assertStatus(403);

    // Merchant A cannot delete Inv B
    $response = $this->actingAs($merchantA)->delete("/admin/inventory/{$invB->id}");
    $response->assertStatus(403);
});

test('merchant can only list and manage their own staff', function () {
    $merchantA = setupMerchantUser(['manage_staff']);
    $merchantB = setupMerchantUser(['manage_staff']);

    // Create staff
    $staffA = Specialist::create(['name' => 'Staff Merchant A', 'user_id' => $merchantA->id, 'status' => true]);
    $staffB = Specialist::create(['name' => 'Staff Merchant B', 'user_id' => $merchantB->id, 'status' => true]);

    // Merchant A listing staff
    $response = $this->actingAs($merchantA)->get('/admin/staff');
    $response->assertOk();
    $response->assertSee('Staff Merchant A');
    $response->assertDontSee('Staff Merchant B');

    // Merchant A cannot edit Staff B
    $response = $this->actingAs($merchantA)->get("/admin/staff/{$staffB->id}/edit");
    $response->assertStatus(403);

    // Merchant A cannot delete Staff B
    $response = $this->actingAs($merchantA)->delete("/admin/staff/{$staffB->id}");
    $response->assertStatus(403);
});

test('merchant can only list and manage their own users', function () {
    $merchantA = setupMerchantUser(['manage_users']);
    $merchantB = setupMerchantUser(['manage_users']);

    // Create users
    $userA = User::factory()->create(['name' => 'User Merchant A', 'created_by' => $merchantA->id]);
    $userB = User::factory()->create(['name' => 'User Merchant B', 'created_by' => $merchantB->id]);

    // Merchant A listing users
    $response = $this->actingAs($merchantA)->get('/admin/users');
    $response->assertOk();
    $response->assertSee('User Merchant A');
    $response->assertDontSee('User Merchant B');

    // Merchant A cannot edit User B
    $response = $this->actingAs($merchantA)->get("/admin/users/{$userB->id}/edit");
    $response->assertStatus(403);

    // Merchant A cannot delete User B
    $response = $this->actingAs($merchantA)->delete("/admin/users/{$userB->id}");
    $response->assertStatus(403);
});

test('merchant can only list and manage their own bookings', function () {
    $merchantA = setupMerchantUser(['manage_bookings']);
    $merchantB = setupMerchantUser(['manage_bookings']);

    $catA = ServiceCategory::create(['name' => 'Cat A', 'description' => 'D', 'user_id' => $merchantA->id]);
    $catB = ServiceCategory::create(['name' => 'Cat B', 'description' => 'D', 'user_id' => $merchantB->id]);

    $serviceA = Service::create([
        'name' => 'Service A',
        'description' => 'Desc',
        'price' => 50,
        'duration' => 30,
        'category_id' => $catA->id,
        'user_id' => $merchantA->id,
        'status' => true
    ]);
    $serviceB = Service::create([
        'name' => 'Service B',
        'description' => 'Desc',
        'price' => 50,
        'duration' => 30,
        'category_id' => $catB->id,
        'user_id' => $merchantB->id,
        'status' => true
    ]);

    // Create bookings
    $bookingA = Booking::create([
        'full_name' => 'Booking A',
        'email' => 'a@test.com',
        'phone' => '1234567890',
        'appointment_date' => today(),
        'appointment_time' => '10:00:00',
        'service_id' => $serviceA->id,
        'service_category_id' => $catA->id,
        'base_price' => 50,
        'total_price' => 50,
        'payment_status' => 'pending',
        'status' => 'pending'
    ]);

    $bookingB = Booking::create([
        'full_name' => 'Booking B',
        'email' => 'b@test.com',
        'phone' => '1234567890',
        'appointment_date' => today(),
        'appointment_time' => '10:00:00',
        'service_id' => $serviceB->id,
        'service_category_id' => $catB->id,
        'base_price' => 50,
        'total_price' => 50,
        'payment_status' => 'pending',
        'status' => 'pending'
    ]);

    // Merchant A listing bookings
    $response = $this->actingAs($merchantA)->get('/admin/bookings');
    $response->assertOk();
    $response->assertSee('Booking A');
    $response->assertDontSee('Booking B');

    // Merchant A cannot view Booking B
    $response = $this->actingAs($merchantA)->get("/admin/bookings/{$bookingB->id}");
    $response->assertStatus(403);

    // Merchant A cannot complete Booking B
    $response = $this->actingAs($merchantA)->post("/admin/bookings/{$bookingB->id}/complete");
    $response->assertStatus(403);
});

test('admin role user can see all data across all merchants', function () {
    // Create admin user
    $admin = User::factory()->create(['role' => 'admin']);

    $merchantA = setupMerchantUser(['manage_services', 'manage_inventory', 'manage_bookings', 'manage_vendors']);
    $merchantB = setupMerchantUser(['manage_services', 'manage_inventory', 'manage_bookings', 'manage_vendors']);

    // Create brands
    $brandA = Brand::create(['name' => 'Brand Merchant A', 'user_id' => $merchantA->id, 'status' => true]);
    $brandB = Brand::create(['name' => 'Brand Merchant B', 'user_id' => $merchantB->id, 'status' => true]);

    // Create vendors
    $vendorA = Vendor::create(['name' => 'Vendor Merchant A', 'user_id' => $merchantA->id, 'status' => true]);
    $vendorB = Vendor::create(['name' => 'Vendor Merchant B', 'user_id' => $merchantB->id, 'status' => true]);

    // Create categories
    $catA = ServiceCategory::create(['name' => 'Cat Merchant A', 'description' => 'Desc', 'user_id' => $merchantA->id, 'status' => true]);
    $catB = ServiceCategory::create(['name' => 'Cat Merchant B', 'description' => 'Desc', 'user_id' => $merchantB->id, 'status' => true]);

    // Create services
    $serviceA = Service::create([
        'name' => 'Service Merchant A',
        'description' => 'Desc',
        'price' => 50,
        'duration' => 30,
        'category_id' => $catA->id,
        'user_id' => $merchantA->id,
        'status' => true
    ]);
    $serviceA->icon()->create(['image_path' => 'icon.svg']);

    $serviceB = Service::create([
        'name' => 'Service Merchant B',
        'description' => 'Desc',
        'price' => 50,
        'duration' => 30,
        'category_id' => $catB->id,
        'user_id' => $merchantB->id,
        'status' => true
    ]);
    $serviceB->icon()->create(['image_path' => 'icon.svg']);

    // Create bookings
    $bookingA = Booking::create([
        'full_name' => 'Booking A',
        'email' => 'a@test.com',
        'phone' => '1234567890',
        'appointment_date' => today(),
        'appointment_time' => '10:00:00',
        'service_id' => $serviceA->id,
        'service_category_id' => $catA->id,
        'base_price' => 50,
        'total_price' => 50,
        'payment_status' => 'pending',
        'status' => 'pending'
    ]);

    $bookingB = Booking::create([
        'full_name' => 'Booking B',
        'email' => 'b@test.com',
        'phone' => '1234567890',
        'appointment_date' => today(),
        'appointment_time' => '10:00:00',
        'service_id' => $serviceB->id,
        'service_category_id' => $catB->id,
        'base_price' => 50,
        'total_price' => 50,
        'payment_status' => 'pending',
        'status' => 'pending'
    ]);

    // Admin should see both brands
    $response = $this->actingAs($admin)->get('/admin/brands');
    $response->assertOk();
    $response->assertSee('Brand Merchant A');
    $response->assertSee('Brand Merchant B');

    // Admin should see both vendors
    $response = $this->actingAs($admin)->get('/admin/vendors');
    $response->assertOk();
    $response->assertSee('Vendor Merchant A');
    $response->assertSee('Vendor Merchant B');

    // Admin should see both service categories
    $response = $this->actingAs($admin)->get('/admin/categories');
    $response->assertOk();
    $response->assertSee('Cat Merchant A');
    $response->assertSee('Cat Merchant B');

    // Admin should see both services
    $response = $this->actingAs($admin)->get('/admin/services');
    $response->assertOk();
    $response->assertSee('Service Merchant A');
    $response->assertSee('Service Merchant B');

    // Admin should see both bookings
    $response = $this->actingAs($admin)->get('/admin/bookings');
    $response->assertOk();
    $response->assertSee('Booking A');
    $response->assertSee('Booking B');
});

test('merchant can quick-create a service category via AJAX', function () {
    $merchant = setupMerchantUser(['manage_services']);

    $response = $this->actingAs($merchant)
        ->postJson('/admin/categories', [
            'name' => 'AJAX Quick Category',
            'description' => 'Created via AJAX modal',
            'status' => true
        ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'category' => [
            'name' => 'AJAX Quick Category',
            'description' => 'Created via AJAX modal',
            'user_id' => $merchant->id
        ],
        'message' => 'Category created successfully'
    ]);

    $this->assertDatabaseHas('service_categories', [
        'name' => 'AJAX Quick Category',
        'user_id' => $merchant->id
    ]);
});

test('merchant can access and store bookings under the admin layout', function () {
    $merchant = setupMerchantUser(['manage_bookings', 'create_bookings']);
    
    $plan = \App\Models\SubscriptionPlan::firstOrCreate(
        ['slug' => 'premium-plan'],
        ['name' => 'Premium Plan', 'price' => 10.00, 'billing_cycle' => 'monthly', 'is_trial' => false, 'status' => true]
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

    $category = ServiceCategory::create(['name' => 'Haircut', 'user_id' => $merchant->id, 'status' => true]);
    $service = Service::create(['name' => 'Premium Cut', 'category_id' => $category->id, 'price' => 500.00, 'user_id' => $merchant->id, 'status' => true, 'duration' => 30]);

    // 1. Render services selection page
    $response = $this->actingAs($merchant)->get("/admin/services/book");
    $response->assertOk();
    $response->assertSee('Select Service to Book');
    $response->assertSee('Premium Cut');

    // 2. Render create form with service parameter
    $response = $this->actingAs($merchant)->get("/admin/bookings/create?service=" . $service->id);
    $response->assertOk();
    $response->assertSee('Book New Appointment');

    // 3. Submit booking form
    $response = $this->actingAs($merchant)->post("/admin/bookings/store", [
        'fullName' => 'Test Portal Customer',
        'phone' => '1234567890',
        'email' => 'customer@test.com',
        'serviceCategory' => $category->id,
        'service' => $service->id,
        'appointmentDate' => now()->addDay()->format('Y-m-d'),
        'appointmentTime' => '10:00',
    ]);

    $response->assertRedirect("/admin/bookings");

    $this->assertDatabaseHas('bookings', [
        'full_name' => 'Test Portal Customer',
        'phone' => '1234567890',
        'email' => 'customer@test.com',
        'service_id' => $service->id,
        'status' => 'confirmed',
    ]);
});
