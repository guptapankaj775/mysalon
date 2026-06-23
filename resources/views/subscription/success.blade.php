<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription Activated — {{ config('app.name') }}</title>
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
            background: radial-gradient(ellipse at 50% 40%, rgba(34,197,94,0.12) 0%, transparent 60%),
                        radial-gradient(ellipse at 20% 80%, rgba(212,175,55,0.08) 0%, transparent 50%);
            z-index: 0;
        }

        .success-container {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 520px;
            width: 100%;
        }

        /* Animated checkmark */
        .success-icon-wrap {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
        }

        .success-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid rgba(34,197,94,0.3);
            animation: ring-pulse 2s ease-out infinite;
        }

        @keyframes ring-pulse {
            0%   { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            color: #fff;
            position: relative;
            animation: bounce-in 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            opacity: 0;
        }

        @keyframes bounce-in {
            0%   { transform: scale(0); opacity: 0; }
            80%  { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Content */
        .success-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.75rem;
            animation: fade-up 0.5s ease 0.3s both;
        }

        .success-subtitle {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.6);
            margin-bottom: 2rem;
            animation: fade-up 0.5s ease 0.4s both;
        }

        @keyframes fade-up {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        /* Plan card */
        .plan-activated-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 1.75rem;
            margin-bottom: 2rem;
            text-align: left;
            animation: fade-up 0.5s ease 0.5s both;
        }

        .plan-activated-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .plan-activated-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
        }

        .plan-activated-badge {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
        }

        .plan-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .plan-detail-item {
            background: rgba(255,255,255,0.04);
            border-radius: 10px;
            padding: 0.9rem;
        }

        .detail-label {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.4rem;
        }

        .detail-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
        }

        .detail-value.trial { color: #4ade80; }
        .detail-value.gold { color: #D4AF37; }

        /* CTA Button */
        .btn-go-dashboard {
            display: block;
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #000;
            font-weight: 700;
            font-size: 1.05rem;
            border: none;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.3s;
            animation: fade-up 0.5s ease 0.6s both;
        }

        .btn-go-dashboard:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(212,175,55,0.4);
            color: #000;
        }

        /* Confetti particles */
        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 2px;
            animation: fall linear forwards;
        }

        @keyframes fall {
            0%   { transform: translateY(-20px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
    </style>
</head>
<body>

<!-- Confetti Canvas -->
<div class="confetti" id="confetti"></div>

<div class="success-container">

    <!-- Icon -->
    <div class="success-icon-wrap">
        <div class="success-ring"></div>
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
    </div>

    <!-- Title -->
    <h1 class="success-title">
        @if($plan->is_trial)
            Free Trial Activated!
        @else
            Payment Successful!
        @endif
    </h1>
    <p class="success-subtitle">
        @if($plan->is_trial)
            Your {{ $plan->duration_days }}-day free trial has started. Explore all features!
        @else
            Your {{ $plan->name }} subscription is now active. Welcome aboard!
        @endif
    </p>

    <!-- Plan details -->
    <div class="plan-activated-card">
        <div class="plan-activated-header">
            <div class="plan-activated-name">{{ $plan->name }}</div>
            <div class="plan-activated-badge">
                <i class="fas fa-check-circle me-1"></i>Active
            </div>
        </div>

        <div class="plan-details-grid">
            <div class="plan-detail-item">
                <div class="detail-label">Plan Type</div>
                <div class="detail-value {{ $plan->is_trial ? 'trial' : 'gold' }}">
                    {{ $plan->is_trial ? 'Free Trial' : 'Paid Plan' }}
                </div>
            </div>
            <div class="plan-detail-item">
                <div class="detail-label">Duration</div>
                <div class="detail-value">{{ $subscription->expires_at ? $subscription->expires_at->diffInDays(now()) . ' days' : $plan->duration_days . ' days' }}</div>
            </div>
            <div class="plan-detail-item">
                <div class="detail-label">Valid Until</div>
                <div class="detail-value">
                    {{ $subscription->expires_at ? $subscription->expires_at->format('d M Y') : 'N/A' }}
                </div>
            </div>
            <div class="plan-detail-item">
                <div class="detail-label">Amount Paid</div>
                <div class="detail-value {{ $plan->is_trial ? 'trial' : '' }}">
                    {{ $plan->is_trial ? '₹0 (Free)' : '₹' . number_format($subscription->amount_paid, 0) }}
                </div>
            </div>
        </div>

        @if($subscription->payment_reference)
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
            <div class="detail-label">Transaction ID</div>
            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.5); margin-top: 0.3rem; font-family: monospace;">
                {{ $subscription->payment_reference }}
            </div>
        </div>
        @endif
    </div>

    <!-- CTA -->
    <a href="{{ route('dashboard') }}" class="btn-go-dashboard">
        <i class="fas fa-th-large me-2"></i>Go to Dashboard
    </a>

</div>

<script>
// Confetti animation
(function() {
    const container = document.getElementById('confetti');
    const colors = ['#D4AF37', '#4ade80', '#60a5fa', '#f472b6', '#a78bfa'];
    const count = 60;

    for (let i = 0; i < count; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.left = Math.random() * 100 + 'vw';
        p.style.background = colors[Math.floor(Math.random() * colors.length)];
        p.style.animationDuration = (Math.random() * 3 + 2) + 's';
        p.style.animationDelay = (Math.random() * 2) + 's';
        p.style.width = p.style.height = (Math.random() * 8 + 5) + 'px';
        p.style.opacity = Math.random() * 0.8 + 0.2;
        container.appendChild(p);
    }
})();
</script>
</body>
</html>
