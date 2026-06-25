<?php

use App\Models\User;
use App\Models\Inventory;
use App\Models\Vendor;

test('guests cannot access inventory management', function () {
    $response = $this->get('/admin/inventory');
    $response->assertRedirect('/login');
});

test('regular users cannot access inventory management', function () {
    $user = User::factory()->create(['role' => 'user']);
    $response = $this->actingAs($user)->get('/admin/inventory');
    $response->assertStatus(403);
});

test('admins can store inventory with Magento fields', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $vendor = Vendor::create(['name' => 'Vendor Test', 'status' => true]);
    $brand = \App\Models\Brand::create(['name' => 'Schwarzkopf', 'status' => true]);
    $category = \App\Models\InventoryCategory::create(['name' => 'Hair Spray', 'status' => true]);

    $response = $this->actingAs($admin)->post('/admin/inventory', [
        'item_name'                   => 'Oasis Spray Gold',
        'sku'                         => 'OAS-SPR-002',
        'brand_id'                    => $brand->id,
        'inventory_category_id'       => $category->id,
        'division'                    => 'Styling',
        'hsn_code'                    => '33051090',
        'description'                 => 'Professional styling spray',
        'status'                      => '1',
        'price'                       => '450.00',
        'mrp'                         => '499.00',
        'discount_percent'            => '10.00',
        'cost'                        => '300.00',
        'purchase_discount_percent'   => '5.00',
        'additional_discount_percent' => '2.50',
        'additional_discount_amount'  => '7.50',
        'taxable_amount'              => '277.50',
        'special_price'               => '399.00',
        'tax_class'                   => 'Taxable Goods',
        'gst_percent'                 => '18.00',
        'gst_input'                   => '49.95',
        'gst_output'                  => '71.91',
        'quantity'                    => '25',
        'min_quantity'                => '5',
        'stock_status'                => '1',
        'manage_stock'                => '1',
        'weight'                      => '0.50',
        'unit'                        => 'ML',
        'unit_value'                  => '250',
        'size'                        => 'Medium',
        'vendor_id'                   => $vendor->id,
    ]);

    $response->assertRedirect('/admin/inventory');

    $item = Inventory::where('item_name', 'Oasis Spray Gold')->first();
    $this->assertNotNull($item);
    $this->assertTrue($item->status);
    $this->assertEquals($brand->id, $item->brand_id);
    $this->assertSame('Schwarzkopf', $item->brand->name);
    $this->assertEquals($category->id, $item->inventory_category_id);
    $this->assertSame('Hair Spray', $item->category->name);
    $this->assertSame('Styling', $item->division);
    $this->assertSame('33051090', $item->hsn_code);
    $this->assertEquals(450.00, $item->price);
    $this->assertEquals(499.00, $item->mrp);
    $this->assertEquals(10.00, $item->discount_percent);
    $this->assertEquals(300.00, $item->cost);
    $this->assertEquals(5.00, $item->purchase_discount_percent);
    $this->assertEquals(2.50, $item->additional_discount_percent);
    $this->assertEquals(7.50, $item->additional_discount_amount);
    $this->assertEquals(277.50, $item->taxable_amount);
    $this->assertEquals(399.00, $item->special_price);
    $this->assertSame('Taxable Goods', $item->tax_class);
    $this->assertEquals(18.00, $item->gst_percent);
    $this->assertEquals(49.95, $item->gst_input);
    $this->assertEquals(71.91, $item->gst_output);
    $this->assertEquals(25, $item->quantity);
    $this->assertTrue($item->stock_status);
    $this->assertTrue($item->manage_stock);
    $this->assertEquals(0.50, $item->weight);
    $this->assertSame('ML', $item->unit);
    $this->assertEquals(250.00, $item->unit_value);
    $this->assertSame('Medium', $item->size);
    $this->assertEquals($vendor->id, $item->vendor_id);
});

test('admins can update inventory with Magento fields', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $brand = \App\Models\Brand::create(['name' => 'Wella', 'status' => true]);
    $category = \App\Models\InventoryCategory::create(['name' => 'Wax', 'status' => true]);
    $item = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Hair Wax Red',
        'price' => 150.00,
        'quantity' => 10,
        'min_quantity' => 2,
        'status' => true,
        'manage_stock' => true,
    ]);

    $response = $this->actingAs($admin)->put("/admin/inventory/{$item->id}", [
        'item_name'                   => 'Hair Wax Red Updated',
        'price'                       => '180.00',
        'mrp'                         => '199.00',
        'discount_percent'            => '5.00',
        'cost'                        => '100.00',
        'purchase_discount_percent'   => '3.00',
        'additional_discount_percent' => '1.00',
        'additional_discount_amount'  => '1.00',
        'taxable_amount'              => '96.00',
        'special_price'               => '160.00',
        'tax_class'                   => 'None',
        'gst_percent'                 => '12.00',
        'gst_input'                   => '11.52',
        'gst_output'                  => '19.20',
        'quantity'                    => '15',
        'min_quantity'                => '3',
        'stock_status'                => '0',
        'manage_stock'                => '0',
        'weight'                      => '0.20',
        'status'                      => '0',
        'brand_id'                    => $brand->id,
        'inventory_category_id'       => $category->id,
        'division'                    => 'Styling',
        'sku'                         => 'WEL-WAX-001',
        'hsn_code'                    => '33051090',
        'size'                        => 'Small',
    ]);

    $response->assertRedirect('/admin/inventory');

    $item->refresh();
    $this->assertSame('Hair Wax Red Updated', $item->item_name);
    $this->assertFalse($item->status);
    $this->assertFalse($item->manage_stock);
    $this->assertFalse($item->stock_status);
    $this->assertEquals(180.00, $item->price);
    $this->assertEquals(199.00, $item->mrp);
    $this->assertEquals(5.00, $item->discount_percent);
    $this->assertEquals(100.00, $item->cost);
    $this->assertEquals(3.00, $item->purchase_discount_percent);
    $this->assertEquals(1.00, $item->additional_discount_percent);
    $this->assertEquals(1.00, $item->additional_discount_amount);
    $this->assertEquals(96.00, $item->taxable_amount);
    $this->assertSame('None', $item->tax_class);
    $this->assertEquals(12.00, $item->gst_percent);
    $this->assertEquals(11.52, $item->gst_input);
    $this->assertEquals(19.20, $item->gst_output);
    $this->assertEquals(15, $item->quantity);
    $this->assertEquals(3, $item->min_quantity);
    $this->assertEquals(0.20, $item->weight);
    $this->assertEquals($brand->id, $item->brand_id);
    $this->assertSame('Wella', $item->brand->name);
    $this->assertEquals($category->id, $item->inventory_category_id);
    $this->assertSame('Wax', $item->category->name);
    $this->assertSame('Styling', $item->division);
    $this->assertSame('WEL-WAX-001', $item->sku);
    $this->assertSame('33051090', $item->hsn_code);
    $this->assertSame('Small', $item->size);
});
