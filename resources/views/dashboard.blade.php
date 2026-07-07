<x-admin-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <style>
        .profile-image {
            position: relative;
            width: 90px;
            height: 90px;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #D4AF37;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar-fallback {
            width: 100%;
            height: 100%;
            background: #f8f9fa;
            color: #d4af37;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .upload-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 28px;
            height: 28px;
            background: #D4AF37;
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-btn:hover {
            background: #E6B800;
            transform: scale(1.1);
        }

        /* Light theme input styles */
        .form-control:not(textarea),
        .form-select {
            background-color: #ffffff !important;
            border: 1px solid #ced4da !important;
            color: #212529 !important;
            padding: 0.20rem 0.55rem;
            font-size: 0.95rem;
            height: 30px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        textarea.form-control {
            background-color: #ffffff !important;
            border: 1px solid #ced4da !important;
            color: #212529 !important;
            padding: 0.375rem 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #ffffff !important;
            border-color: #D4AF37 !important;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25) !important;
            color: #212529 !important;
            outline: none;
        }

        .form-control:disabled {
            background-color: #e9ecef !important;
            border-color: #ced4da !important;
            color: #6c757d !important;
        }

        .form-control::placeholder {
            color: #6c757d !important;
        }

        .form-check-input {
            background-color: #ffffff !important;
            border-color: rgba(0, 0, 0, 0.25) !important;
        }

        .form-check-input:checked {
            background-color: #D4AF37 !important;
            border-color: #D4AF37 !important;
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
            color: #2C2C2C !important;
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
            border: 1px solid #D4AF37 !important;
            color: #D4AF37 !important;
            background: transparent !important;
            padding: 0.7rem 1.1rem;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-verify-gst:hover {
            background: #D4AF37 !important;
            color: #fff !important;
        }

        .btn-save {
            background-color: #D4AF37 !important;
            color: #2c2c2c !important;
            border: 2px solid #D4AF37 !important;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            background-color: transparent !important;
            color: #D4AF37 !important;
            border-color: #D4AF37 !important;
            transform: translateY(-2px);
        }

        .form-group label {
            color: #2C2C2C !important;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        /* Light stats cards styling */
        .action-card {
            background: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
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
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
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
            font-size: 2.2rem !important;
            color: #D4AF37 !important;
            margin: 0.5rem 0;
            font-weight: 700;
        }

        .action-card p {
            color: #6c757d !important;
            margin: 0;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Light section cards styling */
        .section-card {
            background: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card-header h3 {
            color: #2C2C2C !important;
            font-weight: 600;
        }

        /* Light appointment items */
        .appointment-item {
            background: #fdfdfd !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            color: #2C2C2C !important;
        }

        .appointment-item:hover {
            background: #f8f9fa !important;
        }

        .appointment-info h4 {
            color: #2C2C2C !important;
        }

        .appointment-info p {
            color: #6c757d !important;
        }

        .appointment-date .month {
            color: #6c757d !important;
        }

        .dashboard-header h2 {
            color: #2C2C2C !important;
            font-weight: 600;
        }

        /* Status colors */
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
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
            font-weight: 500;
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
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-reschedule:hover {
            background: #1e88e5;
            color: white;
        }

        .btn-gold {
            background-color: #D4AF37 !important;
            color: #2c2c2c !important;
            border: 1px solid #D4AF37 !important;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background-color: #2c2c2c !important;
            border-color: #2c2c2c !important;
            color: #D4AF37 !important;
        }

        .locked-section {
            position: relative;
        }

        .locked-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.88);
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
            color: rgba(212, 175, 55, 0.8);
        }

        .locked-overlay p {
            color: #2c2c2c;
            font-size: 0.95rem;
            margin: 0;
            text-align: center;
            padding: 0 1rem;
            font-weight: 500;
        }

        .locked-overlay .btn-unlock {
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #fff;
            font-weight: 700;
            padding: 0.55rem 1.5rem;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .subscription-notice {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15), rgba(212, 175, 55, 0.05));
            border: 1px solid rgba(212, 175, 55, 0.35);
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
            background: rgba(212, 175, 55, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
            color: #B8860B;
        }

        .subscription-notice .notice-text {
            flex: 1;
            color: #2c2c2c;
            font-size: 0.9rem;
        }

        .subscription-notice .notice-text strong {
            color: #B8860B;
        }

        .subscription-notice .btn-subscribe {
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #fff;
            font-weight: 700;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            white-space: nowrap;
            font-size: 0.85rem;
        }

        .trial-expiry-badge {
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.25);
            border-radius: 50px;
            padding: 0.3rem 0.9rem;
            font-size: 0.8rem;
            color: #16a34a;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        .btn-book-appointment {
            background: #D4AF37;
            color: #2c2c2c;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            border: 2px solid #D4AF37;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-book-appointment:hover {
            background: transparent;
            color: #D4AF37;
            transform: translateY(-2px);
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

        function detectLocation() {
            const btn = document.getElementById('btnDetectLocation');
            const input = document.getElementById('locationInput');
            const originalText = btn.innerHTML;

            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.display_name) {
                                input.value = data.display_name;
                                document.getElementById('latitudeInput').value = lat;
                                document.getElementById('longitudeInput').value = lon;
                            } else {
                                alert('Could not resolve address for your coordinates.');
                            }
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        })
                        .catch(error => {
                            console.error('Error reverse geocoding:', error);
                            alert('Error getting address details.');
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        });
                },
                function (error) {
                    console.error('Geolocation error:', error);
                    alert('Unable to retrieve your location. Please ensure location permissions are granted.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        function handleSalonTypeChange(selectElement) {
            var customInput = document.getElementById('salonTypeCustom');
            var customGroup = document.getElementById('salonTypeCustomGroup');
            if (selectElement.value === 'custom') {
                customGroup.classList.remove('d-none');
                customInput.setAttribute('name', 'salon_type');
                selectElement.removeAttribute('name');
                customInput.value = '';
                customInput.focus();
            } else {
                customGroup.classList.add('d-none');
                customInput.removeAttribute('name');
                selectElement.setAttribute('name', 'salon_type');
            }
        }

        function saveCustomSalonType() {
            var selectElement = document.getElementById('salonTypeSelect');
            var customInput = document.getElementById('salonTypeCustom');
            var customGroup = document.getElementById('salonTypeCustomGroup');
            var customVal = customInput.value.trim();

            if (customVal === '') {
                alert('Please enter a custom salon type.');
                return;
            }

            // Check if option already exists
            var exists = false;
            for (var i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value.toLowerCase() === customVal.toLowerCase()) {
                    selectElement.selectedIndex = i;
                    exists = true;
                    break;
                }
            }

            if (!exists) {
                // Create new option
                var newOption = document.createElement('option');
                newOption.value = customVal;
                newOption.text = customVal;
                
                // Insert before the last option (+ Add Custom Type...)
                selectElement.add(newOption, selectElement.options[selectElement.options.length - 1]);
                selectElement.value = customVal;
            }

            // Switch name attribute back to select element
            selectElement.setAttribute('name', 'salon_type');
            customInput.removeAttribute('name');
            
            // Hide the custom input group
            customGroup.classList.add('d-none');
        }

        function handleSalonModelChange(selectElement) {
            var franchiseeGroup = document.getElementById('franchiseeGroup');
            var franchiseeSelect = document.getElementById('franchiseeSelect');
            var franchiseeCustom = document.getElementById('franchiseeCustom');
            var franchiseeCustomGroup = document.getElementById('franchiseeCustomGroup');

            if (selectElement.value === 'Franchisee') {
                franchiseeGroup.classList.remove('d-none');
                franchiseeSelect.setAttribute('name', 'franchisee_name');
            } else {
                franchiseeGroup.classList.add('d-none');
                franchiseeSelect.removeAttribute('name');
                franchiseeCustom.removeAttribute('name');
                franchiseeSelect.value = '';
                franchiseeCustom.value = '';
                franchiseeCustomGroup.classList.add('d-none');
            }
        }

        function handleFranchiseeChange(selectElement) {
            var customInput = document.getElementById('franchiseeCustom');
            var customGroup = document.getElementById('franchiseeCustomGroup');
            if (selectElement.value === 'custom') {
                customGroup.classList.remove('d-none');
                customInput.setAttribute('name', 'franchisee_name');
                selectElement.removeAttribute('name');
                customInput.value = '';
                customInput.focus();
            } else {
                customGroup.classList.add('d-none');
                customInput.removeAttribute('name');
                selectElement.setAttribute('name', 'franchisee_name');
            }
        }

        function saveCustomFranchisee() {
            var selectElement = document.getElementById('franchiseeSelect');
            var customInput = document.getElementById('franchiseeCustom');
            var customGroup = document.getElementById('franchiseeCustomGroup');
            var customVal = customInput.value.trim();

            if (customVal === '') {
                alert('Please enter a franchisee name.');
                return;
            }

            var exists = false;
            for (var i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value.toLowerCase() === customVal.toLowerCase()) {
                    selectElement.selectedIndex = i;
                    exists = true;
                    break;
                }
            }

            if (!exists) {
                var newOption = document.createElement('option');
                newOption.value = customVal;
                newOption.text = customVal;
                selectElement.add(newOption, selectElement.options[selectElement.options.length - 1]);
                selectElement.value = customVal;
            }

            selectElement.setAttribute('name', 'franchisee_name');
            customInput.removeAttribute('name');
            customGroup.classList.add('d-none');
        }
    </script>
    @endpush



    @section('content')
    @php
    $activeTab = request()->query('tab', 'overview');
    if (session('status') === 'profile-updated' || session('status') === 'password-updated' || $errors->any() || $errors->updatePassword->any()) {
        $activeTab = 'profile';
    }
    @endphp

    <!-- Dashboard Section -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
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
                        <div class="tab-pane fade {{ $activeTab === 'overview' ? 'show active' : '' }}" id="overview">
                            <div class="dashboard-header">
                                <h2>My Dashboard</h2>
                                @if(!in_array('booking', $limitedFeatures ?? []))
                                <a href="{{ route('services') }}" class="btn btn-book-appointment">
                                    <i class="fas fa-plus"></i> Book New Appointment
                                </a>
                                @else
                                <a href="{{ route('subscription.index') }}" class="btn btn-book-appointment" style="background: rgba(0,0,0,0.03); border: 1px dashed rgba(0,0,0,0.15); color: #2C2C2C;">
                                    <i class="fas fa-lock text-muted"></i> Subscribe to Book
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
                                            @if($appointment->payment_status === 'paid')
                                            <a href="{{ route('booking.invoice', $appointment->id) }}" target="_blank" class="btn btn-outline-warning btn-sm me-2" style="border-radius: 20px; border-color: #D4AF37; color: #D4AF37; font-weight: 500; text-decoration: none; padding: 8px 16px;">
                                                <i class="fas fa-file-invoice me-1"></i> Invoice
                                            </a>
                                            @endif
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
                        <div class="tab-pane fade {{ $activeTab === 'appointments' ? 'show active' : '' }}" id="appointments">
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
                                            @if($appointment->payment_status === 'paid')
                                            <a href="{{ route('booking.invoice', $appointment->id) }}" target="_blank" class="btn btn-outline-warning btn-sm me-2" style="border-radius: 20px; border-color: #D4AF37; color: #D4AF37; font-weight: 500; text-decoration: none; padding: 8px 16px;">
                                                <i class="fas fa-file-invoice me-1"></i> Invoice
                                            </a>
                                            @endif
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
                                            @if($appointment->payment_status === 'paid')
                                            <a href="{{ route('booking.invoice', $appointment->id) }}" target="_blank" class="btn btn-outline-warning btn-sm me-2" style="border-radius: 20px; border-color: #D4AF37; color: #D4AF37; font-weight: 500; text-decoration: none; padding: 8px 16px;">
                                                <i class="fas fa-file-invoice me-1"></i> Invoice
                                            </a>
                                            @endif
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
                        <div class="tab-pane fade {{ $activeTab === 'profile' ? 'show active' : '' }}" id="profile">
                            <div class="dashboard-header">
                                <h2>Profile Settings</h2>
                            </div>
                            @if (session('status') === 'profile-updated')
                            <div class="alert alert-success">
                                Profile details updated successfully.
                            </div>
                            @endif

                             <div class="section-card py-3 mb-3 text-center">
                                <form id="profile-photo-form" action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')
                                    <div class="profile-image mb-2">
                                        @if($user->profile_photo)
                                            <img
                                                src="{{ Storage::url($user->profile_photo) }}"
                                                alt="Profile"
                                                class="img-fluid rounded-circle" />
                                        @else
                                            <div class="profile-avatar-fallback">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/*" onchange="this.form.submit()">
                                        <button type="button" class="upload-btn" title="Change Photo" onclick="document.getElementById('profile_photo').click();">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                </form>
                                @if(session('success') && session('status') !== 'profile-updated')
                                <div class="mt-2 alert alert-success">
                                    {{ session('success') }}
                                </div>
                                @endif
                                @if($errors->any() && !$errors->updatePassword->any() && !$errors->userDeletion->any() && session('status') !== 'profile-updated')
                                <div class="mt-2 alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                                @endif
                                <h5 class="mt-2 mb-1" style="color: #2C2C2C; font-weight: 600;">{{ $user->name }}</h5>
                                <p class="member-since text-muted mb-1" style="font-size: 0.85rem;">Member since {{ $user->created_at->format('F Y') }}</p>
                            </div>

                            <form id="profileForm" class="profile-form" method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PATCH')

                                <div class="section-card">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="salonName">Salon Name</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('salon_name') is-invalid @enderror"
                                                    id="salonName"
                                                    name="salon_name"
                                                    value="{{ old('salon_name', $user->salon_name) }}"
                                                    placeholder="e.g. Glamour Hair Studio"
                                                    required />
                                                @error('salon_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="salonTypeSelect">Salon Type</label>
                                                @php
                                                    $defaultTypes = ['Mens Salon', 'Female Salon', 'Unisex Salon', 'Makeup Studio', 'Academy + Salon', 'Academy'];
                                                    $currentType = old('salon_type', $user->salon_type);
                                                    $allTypes = $defaultTypes;
                                                    if ($currentType && !in_array($currentType, $defaultTypes)) {
                                                        $allTypes[] = $currentType;
                                                    }
                                                @endphp
                                                <select
                                                    class="form-select @error('salon_type') is-invalid @enderror"
                                                    id="salonTypeSelect"
                                                    name="salon_type"
                                                    onchange="handleSalonTypeChange(this)"
                                                    required>
                                                    <option value="" disabled {{ !$currentType ? 'selected' : '' }}>Select Salon Type</option>
                                                    @foreach($allTypes as $type)
                                                        <option value="{{ $type }}" {{ $currentType === $type ? 'selected' : '' }}>{{ $type }}</option>
                                                    @endforeach
                                                    <option value="custom">+ Add Custom Type...</option>
                                                </select>
                                                
                                                <div id="salonTypeCustomGroup" class="input-group mt-2 d-none">
                                                    <input
                                                        type="text"
                                                        class="form-control @error('salon_type') is-invalid @enderror"
                                                        id="salonTypeCustom"
                                                        placeholder="Enter custom salon type"
                                                        onkeypress="if(event.key === 'Enter') { event.preventDefault(); saveCustomSalonType(); }" />
                                                    <button type="button" class="btn btn-gold" id="btnSaveCustomType" onclick="saveCustomSalonType()" title="Save Custom Type">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </div>
                                                
                                                @error('salon_type')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="salonModelSelect">Salon Model</label>
                                                @php
                                                    $currentModel = old('salon_model', $user->salon_model);
                                                @endphp
                                                <select
                                                    class="form-select @error('salon_model') is-invalid @enderror"
                                                    id="salonModelSelect"
                                                    name="salon_model"
                                                    onchange="handleSalonModelChange(this)"
                                                    required>
                                                    <option value="" disabled {{ !$currentModel ? 'selected' : '' }}>Select</option>
                                                    <option value="Franchisee" {{ $currentModel === 'Franchisee' ? 'selected' : '' }}>Franchisee</option>
                                                    <option value="Self Owned" {{ $currentModel === 'Self Owned' ? 'selected' : '' }}>Self Owned</option>
                                                </select>
                                                @error('salon_model')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Franchisee Brand/Name Section, visible only when Salon Model is Franchisee -->
                                        @php
                                            $showFranchisee = $currentModel === 'Franchisee';
                                            $currentFranchisee = old('franchisee_name', $user->franchisee_name);
                                            
                                            // Fetch other franchisees registered in the system
                                            $dbFranchisees = \App\Models\User::where('salon_model', 'Franchisee')
                                                ->whereNotNull('franchisee_name')
                                                ->where('franchisee_name', '!=', '')
                                                ->distinct()
                                                ->pluck('franchisee_name')
                                                ->toArray();
                                            
                                            $allFranchisees = $dbFranchisees;
                                            if ($currentFranchisee && !in_array($currentFranchisee, $allFranchisees)) {
                                                $allFranchisees[] = $currentFranchisee;
                                            }
                                        @endphp
                                        <div class="col-md-3 {{ $showFranchisee ? '' : 'd-none' }}" id="franchiseeGroup">
                                            <div class="form-group">
                                                <label for="franchiseeSelect">Franchisee Name</label>
                                                <select
                                                    class="form-select @error('franchisee_name') is-invalid @enderror"
                                                    id="franchiseeSelect"
                                                    {!! $showFranchisee ? 'name="franchisee_name"' : '' !!}
                                                    onchange="handleFranchiseeChange(this)">
                                                    <option value="" disabled {{ !$currentFranchisee ? 'selected' : '' }}>Select Franchisee</option>
                                                    @foreach($allFranchisees as $franchise)
                                                        <option value="{{ $franchise }}" {{ $currentFranchisee === $franchise ? 'selected' : '' }}>{{ $franchise }}</option>
                                                    @endforeach
                                                    <option value="custom">+ Add New Franchisee...</option>
                                                </select>

                                                <div id="franchiseeCustomGroup" class="input-group mt-2 d-none">
                                                    <input
                                                        type="text"
                                                        class="form-control @error('franchisee_name') is-invalid @enderror"
                                                        id="franchiseeCustom"
                                                        placeholder="Enter franchisee name"
                                                        onkeypress="if(event.key === 'Enter') { event.preventDefault(); saveCustomFranchisee(); }" />
                                                    <button type="button" class="btn btn-gold" id="btnSaveFranchisee" onclick="saveCustomFranchisee()" title="Save Franchisee">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </div>
                                                @error('franchisee_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="firstName">First Name</label>
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
                                        <div class="col-md-3">
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
                                        <div class="col-md-3">
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
                                        <div class="col-md-3">
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
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="locationInput">Location</label>
                                                <div class="input-group">
                                                    <input
                                                        type="text"
                                                        class="form-control @error('location') is-invalid @enderror"
                                                        id="locationInput"
                                                        name="location"
                                                        placeholder="Your location address"
                                                        value="{{ old('location', $user->location) }}" />
                                                    <input type="hidden" name="latitude" id="latitudeInput" value="{{ old('latitude', $user->latitude) }}" />
                                                    <input type="hidden" name="longitude" id="longitudeInput" value="{{ old('longitude', $user->longitude) }}" />
                                                    <button type="button" class="btn btn-gold" id="btnDetectLocation" onclick="detectLocation()">
                                                        <i class="fas fa-map-marker-alt" style="height: 30px !important;"></i> Auto Detect
                                                    </button>
                                                </div>
                                                @error('location')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tax and Billing Section -->
                                    <div class="border-top my-4 pt-4" style="border-color: rgba(0, 0, 0, 0.08) !important;">
                                        <h5 class="mb-3" style="color: #D4AF37; font-weight: 600;">Tax & Billing</h5>
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
                                    </div>

                                    <!-- Password Change Section -->
                                    <div class="border-top my-4 pt-4" style="border-color: rgba(0, 0, 0, 0.08) !important;">
                                        <h5 class="mb-3" style="color: #D4AF37; font-weight: 600;">Change Password</h5>
                                    </div>
                                    @if (session('password-status') === 'password-updated' || session('status') === 'password-updated')
                                    <div class="alert alert-success">
                                        Password updated successfully.
                                    </div>
                                    @endif
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="currentPassword">Current Password</label>
                                                <input
                                                    type="password"
                                                    class="form-control @error('current_password') is-invalid @enderror"
                                                    id="currentPassword"
                                                    name="current_password" />
                                                @error('current_password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="newPassword">New Password</label>
                                                <input
                                                    type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="newPassword"
                                                    name="password" />
                                                @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="confirmNewPassword">Confirm New Password</label>
                                                <input
                                                    type="password"
                                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                                    id="confirmNewPassword"
                                                    name="password_confirmation" />
                                                @error('password_confirmation')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 text-end">
                                        <button type="submit" class="btn btn-save px-4">
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Inventory Tab -->
                        <div class="tab-pane fade {{ $activeTab === 'inventory' ? 'show active' : '' }}" id="inventory">
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
                                    <table class="table align-middle">
                                        <thead>
                                            <tr style="border-bottom: 2px solid rgba(0, 0, 0, 0.08); color: #D4AF37;">
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
                                            <tr style="border-bottom: 1px solid rgba(0, 0, 0, 0.05); vertical-align: middle;">
                                                <td class="py-3 font-weight-bold" style="color: #2c2c2c;">{{ $item->item_name }}</td>
                                                <td class="py-3"><code>{{ $item->sku ?? '-' }}</code></td>
                                                <td class="py-3" style="color: #2c2c2c;">{{ $item->quantity }}</td>
                                                <td class="py-3">
                                                    @if($item->quantity == 0)
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    @elseif($item->quantity <= $item->min_quantity)
                                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                                    @else
                                                        <span class="badge bg-success">In Stock</span>
                                                    @endif
                                                </td>
                                                <td class="py-3" style="color: #2c2c2c;">Rs. {{ number_format($item->price, 2) }}</td>
                                                <td class="py-3 text-truncate text-muted" style="max-width: 250px;">{{ $item->description ?? '-' }}</td>
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
    </div>
    @endsection
</x-admin-layout>
