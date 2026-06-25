<?php

use App\Models\User;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Inventory;

test('guests cannot access services management', function () {
    $response = $this->get('/admin/services');
    $response->assertRedirect('/login');
});

test('regular users cannot access services management', function () {
    $user = User::factory()->create(['role' => 'user']);
    $response = $this->actingAs($user)->get('/admin/services');
    $response->assertStatus(403);
});

test('admins can store service with mapped inventories', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = ServiceCategory::create(['name' => 'Hair Care', 'status' => true]);
    
    $inventory1 = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Shampoo A',
        'sku' => 'SH-A-001',
        'quantity' => 10,
        'price' => 150.00,
        'min_quantity' => 2,
    ]);

    $inventory2 = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Conditioner B',
        'sku' => 'CO-B-001',
        'quantity' => 5,
        'price' => 200.00,
        'min_quantity' => 1,
    ]);

    $response = $this->actingAs($admin)->post('/admin/services', [
        'name' => 'Luxury Hair Spa',
        'description' => 'A deeply conditioning hair spa',
        'duration' => 60,
        'price' => 1200.00,
        'category_id' => $category->id,
        'status' => '1',
        'icon' => 'fa-spa',
        'features' => ['Wash', 'Deep conditioning massage', 'Blow dry'],
        'inventories' => [$inventory1->id, $inventory2->id],
    ]);

    $response->assertRedirect('/admin/services');

    $service = Service::where('name', 'Luxury Hair Spa')->first();
    $this->assertNotNull($service);
    $this->assertCount(2, $service->inventories);
    $this->assertTrue($service->inventories->contains($inventory1->id));
    $this->assertTrue($service->inventories->contains($inventory2->id));
});

test('admins can update service with updated mapped inventories', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = ServiceCategory::create(['name' => 'Hair Care', 'status' => true]);
    
    $inventory1 = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Shampoo A',
        'sku' => 'SH-A-001',
        'quantity' => 10,
        'price' => 150.00,
        'min_quantity' => 2,
    ]);

    $inventory2 = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Conditioner B',
        'sku' => 'CO-B-001',
        'quantity' => 5,
        'price' => 200.00,
        'min_quantity' => 1,
    ]);

    $service = Service::create([
        'name' => 'Standard Hair Spa',
        'description' => 'A standard hair spa',
        'duration' => 45,
        'price' => 800.00,
        'category_id' => $category->id,
        'status' => true,
    ]);
    $service->icon()->create(['image_path' => 'fa-spa']);
    $service->inventories()->sync([$inventory1->id]);

    $response = $this->actingAs($admin)->put("/admin/services/{$service->id}", [
        'name' => 'Standard Hair Spa Updated',
        'description' => 'Updated desc',
        'duration' => 50,
        'price' => 900.00,
        'category_id' => $category->id,
        'status' => '1',
        'icon' => 'fa-hair-dryer',
        'inventories' => [$inventory2->id],
    ]);

    $response->assertRedirect('/admin/services');

    $service->refresh();
    $this->assertSame('Standard Hair Spa Updated', $service->name);
    $this->assertCount(1, $service->inventories);
    $this->assertTrue($service->inventories->contains($inventory2->id));
    $this->assertFalse($service->inventories->contains($inventory1->id));
});

test('deleting service cleans up service_inventory pivot mappings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = ServiceCategory::create(['name' => 'Hair Care', 'status' => true]);
    
    $inventory = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Shampoo A',
        'sku' => 'SH-A-001',
        'quantity' => 10,
        'price' => 150.00,
        'min_quantity' => 2,
    ]);

    $service = Service::create([
        'name' => 'Standard Hair Spa',
        'description' => 'A standard hair spa',
        'duration' => 45,
        'price' => 800.00,
        'category_id' => $category->id,
        'status' => true,
    ]);
    $service->icon()->create(['image_path' => 'fa-spa']);
    $service->inventories()->sync([$inventory->id]);

    $this->assertDatabaseHas('service_inventory_mapping', [
        'service_id' => $service->id,
        'inventory_id' => $inventory->id
    ]);

    $response = $this->actingAs($admin)->delete("/admin/services/{$service->id}");
    $response->assertRedirect('/admin/services');

    $this->assertNull(Service::find($service->id));
    $this->assertDatabaseMissing('service_inventory_mapping', [
        'service_id' => $service->id,
        'inventory_id' => $inventory->id
    ]);
});

test('deleting inventory cleans up service_inventory pivot mappings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = ServiceCategory::create(['name' => 'Hair Care', 'status' => true]);
    
    $inventory = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Shampoo A',
        'sku' => 'SH-A-001',
        'quantity' => 10,
        'price' => 150.00,
        'min_quantity' => 2,
    ]);

    $service = Service::create([
        'name' => 'Standard Hair Spa',
        'description' => 'A standard hair spa',
        'duration' => 45,
        'price' => 800.00,
        'category_id' => $category->id,
        'status' => true,
    ]);
    $service->icon()->create(['image_path' => 'fa-spa']);
    $service->inventories()->sync([$inventory->id]);

    $this->assertDatabaseHas('service_inventory_mapping', [
        'service_id' => $service->id,
        'inventory_id' => $inventory->id
    ]);

    $inventory->delete();

    $this->assertDatabaseMissing('service_inventory_mapping', [
        'service_id' => $service->id,
        'inventory_id' => $inventory->id
    ]);
});
