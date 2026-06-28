<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'amount',
        'status',
        'group_id',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->group_id)) {
                $group = Group::firstOrCreate(['name' => 'Debtor']);
                $invoice->group_id = $group->id;
            }
        });
    }

    /**
     * Get the group assigned to this invoice.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
