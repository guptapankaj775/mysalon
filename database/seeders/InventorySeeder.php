<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\User;
use App\Models\Brand;
use App\Models\InventoryCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to safely truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Inventory::truncate();
        Brand::truncate();
        InventoryCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        // 1. Create Brands
        $loreal = Brand::create([
            'name' => 'L\'Oreal Professional',
            'description' => 'Global leader in professional hair colors and styling cosmetics.',
            'status' => true,
        ]);

        $wella = Brand::create([
            'name' => 'Wella Professionals',
            'description' => 'Premium German salon hair care and hair color brand.',
            'status' => true,
        ]);

        $schwarzkopf = Brand::create([
            'name' => 'Schwarzkopf Professional',
            'description' => 'Innovative hair styling, care, and color products.',
            'status' => true,
        ]);

        // 2. Create Categories
        $hairColor = InventoryCategory::create([
            'name' => 'Hair Color',
            'description' => 'Permanent, demi-permanent, and semi-permanent hair colors.',
            'status' => true,
        ]);

        $hairOils = InventoryCategory::create([
            'name' => 'Hair Oils & Serums',
            'description' => 'Reconstructive oils, elixirs, and nourishing hair serums.',
            'status' => true,
        ]);

        $styling = InventoryCategory::create([
            'name' => 'Styling Products',
            'description' => 'Hairsprays, styling powders, clays, and styling gels.',
            'status' => true,
        ]);

        $shampoo = InventoryCategory::create([
            'name' => 'Shampoos',
            'description' => 'Sulfate-free, moisturizing, and color-protecting shampoos.',
            'status' => true,
        ]);

        // 3. Create Inventory Items with complete Magento-style fields
        Inventory::create([
            'user_id'                     => $adminId,
            'item_name'                   => 'Majirel Permanent Hair Color 6.3',
            'sku'                         => 'LOK-MAJ-63',
            'description'                 => 'Professional permanent hair color cream giving perfect coverage of grey hair.',
            'quantity'                    => 50,
            'price'                       => 450.00,
            'mrp'                         => 499.00,
            'discount_percent'            => 10.00,
            'cost'                        => 300.00,
            'purchase_discount_percent'   => 5.00,
            'additional_discount_percent' => 2.50,
            'additional_discount_amount'  => 7.50,
            'taxable_amount'              => 277.50,
            'special_price'               => 399.00,
            'tax_class'                   => 'Taxable Goods',
            'gst_percent'                 => 18.00,
            'gst_input'                   => 49.95,
            'gst_output'                  => 71.91,
            'min_quantity'                => 10,
            'stock_status'                => true,
            'manage_stock'                => true,
            'unit'                        => 'ML',
            'unit_value'                  => 50.00,
            'size'                        => 'Standard',
            'weight'                      => 0.1,
            'division'                    => 'Hair Care',
            'hsn_code'                    => '33051090',
            'vendor_id'                   => 1, // L'Oreal Professional
            'brand_id'                    => $loreal->id,
            'inventory_category_id'       => $hairColor->id,
            'status'                      => true,
        ]);

        Inventory::create([
            'user_id'                     => $adminId,
            'item_name'                   => 'Wella SP Luxe Oil',
            'sku'                         => 'WEL-LUX-100',
            'description'                 => 'Reconstructive elixir oil with keratin to restore hair structure and add shine.',
            'quantity'                    => 20,
            'price'                       => 1800.00,
            'mrp'                         => 1999.00,
            'discount_percent'            => 10.00,
            'cost'                        => 1200.00,
            'purchase_discount_percent'   => 5.00,
            'additional_discount_percent' => 2.00,
            'additional_discount_amount'  => 24.00,
            'taxable_amount'              => 1116.00,
            'special_price'               => 1700.00,
            'tax_class'                   => 'Taxable Goods',
            'gst_percent'                 => 18.00,
            'gst_input'                   => 200.88,
            'gst_output'                  => 306.00,
            'min_quantity'                => 5,
            'stock_status'                => true,
            'manage_stock'                => true,
            'unit'                        => 'ML',
            'unit_value'                  => 100.00,
            'size'                        => 'Large',
            'weight'                      => 0.25,
            'division'                    => 'Hair Care',
            'hsn_code'                    => '33059011',
            'vendor_id'                   => 2, // Wella Professionals
            'brand_id'                    => $wella->id,
            'inventory_category_id'       => $hairOils->id,
            'status'                      => true,
        ]);

        Inventory::create([
            'user_id'                     => $adminId,
            'item_name'                   => 'Osis+ Dust It Mattifying Powder',
            'sku'                         => 'SCH-OSI-10',
            'description'                 => 'Lightweight styling powder that provides long-lasting hold and strong texture.',
            'quantity'                    => 35,
            'price'                       => 850.00,
            'mrp'                         => 950.00,
            'discount_percent'            => 10.50,
            'cost'                        => 550.00,
            'purchase_discount_percent'   => 4.00,
            'additional_discount_percent' => 1.50,
            'additional_discount_amount'  => 8.25,
            'taxable_amount'              => 519.75,
            'special_price'               => 799.00,
            'tax_class'                   => 'Taxable Goods',
            'gst_percent'                 => 18.00,
            'gst_input'                   => 93.56,
            'gst_output'                  => 143.82,
            'min_quantity'                => 8,
            'stock_status'                => true,
            'manage_stock'                => true,
            'unit'                        => 'Gram',
            'unit_value'                  => 10.00,
            'size'                        => 'Small',
            'weight'                      => 0.05,
            'division'                    => 'Styling',
            'hsn_code'                    => '33059019',
            'vendor_id'                   => 3, // Schwarzkopf Professional
            'brand_id'                    => $schwarzkopf->id,
            'inventory_category_id'       => $styling->id,
            'status'                      => true,
        ]);

        Inventory::create([
            'user_id'                     => $adminId,
            'item_name'                   => "L'Oreal Elnett Satin Hairspray Extra Strong Hold",
            'sku'                         => 'LOK-ELN-500',
            'description'                 => 'Legendary hair spray offering long-lasting hold with satin finish and easy brushing.',
            'quantity'                    => 15,
            'price'                       => 650.00,
            'mrp'                         => 720.00,
            'discount_percent'            => 9.72,
            'cost'                        => 420.00,
            'purchase_discount_percent'   => 5.00,
            'additional_discount_percent' => 2.00,
            'additional_discount_amount'  => 8.40,
            'taxable_amount'              => 390.60,
            'special_price'               => 599.00,
            'tax_class'                   => 'Taxable Goods',
            'gst_percent'                 => 18.00,
            'gst_input'                   => 70.31,
            'gst_output'                  => 107.82,
            'min_quantity'                => 5,
            'stock_status'                => true,
            'manage_stock'                => true,
            'unit'                        => 'ML',
            'unit_value'                  => 500.00,
            'size'                        => 'Large',
            'weight'                      => 0.6,
            'division'                    => 'Styling',
            'hsn_code'                    => '33053000',
            'vendor_id'                   => 1, // L'Oreal Professional
            'brand_id'                    => $loreal->id,
            'inventory_category_id'       => $styling->id,
            'status'                      => true,
        ]);

        Inventory::create([
            'user_id'                     => $adminId,
            'item_name'                   => 'Wella Elements Renewing Shampoo',
            'sku'                         => 'WEL-ELE-250',
            'description'                 => 'Sulfate-free shampoo that restores hair moisture and prevents keratin degeneration.',
            'quantity'                    => 3, // Low stock trigger
            'price'                       => 950.00,
            'mrp'                         => 1050.00,
            'discount_percent'            => 9.52,
            'cost'                        => 620.00,
            'purchase_discount_percent'   => 5.00,
            'additional_discount_percent' => 2.00,
            'additional_discount_amount'  => 12.40,
            'taxable_amount'              => 576.60,
            'special_price'               => 899.00,
            'tax_class'                   => 'Taxable Goods',
            'gst_percent'                 => 18.00,
            'gst_input'                   => 103.79,
            'gst_output'                  => 161.82,
            'min_quantity'                => 6,
            'stock_status'                => true,
            'manage_stock'                => true,
            'unit'                        => 'ML',
            'unit_value'                  => 250.00,
            'size'                        => 'Medium',
            'weight'                      => 0.3,
            'division'                    => 'Hair Care',
            'hsn_code'                    => '33051010',
            'vendor_id'                   => 2, // Wella Professionals
            'brand_id'                    => $wella->id,
            'inventory_category_id'       => $shampoo->id,
            'status'                      => true,
        ]);
    }
}
