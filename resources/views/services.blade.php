<x-app-layout>
    @section('content')
    <main style="background-color: var(--light-bg, #F4F8F9);">
        <!-- Page Header -->
        <header class="page-header service-header">
            <div class="container text-center">
                <span class="hero-tagline d-block mb-2 text-uppercase" style="letter-spacing: 2px; color: #00A3B1; font-weight: 700; font-size: 13px;">EXCLUSIVELY AT {{ isset($currentSalon) ? strtoupper($currentSalon->salon_name) : 'SALONJC' }}</span>
                <h1 class="display-4 fw-bold" style="color: #FFFFFF;">Our Beauty Services</h1>
                <p class="lead text-white-75 mb-0">Experience luxury beauty treatments tailored just for you</p>
            </div>
        </header>

        @foreach($categories as $category)
        @php
        $bgClass = $loop->even ? 'bg-white' : '';
        $iconClass = match($category->name) {
        'Bridal Services' => 'fa-ring',
        'Facial Services' => 'fa-spa',
        'Hair Services' => 'fa-cut',
        'Makeup Services' => 'fa-magic',
        default => 'fa-star'
        };
        @endphp

        <section class="service-section py-5 {{ $bgClass }}" id="{{ Str::slug($category->name) }}">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="text-center section-title mb-5" data-aos="fade-up">
                            <span class="subtitle">{{ $category->name }}</span>
                            <h2>{{ $category->description ?: $category->name }}</h2>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    @if($category->name === 'Bridal Services')
                    @foreach($category->services as $service)
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <x-bridal-service-card
                            :title="$service->name"
                            :price="'Rs. ' . number_format($service->price, 2)"
                            :duration="$service->duration . ' mins'"
                            :features="$service->features"
                            :package-type="Str::slug($service->name)"
                            :service-id="$service->id" />
                    </div>
                    @endforeach
                    @else
                    @foreach($category->services as $service)
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <x-service-card
                            :title="$service->name"
                            :description="$service->description"
                            :price="'Rs. ' . number_format($service->price, 2)"
                            :duration="$service->duration . ' mins'"
                            :icon="$service->images->where('is_primary', true)->first()?->image_path ?? 'fas ' . $iconClass"
                            :service-id="$service->id" />
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </section>
        @endforeach
    </main>
    @endsection
</x-app-layout>
