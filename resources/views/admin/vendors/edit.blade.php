<x-admin-layout>
    @push('styles')
    <style>
        .btn-gold {
            background-color: #D4AF37;
            color: #fff;
            border-color: #D4AF37;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background-color: #bfa130;
            border-color: #bfa130;
            color: #fff;
            box-shadow: 0 4px 6px rgba(212, 175, 55, 0.2);
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }
        
        .form-check-input:checked {
            background-color: #D4AF37;
            border-color: #D4AF37;
        }
        
        .form-check-input:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 12px 20px;
        }

        .nav-tabs .nav-link.active {
            color: #D4AF37;
            border-bottom: 2px solid #D4AF37;
            background: transparent;
        }

        .nav-tabs .nav-link:hover {
            color: #D4AF37;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
        }

        .vendor-logo-preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            background: #fff;
            padding: 4px;
        }
    </style>
    @endpush

    @section('title', 'Edit Vendor')

    @section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h1 class="h2 text-dark fw-bold">Edit Vendor</h1>
                <p class="text-muted mb-0">Modify supplier profile details, banking, and settings.</p>
            </div>
            <div>
                <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary px-4">
                    <i class="fas fa-arrow-left me-2"></i>Back to Vendors
                </a>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="form-card border-0">
            <form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-4" id="vendorTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                            <i class="fas fa-info-circle me-2"></i>General Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="banking-tab" data-bs-toggle="tab" data-bs-target="#banking" type="button" role="tab" aria-controls="banking" aria-selected="false">
                            <i class="fas fa-university me-2"></i>Payment & Banking
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="false">
                            <i class="fas fa-map-marker-alt me-2"></i>Address & Notes
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="vendorTabContent">
                    
                    <!-- TAB 1: GENERAL INFO -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $vendor->name) }}" required placeholder="e.g. L'Oreal Professional">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_name" class="form-label fw-semibold">Contact Person Name</label>
                                <input type="text" class="form-control bg-light @error('contact_name') is-invalid @enderror" 
                                       id="contact_name" name="contact_name" value="{{ old('contact_name', $vendor->contact_name) }}" placeholder="e.g. John Smith">
                                @error('contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <input type="email" class="form-control bg-light @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $vendor->email) }}" placeholder="e.g. orders@loreal.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                <input type="text" class="form-control bg-light @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $vendor->phone) }}" placeholder="e.g. +1 555-0199">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label fw-semibold">Website URL</label>
                                <input type="text" class="form-control bg-light @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website', $vendor->website) }}" placeholder="e.g. https://www.loreal.com">
                                @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tax_number" class="form-label fw-semibold">Tax Registration ID (GST/VAT)</label>
                                <input type="text" class="form-control bg-light @error('tax_number') is-invalid @enderror" 
                                       id="tax_number" name="tax_number" value="{{ old('tax_number', $vendor->tax_number) }}" placeholder="e.g. 29GGPPP1234A1Z5">
                                @error('tax_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label fw-semibold">Brand Logo</label>
                            @if($vendor->logo_path)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $vendor->logo_path) }}" class="vendor-logo-preview" alt="current logo">
                                    <div class="form-text small text-muted">Current brand logo preview.</div>
                                </div>
                            @endif
                            <input class="form-control bg-light @error('logo') is-invalid @enderror" type="file" id="logo" name="logo" accept="image/*">
                            <div class="form-text text-muted">JPEG, PNG, JPG, GIF max 2MB. Leave empty to keep existing logo.</div>
                            @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- TAB 2: BANKING DETAILS -->
                    <div class="tab-pane fade" id="banking" role="tabpanel" aria-labelledby="banking-tab">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_terms" class="form-label fw-semibold">Payment Terms</label>
                                <select class="form-select bg-light @error('payment_terms') is-invalid @enderror" id="payment_terms" name="payment_terms">
                                    <option value="">Select Contract Terms (Optional)</option>
                                    <option value="Immediate" {{ old('payment_terms', $vendor->payment_terms) == 'Immediate' ? 'selected' : '' }}>Immediate / Advance</option>
                                    <option value="COD" {{ old('payment_terms', $vendor->payment_terms) == 'COD' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                                    <option value="Net 15" {{ old('payment_terms', $vendor->payment_terms) == 'Net 15' ? 'selected' : '' }}>Net 15 Days</option>
                                    <option value="Net 30" {{ old('payment_terms', $vendor->payment_terms) == 'Net 30' ? 'selected' : '' }}>Net 30 Days</option>
                                    <option value="Net 60" {{ old('payment_terms', $vendor->payment_terms) == 'Net 60' ? 'selected' : '' }}>Net 60 Days</option>
                                </select>
                                @error('payment_terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="bank_name" class="form-label fw-semibold">Bank Name</label>
                                <input type="text" class="form-control bg-light @error('bank_name') is-invalid @enderror" 
                                       id="bank_name" name="bank_name" value="{{ old('bank_name', $vendor->bank_name) }}" placeholder="e.g. HDFC Bank">
                                @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bank_account" class="form-label fw-semibold">Bank Account Number / IBAN</label>
                                <input type="text" class="form-control bg-light @error('bank_account') is-invalid @enderror" 
                                       id="bank_account" name="bank_account" value="{{ old('bank_account', $vendor->bank_account) }}" placeholder="e.g. 5010023456789">
                                @error('bank_account')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="bank_code" class="form-label fw-semibold">IFSC / SWIFT / Sort Code</label>
                                <input type="text" class="form-control bg-light @error('bank_code') is-invalid @enderror" 
                                       id="bank_code" name="bank_code" value="{{ old('bank_code', $vendor->bank_code) }}" placeholder="e.g. HDFC0000123">
                                @error('bank_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: ADDRESS & NOTES -->
                    <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                        <div class="mb-3">
                            <label for="address" class="form-label fw-semibold">Office Address</label>
                            <textarea class="form-control bg-light @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" placeholder="Enter physical or billing address...">{{ old('address', $vendor->address) }}</textarea>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Vendor Description & Contract Notes</label>
                            <textarea class="form-control bg-light @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" placeholder="Enter agreements, notes or catalog information...">{{ old('description', $vendor->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="my-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                           {{ old('status', $vendor->status) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="status">Active Supplier (Check if active)</label>
                    <div class="form-text text-muted">Inactive vendors cannot be mapped to new inventory items.</div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-gold px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    @endpush
</x-admin-layout>
