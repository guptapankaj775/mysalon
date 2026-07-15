<x-admin-layout>
    @section('title', 'Book New Appointment')

    @push('styles')
    <style>
        .booking-page {
            padding: 0;
        }

        .magento-sticky-header {
            background: #fff;
            padding: 15px 30px;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
            margin-bottom: 30px;
        }

        .magento-panel {
            border: 1px solid #e2e8f0;
            border-radius: 10px !important;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            background: white;
        }

        .magento-panel-header {
            background-color: #fdfaf2;
            color: #bfa13d;
            font-weight: 600;
            border-bottom: 1px solid #f7edd4;
            padding: 18px 24px;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
        }

        .magento-panel-header i {
            font-size: 1.15rem;
            width: 28px;
        }

        .magento-panel-body {
            padding: 25px 30px;
            background: white;
        }

        .form-control:not(textarea),
        .form-select,
        .input-group .btn {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            color: #1f2937 !important;
            font-size: 0.95rem !important;
            height: 30px !important;
            border-radius: 6px !important;
            box-sizing: border-box !important;
            transition: all 0.2s ease-in-out;
        }

        .form-select {
            padding: unset !important;
            padding-left: 2px !important;
        }

        textarea.form-control {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            color: #1f2937 !important;
            padding: 0.5rem 0.75rem;
            font-size: 0.95rem;
            border-radius: 6px !important;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            background-color: #ffffff !important;
            border-color: #D4AF37 !important;
            box-shadow: none !important;
            color: #1f2937 !important;
            outline: none !important;
        }

        .input-group-text {
            height: 30px !important;
            font-size: 0.9rem !important;
            border-radius: 6px 0 0 6px !important;
            background-color: #f3f4f6 !important;
            border: 1px solid #e5e7eb !important;
        }

        .form-label {
            font-size: 0.85rem !important;
            margin-bottom: 4px !important;
            color: #4b5563 !important;
        }
    </style>
    @endpush

    @section('content')
    <div class="booking-page">
        <form action="{{ isset($currentSalon) ? route('salon.customer.bookings.store', ['salon' => $currentSalon->slug]) : route('customer.bookings.store') }}" method="POST" id="bookingForm">
            @csrf

            <!-- Sticky Header -->
            <div class="magento-sticky-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small uppercase fw-bold">Customer Portal</span>
                    <h1 class="h3 mb-0 fw-bold">Book New Appointment</h1>
                </div>
                <div class="actions">
                    <a href="{{ isset($currentSalon) ? route('salon.customer.services.book', ['salon' => $currentSalon->slug]) : route('customer.services.book') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-chevron-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="container">
                <div class="magento-panel">
                    <div class="magento-panel-header">
                        <i class="fas fa-calendar-plus text-warning me-2"></i> Booking Information
                    </div>
                    <div class="magento-panel-body">
                        
                        <!-- Customer Details -->
                        <h5 class="border-bottom pb-2 mb-3 text-warning fw-bold"><i class="fas fa-user me-2"></i> Personal Details</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fullName" class="form-label fw-bold">Your Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="fullName" id="fullName" class="form-control border-gold-focus @error('fullName') is-invalid @enderror" placeholder="e.g. John Doe" value="{{ old('fullName', request('fullName', Auth::user()->name . (Auth::user()->last_name ? ' ' . Auth::user()->last_name : ''))) }}" required>
                                @error('fullName')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="phone" class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="phone" class="form-control border-gold-focus @error('phone') is-invalid @enderror" placeholder="e.g. 9876543210" value="{{ old('phone', request('phone', Auth::user()->phone)) }}" required>
                                @error('phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control border-gold-focus @error('email') is-invalid @enderror" placeholder="e.g. customer@example.com" value="{{ old('email', request('email', Auth::user()->email)) }}" required>
                                @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Service Details -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4 text-warning fw-bold"><i class="fas fa-cut me-2"></i> Service Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="serviceCategory" class="form-label fw-bold">Service Category <span class="text-danger">*</span></label>
                                <select name="serviceCategory" id="serviceCategory" class="form-select border-gold-focus @error('serviceCategory') is-invalid @enderror" required onchange="filterServices(this)">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('serviceCategory', request('serviceCategory')) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('serviceCategory')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="service" class="form-label fw-bold">Service <span class="text-danger">*</span></label>
                                <select name="service" id="service" class="form-select border-gold-focus @error('service') is-invalid @enderror" required {{ count($services) ? '' : 'disabled' }}>
                                    <option value="">Select Service</option>
                                    @foreach($services as $srv)
                                        <option value="{{ $srv->id }}" {{ old('service', request('service')) == $srv->id ? 'selected' : '' }}>
                                            {{ $srv->name }} - Rs. {{ number_format($srv->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Schedule Details -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4 text-warning fw-bold"><i class="fas fa-clock me-2"></i> Preferred Schedule</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="appointmentDate" class="form-label fw-bold">Preferred Date <span class="text-danger">*</span></label>
                                <input type="date" name="appointmentDate" id="appointmentDate" class="form-control border-gold-focus @error('appointmentDate') is-invalid @enderror" min="{{ date('Y-m-d') }}" value="{{ old('appointmentDate', request('appointmentDate', date('Y-m-d'))) }}" required>
                                @error('appointmentDate')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="appointmentTime" class="form-label fw-bold">Preferred Time <span class="text-danger">*</span></label>
                                <select name="appointmentTime" id="appointmentTime" class="form-select border-gold-focus @error('appointmentTime') is-invalid @enderror" required>
                                    <option value="">Select Time</option>
                                    @foreach($timeSlots as $slot)
                                        <option value="{{ $slot }}" {{ old('appointmentTime', request('appointmentTime')) == $slot ? 'selected' : '' }}>
                                            {{ $slot }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('appointmentTime')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Special Requirements -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="requirements" class="form-label fw-bold">Special Requirements / Allergies</label>
                                <textarea name="requirements" id="requirements" class="form-control border-gold-focus" rows="3" placeholder="Specify any requirements...">{{ old('requirements') }}</textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ isset($currentSalon) ? route('salon.customer.services.book', ['salon' => $currentSalon->slug]) : route('customer.services.book') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-4" style="background-color: #D4AF37; border-color: #D4AF37;">
                                <i class="fas fa-arrow-right me-1"></i> Proceed to Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function filterServices(select) {
            const form = document.getElementById('bookingForm');
            
            // Set action to current location with GET method
            form.action = window.location.pathname;
            form.method = "GET";

            // Temporarily bypass standard HTML5 validation
            const inputs = form.querySelectorAll('[required]');
            inputs.forEach(input => {
                if (input.id !== 'serviceCategory') {
                    input.removeAttribute('required');
                }
            });

            form.submit();
        }
    </script>
    @endpush
    @endsection
</x-admin-layout>
