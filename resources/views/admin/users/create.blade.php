<x-admin-layout>
    @section('title', 'Add New Salon / User')

    @section('content')
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h3 class="h4 text-gray-900 font-bold mb-0">Add New Salon / User</h3>
                <p class="text-muted small mb-0">Create a new salon business entity or user account.</p>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Salon List
                </a>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <h6 class="alert-heading font-semibold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Validation Errors:</h6>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <!-- Section 1: Salon Business Details -->
                    <div class="mb-4">
                        <h5 class="card-title text-gold font-semibold border-bottom pb-2 mb-3">
                            <i class="fas fa-store me-2"></i>Salon Business Details
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="salon_name" class="form-label font-medium">Salon Business Name</label>
                                <input type="text" class="form-control border-gold-focus @error('salon_name') is-invalid @enderror"
                                    id="salon_name" name="salon_name" value="{{ old('salon_name') }}" placeholder="e.g. Royal Glamour Salon">
                                @error('salon_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="slug" class="form-label font-medium">Salon URL Slug</label>
                                <input type="text" class="form-control border-gold-focus @error('slug') is-invalid @enderror"
                                    id="slug" name="slug" value="{{ old('slug') }}" placeholder="e.g. royal-glamour (auto-generated if empty)">
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Unique URL identifier used for salon login and portal access.</small>
                                @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="salon_type" class="form-label font-medium">Salon Type</label>
                                <select class="form-select border-gold-focus @error('salon_type') is-invalid @enderror" id="salon_type" name="salon_type">
                                    <option value="Unisex Salon" {{ old('salon_type') === 'Unisex Salon' ? 'selected' : '' }}>Unisex Salon</option>
                                    <option value="Hair Salon" {{ old('salon_type') === 'Hair Salon' ? 'selected' : '' }}>Hair Salon</option>
                                    <option value="Beauty Parlour" {{ old('salon_type') === 'Beauty Parlour' ? 'selected' : '' }}>Beauty Parlour</option>
                                    <option value="Spa & Wellness" {{ old('salon_type') === 'Spa & Wellness' ? 'selected' : '' }}>Spa & Wellness</option>
                                    <option value="Nail Studio" {{ old('salon_type') === 'Nail Studio' ? 'selected' : '' }}>Nail Studio</option>
                                </select>
                                @error('salon_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label font-medium">Salon Phone / Mobile</label>
                                <input type="text" class="form-control border-gold-focus @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. +91 9876543210">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="address" class="form-label font-medium">Full Address</label>
                                <input type="text" class="form-control border-gold-focus @error('address') is-invalid @enderror"
                                    id="address" name="address" value="{{ old('address') }}" placeholder="e.g. Shop 12, Main Street Block A">
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label font-medium">City</label>
                                <input type="text" class="form-control border-gold-focus @error('city') is-invalid @enderror"
                                    id="city" name="city" value="{{ old('city') }}" placeholder="e.g. Mumbai">
                            </div>
                            <div class="col-md-4">
                                <label for="state" class="form-label font-medium">State</label>
                                <input type="text" class="form-control border-gold-focus @error('state') is-invalid @enderror"
                                    id="state" name="state" value="{{ old('state') }}" placeholder="e.g. Maharashtra">
                            </div>
                            <div class="col-md-4">
                                <label for="zip" class="form-label font-medium">Zip / Postal Code</label>
                                <input type="text" class="form-control border-gold-focus @error('zip') is-invalid @enderror"
                                    id="zip" name="zip" value="{{ old('zip') }}" placeholder="e.g. 400001">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Account Owner & Login Details -->
                    <div class="mb-4">
                        <h5 class="card-title text-gold font-semibold border-bottom pb-2 mb-3">
                            <i class="fas fa-user-shield me-2"></i>Account Owner & Login Credentials
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label font-medium">Owner Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-gold-focus @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Anchal Parashar" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label font-medium">Login Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control border-gold-focus @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" placeholder="owner@salon.com" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label font-medium">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control border-gold-focus @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label font-medium">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control border-gold-focus" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label font-medium">Account Access Role <span class="text-danger">*</span></label>
                                <select class="form-select border-gold-focus @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>Salon Owner / Merchant (User)</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Salon Staff Member</option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input border-2" id="is_verified" name="is_verified" value="1"
                                        {{ old('is_verified', true) ? 'checked' : '' }}>
                                    <label for="is_verified" class="form-check-label font-medium">Verified Salon Account (Enable Login)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4 font-semibold text-dark shadow-sm">
                            <i class="fas fa-check me-1"></i> Save & Create Salon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
</x-admin-layout>
