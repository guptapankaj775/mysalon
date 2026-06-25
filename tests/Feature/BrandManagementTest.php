<?php

use App\Models\User;
use App\Models\Brand;
use App\Models\Inventory;

test('guests cannot access brand management', function () {
    $response = $this->get('/admin/brands');
    $response->assertRedirect('/login');
});

test('regular users cannot access brand management', function () {
    $user = User::factory()->create(['role' => 'user']);
    $response = $this->actingAs($user)->get('/admin/brands');
    $response->assertStatus(403);
});

test('staff users cannot access brand management', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $response = $this->actingAs($staff)->get('/admin/brands');
    $response->assertStatus(403);
});

test('admins can list brands', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get('/admin/brands');
    $response->assertOk();
    $response->assertViewHas('brands');
});

test('admins can render create brand page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get('/admin/brands/create');
    $response->assertOk();
});

test('admins can store a brand', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)->post('/admin/brands', [
        'name' => 'L\'Oreal Professional',
        'description' => 'Hair cosmetics brand',
        'status' => '1',
    ]);

    $response->assertRedirect('/admin/brands');
    
    $brand = Brand::where('name', 'L\'Oreal Professional')->first();
    $this->assertNotNull($brand);
    $this->assertSame('Hair cosmetics brand', $brand->description);
    $this->assertTrue($brand->status);
});

test('admins can render edit brand page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $brand = Brand::create(['name' => 'Wella Professionals', 'status' => true]);

    $response = $this->actingAs($admin)->get("/admin/brands/{$brand->id}/edit");
    $response->assertOk();
    $response->assertViewHas('brand');
});

test('admins can update a brand', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $brand = Brand::create(['name' => 'Wella Professionals', 'status' => true]);

    $response = $this->actingAs($admin)->put("/admin/brands/{$brand->id}", [
        'name' => 'Wella Professionals Updated',
        'description' => 'Updated description',
        'status' => '0',
    ]);

    $response->assertRedirect('/admin/brands');

    $brand->refresh();
    $this->assertSame('Wella Professionals Updated', $brand->name);
    $this->assertSame('Updated description', $brand->description);
    $this->assertFalse($brand->status);
});

test('admins can delete a brand and associated inventories have brand_id set to null', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $brand = Brand::create(['name' => 'Schwarzkopf', 'status' => true]);
    
    $item = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Oasis Spray Gold',
        'price' => 450.00,
        'quantity' => 25,
        'min_quantity' => 5,
        'status' => true,
        'brand_id' => $brand->id,
    ]);

    $response = $this->actingAs($admin)->delete("/admin/brands/{$brand->id}");
    $response->assertRedirect('/admin/brands');

    $this->assertNull(Brand::find($brand->id));
    
    $item->refresh();
    $this->assertNull($item->brand_id);
});
