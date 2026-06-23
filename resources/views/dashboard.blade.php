<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <!-- Add Bootstrap CSS if not already included in the layout -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-image {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 35px;
            height: 35px;
            background: #D4AF37;
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-btn:hover {
            background: #E6B800;
            transform: scale(1.1);
        }

        /* Dark theme input styles */
        .form-control {
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.12);
            border-color: #D4AF37;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
            color: #fff;
        }

        .form-control:disabled {
            background-color: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-check-input {
            background-color: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.25);
        }

        .form-check-input:checked {
            background-color: #D4AF37;
            border-color: #D4AF37;
        }

        .tax-billing-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .tax-billing-title {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: #fff;
            font-weight: 700;
        }

        .tax-billing-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #D4AF37;
            display: inline-block;
        }

        .tax-billing-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .profile-header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-verify-gst {
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
            padding: 0.7rem 1.1rem;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-verify-gst:hover {
            border-color: #D4AF37;
            color: #D4AF37;
        }

        .btn-save {
            background-color: var(--primary-color) !important;
            color: var(--dark-color) !important;
            border: 2px solid var(--primary-color) !important;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            background-color: transparent !important;
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            transform: translateY(-2px);
        }

        @media (max-width: 575.98px) {
            .dashboard-header {
                align-items: stretch;
                flex-direction: column;
                gap: 1rem;
            }

            .profile-header-actions {
                width: 100%;
            }

            .profile-header-actions .btn-save {
                width: 100%;
            }

            .tax-billing-header {
                align-items: stretch;
                flex-direction: column;
            }

            .tax-billing-actions {
                justify-content: space-between;
            }
        }

        .form-group label {
            color: #fff;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        /* Stats cards styling */
        .action-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            height: 100%;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: all 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            background: #D4AF37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .action-icon i {
            font-size: 1.5rem;
            color: #fff;
        }

        .action-card h4 {
            font-size: 1.5rem;
            color: #fff;
            margin: 0.5rem 0;
            font-weight: 600;
        }

        .action-card p {
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
            font-size: 0.9rem;
        }

        /* Status badges */
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 8px;
        }

        .status.pending {
            background: #fff3e0;
            color: #e65100;
        }

        .status.confirmed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status.cancelled {
            background: #ffebee;
            color: #c62828;
        }

        .status.completed {
            background: #e3f2fd;
            color: #1565c0;
        }

        /* Action buttons */
        .btn-cancel {
            background: #ffebee;
            color: #c62828;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #ef5350;
            color: white;
        }

        .btn-reschedule {
            background: #e3f2fd;
            color: #1565c0;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn-reschedule:hover {
            background: #1e88e5;
            color: white;
        }

        .btn-review {
            background: #e8f5e9;
            color: #2e7d32;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn-review:hover {
            background: #43a047;
            color: white;
        }
        /* Locked feature overlay */
        .locked-section {
            position: relative;
        }

        .locked-overlay {
            position: absolute;
            inset: 0;
            background: rgba(10,10,15,0.82);
            backdrop-filter: blur(4px);
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            gap: 0.75rem;
        }

        .locked-overlay i {
            font-size: 2.5rem;
            color: rgba(212,175,55,0.6);
        }

        .locked-overlay p {
            color: rgba(255,255,255,0.6);
            font-size: 0.95rem;
            margin: 0;
            text-align: center;
            padding: 0 1rem;
        }

        .locked-overlay .btn-unlock {
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #000;
            font-weight: 700;
            padding: 0.55rem 1.5rem;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            font-size: 0.9rem;
        }

        /* Subscription notice banner */
        .subscription-notice {
            background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(239,68,68,0.08));
            border: 1px solid rgba(245,158,11,0.35);
            border-radius: 14px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .subscription-notice .notice-icon {
            width: 42px;
            height: 42px;
            background: rgba(245,158,11,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
            color: #fbbf24;
        }

        .subscription-notice .notice-text {
            flex: 1;
            color: rgba(255,255,255,0.85);
            font-size: 0.9rem;
        }

        .subscription-notice .notice-text strong {
            color: #fbbf24;
        }

        .subscription-notice .btn-subscribe {
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #000;
            font-weight: 700;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            white-space: nowrap;
            font-size: 0.85rem;
        }

        /* Trial expiry badge */
        .trial-expiry-badge {
            background: rgba(34,197,94,0.12);
            border: 1px solid rgba(34,197,94,0.25);
            border-radius: 50px;
            padding: 0.3rem 0.9rem;
            font-size: 0.8rem;
            color: #4ade80;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.5rem;
        }

        .trial-expiry-badge.expiring {
            background: rgba(239,68,68,0.12);
            border-color: rgba(239,68,68,0.25);
            color: #f87171;
        }
    </style>
    @endpush

    @push('scripts')
    <!-- Add jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle booking cancellation
            $('.btn-cancel').click(function() {
                if (confirm('Are you sure you want to cancel this booking?')) {
                    const appointmentItem = $(this).closest('.appointment-item');
                    const bookingId = appointmentItem.data('booking-id');

                    $.ajax({
                        url: `/bookings/${bookingId}/cancel`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            window.location.reload();
                        },
                        error: function(error) {
                            alert('Failed to cancel booking. Please try again.');
                        }
                    });
                }
            });

            // Handle reschedule button clicks
            $('.btn-reschedule').on('click', function(e) {
                e.preventDefault();
                const appointmentItem = $(this).closest('.appointment-item');
                const bookingId = appointmentItem.data('booking-id');

                if (confirm('Do you want to extend your appointment by 1 day?')) {
                    $.ajax({
                        url: `/bookings/${bookingId}/reschedule`,
                        type: 'POST',
                        data: {
                            extend_days: 1
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            window.location.reload();
                        },
                        error: function(error) {
                            if (error.responseJSON && error.responseJSON.message) {
                                alert(error.responseJSON.message);
                            } else {
                                alert('Failed to reschedule booking. Please try again.');
                            }
                        }
                    });
                }
            });
        });
    </script>
    @endpush



    @section('content')
    @php
    $showProfileTab = session('status') === 'profile-updated' || session('status') === 'password-updated' || $errors->any() || $errors->updatePassword->any();
    @endphp

    <!-- Dashboard Section -->
    <section class="dashboard-section">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="dashboard-sidebar">
                        <div class="text-center user-profile">
                            <form id="profile-photo-form" action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('POST')
                                <div class="profile-image">
                                    <img
                                        src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : asset('assets/img/default-avatar.jpg') }}"
                                        alt="Profile"
                                        class="img-fluid rounded-circle" />
                                    <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/*" onchange="this.form.submit()">
                                    <button type="button" class="upload-btn" title="Change Photo" onclick="document.getElementById('profile_photo').click();">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </div>
                            </form>
                            @if(session('success'))
                            <div class="mt-2 alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            @if($errors->any())
                            <div class="mt-2 alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                            @endif
                            <h4 class="mt-3">{{ $user->name }}</h4>
                            <p class="member-since">Member since {{ $user->created_at->format('F Y') }}</p>
                        </div>
                        <nav class="dashboard-nav">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a
                                        class="nav-link {{ $showProfileTab ? '' : 'active' }}"
                                        href="#overview"
                                        data-bs-toggle="tab">
                                        <i class="fas fa-th-large"></i> Dashboard Overview
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a
                                        class="nav-link"
                                        href="#appointments"
                                        data-bs-toggle="tab">
                                        <i class="fas fa-calendar-alt"></i> My Appointments
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a
                                        class="nav-link"
                                        href="#inventory"
                                        data-bs-toggle="tab">
                                        <i class="fas fa-boxes"></i> My Created Inventory
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $showProfileTab ? 'active' : '' }}" href="#profile" data-bs-toggle="tab">
                                        <i class="fas fa-user-edit"></i> Profile Settings
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <!-- Subscription Notice Banner -->
                    @if(!$hasActivePlan && $noticeMessage)
                    <div class="subscription-notice">
                        <div class="notice-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="notice-text">
                            {{ $noticeMessage }}
                        </div>
                        <a href="{{ route('subscription.index') }}" class="btn-subscribe">
                            <i class="fas fa-crown me-1"></i>Subscribe
                        </a>
                    </div>
                    @endif

                    @if($activeSubscription && $activeSubscription->days_remaining <= 7 && $activeSubscription->days_remaining > 0)
                    <div class="subscription-notice" style="background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(139,92,246,0.08)); border-color: rgba(59,130,246,0.3);">
                        <div class="notice-icon" style="background: rgba(59,130,246,0.2); color: #60a5fa;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="notice-text">
                            Your <strong style="color:#60a5fa;">{{ $activeSubscription->plan->name }}</strong> plan expires in
                            <strong style="color:#60a5fa;">{{ $activeSubscription->days_remaining }} day(s)</strong>.
                            Renew to keep full access.
                        </div>
                        <a href="{{ route('subscription.index') }}" class="btn-subscribe" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff;">
                            <i class="fas fa-sync me-1"></i>Renew
                        </a>
                    </div>
                    @endif

                    <div class="tab-content">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade {{ $showProfileTab ? '' : 'show active' }}" id="overview">
                            <div class="dashboard-header">
                                <h2>My Dashboard</h2>
                                @if(!in_array('booking', $limitedFeatures ?? []))
                                <a href="{{ route('services') }}" class="btn btn-book-appointment">
                                    <i class="fas fa-plus"></i> Book New Appointment
                                </a>
                                @else
                                <a href="{{ route('subscription.index') }}" class="btn btn-book-appointment" style="background: rgba(255,255,255,0.08); border: 1px dashed rgba(255,255,255,0.2);">
                                    <i class="fas fa-lock"></i> Subscribe to Book
                                </a>
                                @endif
                            </div>

                            <!-- Quick Actions -->
                            <div class="quick-actions">
                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-3">
                                        <div class="action-card">
                                            <div class="action-icon" style="background: #2196F3;">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                            <h4>{{ $upcomingAppointments->count() }}</h4>
                                            <p>Upcoming Appointments</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="action-card">
                                            <div class="action-icon" style="background: #9C27B0;">
                                                <i class="fas fa-history"></i>
                                            </div>
                                            <h4>{{ $pastAppointments->count() }}</h4>
                                            <p>Past Appointments</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="action-card">
                                            <div class="action-icon" style="background: #4CAF50;">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <h4>{{ $completedSessions }}</h4>
                                            <p>Completed Sessions</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="action-card">
                                            <div class="action-icon" style="background: #FF9800;">
                                                <i class="fas fa-coins"></i>
                                            </div>
                                            <h4>Rs. {{ number_format($totalSpent, 2) }}</h4>
                                            <p>Total Spent</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Upcoming Appointments -->
                            <div class="mt-4 section-card locked-section">
                                @if(in_array('appointments', $limitedFeatures ?? []))
                                <div class="locked-overlay">
                                    <i class="fas fa-lock"></i>
                                    <p>Appointment history is locked.<br>Subscribe to view all your appointments.</p>
                                    <a href="{{ route('subscription.index') }}" class="btn-unlock">Unlock Now</a>
                                </div>
                                @endif
                                <div class="card-header">
                                    <h3>Upcoming Appointments</h3>
                                    <a
                                        href="#appointments"
                                        class="view-all"
                                        data-bs-toggle="tab">View All</a>
                                </div>
                                <div class="appointment-list">
                                    @forelse($upcomingAppointments as $appointment)
                                    <div class="appointment-item" data-booking-id="{{ $appointment->id }}">
                                        <div class="appointment-date">
                                            <span class="date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</span>
                                            <span class="month">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</span>
                                        </div>
                                        <div class="appointment-info">
                                            <h4>{{ $appointment->service->name }}</h4>
                                            <p><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                                            <p><i class="fas fa-money-bill"></i> Rs. {{ number_format($appointment->total_price, 2) }}</p>
                                            <span class="status {{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                                        </div>
                                        <div class="appointment-actions">
                                            @if($appointment->status === 'pending')
                                            <button class="btn btn-cancel">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                            @elseif($appointment->status === 'confirmed')
                                            <button class="btn btn-reschedule">
                                                <i class="fas fa-calendar-alt"></i> Reschedule
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @empty
                                    <div class="py-4 text-center">
                                        <p>No upcoming appointments</p>
                                        @if(!in_array('booking', $limitedFeatures ?? []))
                                        <a href="{{ route('services') }}" class="mt-2 btn btn-primary">Book Now</a>
                                        @endif
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Appointments Tab -->
                        <div class="tab-pane fade" id="appointments">
                            <div class="dashboard-header">
                                <h2>My Appointments</h2>
                                <div class="appointment-filters">
                                    <button class="btn btn-filter active">All</button>
                                    <button class="btn btn-filter">Upcoming</button>
                                    <button class="btn btn-filter">Past</button>
                                    <button class="btn btn-filter">Cancelled</button>
                                </div>
                            </div>
                            <div class="appointments-timeline locked-section">
                                @if(in_array('appointments', $limitedFeatures ?? []))
                                <div class="locked-overlay" style="min-height: 200px;">
                                    <i class="fas fa-lock"></i>
                                    <p>Full appointment history is locked.<br>Subscribe to view all your appointments.</p>
                                    <a href="{{ route('subscription.index') }}" class="btn-unlock">Unlock Now</a>
                                </div>
                                @endif

                                <!-- Upcoming Appointments -->
                                <div class="mb-4 timeline-section">
                                    <h3>Upcoming Appointments</h3>
                                    @forelse($upcomingAppointments as $appointment)
                                    <div class="appointment-item" data-booking-id="{{ $appointment->id }}">
                                        <div class="appointment-date">
                                            <span class="date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</span>
                                            <span class="month">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</span>
                                        </div>
                                        <div class="appointment-info">
                                            <h4>{{ $appointment->service->name }}</h4>
                                            <p><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                                            <p><i class="fas fa-money-bill"></i> Rs. {{ number_format($appointment->total_price, 2) }}</p>
                                            <span class="status {{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                                        </div>
                                        <div class="appointment-actions">
                                            @if($appointment->status === 'pending')
                                            <button class="btn btn-cancel">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                            @elseif($appointment->status === 'confirmed')
                                            <button class="btn btn-reschedule">
                                                <i class="fas fa-calendar-alt"></i> Reschedule
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @empty
                                    <div class="py-4 text-center">
                                        <p>No upcoming appointments</p>
                                        <a href="{{ route('services') }}" class="mt-2 btn btn-primary">Book Now</a>
                                    </div>
                                    @endforelse
                                </div>

                                <!-- Past Appointments -->
                                <div class="timeline-section">
                                    <h3>Past Appointments</h3>
                                    @forelse($pastAppointments as $appointment)
                                    <div class="appointment-item {{ $appointment->status }}" data-booking-id="{{ $appointment->id }}">
                                        <div class="appointment-date">
                                            <span class="date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</span>
                                            <span class="month">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</span>
                                        </div>
                                        <div class="appointment-info">
                                            <h4>{{ $appointment->service->name }}</h4>
                                            <p><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                                            <p><i class="fas fa-money-bill"></i> Rs. {{ number_format($appointment->total_price, 2) }}</p>
                                            <span class="status {{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                                        </div>
                                        <div class="appointment-actions">
                                            @if($appointment->status === 'completed')
                                            @if(!$appointment->feedback)
                                            <a href="{{ route('feedback.create', $appointment->id) }}"
                                                class="btn btn-primary btn-sm me-2">
                                                <i class="fas fa-star"></i> Write Review
                                            </a>
                                            @else
                                            <span class="text-success"><i class="fas fa-check"></i> Review Submitted</span>
                                            @endif
                                            <button class="btn btn-secondary btn-sm btn-rebook">Book Again</button>
                                            @endif
                                        </div>
                                    </div>
                                    @empty
                                    <div class="py-4 text-center">
                                        <p>No past appointments</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Profile Tab -->
                        <div class="tab-pane fade {{ $showProfileTab ? 'show active' : '' }}" id="profile">
                            <div class="dashboard-header">
                                <h2>Profile Settings</h2>
                                <div class="profile-header-actions">
                                    <button type="submit" form="profileForm" class="btn btn-save">
                                        Save Profile
                                    </button>
                                </div>
                            </div>
                            @if (session('status') === 'profile-updated')
                            <div class="alert alert-success">
                                Profile details updated successfully.
                            </div>
                            @endif
                            <form id="profileForm" class="profile-form" method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PATCH')

                                <div class="section-card">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="firstName">Name</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="firstName"
                                                    name="name"
                                                    value="{{ old('name', $user->name) }}"
                                                    required />
                                                @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lastName">Last Name</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('last_name') is-invalid @enderror"
                                                    id="lastName"
                                                    name="last_name"
                                                    value="{{ old('last_name', $user->last_name) }}" />
                                                @error('last_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email Address</label>
                                                <input
                                                    type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="email"
                                                    name="email"
                                                    value="{{ old('email', $user->email) }}"
                                                    required />
                                                @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone Number</label>
                                                <input
                                                    type="tel"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    id="phone"
                                                    name="phone"
                                                    value="{{ old('phone', $user->phone) }}" />
                                                @error('phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-save">
                                                Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tax and Billing Section -->
                                <div class="mt-4 section-card">
                                    <div class="tax-billing-header">
                                        <div class="tax-billing-title">
                                            <span class="tax-billing-dot"></span>
                                            <span>Tax & Billing</span>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-10">
                                            <input
                                                type="text"
                                                class="form-control @error('gst_number') is-invalid @enderror"
                                                id="gstNumber"
                                                name="gst_number"
                                                placeholder="GST number"
                                                value="{{ old('gst_number', $user->gst_number) }}" />
                                            @error('gst_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <button type="button" class="btn btn-verify-gst">Verify</button>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="hasNoGst"
                                                    name="has_no_gst"
                                                    value="1"
                                                    {{ old('has_no_gst', $user->has_no_gst) ? 'checked' : '' }}>
                                                <label class="form-check-label text-white" for="hasNoGst">
                                                    I don't have a GST number
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <input
                                                type="text"
                                                class="form-control @error('billing_name') is-invalid @enderror"
                                                id="billingName"
                                                name="billing_name"
                                                placeholder="Billing name"
                                                value="{{ old('billing_name', $user->billing_name) }}" />
                                            @error('billing_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <input
                                                type="text"
                                                class="form-control @error('trade_name') is-invalid @enderror"
                                                id="tradeName"
                                                name="trade_name"
                                                placeholder="Trade name"
                                                value="{{ old('trade_name', $user->trade_name) }}" />
                                            @error('trade_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <textarea
                                                class="form-control @error('billing_address') is-invalid @enderror"
                                                id="billingAddress"
                                                name="billing_address"
                                                rows="4"
                                                placeholder="Billing address">{{ old('billing_address', $user->billing_address) }}</textarea>
                                            @error('billing_address')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-save">
                                                Save Section
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- Password Change Section -->
                            <div class="mt-4 section-card">
                                <h3>Change Password</h3>
                                @if (session('status') === 'password-updated')
                                <div class="alert alert-success">
                                    Password updated successfully.
                                </div>
                                @endif
                                <form id="passwordForm" class="password-form" method="POST" action="{{ route('password.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="currentPassword">Current Password</label>
                                                <input
                                                    type="password"
                                                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                                    id="currentPassword"
                                                    name="current_password"
                                                    required />
                                                @error('current_password', 'updatePassword')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="newPassword">New Password</label>
                                                <input
                                                    type="password"
                                                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                                    id="newPassword"
                                                    name="password"
                                                    required />
                                                @error('password', 'updatePassword')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="confirmNewPassword">Confirm New Password</label>
                                                <input
                                                    type="password"
                                                    class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                                    id="confirmNewPassword"
                                                    name="password_confirmation"
                                                    required />
                                                @error('password_confirmation', 'updatePassword')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-save">
                                                Update Password
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Inventory Tab -->
                        <div class="tab-pane fade" id="inventory">
                            <div class="dashboard-header d-flex justify-content-between align-items-center">
                                <h2>My Created Inventory</h2>
                            </div>
                            
                            <div class="mt-4 section-card locked-section">
                                @if(in_array('inventory', $limitedFeatures ?? []))
                                <div class="locked-overlay">
                                    <i class="fas fa-lock"></i>
                                    <p>Inventory management is locked.<br>Subscribe to manage your inventory.</p>
                                    <a href="{{ route('subscription.index') }}" class="btn-unlock">Unlock Now</a>
                                </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table" style="background: transparent; color: #fff;">
                                        <thead>
                                            <tr style="border-bottom: 2px solid rgba(255, 255, 255, 0.1); color: #D4AF37;">
                                                <th class="py-3">Item Name</th>
                                                <th class="py-3">SKU</th>
                                                <th class="py-3">Quantity</th>
                                                <th class="py-3">Status</th>
                                                <th class="py-3">Price</th>
                                                <th class="py-3">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @forelse($user->createdInventories as $item)
                                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); vertical-align: middle;">
                                                <td class="py-3 font-weight-bold" style="color: #fff;">{{ $item->item_name }}</td>
                                                <td class="py-3"><code>{{ $item->sku ?? '-' }}</code></td>
                                                <td class="py-3" style="color: #fff;">{{ $item->quantity }}</td>
                                                <td class="py-3">
                                                    @if($item->quantity == 0)
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    @elseif($item->quantity <= $item->min_quantity)
                                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                                    @else
                                                        <span class="badge bg-success">In Stock</span>
                                                    @endif
                                                </td>
                                                <td class="py-3" style="color: #fff;">Rs. {{ number_format($item->price, 2) }}</td>
                                                <td class="py-3 text-truncate" style="max-width: 250px; color: rgba(255,255,255,0.7);">{{ $item->description ?? '-' }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <i class="fas fa-box-open fa-2x mb-3" style="color: #D4AF37; opacity: 0.5;"></i>
                                                    <p class="mb-0">You haven't created any inventory items yet.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
</x-app-layout>
