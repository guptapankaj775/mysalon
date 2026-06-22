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
        'sku',
        'description',
        'quantity',
        'price',
        'min_quantity',
        'unit_value',
        'unit',
    ];

    /**
     * Get the user who created this inventory item.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
