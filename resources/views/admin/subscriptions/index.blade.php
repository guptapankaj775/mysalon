@extends('layouts.admin')

@section('title', 'Subscription Plans')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Subscription Plans</h1>
            <p class="text-muted mb-0">Manage available subscription plans</p>
        </div>
        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-warning text-dark fw-bold">
            <i class="fas fa-plus me-2"></i>Add New Plan
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        @forelse($plans as $plan)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm" style="background: rgba(255,255,255,0.05); border-radius: 16px; border: 1px solid rgba(255,255,255,0.08) !important;">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="mb-0 fw-bold text-white">{{ $plan->name }}</h5>
                                @if($plan->is_trial)
                                <span class="badge bg-success" style="font-size:0.7rem;">Trial</span>
                                @endif
                            </div>
                            <div class="text-muted small">{{ $plan->slug }}</div>
                        </div>
                        <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Price -->
                    <div class="mb-3 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                        @if($plan->price == 0)
                            <div class="fs-2 fw-bold" style="color: #4ade80;">Free</div>
                        @else
                            <div class="fs-2 fw-bold text-warning">₹{{ number_format($plan->price, 0) }}</div>
                        @endif
                        <div class="text-muted small">{{ $plan->duration_days }} days access</div>
                    </div>

                    <!-- Description -->
                    @if($plan->description)
                    <p class="text-muted small mb-3">{{ $plan->description }}</p>
                    @endif

                    <!-- Features -->
                    @if($plan->features)
                    <ul class="list-unstyled mb-3">
                        @foreach(array_slice($plan->features, 0, 4) as $feature)
                        <li class="small text-muted mb-1">
                            <i class="fas fa-check text-success me-1" style="font-size:0.7rem;"></i>
                            {{ $feature }}
                        </li>
                        @endforeach
                        @if(count($plan->features) > 4)
                        <li class="small text-muted">+ {{ count($plan->features) - 4 }} more...</li>
                        @endif
                    </ul>
                    @endif

                    <!-- Stats -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="text-center">
                            <div class="fw-bold text-white">{{ $plan->subscriptions_count }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">Total Subs</div>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold text-white">{{ $plan->sort_order }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">Sort Order</div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.subscriptions.edit', $plan) }}" class="btn btn-sm btn-outline-warning flex-fill">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <form action="{{ route('admin.subscriptions.destroy', $plan) }}" method="POST"
                              onsubmit="return confirm('Delete this plan? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No subscription plans yet</h5>
                <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-warning mt-3 text-dark fw-bold">
                    Create First Plan
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
