<x-admin-layout>
    @push('styles')
    <style>
        .subscription-page {
            padding: 2rem 0;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(212,175,55,0.12);
            border: 1px solid rgba(212,175,55,0.25);
            border-radius: 50px;
            padding: 0.4rem 1.2rem;
            font-size: 0.85rem;
            color: #b38600;
            font-weight: 600;
            margin-bottom: 1.2rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2c2c2c;
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #666;
            max-width: 550px;
            margin: 0 auto;
        }

        .trial-highlight {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.25);
            color: #16a34a;
            border-radius: 50px;
            padding: 0.35rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 1rem;
        }

        /* Plan Cards */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.25rem;
            max-width: 950px;
            margin: 0 auto;
        }

        .plan-card {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.015);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .plan-card:hover {
            border-color: #D4AF37;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.06), 0 0 0 1px rgba(212,175,55,0.12);
        }

        .plan-card.featured {
            background: linear-gradient(135deg, rgba(212,175,55,0.03) 0%, rgba(184,134,11,0.01) 100%);
            border-color: rgba(212,175,55,0.35);
            box-shadow: 0 8px 25px rgba(212,175,55,0.06);
        }

        .plan-card.trial-card {
            background: linear-gradient(135deg, rgba(34,197,94,0.03) 0%, rgba(16,185,129,0.01) 100%);
            border-color: rgba(34,197,94,0.2);
        }

        .plan-card.trial-card:hover {
            border-color: rgba(34,197,94,0.4);
            box-shadow: 0 10px 25px rgba(0,0,0,0.06), 0 0 0 1px rgba(34,197,94,0.12);
        }

        /* Badge */
        .plan-badge {
            position: absolute;
            top: -1px;
            right: 1.2rem;
            padding: 0.3rem 0.8rem;
            border-radius: 0 0 10px 10px;
            font-size: 0.7rem;
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
            color: #fff;
        }

        /* Plan content */
        .plan-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 0.85rem;
        }

        .icon-trial { background: rgba(34,197,94,0.1); color: #16a34a; }
        .icon-basic { background: rgba(59,130,246,0.1); color: #2563eb; }
        .icon-pro   { background: rgba(212,175,55,0.12); color: #b38600; }

        .plan-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #2c2c2c;
            margin-bottom: 0.25rem;
        }

        .plan-description {
            font-size: 0.82rem;
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
            min-height: auto;
        }

        .plan-price {
            margin-bottom: 1.25rem;
        }

        .price-amount {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
            color: #2c2c2c;
        }

        .price-amount.free-price {
            background: linear-gradient(135deg, #16a34a, #15803d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .price-currency {
            font-size: 1.15rem;
            font-weight: 600;
            vertical-align: top;
            margin-top: 0.3rem;
            display: inline-block;
            color: #444;
        }

        .price-period {
            font-size: 0.8rem;
            color: #777;
            margin-top: 0.2rem;
        }

        .trial-duration {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: rgba(34,197,94,0.08);
            color: #16a34a;
            border-radius: 6px;
            padding: 0.2rem 0.6rem;
            font-size: 0.78rem;
            font-weight: 600;
            margin-top: 0.4rem;
        }

        /* Features */
        .plan-features {
            list-style: none;
            margin-bottom: 1.25rem;
            padding-left: 0;
            flex-grow: 1;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.82rem;
            color: #444;
            padding: 0.3rem 0;
        }

        .plan-features li .feat-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            flex-shrink: 0;
        }

        .feat-icon-trial { background: rgba(34,197,94,0.1); color: #16a34a; }
        .feat-icon-basic { background: rgba(59,130,246,0.1); color: #2563eb; }
        .feat-icon-pro   { background: rgba(212,175,55,0.12); color: #b38600; }

        /* Buttons */
        .btn-select-trial {
            display: block;
            width: 100%;
            padding: 0.65rem;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-select-trial:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(34,197,94,0.25);
        }

        .btn-select-basic {
            display: block;
            width: 100%;
            padding: 0.65rem;
            background: rgba(59,130,246,0.06);
            color: #2563eb;
            font-weight: 700;
            font-size: 0.9rem;
            border: 1px solid rgba(59,130,246,0.15);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-select-basic:hover {
            background: rgba(59,130,246,0.12);
            border-color: rgba(59,130,246,0.3);
            transform: translateY(-2px);
        }

        .btn-select-pro {
            display: block;
            width: 100%;
            padding: 0.65rem;
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-select-pro:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(212,175,55,0.2);
        }

        /* Footer note */
        .footer-note {
            text-align: center;
            margin-top: 3rem;
            color: #666;
            font-size: 0.85rem;
        }

        .footer-note i { color: #16a34a; margin-right: 0.25rem; }

        @media (max-width: 768px) {
            .plans-grid { grid-template-columns: 1fr; max-width: 420px; }
        }
    </style>
    @endpush

    @section('content')
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

                    <div>
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
                    </div>

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
    <form id="plan-select-form" action="{{ isset($currentSalon) ? route('salon.subscription.select', ['salon' => $currentSalon->slug]) : route('subscription.select') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="plan_id" id="selected-plan-id">
    </form>
    @endsection

    @push('scripts')
    <script>
        function selectPlan(planId) {
            document.getElementById('selected-plan-id').value = planId;
            document.getElementById('plan-select-form').submit();
        }
    </script>
    @endpush
</x-admin-layout>
