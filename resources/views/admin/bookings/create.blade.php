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
            height: 34px !important;
            border-radius: 6px !important;
            box-sizing: border-box !important;
            transition: all 0.2s ease-in-out;
        }

        .form-select {
            padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;
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

        .form-label {
            font-size: 0.85rem !important;
            margin-bottom: 4px !important;
            color: #4b5563 !important;
        }

        .service-row {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }

        .btn-remove-row {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #dc3545;
            background: none;
            border: none;
            cursor: pointer;
        }
    </style>
    @endpush

    @section('content')
    <div class="booking-page">
        <form action="{{ route('admin.bookings.store') }}" method="POST" id="bookingForm">
            @csrf

            <!-- Sticky Header -->
            <div class="magento-sticky-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small uppercase fw-bold">Appointments Portal</span>
                    <h1 class="h3 mb-0 fw-bold">Book New Appointment</h1>
                </div>
                <div class="actions">
                    <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-chevron-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="container">
                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="magento-panel">
                    <div class="magento-panel-header">
                        <i class="fas fa-calendar-plus text-warning me-2"></i> Booking Information
                    </div>
                    <div class="magento-panel-body">
                        
                        <!-- Customer Details -->
                        <h5 class="border-bottom pb-2 mb-3 text-warning fw-bold"><i class="fas fa-user me-2"></i> Customer Details</h5>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="customer_type" class="form-label fw-bold">Customer Type <span class="text-danger">*</span></label>
                                <select name="customer_type" id="customer_type" class="form-select border-gold-focus" onchange="toggleCustomerType(this.value)">
                                    <option value="existing" {{ old('customer_type', 'existing') === 'existing' ? 'selected' : '' }}>Existing Customer</option>
                                    <option value="new" {{ old('customer_type') === 'new' ? 'selected' : '' }}>Create New Customer</option>
                                </select>
                            </div>

                            <!-- Existing Customer Dropdown -->
                            <div class="col-md-8 mb-3" id="existing_customer_section">
                                <label for="customer_id" class="form-label fw-bold">Select Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-select border-gold-focus @error('customer_id') is-invalid @enderror">
                                    <option value="">Select Existing Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->phone ?: 'No Phone' }} - {{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- New Customer Fields -->
                        <div class="row d-none" id="new_customer_section">
                            <div class="col-md-4 mb-3">
                                <label for="fullName" class="form-label fw-bold">Customer Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="fullName" id="fullName" class="form-control border-gold-focus @error('fullName') is-invalid @enderror" placeholder="e.g. John Doe" value="{{ old('fullName') }}">
                                @error('fullName')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="phone" class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="phone" class="form-control border-gold-focus @error('phone') is-invalid @enderror" placeholder="e.g. 9876543210" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control border-gold-focus @error('email') is-invalid @enderror" placeholder="e.g. customer@example.com" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Schedule and Staff Details -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4 text-warning fw-bold"><i class="fas fa-clock me-2"></i> Schedule & Staff Assignment</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="appointmentDate" class="form-label fw-bold">Preferred Date <span class="text-danger">*</span></label>
                                <input type="date" name="appointmentDate" id="appointmentDate" class="form-control border-gold-focus @error('appointmentDate') is-invalid @enderror" min="{{ date('Y-m-d') }}" value="{{ old('appointmentDate', date('Y-m-d')) }}" required onchange="triggerAvailabilityCheck()">
                                @error('appointmentDate')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="appointmentTime" class="form-label fw-bold">Preferred Time <span class="text-danger">*</span></label>
                                <select name="appointmentTime" id="appointmentTime" class="form-select border-gold-focus @error('appointmentTime') is-invalid @enderror" required onchange="triggerStaffAvailabilityCheck()" disabled>
                                    <option value="">Select Date and Services First</option>
                                </select>
                                @error('appointmentTime')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="staff_id" class="form-label fw-bold">Assign Staff Member <span class="text-danger">*</span></label>
                                <select name="staff_id" id="staff_id" class="form-select border-gold-focus @error('staff_id') is-invalid @enderror" required disabled>
                                    <option value="">Select Schedule and Services First</option>
                                </select>
                                @error('staff_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Service Details -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4 text-warning fw-bold d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-cut me-2"></i> Services Selection</span>
                            <button type="button" class="btn btn-outline-warning btn-sm text-dark" onclick="addServiceRow()">
                                <i class="fas fa-plus me-1"></i> Add Service
                            </button>
                        </h5>
                        
                        <div id="services-container">
                            <!-- Dynamic rows will go here -->
                        </div>

                        @error('services')
                            <div class="alert alert-danger py-2 mt-2">{{ $message }}</div>
                        @enderror

                        <!-- Special Requirements -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4 text-warning fw-bold"><i class="fas fa-edit me-2"></i> Notes & Remarks</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="requirements" class="form-label fw-bold">Special Requirements / Notes</label>
                                <textarea name="requirements" id="requirements" class="form-control border-gold-focus" rows="3" placeholder="Specify any requirements...">{{ old('requirements') }}</textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-4" style="background-color: #D4AF37; border-color: #D4AF37;">
                                <i class="fas fa-save me-1"></i> Save Booking
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let serviceIndex = 0;
        const allServices = @json($allServices);

        // Dynamically determine base URL depending on if we are in the tenant portal
        const path = window.location.pathname;
        let baseUrl = '/admin';
        if (path.includes('/portal/')) {
            const portalIndex = path.indexOf('/portal/');
            baseUrl = path.substring(0, portalIndex) + '/portal';
        }

        function toggleCustomerType(val) {
            const existingSec = document.getElementById('existing_customer_section');
            const newSec = document.getElementById('new_customer_section');

            if (val === 'existing') {
                existingSec.classList.remove('d-none');
                newSec.classList.add('d-none');
                document.getElementById('fullName').removeAttribute('required');
                document.getElementById('phone').removeAttribute('required');
                document.getElementById('email').removeAttribute('required');
                document.getElementById('customer_id').setAttribute('required', 'required');
            } else {
                existingSec.classList.add('d-none');
                newSec.classList.remove('d-none');
                document.getElementById('customer_id').removeAttribute('required');
                document.getElementById('fullName').setAttribute('required', 'required');
                document.getElementById('phone').setAttribute('required', 'required');
                document.getElementById('email').setAttribute('required', 'required');
            }
        }

        function addServiceRow() {
            const container = document.getElementById('services-container');
            const index = serviceIndex++;

            let optionsHtml = '<option value="">Select Service</option>';
            allServices.forEach(s => {
                optionsHtml += `<option value="${s.id}">${s.name} - Rs. ${parseFloat(s.price).toFixed(2)} (${s.duration} mins)</option>`;
            });

            const rowHtml = `
                <div class="service-row" id="service-row-${index}">
                    <button type="button" class="btn-remove-row" onclick="removeServiceRow(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Service <span class="text-danger">*</span></label>
                            <select name="services[${index}][service_id]" class="form-select border-gold-focus service-select" required onchange="triggerAvailabilityCheck()">
                                ${optionsHtml}
                            </select>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', rowHtml);
            
            const rows = container.querySelectorAll('.service-row');
            if (rows.length === 1) {
                rows[0].querySelector('.btn-remove-row').style.display = 'none';
            } else {
                rows.forEach(r => {
                    const btn = r.querySelector('.btn-remove-row');
                    if (btn) btn.style.display = 'block';
                });
            }
        }

        function removeServiceRow(index) {
            const row = document.getElementById(`service-row-${index}`);
            if (row) {
                row.remove();
            }

            const container = document.getElementById('services-container');
            const rows = container.querySelectorAll('.service-row');
            if (rows.length === 1) {
                rows[0].querySelector('.btn-remove-row').style.display = 'none';
            }

            triggerAvailabilityCheck();
        }

        function triggerAvailabilityCheck() {
            const date = document.getElementById('appointmentDate').value;
            const timeSelect = document.getElementById('appointmentTime');
            const staffSelect = document.getElementById('staff_id');

            // Calculate total duration of all selected services
            let totalDuration = 0;
            const selects = document.querySelectorAll('.service-select');
            
            selects.forEach(select => {
                const sId = select.value;
                if (sId) {
                    const match = allServices.find(s => s.id == sId);
                    if (match) {
                        totalDuration += parseInt(match.duration);
                    }
                }
            });

            if (!date || totalDuration === 0) {
                timeSelect.innerHTML = '<option value="">Select Date and Services First</option>';
                timeSelect.disabled = true;
                staffSelect.innerHTML = '<option value="">Select Schedule and Services First</option>';
                staffSelect.disabled = true;
                return;
            }

            const currentSelectedTime = timeSelect.value;
            timeSelect.innerHTML = '<option value="">Checking Available Times...</option>';
            timeSelect.disabled = true;

            fetch(`${baseUrl}/bookings/available-slots?date=${date}&duration=${totalDuration}`)
                .then(res => res.json())
                .then(slots => {
                    timeSelect.innerHTML = '<option value="">Select Time</option>';
                    if (slots.length === 0) {
                        timeSelect.innerHTML = '<option value="">No Slots Available for this Duration</option>';
                        staffSelect.innerHTML = '<option value="">No Staff Available</option>';
                        staffSelect.disabled = true;
                    } else {
                        slots.forEach(slot => {
                            timeSelect.innerHTML += `<option value="${slot}">${slot}</option>`;
                        });
                        
                        if (slots.includes(currentSelectedTime)) {
                            timeSelect.value = currentSelectedTime;
                        } else {
                            timeSelect.value = '';
                        }
                        
                        timeSelect.disabled = false;

                        // Trigger staff check if we have a valid time selected
                        if (timeSelect.value) {
                            triggerStaffAvailabilityCheck();
                        } else {
                            staffSelect.innerHTML = '<option value="">Select Time First</option>';
                            staffSelect.disabled = true;
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                });
        }

        function triggerStaffAvailabilityCheck() {
            const date = document.getElementById('appointmentDate').value;
            const time = document.getElementById('appointmentTime').value;
            const staffSelect = document.getElementById('staff_id');

            // Calculate total duration of all selected services
            let totalDuration = 0;
            const selects = document.querySelectorAll('.service-select');
            
            selects.forEach(select => {
                const sId = select.value;
                if (sId) {
                    const match = allServices.find(s => s.id == sId);
                    if (match) {
                        totalDuration += parseInt(match.duration);
                    }
                }
            });

            if (!date || !time || totalDuration === 0) {
                staffSelect.innerHTML = '<option value="">Select Schedule and Services First</option>';
                staffSelect.disabled = true;
                return;
            }

            staffSelect.innerHTML = '<option value="">Checking Availability...</option>';
            staffSelect.disabled = true;

            fetch(`${baseUrl}/bookings/check-staff-availability?date=${date}&time=${time}&duration=${totalDuration}`)
                .then(res => res.json())
                .then(data => {
                    staffSelect.innerHTML = '<option value="">Select Available Staff</option>';
                    if (data.length === 0) {
                        staffSelect.innerHTML = '<option value="">No Staff Available (Overlapping schedule)</option>';
                    } else {
                        data.forEach(staff => {
                            staffSelect.innerHTML += `<option value="${staff.id}">${staff.name}</option>`;
                        });
                        staffSelect.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    staffSelect.innerHTML = '<option value="">Error checking availability</option>';
                });
        }

        // Initialize with one row
        document.addEventListener('DOMContentLoaded', () => {
            addServiceRow();
            toggleCustomerType('existing');
            
            @if(isset($preselectedServiceId))
                const firstRowSelect = document.querySelector('.service-select');
                if (firstRowSelect) {
                    firstRowSelect.value = "{{ $preselectedServiceId }}";
                    triggerAvailabilityCheck();
                }
            @endif
        });
    </script>
    @endpush
    @endsection
</x-admin-layout>
