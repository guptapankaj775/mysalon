<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0f;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at 30% 20%, rgba(212,175,55,0.1) 0%, transparent 50%),
                        radial-gradient(ellipse at 70% 80%, rgba(59,130,246,0.08) 0%, transparent 50%);
            z-index: 0;
        }

        .payment-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 900px;
        }

        /* Demo Banner */
        .demo-banner {
            background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(251,191,36,0.1));
            border: 1px solid rgba(245,158,11,0.4);
            border-radius: 12px;
            padding: 0.9rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: #fbbf24;
        }

        .demo-banner i { font-size: 1.1rem; }

        /* Card layout */
        .payment-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1.4fr;
        }

        @media (max-width: 768px) {
            .payment-card { grid-template-columns: 1fr; }
            .order-summary { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.08); }
        }

        /* Order Summary */
        .order-summary {
            background: rgba(255,255,255,0.02);
            border-right: 1px solid rgba(255,255,255,0.08);
            padding: 2.5rem;
        }

        .order-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.4);
            margin-bottom: 1.5rem;
        }

        .plan-summary-card {
            background: rgba(212,175,55,0.08);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .plan-summary-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #D4AF37;
            margin-bottom: 0.5rem;
        }

        .plan-summary-duration {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.5);
            margin-bottom: 1.5rem;
        }

        .plan-summary-price {
            font-size: 2.2rem;
            font-weight: 800;
            color: #fff;
        }

        .plan-summary-price span {
            font-size: 1rem;
            color: rgba(255,255,255,0.5);
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.88rem;
            color: rgba(255,255,255,0.65);
            padding: 0.4rem 0;
        }

        .feature-list li i {
            color: #4ade80;
            font-size: 0.75rem;
            width: 16px;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255,255,255,0.4);
            font-size: 0.8rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .secure-badge i { color: #4ade80; }

        /* Payment Form */
        .payment-form {
            padding: 2.5rem;
        }

        .form-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }

        .form-subtitle {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.5);
            margin-bottom: 2rem;
        }

        /* Payment Method Tabs */
        .method-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: rgba(255,255,255,0.04);
            border-radius: 12px;
            padding: 0.4rem;
        }

        .method-tab {
            flex: 1;
            padding: 0.6rem;
            border: none;
            background: transparent;
            color: rgba(255,255,255,0.5);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.25s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
        }

        .method-tab.active {
            background: rgba(212,175,55,0.2);
            color: #D4AF37;
        }

        .method-tab i { font-size: 1.2rem; }

        /* Form fields */
        .form-group { margin-bottom: 1.2rem; }

        .form-label {
            display: block;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.25s;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: rgba(212,175,55,0.5);
            background: rgba(255,255,255,0.08);
            box-shadow: 0 0 0 3px rgba(212,175,55,0.1);
        }

        .form-input::placeholder { color: rgba(255,255,255,0.3); }

        .card-icons {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .card-icon {
            padding: 0.25rem 0.6rem;
            background: rgba(255,255,255,0.08);
            border-radius: 6px;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
        }

        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        /* UPI Section */
        .upi-section { display: none; }
        .netbanking-section { display: none; }

        .upi-apps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .upi-app {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 0.75rem 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
        }

        .upi-app:hover, .upi-app.selected {
            border-color: rgba(212,175,55,0.4);
            background: rgba(212,175,55,0.1);
        }

        .upi-app i { font-size: 1.5rem; display: block; margin-bottom: 0.3rem; }
        .upi-app span { font-size: 0.75rem; color: rgba(255,255,255,0.6); }

        .bank-select {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #fff;
            font-size: 0.95rem;
            appearance: none;
        }

        .bank-select option { background: #1a1a2e; }

        /* Pay Button */
        .btn-pay {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #000;
            font-weight: 700;
            font-size: 1.05rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212,175,55,0.4);
        }

        .btn-pay:active { transform: translateY(0); }

        .pay-security {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.35);
        }

        .pay-security i { color: #4ade80; }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255,255,255,0.4);
            font-size: 0.9rem;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: color 0.25s;
        }

        .back-link:hover { color: rgba(255,255,255,0.7); }

        /* Loading overlay */
        .processing-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .spinner-ring {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(212,175,55,0.2);
            border-top-color: #D4AF37;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .processing-text {
            color: rgba(255,255,255,0.7);
            font-size: 1rem;
        }
    </style>
</head>
<body>

<!-- Processing Overlay -->
<div class="processing-overlay" id="processingOverlay">
    <div class="spinner-ring"></div>
    <div class="processing-text">Processing payment...</div>
</div>

<div class="payment-container">

    <a href="{{ route('subscription.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Plans
    </a>

    <!-- Demo Banner -->
    <div class="demo-banner">
        <i class="fas fa-flask"></i>
        <div>
            <strong>DEMO MODE</strong> — This is a sample payment screen. No real payment will be processed.
            You can enter any card details to proceed.
        </div>
    </div>

    <div class="payment-card">

        <!-- Order Summary -->
        <div class="order-summary">
            <div class="order-title">Order Summary</div>

            <div class="plan-summary-card">
                <div class="plan-summary-name">{{ $plan->name }}</div>
                <div class="plan-summary-duration">
                    <i class="fas fa-clock me-1"></i>{{ $plan->duration_days }} days access
                </div>
                <div class="plan-summary-price">
                    ₹{{ number_format($plan->price, 0) }}
                    <span>/ {{ $plan->duration_days }} days</span>
                </div>
            </div>

            @if($plan->features)
            <ul class="feature-list">
                @foreach($plan->features as $feature)
                <li>
                    <i class="fas fa-check-circle"></i>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>
            @endif

            <div class="secure-badge">
                <i class="fas fa-lock"></i>
                <span>256-bit SSL secured checkout</span>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="payment-form">
            <div class="form-title">Complete Payment</div>
            <div class="form-subtitle">Enter your payment details below</div>

            <!-- Method Tabs -->
            <div class="method-tabs">
                <button class="method-tab active" onclick="switchMethod('card', this)" type="button">
                    <i class="fas fa-credit-card"></i> Card
                </button>
                <button class="method-tab" onclick="switchMethod('upi', this)" type="button">
                    <i class="fas fa-mobile-alt"></i> UPI
                </button>
                <button class="method-tab" onclick="switchMethod('netbanking', this)" type="button">
                    <i class="fas fa-university"></i> Net Banking
                </button>
            </div>

            <form method="POST" action="{{ route('subscription.payment.process', $subscription->id) }}" onsubmit="showProcessing()">
                @csrf
                <input type="hidden" name="payment_method" id="payment_method" value="card">

                <!-- Card Section -->
                <div id="card-section">
                    <div class="form-group">
                        <label class="form-label">Card Number</label>
                        <input type="text" class="form-input" placeholder="1234 5678 9012 3456" maxlength="19"
                            oninput="formatCard(this)">
                        <div class="card-icons">
                            <span class="card-icon">VISA</span>
                            <span class="card-icon">MC</span>
                            <span class="card-icon">AMEX</span>
                            <span class="card-icon">RuPay</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cardholder Name</label>
                        <input type="text" class="form-input" placeholder="Name on card">
                    </div>
                    <div class="row-2">
                        <div class="form-group">
                            <label class="form-label">Expiry Date</label>
                            <input type="text" class="form-input" placeholder="MM / YY" maxlength="7"
                                oninput="formatExpiry(this)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CVV</label>
                            <input type="password" class="form-input" placeholder="•••" maxlength="4">
                        </div>
                    </div>
                </div>

                <!-- UPI Section -->
                <div id="upi-section" class="upi-section">
                    <div class="upi-apps">
                        <div class="upi-app" onclick="selectUpi(this)">
                            <i class="fas fa-mobile-alt" style="color:#6366f1;"></i>
                            <span>GPay</span>
                        </div>
                        <div class="upi-app" onclick="selectUpi(this)">
                            <i class="fas fa-mobile-alt" style="color:#0a66c2;"></i>
                            <span>PhonePe</span>
                        </div>
                        <div class="upi-app" onclick="selectUpi(this)">
                            <i class="fas fa-mobile-alt" style="color:#002970;"></i>
                            <span>Paytm</span>
                        </div>
                        <div class="upi-app" onclick="selectUpi(this)">
                            <i class="fas fa-mobile-alt" style="color:#D4AF37;"></i>
                            <span>BHIM</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Or enter UPI ID</label>
                        <input type="text" class="form-input" placeholder="yourname@upi">
                    </div>
                </div>

                <!-- Net Banking Section -->
                <div id="netbanking-section" class="netbanking-section">
                    <div class="form-group">
                        <label class="form-label">Select Bank</label>
                        <select class="bank-select">
                            <option value="">Choose your bank</option>
                            <option>State Bank of India</option>
                            <option>HDFC Bank</option>
                            <option>ICICI Bank</option>
                            <option>Axis Bank</option>
                            <option>Kotak Mahindra Bank</option>
                            <option>Punjab National Bank</option>
                            <option>Bank of Baroda</option>
                            <option>Other Bank</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-pay">
                    <i class="fas fa-lock me-1"></i>
                    Pay ₹{{ number_format($plan->price, 0) }} Securely
                </button>

                <div class="pay-security">
                    <i class="fas fa-shield-alt"></i>
                    Your payment info is encrypted and secure
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function switchMethod(method, btn) {
        // Update tabs
        document.querySelectorAll('.method-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');

        // Update sections
        document.getElementById('card-section').style.display = 'none';
        document.getElementById('upi-section').style.display = 'none';
        document.getElementById('netbanking-section').style.display = 'none';
        document.getElementById(method + '-section').style.display = 'block';

        // Update hidden input
        document.getElementById('payment_method').value = method;
    }

    function formatCard(input) {
        let v = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let matches = v.match(/\d{4,16}/g);
        let match = matches && matches[0] || '';
        let parts = [];
        for (let i = 0, len = match.length; i < len; i += 4) {
            parts.push(match.substring(i, i + 4));
        }
        input.value = parts.length ? parts.join(' ') : v;
    }

    function formatExpiry(input) {
        let v = input.value.replace(/\D/g, '');
        if (v.length >= 2) {
            input.value = v.substring(0, 2) + ' / ' + v.substring(2, 4);
        } else {
            input.value = v;
        }
    }

    function selectUpi(el) {
        document.querySelectorAll('.upi-app').forEach(a => a.classList.remove('selected'));
        el.classList.add('selected');
    }

    function showProcessing() {
        document.getElementById('processingOverlay').style.display = 'flex';
    }
</script>
</body>
</html>
