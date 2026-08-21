<x-admin-layout>
    @section('title', 'Staff Dashboard')

    @push('styles')
    <style>
        .staff-dashboard {
            padding: 20px;
        }

        .welcome-card {
            background: linear-gradient(135deg, #fdfaf2 0%, #f7edd4 100%);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
        }

        .welcome-card h1 {
            color: #bfa13d;
            font-weight: 700;
        }

        .booking-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }

        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -1px rgba(0, 0, 0, 0.04);
            border-color: #00A3B1;
        }

        .booking-card-header {
            background-color: #fcfcfc;
            border-bottom: 1px solid #f1f3f5;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .booking-card-body {
            padding: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            color: #4b5563;
        }

        .detail-item i {
            width: 24px;
            color: #00A3B1;
            font-size: 1.1rem;
        }

        .badge-assigned {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .badge-in-progress {
            background-color: #fef3c7;
            color: #b45309;
        }

        .badge-completed {
            background-color: #dcfce7;
            color: #15803d;
        }

        .action-btn {
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 6px;
            transition: all 0.2s;
        }
    </style>
    @endpush

    @section('content')
    <div class="staff-dashboard">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <span class="text-muted small uppercase fw-bold">Welcome Back</span>
            <h1 class="h2 mb-1">{{ Auth::user()->name }}</h1>
            <p class="text-muted mb-0">Here is your schedule and assigned services for today.</p>
        </div>

        @can('create_bookings')
        <div class="mb-4">
            <a href="{{ isset($currentSalon) ? route('admin.bookings.create', ['salon' => $currentSalon->slug]) : route('admin.bookings.create') }}" class="btn btn-warning text-dark fw-bold">
                <i class="fas fa-plus-circle me-2"></i>Book New Appointment
            </a>
        </div>
        @endcan

        <!-- Bookings Section -->
        <h3 class="h4 mb-4 fw-bold text-dark"><i class="fas fa-calendar-alt text-warning me-2"></i> Your Assigned Work</h3>

        <div class="row">
            <div class="col-12">
                @php
                    $groupedBookings = $assignedServices->groupBy('booking_id');
                @endphp
                @forelse($groupedBookings as $bookingId => $services)
                    @php
                        $firstService = $services->first();
                        $booking = $firstService->booking;
                    @endphp
                    <div class="booking-card">
                        <div class="booking-card-header">
                            <div>
                                <span class="fw-bold text-muted me-2">Booking ID:</span>
                                <span class="badge bg-secondary">#{{ $booking->id }}</span>
                            </div>
                            <div>
                                @if($booking->status === 'assigned')
                                    <span class="badge badge-assigned px-3 py-2 fw-semibold">Assigned</span>
                                @elseif($booking->status === 'in_progress')
                                    <span class="badge badge-in-progress px-3 py-2 fw-semibold">In Progress</span>
                                @elseif($booking->status === 'completed' || $booking->status === 'closed')
                                    <span class="badge badge-completed px-3 py-2 fw-semibold">Completed / Closed</span>
                                @endif
                            </div>
                        </div>
                        <div class="booking-card-body">
                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <div class="detail-item">
                                        <i class="fas fa-user"></i>
                                        <div>
                                            <span class="text-muted small d-block">Customer</span>
                                            <strong>{{ $booking->full_name }}</strong>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <div>
                                            <span class="text-muted small d-block">Schedule</span>
                                            <strong>{{ date('M j, Y', strtotime($booking->appointment_date)) }} at {{ date('g:i A', strtotime($booking->appointment_time)) }}</strong>
                                        </div>
                                    </div>
                                    @if($booking->special_requirements)
                                        <div class="detail-item mt-2 bg-light p-2 rounded mb-3">
                                            <i class="fas fa-exclamation-circle text-danger"></i>
                                            <div>
                                                <span class="text-muted small d-block">Notes / Allergies</span>
                                                <span class="text-danger fw-semibold">{{ $booking->special_requirements }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-4">
                                        @if($booking->status === 'assigned')
                                            <form action="{{ route('admin.bookings.all-services.start', $booking->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-warning action-btn text-dark">
                                                    <i class="fas fa-play me-1"></i> Start Booking
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'in_progress')
                                            <form action="{{ route('admin.bookings.all-services.complete', $booking->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success action-btn text-white">
                                                    <i class="fas fa-check me-1"></i> Close Booking
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'closed')
                                            <button class="btn btn-outline-success action-btn" disabled>
                                                <i class="fas fa-check-double me-1"></i> Booking Closed
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                                    <h5 class="fw-bold text-muted mb-3"><i class="fas fa-cut text-warning me-2"></i> Services Assigned</h5>
                                    @foreach($services as $bs)
                                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                            <div>
                                                <span class="fw-semibold">{{ $bs->service->name }}</span>
                                                <span class="text-muted small d-block">{{ $bs->service->duration }} mins</span>
                                            </div>
                                            <div>
                                                @if($bs->status === 'assigned')
                                                    <span class="badge badge-assigned px-2 py-1">Assigned</span>
                                                @elseif($bs->status === 'in_progress')
                                                    <span class="badge badge-in-progress px-2 py-1">In Progress</span>
                                                @elseif($bs->status === 'completed')
                                                    <span class="badge badge-completed px-2 py-1">Completed</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info py-4 text-center">
                        <i class="fas fa-info-circle mb-2" style="font-size: 1.5rem;"></i>
                        <p class="mb-0">No active services assigned to you currently.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @endsection
</x-admin-layout>
