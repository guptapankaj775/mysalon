<x-admin-layout>
    @push('styles')
    <style>
        .brands-page {
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
            padding-bottom: 50px;
        }

        .magento-sticky-header {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1000;
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .form-card {
            background: white;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            padding: 30px;
        }

        .form-switch-gold .form-check-input {
            width: 3rem;
            height: 1.5rem;
            cursor: pointer;
        }

        .form-switch-gold .form-check-input:checked {
            background-color: #00A3B1;
            border-color: #00A3B1;
        }

        .border-gold-focus:focus {
            border-color: #00A3B1 !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 163, 177, 0.25) !important;
        }
    </style>
    @endpush

    @section('content')
    <div class="brands-page">
        <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="magento-sticky-header d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="mb-0 fw-bold" style="font-size: 1.15rem; color: #2d3748;">Edit Brand: {{ $brand->name }}</h5>
                </div>
                <div class="actions d-flex gap-2">
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center" style="height: 30px; font-size: 0.85rem;">
                        <i class="fas fa-chevron-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-warning text-dark fw-bold btn-sm d-flex align-items-center" style="background-color: #00A3B1; border-color: #00A3B1; height: 30px; font-size: 0.85rem;">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                </div>
            </div>

            <div class="container">
                <div class="form-card">
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <label for="name" class="form-label fw-bold">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control border-gold-focus @error('name') is-invalid @enderror" placeholder="e.g. L'Oreal, Schwarzkopf" value="{{ old('name', $brand->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold d-block">Status</label>
                            <div class="form-check form-switch form-switch-gold ps-0 d-flex align-items-center mt-2">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="status" name="status" value="1"
                                    {{ old('status', $brand->status) ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="status">Enabled</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control border-gold-focus @error('description') is-invalid @enderror" placeholder="Write description notes about this brand...">{{ old('description', $brand->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @endsection
</x-admin-layout>
