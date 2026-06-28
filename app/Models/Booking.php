<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'service_category_id',
        'service_id',
        'appointment_date',
        'appointment_time',
        'stylist_id',
        'special_requirements',
        'base_price',
        'addons_price',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'confirmed_at',
        'user_id' // Add user_id to fillable
    ];

    protected static function booted()
    {
        static::created(function ($booking) {
            if ($booking->payment_status === 'paid') {
                $invoiceNumber = 'INV-BOOK-' . $booking->id;
                SalesInvoice::firstOrCreate(
                    ['invoice_number' => $invoiceNumber],
                    [
                        'customer_name' => $booking->full_name,
                        'amount' => $booking->total_price,
                        'status' => 'paid',
                    ]
                );
            }
        });

        static::updated(function ($booking) {
            if ($booking->wasChanged('payment_status') && $booking->payment_status === 'paid') {
                $invoiceNumber = 'INV-BOOK-' . $booking->id;
                SalesInvoice::firstOrCreate(
                    ['invoice_number' => $invoiceNumber],
                    [
                        'customer_name' => $booking->full_name,
                        'amount' => $booking->total_price,
                        'status' => 'paid',
                    ]
                );
            }
        });
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
}
