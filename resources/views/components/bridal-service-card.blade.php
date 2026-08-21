<div class="service-package-card shadow-sm">
    <div class="package-header">
        <h3>{{ $title }}</h3>
        <span class="price">Starting from {{ $price }}</span>
        <span class="duration"><i class="far fa-clock me-1"></i>Duration: {{ $duration }}</span>
    </div>
    <div class="package-content">
        <ul>
            @foreach($features as $feature)
            <li>
                <i class="fas fa-check-circle"></i> {{ $feature }}
            </li>
            @endforeach
        </ul>
        <a href="{{ isset($currentSalon) ? route('salon.booking', ['salon' => $currentSalon->slug]) : route('booking') }}?service={{ $serviceId }}" class="btn btn-book" data-package="{{ $packageType }}">
            Book Package
        </a>
    </div>
</div>

@push('styles')
<style>
    .service-package-card {
        background: #FFFFFF !important;
        border: 1px solid rgba(0, 163, 177, 0.18) !important;
        border-radius: 16px !important;
        padding: 30px;
        height: 100%;
        transition: all 0.3s ease-in-out;
        color: #121A21 !important;
    }

    .service-package-card:hover {
        transform: translateY(-8px);
        border-color: #00A3B1 !important;
        box-shadow: 0 15px 35px rgba(0, 163, 177, 0.12) !important;
    }

    .package-header {
        text-align: center;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(0, 163, 177, 0.15);
    }

    .package-header h3 {
        color: #121A21 !important;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .package-header .price {
        display: block;
        color: #00A3B1 !important;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .package-header .duration {
        color: #5A6A75 !important;
        font-size: 0.9rem;
    }

    .package-content ul {
        list-style: none;
        padding: 0;
        margin: 0 0 25px 0;
    }

    .package-content ul li {
        color: #5A6A75 !important;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .package-content ul li i {
        color: #00A3B1 !important;
        margin-right: 10px;
        font-size: 0.9rem;
    }

    .service-package-card .btn-book {
        width: 100%;
        background-color: #00A3B1 !important;
        border: 2px solid #00A3B1 !important;
        color: #FFFFFF !important;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .service-package-card .btn-book:hover {
        background-color: #008C99 !important;
        border-color: #008C99 !important;
        color: #FFFFFF !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 163, 177, 0.25);
    }
</style>
@endpush
