<?php

use App\Models\User;
use App\Models\Vendor;
use App\Models\Inventory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests cannot access vendor management', function () {
    $response = $this->get('/admin/vendors');
    $response->assertRedirect('/login');
});

test('regular users cannot access vendor management', function () {
    $user = User::factory()->create(['role' => 'user']);
    $response = $this->actingAs($user)->get('/admin/vendors');
    $response->assertStatus(403);
});

test('staff users cannot access vendor management', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $response = $this->actingAs($staff)->get('/admin/vendors');
    $response->assertStatus(403);
});

test('admins can list vendors', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get('/admin/vendors');
    $response->assertOk();
    $response->assertViewHas('vendors');
});

test('admins can create a vendor with advanced details and logo', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    
    $file = UploadedFile::fake()->create('logo.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($admin)->post('/admin/vendors', [
        'name' => 'Loreal Supplier',
        'contact_name' => 'Jane Loreal',
        'email' => 'jane@loreal.com',
        'phone' => '1234567890',
        'website' => 'https://loreal.com',
        'tax_number' => 'VAT-LOREAL-789',
        'payment_terms' => 'Net 30',
        'bank_name' => 'Sparkasse Paris',
        'bank_account' => 'FR763000',
        'bank_code' => 'SPKFR2B',
        'logo' => $file,
        'address' => 'Loreal HQ, Paris',
        'description' => 'Supplier for professional hair colors.',
        'status' => '1',
    ]);

    $response->assertRedirect('/admin/vendors');
    
    $vendor = Vendor::where('name', 'Loreal Supplier')->first();
    $this->assertNotNull($vendor);
    $this->assertSame('Jane Loreal', $vendor->contact_name);
    $this->assertSame('Creditor', $vendor->group->name);
    $this->assertSame('jane@loreal.com', $vendor->email);
    $this->assertSame('https://loreal.com', $vendor->website);
    $this->assertSame('VAT-LOREAL-789', $vendor->tax_number);
    $this->assertSame('Net 30', $vendor->payment_terms);
    $this->assertSame('Sparkasse Paris', $vendor->bank_name);
    $this->assertSame('FR763000', $vendor->bank_account);
    $this->assertSame('SPKFR2B', $vendor->bank_code);
    $this->assertSame('Supplier for professional hair colors.', $vendor->description);
    $this->assertTrue($vendor->status);
    
    $this->assertNotNull($vendor->logo_path);
    Storage::disk('public')->assertExists($vendor->logo_path);
});

test('admins can update a vendor and replace logo', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    
    $initialFile = UploadedFile::fake()->create('initial_logo.jpg', 100, 'image/jpeg');
    $initialPath = Storage::disk('public')->put('vendors', $initialFile);

    $vendor = Vendor::create([
        'name' => 'Wella Co',
        'contact_name' => 'Old Name',
        'logo_path' => $initialPath,
        'status' => true,
    ]);

    $newFile = UploadedFile::fake()->create('new_logo.png', 100, 'image/png');

    $response = $this->actingAs($admin)->put("/admin/vendors/{$vendor->id}", [
        'name' => 'Wella Co Updated',
        'contact_name' => 'New Name',
        'website' => 'https://wella.com',
        'payment_terms' => 'Net 15',
        'logo' => $newFile,
        'status' => '0',
    ]);

    $response->assertRedirect('/admin/vendors');
    
    $vendor->refresh();
    $this->assertSame('Wella Co Updated', $vendor->name);
    $this->assertSame('New Name', $vendor->contact_name);
    $this->assertSame('https://wella.com', $vendor->website);
    $this->assertSame('Net 15', $vendor->payment_terms);
    $this->assertFalse($vendor->status);
    
    // Check that old logo is deleted and new exists
    Storage::disk('public')->assertMissing($initialPath);
    Storage::disk('public')->assertExists($vendor->logo_path);
});

test('admins can delete a vendor and associated logo is deleted', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    
    $file = UploadedFile::fake()->create('logo.jpg', 100, 'image/jpeg');
    $path = Storage::disk('public')->put('vendors', $file);

    $vendor = Vendor::create([
        'name' => 'Matrix Supp',
        'logo_path' => $path,
        'status' => true,
    ]);

    $inventory = Inventory::create([
        'user_id' => $admin->id,
        'item_name' => 'Matrix Wax',
        'sku' => 'MAT-WAX-001',
        'quantity' => 10,
        'price' => 250.00,
        'min_quantity' => 2,
        'vendor_id' => $vendor->id,
    ]);

    $response = $this->actingAs($admin)->delete("/admin/vendors/{$vendor->id}");
    $response->assertRedirect('/admin/vendors');

    $this->assertNull(Vendor::find($vendor->id));
    Storage::disk('public')->assertMissing($path); // Logo file cleaned up
    
    $inventory->refresh();
    $this->assertNull($inventory->vendor_id);
});

test('admins can assign vendor to inventory item', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $vendor = Vendor::create([
        'name' => 'Schwarzkopf',
        'status' => true,
    ]);

    $response = $this->actingAs($admin)->post('/admin/inventory', [
        'item_name' => 'Osis Dust It',
        'sku' => 'OSIS-DUST-01',
        'quantity' => 15,
        'price' => 950.00,
        'min_quantity' => 3,
        'vendor_id' => $vendor->id,
        'unit' => 'Gram',
        'unit_value' => '10.00',
    ]);

    $response->assertRedirect('/admin/inventory');

    $item = Inventory::where('item_name', 'Osis Dust It')->first();
    $this->assertNotNull($item);
    $this->assertEquals($vendor->id, $item->vendor_id);
    $this->assertSame('Schwarzkopf', $item->vendor->name);
});
