@extends('layouts.admin')

@section('title', 'Create Subscription Plan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Create Subscription Plan</h1>
            <p class="text-muted mb-0">Add a new plan for salon users</p>
        </div>
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Plans
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08) !important; border-radius: 16px;">
                <div class="card-body p-4">
                    <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label text-white fw-semibold">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="e.g. Basic, Pro, Premium" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-white fw-semibold">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control"
                                    value="{{ old('sort_order', 0) }}" min="0">
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="2"
                                    placeholder="Brief description of this plan">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Price (₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price', 0) }}" min="0" step="0.01" required>
                                </div>
                                <small class="text-muted">Set 0 for free plans</small>
                                @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Duration (Days) <span class="text-danger">*</span></label>
                                <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror"
                                    value="{{ old('duration_days', 30) }}" min="1" required>
                                @error('duration_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white fw-semibold">Features</label>
                                <textarea name="features" class="form-control" rows="6"
                                    placeholder="Enter one feature per line:&#10;Full dashboard access&#10;Unlimited bookings&#10;Priority support">{{ old('features') }}</textarea>
                                <small class="text-muted">One feature per line</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_trial" id="is_trial"
                                        value="1" {{ old('is_trial') ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="is_trial">
                                        <i class="fas fa-gift me-1 text-success"></i> This is a Free Trial plan
                                    </label>
                                </div>
                                <small class="text-muted">Trial plans skip payment and activate immediately</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="is_active">
                                        <i class="fas fa-eye me-1 text-info"></i> Active (visible to users)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-4">
                                <i class="fas fa-save me-2"></i>Create Plan
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
