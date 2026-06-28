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
