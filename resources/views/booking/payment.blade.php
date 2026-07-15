@php
    $layout = Auth::check() ? 'admin-layout' : 'app-layout';
@endphp
<x-dynamic-component :component="$layout">
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/booking.css') }}">
    <style>
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .is-valid {
            border-color: #198754 !important;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .payment-option-card {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            background-color: #1e1e1e !important;
            border: 2px solid #333 !important;
        }
        .payment-option-card:hover {
            border-color: #ffc107 !important;
            transform: translateY(-2px);
        }
        .payment-option-card.active {
            border-color: #ffc107 !important;
            background-color: #2a2a2a !important;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.15);
        }
    </style>
    @endpush

    @section('content')
    <main>
        <!-- Page Header -->
        <header class="booking-header">
            <div class="container">
                <div class="row">
                    <div class="text-center col-12" data-aos="fade-up">
                        <h1>Payment Details</h1>
                        <p class="lead">Complete your booking by making the payment</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Payment Form Section -->
        <section class="payment-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <!-- Booking Summary -->
                                <div class="mb-4 booking-summary">
                                    <h4>Booking Summary</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Service:</strong> {{ $booking->service->name }}</p>
                                            <p><strong>Date:</strong> {{ $booking->appointment_date }}</p>
                                            <p><strong>Time:</strong> {{ $booking->appointment_time }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Base Price:</strong> Rs. {{number_format($booking->base_price, 2)}}</p>
                                            <p><strong>Service Fee (3%):</strong> Rs. {{number_format($booking->addons_price, 2)}}</p>
                                            <p><strong>Total Amount:</strong> Rs. {{number_format($booking->total_price, 2)}}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Form -->
                                <form method="POST" action="{{ isset($currentSalon) ? route('salon.booking.payment.process', ['salon' => $currentSalon->slug, 'id' => $booking->id]) : route('booking.payment.process', $booking->id) }}" class="payment-form">
                                    @csrf
                                    
                                    <!-- Payment Method Toggle -->
                                    <div class="mb-4">
                                        <label class="form-label d-block fw-semibold mb-3" style="color: #fff;">Choose Payment Method *</label>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="payment-option-card border rounded p-3 text-center active" id="payOnlineOpt">
                                                    <input type="radio" name="payment_option" id="payment_option_online" value="online" checked style="display:none;">
                                                    <i class="fas fa-credit-card mb-2 fs-3" style="color: #ffc107;"></i>
                                                    <h5 class="mb-1 text-white">Pay Online</h5>
                                                    <p class="text-muted small mb-0">Pay now securely using Credit/Debit Card</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="payment-option-card border rounded p-3 text-center" id="payAtShopOpt">
                                                    <input type="radio" name="payment_option" id="payment_option_shop" value="pay_at_shop" style="display:none;">
                                                    <i class="fas fa-store mb-2 fs-3" style="color: #ffc107;"></i>
                                                    <h5 class="mb-1 text-white">Pay at Shop</h5>
                                                    <p class="text-muted small mb-0">Confirm booking now, pay at the salon</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="cardDetailsContainer">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="card_holder">Card Holder Name *</label>
                                                    <input type="text" class="form-control" id="card_holder" name="card_holder" required>
                                                    @error('card_holder')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="card_number">Card Number *</label>
                                                    <input type="text" class="form-control" id="card_number" name="card_number" required
                                                        pattern="\d{16}" maxlength="16" placeholder="1234567890123456">
                                                    @error('card_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="card_expiry">Expiry Date (MM/YY) *</label>
                                                    <input type="text" class="form-control" id="card_expiry" name="card_expiry" required
                                                        pattern="\d{2}/\d{2}" maxlength="5" placeholder="MM/YY">
                                                    @error('card_expiry')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="card_cvv">CVV *</label>
                                                    <input type="text" class="form-control" id="card_cvv" name="card_cvv" required
                                                        pattern="\d{3}" maxlength="3" placeholder="123">
                                                    @error('card_cvv')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pay at Shop Message (hidden by default) -->
                                    <div id="payAtShopMessage" class="d-none my-4 p-3 rounded" style="background-color: #2b2b2b; border-left: 4px solid #ffc107;">
                                        <h5 class="text-white mb-2"><i class="fas fa-info-circle text-warning me-2"></i> Pay at Shop Info</h5>
                                        <p class="text-white-50 mb-0">You will pay the total amount of <strong>Rs. {{number_format($booking->total_price, 2)}}</strong> at the salon at the time of your appointment. No card details or pre-payment is required to confirm your booking.</p>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Pay Now</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    @endsection

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payOnlineOpt = document.getElementById('payOnlineOpt');
            const payAtShopOpt = document.getElementById('payAtShopOpt');
            const onlineRadio = document.getElementById('payment_option_online');
            const shopRadio = document.getElementById('payment_option_shop');
            const cardDetailsContainer = document.getElementById('cardDetailsContainer');
            const payAtShopMessage = document.getElementById('payAtShopMessage');
            const submitBtn = document.getElementById('submitBtn');

            const cardNumber = document.getElementById('card_number');
            const cardExpiry = document.getElementById('card_expiry');
            const cardCvv = document.getElementById('card_cvv');
            const cardHolder = document.getElementById('card_holder');
            const form = document.querySelector('.payment-form');

            // Handle toggles
            payOnlineOpt.addEventListener('click', function() {
                payOnlineOpt.classList.add('active');
                payAtShopOpt.classList.remove('active');
                onlineRadio.checked = true;
                
                cardDetailsContainer.classList.remove('d-none');
                payAtShopMessage.classList.add('d-none');
                submitBtn.textContent = 'Pay Now';

                // Add required attributes
                cardNumber.setAttribute('required', '');
                cardExpiry.setAttribute('required', '');
                cardCvv.setAttribute('required', '');
                cardHolder.setAttribute('required', '');
            });

            payAtShopOpt.addEventListener('click', function() {
                payAtShopOpt.classList.add('active');
                payOnlineOpt.classList.remove('active');
                shopRadio.checked = true;
                
                cardDetailsContainer.classList.add('d-none');
                payAtShopMessage.classList.remove('d-none');
                submitBtn.textContent = 'Confirm Booking';

                // Remove required attributes
                cardNumber.removeAttribute('required');
                cardExpiry.removeAttribute('required');
                cardCvv.removeAttribute('required');
                cardHolder.removeAttribute('required');

                // Clear any validation visual cues
                cardNumber.classList.remove('is-invalid', 'is-valid');
                cardExpiry.classList.remove('is-invalid', 'is-valid');
                cardCvv.classList.remove('is-invalid', 'is-valid');
                cardHolder.classList.remove('is-invalid', 'is-valid');
                document.querySelectorAll('.error-message').forEach(el => el.classList.remove('show'));
            });

            // Add error message elements
            function addErrorMessageElement(input, message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.id = input.id + '_error';
                errorDiv.textContent = message;
                input.parentNode.appendChild(errorDiv);
            }

            // Initialize error messages
            addErrorMessageElement(cardNumber, 'Please enter a valid 16-digit card number');
            addErrorMessageElement(cardExpiry, 'Please enter a valid expiry date (MM/YY)');
            addErrorMessageElement(cardCvv, 'Please enter a valid 3-digit CVV');
            addErrorMessageElement(cardHolder, 'Please enter the card holder name');

            // Validate card number
            cardNumber.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '').substring(0, 16);
                const errorElement = document.getElementById('card_number_error');

                if (this.value.length === 16) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                    errorElement.classList.remove('show');
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    errorElement.classList.add('show');
                }
            });

            // Validate expiry date
            cardExpiry.addEventListener('input', function(e) {
                this.value = this.value
                    .replace(/\D/g, '')
                    .replace(/^(\d{2})/, '$1/')
                    .substring(0, 5);

                const errorElement = document.getElementById('card_expiry_error');
                const pattern = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;

                if (pattern.test(this.value)) {
                    const [month, year] = this.value.split('/');
                    const now = new Date();
                    const expiry = new Date(2000 + parseInt(year), parseInt(month) - 1);

                    if (expiry > now) {
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                        errorElement.classList.remove('show');
                    } else {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                        errorElement.textContent = 'Card has expired';
                        errorElement.classList.add('show');
                    }
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    errorElement.textContent = 'Please enter a valid expiry date (MM/YY)';
                    errorElement.classList.add('show');
                }
            });

            // Validate CVV
            cardCvv.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '').substring(0, 3);
                const errorElement = document.getElementById('card_cvv_error');

                if (this.value.length === 3) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                    errorElement.classList.remove('show');
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    errorElement.classList.add('show');
                }
            });

            // Validate card holder name
            cardHolder.addEventListener('input', function(e) {
                const errorElement = document.getElementById('card_holder_error');
                const pattern = /^[a-zA-Z\s]{3,}$/;

                if (pattern.test(this.value)) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                    errorElement.classList.remove('show');
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    errorElement.classList.add('show');
                }
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                if (onlineRadio.checked) {
                    const isCardNumberValid = cardNumber.value.length === 16;
                    const isExpiryValid = /^(0[1-9]|1[0-2])\/([0-9]{2})$/.test(cardExpiry.value);
                    const isCvvValid = cardCvv.value.length === 3;
                    const isHolderValid = /^[a-zA-Z\s]{3,}$/.test(cardHolder.value);

                    if (!isCardNumberValid || !isExpiryValid || !isCvvValid || !isHolderValid) {
                        e.preventDefault();

                        if (!isCardNumberValid) {
                            cardNumber.classList.add('is-invalid');
                            document.getElementById('card_number_error').classList.add('show');
                        }
                        if (!isExpiryValid) {
                            cardExpiry.classList.add('is-invalid');
                            document.getElementById('card_expiry_error').classList.add('show');
                        }
                        if (!isCvvValid) {
                            cardCvv.classList.add('is-invalid');
                            document.getElementById('card_cvv_error').classList.add('show');
                        }
                        if (!isHolderValid) {
                            cardHolder.classList.add('is-invalid');
                            document.getElementById('card_holder_error').classList.add('show');
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-dynamic-component>
