<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_name',
        'status',
        'brand_id',
        'inventory_category_id',
        'division',
        'sku',
        'hsn_code',
        'description',
        'quantity',
        'stock_status',
        'manage_stock',
        'price',
        'mrp',
        'discount_percent',
        'cost',
        'purchase_discount_percent',
        'additional_discount_percent',
        'additional_discount_amount',
        'taxable_amount',
        'special_price',
        'tax_class',
        'gst_percent',
        'gst_input',
        'gst_output',
        'min_quantity',
        'unit_value',
        'size',
        'weight',
        'unit',
        'vendor_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'stock_status' => 'boolean',
        'manage_stock' => 'boolean',
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'cost' => 'decimal:2',
        'purchase_discount_percent' => 'decimal:2',
        'additional_discount_percent' => 'decimal:2',
        'additional_discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'special_price' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_input' => 'decimal:2',
        'gst_output' => 'decimal:2',
        'weight' => 'decimal:2',
        'unit_value' => 'decimal:2',
        'quantity' => 'integer',
        'min_quantity' => 'integer',
        'brand_id' => 'integer',
        'inventory_category_id' => 'integer',
    ];

    /**
     * Get the user who created this inventory item.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the vendor that supplies this inventory item.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the services that consume this inventory item.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_inventory_mapping');
    }

    /**
     * Get the brand this inventory item belongs to.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the category this inventory item belongs to.
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }
}
