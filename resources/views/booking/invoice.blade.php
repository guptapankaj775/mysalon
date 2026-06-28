<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $salesInvoice->invoice_number }} - SalonJC</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #D4AF37;       /* Luxury Gold */
            --secondary-color: #2C2C2C;     /* Charcoal Gray */
            --dark-color: #1A1A1A;          /* Deep Black */
            --light-bg: #F8F6F0;           /* Cream Background */
            --text-color: #333333;
            --success-color: #28A745;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
            font-size: 0.95rem;
            padding: 40px 0;
        }

        .invoice-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(212, 175, 55, 0.15);
            overflow: hidden;
            max-width: 1150px;
            margin: 0 auto;
        }

        .invoice-header {
            background-color: var(--dark-color);
            color: #FFFFFF;
            padding: 40px;
            border-bottom: 5px solid var(--primary-color);
        }

        .invoice-header h1 {
            color: var(--primary-color);
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
        }

        .invoice-body {
            padding: 40px;
        }

        .salon-logo span {
            color: var(--primary-color);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 6px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-invoice {
            margin-top: 30px;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .table-invoice thead {
            background-color: var(--dark-color);
            color: #FFFFFF;
        }

        .table-invoice th {
            font-weight: 600;
            padding: 10px 6px;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.2px;
            vertical-align: middle;
            border: none;
        }

        .table-invoice td {
            padding: 10px 6px;
            vertical-align: middle;
            font-size: 0.75rem;
            border-bottom: 1px solid #EDF2F7;
        }

        .table-invoice tbody tr:last-child td {
            border-bottom: none;
        }

        .badge-paid {
            background-color: var(--success-color);
            color: white;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .invoice-summary {
            background-color: #FAFAFA;
            border-radius: 10px;
            padding: 25px;
            border: 1px solid #E2E8F0;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .summary-row:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px solid #E2E8F0;
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--dark-color);
        }

        .action-bar {
            max-width: 1150px;
            margin: 25px auto 0;
            display: flex;
            justify-content: space-between;
        }

        .btn-gold {
            background-color: var(--primary-color);
            color: var(--dark-color);
            border: 1px solid var(--primary-color);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background-color: #E6B800;
            border-color: #E6B800;
            color: var(--dark-color);
            transform: translateY(-1px);
        }

        /* Print Media Queries */
        @media print {
            body {
                background-color: #FFFFFF;
                color: #000000;
                padding: 0;
            }

            .invoice-card {
                box-shadow: none;
                border: none;
                max-width: 100%;
                border-radius: 0;
            }

            .invoice-header {
                background-color: #1A1A1A !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-header h1 {
                color: #D4AF37 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table-invoice thead {
                background-color: #1A1A1A !important;
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

            .action-bar {
                display: none !important;
            }
        }

        /* PDF Render Styles for compact layouts */
        .pdf-render {
            max-width: 950px !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
        }

        .pdf-render .invoice-header {
            padding: 20px 30px !important;
        }

        .pdf-render .invoice-body {
            padding: 20px 30px !important;
        }

        .pdf-render .section-title {
            font-size: 0.8rem !important;
            margin-bottom: 8px !important;
        }

        .pdf-render h5, 
        .pdf-render p, 
        .pdf-render span, 
        .pdf-render td, 
        .pdf-render div {
            font-size: 0.72rem !important;
        }

        .pdf-render .table-invoice {
            margin-top: 15px !important;
        }

        .pdf-render .table-invoice th {
            font-size: 0.62rem !important;
            padding: 6px 3px !important;
        }

        .pdf-render .table-invoice td {
            font-size: 0.65rem !important;
            padding: 6px 3px !important;
        }

        .pdf-render .invoice-summary {
            padding: 12px 15px !important;
            margin-top: 10px !important;
        }

        .pdf-render .summary-row {
            font-size: 0.7rem !important;
            margin-bottom: 5px !important;
        }

        .pdf-render .summary-row:last-child {
            font-size: 0.82rem !important;
            padding-top: 5px !important;
        }
    </style>
</head>
<body>

    <div class="invoice-card">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="salon-logo mb-1 fw-bold">Salon<span>JC</span></h2>
                    <p class="mb-0 text-white-50">Luxury Beauty Salon & Spa</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <h1>INVOICE</h1>
                    <p class="mb-0 text-white-50 mt-1">Invoice No: <strong class="text-white">{{ $salesInvoice->invoice_number }}</strong></p>
                    <p class="mb-0 text-white-50">Invoice Date: <strong class="text-white">{{ $booking->updated_at->format('Y-m-d') }}</strong></p>
                </div>
            </div>
        </div>

        <div class="invoice-body">
            <!-- Salon & Customer Addresses -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="section-title">From (Supplier)</div>
                    <h5 class="fw-bold text-dark">SalonJC</h5>
                    <p class="mb-1 text-muted"><i class="fas fa-map-marker-alt me-2 text-warning"></i> Kaloliya Rd, Pallawela, Sri Lanka</p>
                    <p class="mb-1 text-muted"><i class="fas fa-phone me-2 text-warning"></i> 071 414 7628</p>
                    <p class="mb-0 text-muted"><i class="fas fa-envelope me-2 text-warning"></i> salonjc2092@gmail.com</p>
                </div>
                <div class="col-md-6">
                    <div class="section-title">Billed To (Client)</div>
                    @if($booking->user && ($booking->user->billing_name || $booking->user->gst_number || $booking->user->billing_address))
                        <h5 class="fw-bold text-dark">{{ $booking->user->billing_name ?: $booking->full_name }}</h5>
                        @if($booking->user->trade_name)
                            <p class="mb-1 text-dark fw-semibold">Trade Name: <span class="text-muted fw-normal">{{ $booking->user->trade_name }}</span></p>
                        @endif
                        @if($booking->user->gst_number)
                            <p class="mb-1 text-dark fw-semibold">GSTIN: <span class="text-muted fw-normal">{{ $booking->user->gst_number }}</span></p>
                        @endif
                        @if($booking->user->billing_address)
                            <p class="mb-1 text-muted"><i class="fas fa-map-marker-alt me-2 text-warning"></i> {{ $booking->user->billing_address }}</p>
                        @endif
                    @else
                        <h5 class="fw-bold text-dark">{{ $booking->full_name }}</h5>
                    @endif
                    <p class="mb-1 text-muted"><i class="fas fa-phone me-2 text-warning"></i> {{ $booking->phone }}</p>
                    <p class="mb-0 text-muted"><i class="fas fa-envelope me-2 text-warning"></i> {{ $booking->email }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive table-invoice">
                <table class="table text-center mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th class="text-start" style="min-width: 140px;">Name</th>
                            <th>Qty</th>
                            <th>M.R.P.</th>
                            <th>Discount</th>
                            <th>Pur.<br>Rate</th>
                            <th>Add.<br>Dis. %</th>
                            <th>Add.<br>Dis.</th>
                            <th>Net Pur.<br>Rate</th>
                            <th>Tax.<br>Amt.</th>
                            <th>GST</th>
                            <th>GST<br>Amt.</th>
                            <th>Sub-Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>SRV-{{ $booking->service_id }}</code></td>
                            <td class="fw-semibold text-start text-dark">{{ $booking->service->name }}</td>
                            <td>1</td>
                            <td>Rs. {{ number_format($booking->base_price, 2) }}</td>
                            <td>Rs. 0.00</td>
                            <td>Rs. {{ number_format($booking->base_price, 2) }}</td>
                            <td>0.00%</td>
                            <td>Rs. 0.00</td>
                            <td class="fw-semibold">Rs. {{ number_format($booking->base_price, 2) }}</td>
                            <td>Rs. {{ number_format($booking->base_price, 2) }}</td>
                            <td>3.00%</td>
                            <td>Rs. {{ number_format($booking->addons_price, 2) }}</td>
                            <td class="fw-bold text-dark">Rs. {{ number_format($booking->total_price, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary & Transaction Details -->
            <div class="row mt-4">
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="section-title">Payment Information</div>
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge-paid me-3"><i class="fas fa-check-circle me-1"></i> Paid</span>
                        <span class="text-muted"><i class="far fa-credit-card me-1"></i> {{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}</span>
                    </div>
                    <p class="mb-1 text-muted"><strong>Transaction ID:</strong> <code>{{ $booking->transaction_id }}</code></p>
                    <p class="mb-0 text-muted"><strong>Date/Time Paid:</strong> {{ $booking->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>
                <div class="col-md-6">
                    <div class="invoice-summary">
                        <div class="summary-row">
                            <span>Sub-Total (Taxable Amount):</span>
                            <span>Rs. {{ number_format($booking->base_price, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>GST Amount (3% Fee):</span>
                            <span>Rs. {{ number_format($booking->addons_price, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Total Amount Paid:</span>
                            <span>Rs. {{ number_format($booking->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 text-center text-muted small border-top pt-3">
                Thank you for choosing SalonJC! We look forward to seeing you again.
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar mb-5">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4 fw-semibold">
            <i class="fas fa-arrow-left me-2"></i>Dashboard
        </a>
        <div class="d-flex gap-2">
            <button id="download-pdf" class="btn btn-gold px-4 text-dark fw-bold">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </button>
            <button onclick="window.print()" class="btn btn-secondary px-4 text-white fw-bold">
                <i class="fas fa-print me-2"></i>Print
            </button>
        </div>
    </div>

    <!-- html2pdf.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.getElementById('download-pdf').addEventListener('click', function () {
            // Scroll to the absolute top-left of the page to prevent html2canvas offset bugs
            window.scrollTo(0, 0);
            
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
            
            // Small delay to allow the browser scrolling to settle
            setTimeout(() => {
                const element = document.querySelector('.invoice-card');
                
                // Save original styles to restore later
                const originalOverflow = element.style.overflow;
                const originalBoxShadow = element.style.boxShadow;
                const originalBorder = element.style.border;
                
                // Disable clipping, border, and shadows for clean PDF output
                element.style.overflow = 'visible';
                element.style.boxShadow = 'none';
                element.style.border = 'none';
                
                // Add the PDF specific styling override class
                element.classList.add('pdf-render');
                
                // PDF configuration options
                const opt = {
                    margin:       [10, 10, 10, 10], // 10mm margins
                    filename:     'Invoice-{{ $salesInvoice->invoice_number }}.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { 
                        scale: 2, 
                        useCORS: true, 
                        backgroundColor: '#ffffff',
                        windowWidth: 1300, // Forces desktop width to prevent column wrapping or cropping
                        scrollX: 0,
                        scrollY: 0,
                        logging: false,
                        letterRendering: true
                    },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(element).save().then(() => {
                    // Restore styles
                    element.classList.remove('pdf-render');
                    element.style.overflow = originalOverflow;
                    element.style.boxShadow = originalBoxShadow;
                    element.style.border = originalBorder;
                    
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }).catch(err => {
                    console.error(err);
                    
                    // Restore styles
                    element.classList.remove('pdf-render');
                    element.style.overflow = originalOverflow;
                    element.style.boxShadow = originalBoxShadow;
                    element.style.border = originalBorder;
                    
                    alert('Failed to generate PDF. Please try printing to PDF instead.');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
            }, 300);
        });
    </script>

</body>
</html>
