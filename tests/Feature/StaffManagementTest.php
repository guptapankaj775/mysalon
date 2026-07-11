<?php

use App\Models\User;
use App\Models\Specialist;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests cannot access staff management', function () {
    $response = $this->get('/admin/staff');
    $response->assertRedirect('/login');
});

test('regular users cannot access staff management', function () {
    $user = User::factory()->create(['role' => 'user']);
    $response = $this->actingAs($user)->get('/admin/staff');
    $response->assertStatus(403);
});

test('admins can list staff', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get('/admin/staff');
    $response->assertOk();
});

test('admins can create staff and map services', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    
    // Create a service first
    $category = ServiceCategory::create(['name' => 'Haircut', 'status' => true]);
    $service = Service::create([
        'name' => 'Men Haircut',
        'description' => 'Test desc',
        'duration' => 30,
        'price' => 500,
        'category_id' => $category->id,
        'status' => true
    ]);

    $file = UploadedFile::fake()->create('staff.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($admin)->post('/admin/staff', [
        'name' => 'John Doe',
        'bio' => 'Experienced barber',
        'image' => $file,
        'status' => 1,
        'services' => [$service->id],
        'mobile_no' => '9876543210',
        'email' => 'john.doe@example.com',
        'job_category' => ['Hair', 'Makeup'],
        'home_address' => '123 Main St, Salon City',
        'religion' => 'Christianity',
    ]);

    $response->assertRedirect('/admin/staff');

    $specialist = Specialist::where('name', 'John Doe')->first();
    $this->assertNotNull($specialist);
    $this->assertSame('Experienced barber', $specialist->bio);
    $this->assertTrue((bool)$specialist->status);
    $this->assertNotNull($specialist->image_path);
    Storage::disk('public')->assertExists($specialist->image_path);
    $this->assertSame('9876543210', $specialist->mobile_no);
    $this->assertSame('john.doe@example.com', $specialist->email);
    $this->assertEquals(['Hair', 'Makeup'], $specialist->job_category);
    $this->assertSame('123 Main St, Salon City', $specialist->home_address);
    $this->assertSame('Christianity', $specialist->religion);

    // Verify services mapping
    $this->assertTrue($specialist->services->contains($service->id));
});

test('admins can update staff details and services', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    
    $specialist = Specialist::create([
        'name' => 'Jane Smith',
        'bio' => 'Old bio',
        'status' => true,
        'mobile_no' => '1234567890',
        'email' => 'jane.smith@example.com',
        'job_category' => ['Makeup'],
        'home_address' => 'Old Address',
        'religion' => 'None',
    ]);

    $category = ServiceCategory::create(['name' => 'Haircut', 'status' => true]);
    $service = Service::create([
        'name' => 'Women Haircut',
        'description' => 'Test desc',
        'duration' => 30,
        'price' => 600,
        'category_id' => $category->id,
        'status' => true
    ]);

    $response = $this->actingAs($admin)->put("/admin/staff/{$specialist->id}", [
        'name' => 'Jane Doe',
        'bio' => 'New bio',
        'status' => 1,
        'services' => [$service->id],
        'mobile_no' => '5555555555',
        'email' => 'jane.doe@example.com',
        'job_category' => ['Pedicure', 'Nail Art'],
        'home_address' => 'New Address',
        'religion' => 'Buddhism',
    ]);

    $response->assertRedirect('/admin/staff');

    $specialist->refresh();
    $this->assertSame('Jane Doe', $specialist->name);
    $this->assertSame('New bio', $specialist->bio);
    $this->assertSame('5555555555', $specialist->mobile_no);
    $this->assertSame('jane.doe@example.com', $specialist->email);
    $this->assertEquals(['Pedicure', 'Nail Art'], $specialist->job_category);
    $this->assertSame('New Address', $specialist->home_address);
    $this->assertSame('Buddhism', $specialist->religion);
    $this->assertTrue($specialist->services->contains($service->id));
});

test('admins can delete staff', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    
    $file = UploadedFile::fake()->create('staff.jpg', 100, 'image/jpeg');
    $path = Storage::disk('public')->put('specialists', $file);

    $specialist = Specialist::create([
        'name' => 'To Delete',
        'bio' => 'Will be deleted',
        'image_path' => $path,
        'status' => true
    ]);

    $response = $this->actingAs($admin)->delete("/admin/staff/{$specialist->id}");
    $response->assertRedirect('/admin/staff');

    $this->assertNull(Specialist::find($specialist->id));
    Storage::disk('public')->assertMissing($path);
});

test('creating staff with email and password creates a corresponding login user', function () {
    $merchant = User::factory()->create(['role' => 'merchant', 'salon_name' => 'Glow Salon', 'slug' => 'glow-salon']);
    
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

    $response = $this->actingAs($merchant)->post('/admin/staff', [
        'name' => 'Staff Login Test',
        'status' => 1,
        'mobile_no' => '1231231234',
        'email' => 'stafftest@example.com',
        'password' => 'password123',
        'job_category' => ['Hair'],
        'home_address' => 'Staff Road',
        'religion' => 'None',
    ]);

    $response->assertRedirect('/admin/staff');

    // Assert user created in users table
    $staffUser = User::where('email', 'stafftest@example.com')->first();
    $this->assertNotNull($staffUser);
    $this->assertSame('Staff Login Test', $staffUser->name);
    $this->assertSame('staff', $staffUser->role);
    $this->assertEquals($merchant->id, $staffUser->created_by);
    $this->assertNull($staffUser->slug);

    // Verify staff login succeeds
    $loginResponse = $this->post('/login', [
        'email' => 'stafftest@example.com',
        'password' => 'password123',
    ]);
    $loginResponse->assertRedirect('/dashboard');
});

test('staff sidebar displays only permitted menu links', function () {
    $merchant = User::factory()->create(['role' => 'merchant', 'salon_name' => 'Glow Salon', 'slug' => 'glow-salon']);
    
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

    // Create staff specialist & login user
    $staffUser = User::create([
        'name' => 'Permitted Staff',
        'email' => 'permitted@example.com',
        'password' => Hash::make('password123'),
        'role' => 'staff',
        'created_by' => $merchant->id,
        'is_verified' => true,
        'salon_name' => $merchant->salon_name,
        'slug' => $merchant->slug,
    ]);

    // Assign custom permission to staff: only 'manage_bookings' (no 'manage_services')
    \App\Models\RolePermission::create([
        'created_by' => $merchant->id,
        'role' => 'staff',
        'permission' => 'manage_bookings',
    ]);

    // Log in as staff and view dashboard/any admin page
    $response = $this->actingAs($staffUser)->get("/" . $merchant->slug . "/dashboard");
    $response->assertOk();

    // Assert that 'Bookings' link is visible
    $response->assertSee('Bookings');
    // Assert that 'Services' link is not visible
    $response->assertDontSee('Services');
});

test('staff user with create_bookings permission can see dashboard booking links', function () {
    $merchant = User::factory()->create(['role' => 'merchant', 'salon_name' => 'Glow Salon', 'slug' => 'glow-salon']);
    
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

    // Create staff specialist & login user
    $staffUser = User::create([
        'name' => 'Booking Staff',
        'email' => 'bookingstaff@example.com',
        'password' => Hash::make('password123'),
        'role' => 'staff',
        'created_by' => $merchant->id,
        'is_verified' => true,
        'salon_name' => $merchant->salon_name,
        'slug' => $merchant->slug,
    ]);

    // Assign 'create_bookings' permission to staff
    \App\Models\RolePermission::create([
        'created_by' => $merchant->id,
        'role' => 'staff',
        'permission' => 'create_bookings',
    ]);

    // View dashboard
    $response = $this->actingAs($staffUser)->get("/" . $merchant->slug . "/dashboard");
    $response->assertOk();

    // Assert that booking link is visible because permission was assigned
    $response->assertSee('Book New Appointment');
});
