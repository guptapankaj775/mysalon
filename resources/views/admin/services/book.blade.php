<x-admin-layout>
    @section('title', 'Select Service to Book')

    @push('styles')
    <style>
        .book-services-page {
            padding: 0;
        }

        .magento-sticky-header {
            background: #fff;
            padding: 15px 30px;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
            margin-bottom: 30px;
        }

        .category-section {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .category-header {
            background-color: #fdfaf2;
            border-bottom: 1px solid #f7edd4;
            padding: 15px 25px;
            display: flex;
            align-items: center;
        }

        .category-title {
            color: #bfa13d;
            font-weight: 600;
            font-size: 1.15rem;
            margin: 0;
        }

        .category-body {
            padding: 25px;
        }

        .service-card {
            border: 1px solid #f1f3f5;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            background: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
            border-color: #d4af37;
        }

        .service-card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .service-name {
            font-size: 1.05rem;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 8px;
        }

        .service-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
            font-size: 0.85rem;
            color: #718096;
        }

        .service-desc {
            font-size: 0.9rem;
            color: #4a5568;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .btn-book-now {
            background-color: #d4af37;
            border-color: #d4af37;
            color: #1a1a1a;
            font-weight: 600;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-book-now:hover {
            background-color: #c59f2a;
            border-color: #c59f2a;
            color: #fff;
        }
    </style>
    @endpush

    @section('content')
    <div class="book-services-page">
        <!-- Sticky Header -->
        <div class="magento-sticky-header d-flex justify-content-between align-items-center">
            <div>
                <span class="text-muted small uppercase fw-bold">Appointments Portal</span>
                <h1 class="h3 mb-0 fw-bold">Select Service to Book</h1>
            </div>
            <div class="actions">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-chevron-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="container">
            @forelse($categories as $category)
                @if($category->services->count() > 0)
                    <div class="category-section">
                        <div class="category-header">
                            @php
                                $iconClass = match($category->name) {
                                    'Bridal Services' => 'fa-ring',
                                    'Facial Services' => 'fa-spa',
                                    'Hair Services' => 'fa-cut',
                                    'Makeup Services' => 'fa-magic',
                                    default => 'fa-star'
                                };
                            @endphp
                            <i class="fas {{ $iconClass }} text-warning me-2" style="font-size: 1.2rem;"></i>
                            <h2 class="category-title">{{ $category->name }}</h2>
                        </div>
                        <div class="category-body">
                            <div class="row row-cols-1 row-cols-md-3 g-4">
                                @foreach($category->services as $service)
                                    <div class="col">
                                        <div class="service-card">
                                            <div class="service-card-body">
                                                <h3 class="service-name">{{ $service->name }}</h3>
                                                <div class="service-meta">
                                                    <span><i class="far fa-clock me-1"></i>{{ $service->duration }} mins</span>
                                                    <span class="fw-bold text-dark"><i class="fas fa-tags me-1 text-muted"></i>Rs. {{ number_format($service->price, 2) }}</span>
                                                </div>
                                                <p class="service-desc">{{ $service->description ?: 'No description available for this service.' }}</p>
                                                <a href="{{ route('admin.bookings.create', ['service' => $service->id]) }}" class="btn btn-book-now">
                                                    <i class="fas fa-calendar-check me-1"></i> Book Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle me-2" style="font-size: 1.5rem;"></i>
                    No services or categories are currently active. Please add services first.
                </div>
            @endforelse
        </div>
    </div>
    @endsection
</x-admin-layout>
