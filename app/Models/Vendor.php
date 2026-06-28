<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'status',
        'website',
        'tax_number',
        'payment_terms',
        'bank_name',
        'bank_account',
        'bank_code',
        'logo_path',
        'description',
        'group_id',
    ];

    protected static function booted()
    {
        static::creating(function ($vendor) {
            if (empty($vendor->group_id)) {
                $group = Group::firstOrCreate(['name' => 'Creditor']);
                $vendor->group_id = $group->id;
            }
        });
    }

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the group assigned to this vendor.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Get the inventory items supplied by this vendor.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
