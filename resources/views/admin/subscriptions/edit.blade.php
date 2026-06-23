@extends('layouts.admin')

@section('title', 'Edit Subscription Plan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Edit Plan: {{ $subscription->name }}</h1>
            <p class="text-muted mb-0">Update subscription plan details</p>
        </div>
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Plans
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08) !important; border-radius: 16px;">
                <div class="card-body p-4">
                    <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label text-white fw-semibold">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $subscription->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-white fw-semibold">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control"
                                    value="{{ old('sort_order', $subscription->sort_order) }}" min="0">
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description', $subscription->description) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Price (₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price', $subscription->price) }}" min="0" step="0.01" required>
                                </div>
                                @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Duration (Days) <span class="text-danger">*</span></label>
                                <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror"
                                    value="{{ old('duration_days', $subscription->duration_days) }}" min="1" required>
                                @error('duration_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white fw-semibold">Features</label>
                                <textarea name="features" class="form-control" rows="6">{{ old('features', $subscription->features ? implode("\n", $subscription->features) : '') }}</textarea>
                                <small class="text-muted">One feature per line</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_trial" id="is_trial"
                                        value="1" {{ old('is_trial', $subscription->is_trial) ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="is_trial">
                                        <i class="fas fa-gift me-1 text-success"></i> Free Trial plan
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="is_active">
                                        <i class="fas fa-eye me-1 text-info"></i> Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Current subscribers warning -->
                        @if($subscription->subscriptions()->where('status','active')->count() > 0)
                        <div class="alert alert-warning mt-3 d-flex align-items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>This plan has <strong>{{ $subscription->subscriptions()->where('status','active')->count() }} active subscriber(s)</strong>. Price/duration changes won't affect existing subscriptions.</span>
                        </div>
                        @endif

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-4">
                                <i class="fas fa-save me-2"></i>Update Plan
                            </button>
                            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
