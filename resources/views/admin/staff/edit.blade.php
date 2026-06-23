<x-admin-layout>
    @push('styles')
    <style>
        .staff-page {
            padding: 40px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .service-category-title {
            color: #D4AF37;
            font-weight: 700;
            border-bottom: 2px solid rgba(212, 175, 55, 0.2);
            padding-bottom: 5px;
            margin-bottom: 15px;
            margin-top: 20px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .current-photo-container {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 15px;
            border: 2px solid #D4AF37;
        }

        .current-photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
    @endpush

    @section('content')
    <div class="staff-page">
        <div class="container">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Edit Staff Member</h1>
                <div class="actions">
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Staff
                    </a>
                </div>
            </div>

            <!-- Staff Form -->
            <div class="form-card">
                <form action="{{ route('admin.staff.update', $specialist->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Left column: Details -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Staff Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $specialist->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Biography / Details</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror"
                                    id="bio" name="bio" rows="4">{{ old('bio', $specialist->bio) }}</textarea>
                                @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Right column: Photo & Status -->
                        <div class="col-md-4">
                            @if($specialist->image_path)
                                <div class="mb-2">
                                    <label class="form-label d-block">Current Profile Photo</label>
                                    <div class="current-photo-container">
                                        <img src="{{ asset('storage/' . $specialist->image_path) }}" alt="{{ $specialist->name }}">
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="image" class="form-label">{{ $specialist->image_path ? 'Change Profile Image' : 'Profile Image' }}</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image" accept="image/*">
                                <small class="text-muted">Maximum file size: 1MB. Format: JPG, PNG, WEBP.</small>
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 mt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                        {{ old('status', $specialist->status) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="status">
                                        Active / Available for Bookings
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mapped Services Section -->
                    <div class="mt-4">
                        <h4 class="h5">Map Services to Staff Member</h4>
                        <p class="text-muted small">Select the services this staff member is trained to provide.</p>

                        @php
                            $groupedServices = $services->groupBy('category_id');
                            $mappedServiceIds = $specialist->services->pluck('id')->toArray();
                        @endphp

                        @forelse($groupedServices as $catId => $catServices)
                            @php
                                $category = $catServices->first()->category;
                            @endphp
                            @if($category)
                                <div class="service-category-title">
                                    <i class="fas {{ $category->icon_class ?? 'fa-scissors' }} me-2"></i> {{ $category->name }}
                                </div>
                                <div class="services-grid">
                                    @foreach($catServices as $service)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="services[]" 
                                            value="{{ $service->id }}" id="service_{{ $service->id }}"
                                            {{ (is_array(old('services')) && in_array($service->id, old('services'))) || (!is_array(old('services')) && in_array($service->id, $mappedServiceIds)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="service_{{ $service->id }}">
                                            {{ $service->name }} (Rs. {{ number_format($service->price, 2) }})
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        @empty
                            <div class="alert alert-warning">No active services available to map.</div>
                        @endforelse
                    </div>

                    <hr class="my-4">

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
</x-admin-layout>
