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
        'services' => [$service->id]
    ]);

    $response->assertRedirect('/admin/staff');

    $specialist = Specialist::where('name', 'John Doe')->first();
    $this->assertNotNull($specialist);
    $this->assertSame('Experienced barber', $specialist->bio);
    $this->assertTrue((bool)$specialist->status);
    $this->assertNotNull($specialist->image_path);
    Storage::disk('public')->assertExists($specialist->image_path);

    // Verify services mapping
    $this->assertTrue($specialist->services->contains($service->id));
});

test('admins can update staff details and services', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    
    $specialist = Specialist::create([
        'name' => 'Jane Smith',
        'bio' => 'Old bio',
        'status' => true
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
        'services' => [$service->id]
    ]);

    $response->assertRedirect('/admin/staff');

    $specialist->refresh();
    $this->assertSame('Jane Doe', $specialist->name);
    $this->assertSame('New bio', $specialist->bio);
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
