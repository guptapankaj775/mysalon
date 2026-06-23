@extends('layouts.admin')

@section('title', 'Subscription Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Subscription Settings</h1>
            <p class="text-muted mb-0">Configure trial duration, feature restrictions, and notices</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('admin.subscription.settings.update') }}" method="POST">
                @csrf

                <!-- Trial Settings -->
                <div class="card border-0 mb-4" style="background: rgba(34,197,94,0.06); border: 1px solid rgba(34,197,94,0.2) !important; border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:44px;height:44px;background:rgba(34,197,94,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-clock text-success fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-white">Free Trial Duration</h5>
                                <p class="text-muted small mb-0">How many days does the free trial last</p>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="number" name="trial_days" class="form-control form-control-lg fw-bold text-center @error('trial_days') is-invalid @enderror"
                                        value="{{ old('trial_days', $trialDays) }}" min="1" max="365" required>
                                    <span class="input-group-text text-muted">days</span>
                                </div>
                                @error('trial_days') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-8">
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    New users who select Free Trial will get <strong class="text-success" id="trial-preview">{{ $trialDays }}</strong> days of access.
                                    Changing this affects only <strong>new</strong> trial activations.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Limited Features -->
                <div class="card border-0 mb-4" style="background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2) !important; border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:44px;height:44px;background:rgba(239,68,68,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-lock text-danger fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-white">Locked Dashboard Features</h5>
                                <p class="text-muted small mb-0">Select which sections are locked for users without an active plan</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach($allFeatures as $key => $label)
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3 {{ in_array($key, $limitedFeatures) ? 'locked-feature' : '' }}"
                                     style="background: rgba(255,255,255,0.04); border: 1px solid {{ in_array($key, $limitedFeatures) ? 'rgba(239,68,68,0.3)' : 'rgba(255,255,255,0.08)' }}; cursor:pointer;"
                                     onclick="document.getElementById('feat-{{ $key }}').click()">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox"
                                            name="limited_features[]"
                                            id="feat-{{ $key }}"
                                            value="{{ $key }}"
                                            {{ in_array($key, $limitedFeatures) ? 'checked' : '' }}>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-white" style="font-size:0.9rem;">{{ $label }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Section: <code>{{ $key }}</code></div>
                                    </div>
                                    <i class="fas {{ in_array($key, $limitedFeatures) ? 'fa-lock text-danger' : 'fa-lock-open text-success' }}"></i>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="alert alert-info mt-3 d-flex gap-2 align-items-start" style="background:rgba(59,130,246,0.1);border-color:rgba(59,130,246,0.3);">
                            <i class="fas fa-info-circle mt-1"></i>
                            <span class="small">Checked features will show a locked overlay on the dashboard for users without an active subscription.</span>
                        </div>
                    </div>
                </div>

                <!-- Notice Message -->
                <div class="card border-0 mb-4" style="background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.2) !important; border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:44px;height:44px;background:rgba(245,158,11,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-bell text-warning fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-white">Notice Message</h5>
                                <p class="text-muted small mb-0">Shown on the dashboard to users without an active plan</p>
                            </div>
                        </div>

                        <textarea name="notice_message" class="form-control @error('notice_message') is-invalid @enderror"
                            rows="3" maxlength="500" placeholder="Notice message for non-subscribers...">{{ old('notice_message', $noticeMessage) }}</textarea>
                        @error('notice_message') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        <!-- Preview -->
                        <div class="mt-3 p-3 rounded-3" style="background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.3);">
                            <div class="small text-muted mb-1">Preview:</div>
                            <div style="color:#fbbf24; font-size:0.9rem;">{{ old('notice_message', $noticeMessage) }}</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning text-dark fw-bold px-5 py-2">
                    <i class="fas fa-save me-2"></i>Save Settings
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('input[name="trial_days"]').addEventListener('input', function() {
    document.getElementById('trial-preview').textContent = this.value || '0';
});
</script>
@endsection
