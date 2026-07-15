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
                $invoice = SalesInvoice::where('invoice_number', $invoiceNumber)->first();
                if ($invoice) {
                    $invoice->update(['status' => 'paid']);
                } else {
                    SalesInvoice::create([
                        'invoice_number' => $invoiceNumber,
                        'customer_name' => $booking->full_name,
                        'amount' => $booking->total_price,
                        'status' => 'paid',
                    ]);
                }
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

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    public function updateStatusFromServices()
    {
        $services = $this->bookingServices()->get();
        if ($services->isEmpty()) {
            return;
        }

        $totalCount = $services->count();
        $completedCount = $services->where('status', 'completed')->count();
        $inProgressCount = $services->where('status', 'in_progress')->count();

        $newStatus = 'pending';

        if ($completedCount === $totalCount) {
            $newStatus = 'closed';
        } elseif ($inProgressCount > 0 || $completedCount > 0) {
            $newStatus = 'in_progress';
        } else {
            $assignedCount = $services->whereNotNull('staff_id')->count();
            if ($assignedCount > 0) {
                $newStatus = 'assigned';
            }
        }

        if ($this->status !== 'cancelled' && $this->status !== 'closed') {
            $this->status = $newStatus;
            
            if ($newStatus === 'closed') {
                $this->completed_at = now();
                
                $invoiceNumber = 'INV-BOOK-' . $this->id;
                SalesInvoice::firstOrCreate(
                    ['invoice_number' => $invoiceNumber],
                    [
                        'customer_name' => $this->full_name,
                        'amount' => $this->total_price,
                        'status' => $this->payment_status === 'paid' ? 'paid' : 'pending',
                    ]
                );
            }
            
            $this->save();
        }
    }
}
