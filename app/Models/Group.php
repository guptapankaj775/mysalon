<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the vendors mapped to this group.
     */
    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    /**
     * Get the sales invoices mapped to this group.
     */
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }
}
