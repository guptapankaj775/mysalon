@extends('layouts.admin')

@section('title', 'Subscribers')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Subscribers</h1>
            <p class="text-muted mb-0">All users who selected a subscription plan</p>
        </div>
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-warning">
            <i class="fas fa-layer-group me-2"></i>Manage Plans
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 text-center p-3" style="background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.2) !important; border-radius: 14px;">
                <div class="fs-2 fw-bold text-info">{{ $stats['total'] }}</div>
                <div class="text-muted small">Total Subscriptions</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 text-center p-3" style="background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.2) !important; border-radius: 14px;">
                <div class="fs-2 fw-bold text-success">{{ $stats['active'] }}</div>
                <div class="text-muted small">Active Subscribers</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 text-center p-3" style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.2) !important; border-radius: 14px;">
                <div class="fs-2 fw-bold text-danger">{{ $stats['expired'] }}</div>
                <div class="text-muted small">Expired</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 text-center p-3" style="background: rgba(212,175,55,0.12); border: 1px solid rgba(212,175,55,0.2) !important; border-radius: 14px;">
                <div class="fs-2 fw-bold text-warning">{{ $stats['trial'] }}</div>
                <div class="text-muted small">On Free Trial</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 mb-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08) !important; border-radius: 14px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label small text-muted mb-1">Search User</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Name or email..." value="{{ request('search') }}">
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label small text-muted mb-1">Plan</label>
                    <select name="plan_id" class="form-select form-select-sm">
                        <option value="">All Plans</option>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.subscribers') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08) !important; border-radius: 14px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0" style="border-radius: 14px; overflow: hidden;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <th class="ps-4 py-3 text-muted small text-uppercase">User</th>
                            <th class="py-3 text-muted small text-uppercase">Plan</th>
                            <th class="py-3 text-muted small text-uppercase">Status</th>
                            <th class="py-3 text-muted small text-uppercase">Started</th>
                            <th class="py-3 text-muted small text-uppercase">Expires</th>
                            <th class="py-3 text-muted small text-uppercase">Payment</th>
                            <th class="py-3 text-muted small text-uppercase">Amount</th>
                            <th class="py-3 text-muted small text-uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $sub)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm" style="width:36px;height:36px;border-radius:50%;background:rgba(212,175,55,0.2);display:flex;align-items:center;justify-content:center;font-weight:700;color:#D4AF37;font-size:0.85rem;flex-shrink:0;">
                                        {{ strtoupper(substr($sub->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-white" style="font-size:0.9rem;">
                                            {{ $sub->user->name ?? 'Deleted User' }}
                                        </div>
                                        <div class="text-muted" style="font-size:0.78rem;">{{ $sub->user->email ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="fw-semibold text-white">{{ $sub->plan->name ?? '—' }}</span>
                                @if($sub->plan && $sub->plan->is_trial)
                                <span class="badge bg-success ms-1" style="font-size:0.65rem;">Trial</span>
                                @endif
                            </td>
                            <td class="py-3">
                                {!! $sub->status_badge !!}
                            </td>
                            <td class="py-3 text-muted small">
                                {{ $sub->starts_at ? $sub->starts_at->format('d M Y') : '—' }}
                            </td>
                            <td class="py-3 text-muted small">
                                @if($sub->expires_at)
                                    <span class="{{ $sub->expires_at->isPast() ? 'text-danger' : ($sub->expires_at->diffInDays() <= 7 ? 'text-warning' : 'text-muted') }}">
                                        {{ $sub->expires_at->format('d M Y') }}
                                        @if($sub->status === 'active' && $sub->expires_at->isFuture())
                                        <br><span style="font-size:0.75rem;">({{ $sub->days_remaining }}d left)</span>
                                        @endif
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3">
                                @php
                                    $payBadge = match($sub->payment_status) {
                                        'paid'  => ['bg-success', 'Paid'],
                                        'free'  => ['bg-info text-dark', 'Free'],
                                        default => ['bg-warning text-dark', 'Pending'],
                                    };
                                @endphp
                                <span class="badge {{ $payBadge[0] }}">{{ $payBadge[1] }}</span>
                            </td>
                            <td class="py-3 text-white fw-semibold">
                                {{ $sub->amount_paid > 0 ? '₹' . number_format($sub->amount_paid, 0) : '—' }}
                            </td>
                            <td class="py-3">
                                <!-- Quick Status Update -->
                                <form action="{{ route('admin.subscribers.update-status', $sub) }}" method="POST" class="d-flex gap-1 align-items-center">
                                    @csrf @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" style="width:auto;font-size:0.8rem;padding:0.25rem 0.5rem;">
                                        @foreach(['active','expired','cancelled','pending'] as $s)
                                        <option value="{{ $s }}" {{ $sub->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Update">
                                        <i class="fas fa-save" style="font-size:0.75rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-id-card fa-2x mb-2 d-block opacity-25"></i>
                                No subscribers found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subscribers->hasPages())
            <div class="p-3 d-flex justify-content-center">
                {{ $subscribers->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
