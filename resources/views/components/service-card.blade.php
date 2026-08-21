<div class="service-card h-100">
    <div class="service-icon mb-3">
        <i class="{{ $icon }}"></i>
    </div>
    <h3>{{ $title }}</h3>
    <p class="text-muted">{{ $description }}</p>
    <div class="service-price">
        <span class="price">{{ $price }}</span>
        <span class="duration">{{ $duration }}</span>
    </div>

    @if($showBookButton)
    <div class="mt-3">
        <a href="{{ isset($currentSalon) ? route('salon.booking', ['salon' => $currentSalon->slug]) : route('booking') }}?service={{ $serviceId }}" class="service-btn">Book Now</a>
    </div>
    @endif
</div>
