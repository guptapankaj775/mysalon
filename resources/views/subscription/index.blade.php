<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Choose Your Plan — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0f;
            color: #fff;
            min-height: 100vh;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at 20% 30%, rgba(212,175,55,0.12) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 70%, rgba(139,90,43,0.1) 0%, transparent 50%);
            z-index: 0;
            pointer-events: none;
        }

        .subscription-page {
            position: relative;
            z-index: 1;
            padding: 3rem 0;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(212,175,55,0.15);
            border: 1px solid rgba(212,175,55,0.3);
            border-radius: 50px;
            padding: 0.4rem 1.2rem;
            font-size: 0.85rem;
            color: #D4AF37;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #D4AF37 60%, #B8860B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .page-header p {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.65);
            max-width: 550px;
            margin: 0 auto;
        }

        .trial-highlight {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(34,197,94,0.15);
            border: 1px solid rgba(34,197,94,0.3);
            color: #4ade80;
            border-radius: 50px;
            padding: 0.35rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 1rem;
        }

        /* Plan Cards */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        .plan-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .plan-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(212,175,55,0.05) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .plan-card:hover {
            border-color: rgba(212,175,55,0.4);
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(212,175,55,0.15);
        }

        .plan-card:hover::before {
            opacity: 1;
        }

        .plan-card.featured {
            background: linear-gradient(135deg, rgba(212,175,55,0.12) 0%, rgba(184,134,11,0.08) 100%);
            border-color: rgba(212,175,55,0.5);
            box-shadow: 0 0 40px rgba(212,175,55,0.15);
        }

        .plan-card.trial-card {
            background: linear-gradient(135deg, rgba(34,197,94,0.08) 0%, rgba(16,185,129,0.05) 100%);
            border-color: rgba(34,197,94,0.3);
        }

        .plan-card.trial-card:hover {
            border-color: rgba(34,197,94,0.6);
            box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(34,197,94,0.2);
        }

        /* Badge */
        .plan-badge {
            position: absolute;
            top: -1px;
            right: 1.5rem;
            padding: 0.4rem 1rem;
            border-radius: 0 0 12px 12px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-trial {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
        }

        .badge-popular {
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #000;
        }

        /* Plan content */
        .plan-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .icon-trial { background: rgba(34,197,94,0.2); color: #4ade80; }
        .icon-basic { background: rgba(59,130,246,0.2); color: #60a5fa; }
        .icon-pro   { background: rgba(212,175,55,0.2); color: #D4AF37; }

        .plan-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.4rem;
        }

        .plan-description {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.55);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .plan-price {
            margin-bottom: 1.75rem;
        }

        .price-amount {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1;
            color: #fff;
        }

        .price-amount.free-price {
            background: linear-gradient(135deg, #4ade80, #22c55e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .price-currency {
            font-size: 1.4rem;
            font-weight: 600;
            vertical-align: top;
            margin-top: 0.5rem;
            display: inline-block;
            color: rgba(255,255,255,0.7);
        }

        .price-period {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.45);
            margin-top: 0.3rem;
        }

        .trial-duration {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(34,197,94,0.15);
            color: #4ade80;
            border-radius: 8px;
            padding: 0.3rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        /* Features */
        .plan-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.75);
            padding: 0.45rem 0;
        }

        .plan-features li .feat-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
        }

        .feat-icon-trial { background: rgba(34,197,94,0.2); color: #4ade80; }
        .feat-icon-basic { background: rgba(59,130,246,0.2); color: #60a5fa; }
        .feat-icon-pro   { background: rgba(212,175,55,0.2); color: #D4AF37; }

        /* Buttons */
        .btn-select-trial {
            display: block;
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-select-trial:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(34,197,94,0.4);
        }

        .btn-select-basic {
            display: block;
            width: 100%;
            padding: 0.9rem;
            background: rgba(59,130,246,0.15);
            color: #60a5fa;
            font-weight: 700;
            font-size: 1rem;
            border: 1px solid rgba(59,130,246,0.3);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-select-basic:hover {
            background: rgba(59,130,246,0.25);
            border-color: rgba(59,130,246,0.6);
            transform: translateY(-2px);
        }

        .btn-select-pro {
            display: block;
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #000;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-select-pro:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212,175,55,0.4);
        }

        /* Footer note */
        .footer-note {
            text-align: center;
            margin-top: 3rem;
            color: rgba(255,255,255,0.4);
            font-size: 0.85rem;
        }

        .footer-note i { color: #4ade80; margin-right: 0.25rem; }

        /* Divider */
        .or-divider {
            text-align: center;
            color: rgba(255,255,255,0.3);
            font-size: 0.8rem;
            margin: 2rem 0 1rem;
        }

        @media (max-width: 768px) {
            .plans-grid { grid-template-columns: 1fr; max-width: 420px; }
            .subscription-page { padding: 2rem 1rem; }
        }
    </style>
</head>
<body>
<div class="subscription-page">
    <div class="container">

        <!-- Header -->
        <div class="page-header">
            <div class="logo-badge">
                <i class="fas fa-crown"></i> SalonJC Subscription
            </div>
            <h1>Choose Your Plan</h1>
            <p>Start with a free trial — no credit card required. Upgrade anytime to unlock full features.</p>
            <div class="trial-highlight">
                <i class="fas fa-gift"></i>
                {{ $trialDays }}-day free trial available for all new accounts
            </div>
        </div>

        <!-- Plans Grid -->
        <div class="plans-grid">
            @foreach($plans as $plan)
            <div class="plan-card {{ $plan->is_trial ? 'trial-card' : ($loop->index == 2 ? 'featured' : '') }}" 
                 onclick="selectPlan({{ $plan->id }})">

                @if($plan->is_trial)
                    <div class="plan-badge badge-trial"><i class="fas fa-star me-1"></i>Start Free</div>
                @elseif($loop->index == 2)
                    <div class="plan-badge badge-popular"><i class="fas fa-fire me-1"></i>Most Popular</div>
                @endif

                <div class="plan-icon {{ $plan->is_trial ? 'icon-trial' : ($loop->index == 1 ? 'icon-basic' : 'icon-pro') }}">
                    @if($plan->is_trial)
                        <i class="fas fa-gift"></i>
                    @elseif($loop->index == 1)
                        <i class="fas fa-store"></i>
                    @else
                        <i class="fas fa-crown"></i>
                    @endif
                </div>

                <div class="plan-name">{{ $plan->name }}</div>
                <div class="plan-description">{{ $plan->description }}</div>

                <div class="plan-price">
                    @if($plan->price == 0)
                        <div class="price-amount free-price">Free</div>
                        <div class="trial-duration">
                            <i class="fas fa-clock"></i>
                            {{ $trialDays }} days trial
                        </div>
                    @else
                        <span class="price-currency">₹</span>
                        <span class="price-amount">{{ number_format($plan->price, 0) }}</span>
                        <div class="price-period">per {{ $plan->duration_days }} days</div>
                    @endif
                </div>

                <ul class="plan-features">
                    @if($plan->features)
                        @foreach($plan->features as $feature)
                        <li>
                            <span class="feat-icon {{ $plan->is_trial ? 'feat-icon-trial' : ($loop->parent->index == 1 ? 'feat-icon-basic' : 'feat-icon-pro') }}">
                                <i class="fas fa-check"></i>
                            </span>
                            {{ $feature }}
                        </li>
                        @endforeach
                    @endif
                </ul>

                <button type="button"
                    class="{{ $plan->is_trial ? 'btn-select-trial' : ($loop->index == 1 ? 'btn-select-basic' : 'btn-select-pro') }}"
                    onclick="selectPlan({{ $plan->id }}); event.stopPropagation();">
                    @if($plan->is_trial)
                        <i class="fas fa-rocket me-2"></i>Start Free Trial
                    @else
                        <i class="fas fa-arrow-right me-2"></i>Get {{ $plan->name }}
                    @endif
                </button>
            </div>
            @endforeach
        </div>

        <!-- Footer note -->
        <div class="footer-note">
            <p><i class="fas fa-check-circle"></i> No credit card required for free trial &nbsp;|&nbsp; Cancel anytime &nbsp;|&nbsp; Secure checkout</p>
        </div>

    </div>
</div>

<!-- Hidden form -->
<form id="plan-select-form" action="{{ route('subscription.select') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="plan_id" id="selected-plan-id">
</form>

<script>
    function selectPlan(planId) {
        document.getElementById('selected-plan-id').value = planId;
        document.getElementById('plan-select-form').submit();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
