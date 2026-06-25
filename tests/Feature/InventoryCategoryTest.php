<?php

use App\Models\User;
use App\Models\InventoryCategory;
use App\Models\Inventory;

test('guests cannot access inventory category management', function () {
    $response = $this->get('/admin/inventory-categories');
    $response->assertRedirect('/login');
});

test('regular users cannot access inventory category management', function () {
    $user = User::factory()->create(['role' => 'user']);
    $response = $this->actingAs($user)->get('/admin/inventory-categories');
    $response->assertStatus(403);
});

test('staff users cannot access inventory category management', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $response = $this->actingAs($staff)->get('/admin/inventory-categories');
    $response->assertStatus(403);
});

test('admins can list inventory categories', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get('/admin/inventory-categories');
    $response->assertOk();
    $response->assertViewHas('categories');
});

test('admins can render create inventory category page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get('/admin/inventory-categories/create');
    $response->assertOk();
});

test('admins can store an inventory category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)->post('/admin/inventory-categories', [
        'name' => 'Hair Care',
        'description' => 'Shampoos and conditioners',
        'status' => '1',
    ]);

    $response->assertRedirect('/admin/inventory-categories');
    
    $category = InventoryCategory::where('name', 'Hair Care')->first();
    $this->assertNotNull($category);
    $this->assertSame('Shampoos and conditioners', $category->description);
    $this->assertTrue($category->status);
});

test('admins can render edit inventory category page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = InventoryCategory::create(['name' => 'Skin Care', 'status' => true]);

    $response = $this->actingAs($admin)->get("/admin/inventory-categories/{$category->id}/edit");
    $response->assertOk();
    $response->assertViewHas('inventoryCategory');
});

test('admins can update an inventory category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = InventoryCategory::create(['name' => 'Skin Care', 'status' => true]);

    $response = $this->actingAs($admin)->put("/admin/inventory-categories/{$category->id}", [
        'name' => 'Skin Care Updated',
        'description' => 'Updated desc',
        'status' => '0',
    ]);

    $response->assertRedirect('/admin/inventory-categories');

    $category->refresh();
    $this->assertSame('Skin Care Updated', $category->name);
    $this->assertSame('Updated desc', $category->description);
    $this->assertFalse($category->status);
});

test('admins can delete an inventory category and associated inventories have inventory_category_id set to null', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = InventoryCategory::create(['name' => 'Hair Styling', 'status' => true]);
    
    $item = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Hair Wax Red',
        'price' => 150.00,
        'quantity' => 10,
        'min_quantity' => 2,
        'status' => true,
        'inventory_category_id' => $category->id,
    ]);

    $response = $this->actingAs($admin)->delete("/admin/inventory-categories/{$category->id}");
    $response->assertRedirect('/admin/inventory-categories');

    $this->assertNull(InventoryCategory::find($category->id));
    
    $item->refresh();
    $this->assertNull($item->inventory_category_id);
});
