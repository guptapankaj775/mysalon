<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Invoice #{{ $posSale->invoice_number }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #121A21;
            --accent-color: #00A3B1;
            --success-color: #28A745;
            --text-color: #333333;
            --muted-color: #718096;
            --border-color: #E2E8F0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            background-color: #F8F9FA;
            padding: 30px 0;
            font-size: 0.9rem;
        }

        .invoice-card {
            background-color: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            max-width: 800px;
            margin: 0 auto;
            overflow: hidden;
        }

        .invoice-header {
            background-color: var(--primary-color);
            color: #FFFFFF;
            padding: 40px;
            border-bottom: 3px solid var(--accent-color);
        }

        .invoice-body {
            padding: 40px;
        }

        .invoice-footer {
            background-color: #FAFAFA;
            border-top: 1px solid var(--border-color);
            padding: 30px 40px;
            text-align: center;
        }

        .brand-logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent-color);
        }

        .badge-paid {
            background-color: var(--success-color);
            color: white;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #1a1a1a;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .table-items th {
            background-color: #F8FAFC;
            color: var(--muted-color);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color);
            padding: 12px 15px;
        }

        .table-items td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .summary-box {
            background-color: #F8FAFC;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid var(--border-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px dashed var(--border-color);
            font-weight: 700;
            color: var(--primary-color);
        }

        .action-bar {
            background-color: #FFFFFF;
            border-bottom: 1px solid var(--border-color);
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        @media print {
            body {
                background-color: #FFFFFF;
                padding: 0;
                color: #000000;
            }

            .action-bar {
                display: none !important;
            }

            .invoice-card {
                box-shadow: none;
                border: none;
                max-width: 100%;
                border-radius: 0;
            }

            .invoice-header {
                background-color: #121A21 !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-paid {
                background-color: #28A745 !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-pending {
                background-color: #ffc107 !important;
                color: #1a1a1a !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar -->
    <div class="action-bar mb-4">
        <div class="max-w-4xl mx-auto d-flex justify-content-between align-items-center" style="max-width: 800px;">
            <a href="{{ $salon ? route('admin.pos.history', ['salon' => $salon->slug]) : route('admin.pos.history') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to History
            </a>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-warning btn-sm text-dark font-semibold">
                    <i class="fas fa-print me-1"></i> Print Invoice
                </button>
                <button onclick="window.close()" class="btn btn-light btn-sm border">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="invoice-card">
        <!-- Header -->
        <div class="invoice-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
            <div>
                <div class="brand-logo mb-2">
                    <i class="fas fa-spa me-1"></i> {{ $salonUser ? $salonUser->salon_name : config('app.name', 'SalonJC') }}
                </div>
                <p class="mb-1 text-white opacity-75">Professional Salon & Spa Services</p>
                @if($salonUser)
                    <p class="mb-0 text-white opacity-75 small"><i class="fas fa-envelope me-1"></i> {{ $salonUser->email }}</p>
                @endif
            </div>
            <div class="text-md-end">
                <h2 class="h4 text-uppercase tracking-wider font-bold mb-2">POS Invoice</h2>
                <p class="mb-1 text-white opacity-75"><strong>Invoice ID:</strong> {{ $posSale->invoice_number }}</p>
                <p class="mb-0 text-white opacity-75"><strong>Date:</strong> {{ $posSale->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <!-- Body -->
        <div class="invoice-body">
            <!-- Customer Metadata -->
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="text-muted text-xs uppercase tracking-wider mb-2">Billed To</div>
                    <h4 class="h6 font-bold text-gray-900 mb-1">{{ $posSale->customer_name }}</h4>
                    @if($posSale->customer_phone)
                        <p class="mb-1 text-muted"><i class="fas fa-phone-alt me-1.5 fs-xs"></i> {{ $posSale->customer_phone }}</p>
                    @endif
                    @if($posSale->customer_email)
                        <p class="mb-0 text-muted"><i class="far fa-envelope me-1.5 fs-xs"></i> {{ $posSale->customer_email }}</p>
                    @endif
                </div>
                <div class="col-md-6 text-md-end mt-4 mt-md-0">
                    <div class="text-muted text-xs uppercase tracking-wider mb-2">Payment Details</div>
                    <div class="mb-2">
                        @if($posSale->payment_status === 'Paid')
                            <span class="badge-paid"><i class="fas fa-check-circle me-1"></i> Paid</span>
                        @else
                            <span class="badge-pending"><i class="fas fa-clock me-1"></i> Unpaid</span>
                        @endif
                    </div>
                    <p class="mb-1 text-muted"><strong>Method:</strong> {{ $posSale->payment_method }}</p>
                    <p class="mb-0 text-muted"><strong>Status:</strong> Completed</p>
                </div>
            </div>

            <!-- Items Table -->
            <h5 class="h6 font-bold text-gray-900 mb-3 text-uppercase tracking-wider">Line Items</h5>
            <div class="table-responsive mb-5">
                <table class="table table-items w-100 mb-0">
                    <thead>
                        <tr>
                            <th style="width: 8%;">#</th>
                            <th>Description</th>
                            <th style="width: 15%;">Type</th>
                            <th style="width: 12%;" class="text-center">Qty</th>
                            <th style="width: 18%;" class="text-end">Price</th>
                            <th style="width: 18%;" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posSale->items as $index => $item)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="font-semibold text-gray-900">{{ $item['name'] }}</td>
                                <td>
                                    <span class="badge {{ $item['type'] === 'service' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} text-2xs px-2 py-0.5 rounded border border-current font-bold uppercase">
                                        {{ $item['type'] }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $item['quantity'] }}</td>
                                <td class="text-end">Rs. {{ number_format($item['price'], 2) }}</td>
                                <td class="text-end font-semibold">Rs. {{ number_format($item['subtotal'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Total Summary -->
            <div class="row justify-content-end">
                <div class="col-md-6 col-lg-5">
                    <div class="summary-box">
                        <div class="summary-row text-muted text-sm">
                            <span>Sub-Total:</span>
                            <span>Rs. {{ number_format($posSale->subtotal, 2) }}</span>
                        </div>
                        <div class="summary-row text-muted text-sm">
                            <span>Tax (GST):</span>
                            <span>Rs. {{ number_format($posSale->tax, 2) }}</span>
                        </div>
                        <div class="summary-row text-danger text-sm">
                            <span>Discount:</span>
                            <span>- Rs. {{ number_format($posSale->discount, 2) }}</span>
                        </div>
                        <div class="summary-row text-base">
                            <span>Grand Total:</span>
                            <span class="text-success">Rs. {{ number_format($posSale->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer text-muted text-xs">
            <p class="mb-2 font-semibold text-gray-800">Thank you for your visit!</p>
            <p class="mb-0">Please keep this copy of the receipt for your records. Visit us again soon.</p>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
