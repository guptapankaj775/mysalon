<?php

use App\Models\Vendor;
use App\Models\SalesInvoice;

test('vendors default to Creditor group', function () {
    $vendor = Vendor::create([
        'name' => 'Test Vendor Group',
        'status' => true,
    ]);

    $vendor->refresh();
    $this->assertNotNull($vendor->group_id);
    $this->assertSame('Creditor', $vendor->group->name);
});

test('sales invoices default to Debtor group', function () {
    $invoice = SalesInvoice::create([
        'invoice_number' => 'INV-2026-001',
        'customer_name'  => 'John Doe',
        'amount'         => 1250.00,
    ]);

    $invoice->refresh();
    $this->assertNotNull($invoice->group_id);
    $this->assertSame('Debtor', $invoice->group->name);
});

test('booking payment generates sales invoice automatically under Debtor group', function () {
    $category = App\Models\ServiceCategory::create([
        'name' => 'Hair Care',
        'status' => true,
    ]);

    $service = App\Models\Service::create([
        'name' => 'Haircut',
        'price' => 100.00,
        'category_id' => $category->id,
        'status' => true,
        'duration' => 30,
    ]);

    $booking = App\Models\Booking::create([
        'full_name' => 'Alice Cooper',
        'phone' => '1234567890',
        'email' => 'alice@example.com',
        'service_category_id' => $category->id,
        'service_id' => $service->id,
        'appointment_date' => '2026-07-01',
        'appointment_time' => '10:00',
        'base_price' => 100.00,
        'addons_price' => 3.00,
        'total_price' => 103.00,
        'status' => 'pending',
        'payment_status' => 'pending',
    ]);

    // Update payment_status to paid
    $booking->update([
        'payment_status' => 'paid',
        'payment_method' => 'credit_card',
        'transaction_id' => 'TXN_TEST_123',
    ]);

    // Check if sales invoice was generated automatically
    $invoice = SalesInvoice::where('invoice_number', 'INV-BOOK-' . $booking->id)->first();
    $this->assertNotNull($invoice);
    $this->assertSame('Alice Cooper', $invoice->customer_name);
    $this->assertEquals(103.00, $invoice->amount);
    $this->assertSame('paid', $invoice->status);
    $this->assertSame('Debtor', $invoice->group->name);
});
