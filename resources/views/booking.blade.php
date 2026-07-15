<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/booking.css') }}">
    <style>
        .city-card {
            border: 2px solid #3a3a3a !important;
            transition: all 0.2s ease-in-out;
            background-color: #222 !important;
            color: #fff !important;
            cursor: pointer;
        }
        .city-card:hover {
            border-color: #ffc107 !important;
            background-color: #333 !important;
            transform: translateY(-2px);
        }
        .city-card.active {
            border-color: #ffc107 !important;
            background-color: #444 !important;
        }
        .city-card i {
            color: #ffc107 !important;
        }
        .modal-content {
            border: 1px solid #444 !important;
        }
        .btn-outline-primary {
            border-color: #ffc107 !important;
            color: #ffc107 !important;
        }
        .btn-outline-primary:hover {
            background-color: #ffc107 !important;
            color: #000 !important;
        }
        #locationStatusText i {
            color: #ffc107;
        }
    </style>
    @endpush

    @section('content')
    <main>
        <!-- Page Header -->
        <header class="booking-header">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center" data-aos="fade-up">
                        <h1>Book Your Appointment</h1>
                        <p class="lead">Schedule your beauty transformation with us</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Booking Form Section -->
        <section class="booking-section">
            <div class="container">
                <form method="POST" action="{{ isset($currentSalon) ? route('salon.bookings.store', ['salon' => $currentSalon->slug]) : route('bookings.store') }}" class="booking-form">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Personal Information -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fullName">Full Name *</label>
                                        <input type="text" class="form-control" id="fullName" name="fullName" required value="{{ old('fullName', auth()->check() ? auth()->user()->name . (auth()->user()->last_name ? ' ' . auth()->user()->last_name : '') : '') }}" />
                                        @error('fullName')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number *</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}" />
                                        @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" required value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" />
                                        @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Salon Selection (if global booking) -->
                                @if(!isset($currentSalon))
                                <div class="col-md-6" id="salonSelectionGroup">
                                    <div class="form-group">
                                        <label for="salon_id">Select Salon *</label>
                                        <div class="d-flex gap-2">
                                            <select class="form-control" id="salon_id" name="salon_id" required>
                                                <option value="">Locating nearest salons...</option>
                                            </select>
                                            <button type="button" class="btn btn-outline-primary px-3 d-flex align-items-center gap-2" id="changeLocationBtn" style="border-radius: 8px;">
                                                <i class="fas fa-map-marker-alt"></i> City
                                            </button>
                                        </div>
                                        <div id="locationStatusText" class="form-text text-muted mt-1 small"></div>
                                    </div>
                                </div>
                                @else
                                <input type="hidden" name="salon_id" id="salon_id" value="{{ $currentSalon->id }}" />
                                @endif

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serviceCategory">Service Category *</label>
                                        <select class="form-control" id="serviceCategory" name="serviceCategory" required>
                                            <option value="">Select a category</option>
                                            @if(isset($currentSalon))
                                                @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ (request('serviceCategory') == $category->id || ($selectedCategory && $selectedCategory->id == $category->id)) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('serviceCategory')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service">Service *</label>
                                        <select class="form-control" id="service" name="service" required disabled>
                                            <option value="">Select a service</option>
                                            @if(isset($currentSalon))
                                                @foreach($services as $serv)
                                                <option value="{{ $serv->id }}" {{ (request('service') == $serv->id || ($selectedService && $selectedService->id == $serv->id)) ? 'selected' : '' }} data-price="{{ $serv->price }}">
                                                    {{ $serv->name }} - Rs. {{ number_format($serv->price, 2) }}
                                                </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('service')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="appointmentDate">Preferred Date *</label>
                                        <input type="date" class="form-control" id="appointmentDate" name="appointmentDate" required min="{{ date('Y-m-d') }}" value="{{ old('appointmentDate') }}" />
                                        @error('appointmentDate')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="appointmentTime">Preferred Time *</label>
                                        <select class="form-control" id="appointmentTime" name="appointmentTime" required>
                                            <option value="">Select time</option>
                                            @foreach($timeSlots as $slot)
                                            <option value="{{ $slot }}" {{ old('appointmentTime') == $slot ? 'selected' : '' }}>
                                                {{ $slot }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('appointmentTime')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="requirements">Special Requirements or Allergies</label>
                                        <textarea class="form-control" id="requirements" name="requirements" rows="3">{{ old('requirements') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="termsAccept" name="termsAccept" required {{ old('termsAccept') ? 'checked' : '' }} />
                                        <label class="form-check-label" for="termsAccept">
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>
                                        </label>
                                        @error('termsAccept')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">Book Appointment</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- City Selection Modal -->
    <div class="modal fade" id="cityModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="cityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; background-color: #1a1a1a; color: #fff;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fs-4 fw-bold" id="cityModalLabel" style="color: #ffc107;">Select Your City</h5>
                </div>
                <div class="modal-body p-4">
                    <!-- Search Input -->
                    <div class="mb-4 input-group" style="border: 1px solid #444; border-radius: 30px; overflow: hidden; background-color: #2b2b2b;">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control bg-transparent border-0 text-white py-3 ps-1" id="citySearch" placeholder="Search for your city" style="box-shadow: none; color: #fff !important;" />
                    </div>

                    <!-- Detect my location button -->
                    <div class="mb-4">
                        <button type="button" class="btn p-0 border-0 bg-transparent fw-semibold d-flex align-items-center gap-2" id="detectLocationBtn" style="color: #ffc107 !important; transition: transform 0.2s ease;">
                            <i class="fas fa-crosshairs"></i> Detect my location
                        </button>
                    </div>

                    <p class="text-muted small uppercase fw-bold tracking-wider mb-3">Popular Cities</p>

                    <!-- Popular Cities Grid -->
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-3" id="popularCitiesGrid">
                        <!-- Mumbai -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Mumbai" style="border-radius: 12px;">
                                <i class="fas fa-city mb-2 fs-3"></i>
                                <span class="fw-medium">Mumbai</span>
                            </div>
                        </div>
                        <!-- Delhi-NCR -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Delhi-NCR" style="border-radius: 12px;">
                                <i class="fas fa-landmark mb-2 fs-3"></i>
                                <span class="fw-medium">Delhi-NCR</span>
                            </div>
                        </div>
                        <!-- Bengaluru -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Bengaluru" style="border-radius: 12px;">
                                <i class="fas fa-laptop-code mb-2 fs-3"></i>
                                <span class="fw-medium">Bengaluru</span>
                            </div>
                        </div>
                        <!-- Hyderabad -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Hyderabad" style="border-radius: 12px;">
                                <i class="fas fa-monument mb-2 fs-3"></i>
                                <span class="fw-medium">Hyderabad</span>
                            </div>
                        </div>
                        <!-- Chandigarh -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Chandigarh" style="border-radius: 12px;">
                                <i class="fas fa-archway mb-2 fs-3"></i>
                                <span class="fw-medium">Chandigarh</span>
                            </div>
                        </div>
                        <!-- Ahmedabad -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Ahmedabad" style="border-radius: 12px;">
                                <i class="fas fa-mosque mb-2 fs-3"></i>
                                <span class="fw-medium">Ahmedabad</span>
                            </div>
                        </div>
                        <!-- Pune -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Pune" style="border-radius: 12px;">
                                <i class="fas fa-fort-awesome mb-2 fs-3"></i>
                                <span class="fw-medium">Pune</span>
                            </div>
                        </div>
                        <!-- Chennai -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Chennai" style="border-radius: 12px;">
                                <i class="fas fa-place-of-worship mb-2 fs-3"></i>
                                <span class="fw-medium">Chennai</span>
                            </div>
                        </div>
                        <!-- Kolkata -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Kolkata" style="border-radius: 12px;">
                                <i class="fas fa-gopuran mb-2 fs-3"></i>
                                <span class="fw-medium">Kolkata</span>
                            </div>
                        </div>
                        <!-- Kochi -->
                        <div class="col">
                            <div class="city-card text-center p-3 h-100 d-flex flex-column align-items-center justify-content-center" data-city="Kochi" style="border-radius: 12px;">
                                <i class="fas fa-ship mb-2 fs-3"></i>
                                <span class="fw-medium">Kochi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>By booking an appointment with SalonJC, you agree to the following terms:</p>
                    <ul>
                        <li>Please arrive 10 minutes before your scheduled appointment time</li>
                        <li>A 24-hour notice is required for cancellation</li>
                        <li>Late arrivals may result in reduced service time</li>
                        <li>Prices may vary based on hair length and service complexity</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
        $(document).ready(function() {
            let selectedSalonId = "{{ isset($currentSalon) ? $currentSalon->id : '' }}";
            let cityModalElement = document.getElementById('cityModal');
            let cityModal = cityModalElement ? new bootstrap.Modal(cityModalElement) : null;

            // Date limits
            const dateInput = $('#appointmentDate');
            const timeInput = $('#appointmentTime');

            function validateDateTime() {
                const selectedDate = dateInput.val();
                const selectedTime = timeInput.val();
                if (selectedDate && selectedTime) {
                    const now = new Date();
                    const selected = new Date(selectedDate + ' ' + selectedTime);
                    if (selected < now) {
                        alert('Please select a future date and time.');
                        timeInput.val('');
                    }
                }
            }

            timeInput.on('change', validateDateTime);
            dateInput.on('change', validateDateTime);

            // Fetch salons based on location or city
            function fetchSalons(params) {
                let url = "{{ route('api.salons') }}";
                let statusText = $('#locationStatusText');
                
                if (params.latitude && params.longitude) {
                    statusText.html('<i class="fas fa-spinner fa-spin"></i> Detecting your location...');
                } else if (params.city) {
                    statusText.html(`<i class="fas fa-map-marker-alt"></i> Searching salons in ${params.city}...`);
                }

                $.getJSON(url, params, function(data) {
                    let dropdown = $('#salon_id');
                    dropdown.empty();

                    if (data.length === 0) {
                        dropdown.append('<option value="">No salons found in this location</option>');
                        statusText.html('<i class="fas fa-exclamation-triangle text-warning"></i> No salons found. Please try another city.');
                        if (cityModal) cityModal.show();
                    } else {
                        dropdown.append('<option value="">Select a salon</option>');
                        data.forEach(function(salon) {
                            let distanceStr = salon.distance ? ` (${parseFloat(salon.distance).toFixed(1)} km away)` : '';
                            dropdown.append(`<option value="${salon.id}">${salon.salon_name} - ${salon.location}${distanceStr}</option>`);
                        });

                        // Select the first salon automatically
                        dropdown.val(data[0].id).trigger('change');

                        if (params.latitude && params.longitude) {
                            statusText.html(`<i class="fas fa-check-circle text-success"></i> Found nearest salon: <strong>${data[0].salon_name}</strong>`);
                        } else if (params.city) {
                            statusText.html(`<i class="fas fa-check-circle text-success"></i> Salons loaded for <strong>${params.city}</strong>`);
                        }
                    }
                }).fail(function() {
                    $('#locationStatusText').html('<i class="fas fa-times-circle text-danger"></i> Failed to load salons.');
                });
            }

            // Geolocation detection
            function detectLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            if (cityModal) cityModal.hide();
                            fetchSalons({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude
                            });
                        },
                        function(error) {
                            console.log("Geolocation error code: " + error.code);
                            $('#locationStatusText').html('<i class="fas fa-exclamation-circle"></i> Geolocation blocked or unavailable. Please select your city.');
                            if (cityModal) cityModal.show();
                        },
                        { timeout: 8000 }
                    );
                } else {
                    if (cityModal) cityModal.show();
                }
            }

            // Start Geolocation if no preselected salon in URL / state
            if (!selectedSalonId) {
                detectLocation();
            }

            // Change Location button click
            $('#changeLocationBtn').on('click', function() {
                if (cityModal) cityModal.show();
            });

            // Detect my location inside modal click
            $('#detectLocationBtn').on('click', function() {
                detectLocation();
            });

            // Popular cities click selection
            $('.city-card').on('click', function() {
                $('.city-card').removeClass('active');
                $(this).addClass('active');
                let city = $(this).data('city');
                if (cityModal) cityModal.hide();
                fetchSalons({ city: city });
            });

            // City Search input filter
            $('#citySearch').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                $('#popularCitiesGrid .col').filter(function() {
                    $(this).toggle($(this).find('.fw-medium').text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Salon dropdown change -> Load categories
            $('#salon_id').on('change', function() {
                let salonId = $(this).val();
                let categoryDropdown = $('#serviceCategory');
                let serviceDropdown = $('#service');

                categoryDropdown.empty().append('<option value="">Select a category</option>');
                serviceDropdown.empty().append('<option value="">Select a service</option>').prop('disabled', true);

                if (!salonId) return;

                let url = `/api/salons/${salonId}/categories`;
                $.getJSON(url, function(data) {
                    data.forEach(function(cat) {
                        categoryDropdown.append(`<option value="${cat.id}">${cat.name}</option>`);
                    });

                    // Trigger preselection if loaded from query parameters
                    let preselectedCategory = "{{ request('serviceCategory') ?: ($selectedCategory ? $selectedCategory->id : '') }}";
                    if (preselectedCategory) {
                        categoryDropdown.val(preselectedCategory).trigger('change');
                    }
                });
            });

            // Category dropdown change -> Load services
            $('#serviceCategory').on('change', function() {
                let salonId = $('#salon_id').val();
                let categoryId = $(this).val();
                let serviceDropdown = $('#service');

                serviceDropdown.empty().append('<option value="">Select a service</option>').prop('disabled', true);

                if (!salonId || !categoryId) return;

                let url = `/api/salons/${salonId}/categories/${categoryId}/services`;
                $.getJSON(url, function(data) {
                    if (data.length > 0) {
                        serviceDropdown.prop('disabled', false);
                        data.forEach(function(serv) {
                            serviceDropdown.append(`<option value="${serv.id}" data-price="${serv.price}">${serv.name} - Rs. ${parseFloat(serv.price).toFixed(2)}</option>`);
                        });

                        // Trigger preselection of service if loaded from query parameters
                        let preselectedService = "{{ request('service') ?: ($selectedService ? $selectedService->id : '') }}";
                        if (preselectedService) {
                            serviceDropdown.val(preselectedService);
                        }
                    }
                });
            });

            // Form validation on final submit
            $('form.booking-form').on('submit', function(e) {
                const required = ['fullName', 'phone', 'email', 'serviceCategory', 'service', 'appointmentDate', 'appointmentTime'];
                if (!$('#salon_id').val()) {
                    required.push('salon_id');
                }

                let isValid = true;

                required.forEach(fieldId => {
                    const field = $(`#${fieldId}`);
                    const value = field.val();

                    if (!value || value.trim() === '') {
                        isValid = false;
                        field.addClass('is-invalid');
                    } else {
                        field.removeClass('is-invalid');
                    }
                });

                if (!$('#termsAccept').is(':checked')) {
                    isValid = false;
                    $('#termsAccept').addClass('is-invalid');
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields and accept the terms and conditions.');
                }
            });

            // Initialize categories if scoped salon is present
            if (selectedSalonId) {
                $('#salon_id').trigger('change');
            }
        });
    </script>
    @endpush
</x-app-layout>
