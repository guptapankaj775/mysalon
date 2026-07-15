<x-admin-layout>
    @push('styles')
    <style>
        .reports-page {
            padding: 30px;
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
        }

        .section-title {
            color: #2C2C2C;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 1.75rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: #D4AF37;
            margin-top: 8px;
            border-radius: 2px;
        }

        .kpi-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            align-items: center;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #D4AF37;
        }

        .kpi-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 20px;
        }

        .kpi-revenue { background: rgba(212, 175, 55, 0.1); color: #D4AF37; }
        .kpi-paid { background: rgba(46, 125, 50, 0.1); color: #2e7d32; }
        .kpi-pending { background: rgba(230, 81, 0, 0.1); color: #e65100; }
        .kpi-bookings { background: rgba(3, 105, 161, 0.1); color: #0369a1; }

        .kpi-content h6 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #777;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .kpi-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2C2C2C;
            margin-bottom: 0;
        }

        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 600;
            color: #2C2C2C;
            font-size: 0.9rem;
        }

        .btn-gold {
            background: #D4AF37;
            color: white;
            border: 1px solid #D4AF37;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background: #B8962D;
            border-color: #B8962D;
            color: white;
        }

        .btn-outline-gold {
            background: transparent;
            color: #D4AF37;
            border: 1px solid #D4AF37;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-gold:hover {
            background: #D4AF37;
            color: white;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .status-pending { background: #fff3e0; color: #e65100; }
        .status-confirmed { background: #e8f5e9; color: #2e7d32; }
        .status-cancelled { background: #ffebee; color: #c62828; }
        .status-completed { background: #e3f2fd; color: #1565c0; }
        .status-closed { background: #eceff1; color: #37474f; }
        .status-in_progress { background: #fffde7; color: #f57f17; }

        .payment-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .payment-paid { background: #e8f5e9; color: #2e7d32; }
        .payment-pending { background: #fff3e0; color: #e65100; }
        .payment-failed { background: #ffebee; color: #c62828; }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 0.85rem;
        }
    </style>
    @endpush

    @section('content')
    <div class="reports-page">
        <h2 class="section-title">Sales Report</h2>

        <!-- KPI Cards Summary Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-icon kpi-revenue">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="kpi-content">
                        <h6>Total Revenue</h6>
                        <h3>₹{{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-icon kpi-paid">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kpi-content">
                        <h6>Paid Revenue</h6>
                        <h3>₹{{ number_format($paidRevenue, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-icon kpi-pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="kpi-content">
                        <h6>Pending Payments</h6>
                        <h3>₹{{ number_format($pendingRevenue, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-icon kpi-bookings">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="kpi-content">
                        <h6>Total Bookings</h6>
                        <h3>{{ $totalBookingsCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card">
            <form method="GET" action="{{ url()->current() }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" for="startDate">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="endDate">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="staffSelect">Staff Filter</label>
                        <select class="form-select" id="staffSelect" name="staff_id">
                            <option value="">All Staff</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ $staffId == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="serviceSelect">Service Filter</label>
                        <select class="form-select" id="serviceSelect" name="service_id">
                            <option value="">All Services</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ $serviceId == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-gold flex-grow-1 py-2">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-outline-gold py-2" title="Reset Filters">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Chart Card -->
        <div class="chart-card">
            <h5 class="fw-bold text-muted mb-4"><i class="fas fa-chart-line text-warning me-2"></i> Sales Trend (Completed & Closed)</h5>
            <div style="height: 320px; position: relative;">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Sales Breakdown Table Card -->
        <div class="table-card">
            <h5 class="fw-bold text-muted mb-4"><i class="fas fa-list text-warning me-2"></i> Granular Sales Breakdown</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(0, 0, 0, 0.08); color: #D4AF37;">
                            <th class="pb-3">Booking ID</th>
                            <th class="pb-3">Customer</th>
                            <th class="pb-3">Date & Time</th>
                            <th class="pb-3">Services</th>
                            <th class="pb-3">Staff</th>
                            <th class="pb-3 text-end">Amount</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-center">Payment</th>
                            <th class="pb-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td class="fw-bold">#{{ $booking->id }}</td>
                            <td>
                                <div>
                                    <span class="fw-semibold d-block text-dark">{{ $booking->full_name }}</span>
                                    <span class="text-muted small">{{ $booking->email ?: $booking->phone }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="fw-semibold d-block">{{ date('M j, Y', strtotime($booking->appointment_date)) }}</span>
                                    <span class="text-muted small">{{ date('g:i A', strtotime($booking->appointment_time)) }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if($booking->bookingServices->isNotEmpty())
                                        @foreach($booking->bookingServices as $bs)
                                            <span class="badge bg-light text-dark border me-1 mb-1">{{ $bs->service->name ?? 'N/A' }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-light text-dark border">{{ $booking->service->name ?? 'N/A' }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if($booking->bookingServices->isNotEmpty())
                                        @foreach($booking->bookingServices->unique('staff_id') as $bs)
                                            <span class="text-dark small d-block"><i class="fas fa-user-circle text-muted me-1"></i>{{ $bs->staff->name ?? 'N/A' }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-dark small"><i class="fas fa-user-circle text-muted me-1"></i>{{ $booking->staff->name ?? 'N/A' }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end fw-bold text-dark">₹{{ number_format($booking->total_price, 2) }}</td>
                            <td class="text-center">
                                <span class="status-badge status-{{ $booking->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="payment-badge payment-{{ $booking->payment_status }}">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-outline-primary btn-action text-primary me-1" title="View Booking Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($booking->status === 'closed' || $booking->status === 'completed')
                                <a href="{{ isset($currentSalon) ? route('salon.booking.invoice', ['salon' => $currentSalon->slug, 'id' => $booking->id]) : route('booking.invoice', $booking->id) }}" target="_blank" class="btn btn-outline-warning btn-action text-warning" title="View Invoice">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No sales matching filters found.</td>
                        </tr>
                        @endempty
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>

    @endsection

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesTrendChart').getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(212, 175, 55, 0.4)');
            gradient.addColorStop(1, 'rgba(212, 175, 55, 0.0)');

            const labels = @json($chartLabels);
            const values = @json($chartValues);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Sales (₹)',
                        data: values,
                        borderColor: '#D4AF37',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#D4AF37',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: ₹' + context.parsed.y.toLocaleString('en-IN', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#777',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                color: '#777',
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return '₹' + value.toLocaleString('en-IN');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
