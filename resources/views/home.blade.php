<x-app-layout>
    @section('content')
    <!-- Hero Section - Moroccanoil Luxury Editorial Style -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center g-4">
                <!-- Left Column (50% Width): Editorial Text & Action CTA -->
                <div class="col-md-6 col-lg-6 text-start">
                    <div class="hero-text-block">
                        <span class="hero-tagline d-block mb-3">EXCLUSIVELY AT {{ isset($currentSalon) ? strtoupper($currentSalon->salon_name) : 'SALONJC' }}</span>
                        <h1 class="hero-editorial-title mb-3">
                            {{ isset($currentSalon) ? $currentSalon->salon_name : 'SalonJC' }} Favourites
                        </h1>
                        <p class="hero-editorial-desc mb-4">
                            From everyday elegance to special occasion styling, discover premier hair, facial, and bridal treatments designed to elevate your natural beauty.
                        </p>
                        <div class="hero-cta-wrapper mb-4 d-flex align-items-center flex-wrap gap-3">
                            <a href="{{ isset($currentSalon) ? route('salon.booking', ['salon' => $currentSalon->slug]) : route('booking') }}" class="btn-moroccan-cta">
                                STAY IN BEAUTY MODE
                            </a>
                            <a href="{{ isset($currentSalon) ? route('salon.services', ['salon' => $currentSalon->slug]) : route('services') }}" class="btn-moroccan-secondary">
                                EXPLORE SERVICES
                            </a>
                        </div>
                        <div class="hero-features-strip d-flex flex-wrap gap-3 pt-3 border-top border-secondary-subtle">
                            <span class="feature-item"><i class="fas fa-star me-2 text-primary"></i> Expert Beauticians</span>
                            <span class="feature-item"><i class="fas fa-certificate me-2 text-primary"></i> Premium Argan Care</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column (50% Width): Moroccanoil Hero Banner Image -->
                <div class="col-md-6 col-lg-6 text-center">
                    <div class="hero-img-block">
                        <div class="position-relative rounded-4 overflow-hidden shadow-lg border border-4 border-white">
                            <img src="{{ asset('assets/img/about/salon-interior.jpg') }}" alt="SalonJC Beauty Experience" class="img-fluid w-100 hero-display-img">
                            <!-- Floating Glassmorphism Badge -->
                            <div class="position-absolute bottom-0 start-0 m-3 bg-white px-3 py-2 rounded-3 shadow border text-start d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 34px; height: 34px; background-color: #00A3B1;">
                                    <i class="fas fa-sparkles" style="font-size: 14px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 12px; letter-spacing: 0.5px;">LUXURY SALON EXPERIENCE</div>
                                    <div class="text-muted" style="font-size: 11px;">100% Organic Products & Expert Stylists</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 services-section">
        <div class="container">
            <div class="section-title">
                <span class="subtitle">Our Services</span>
                <h2>Luxury Beauty Services</h2>
                <p class="text-muted">
                    Experience luxury beauty services with our expert team
                </p>
            </div>
            <div class="row g-4">
                @if(isset($services) && $services->count() > 0)
                    @foreach($services as $service)
                    <div class="col-lg-4 col-md-6">
                        <div class="service-card h-100">
                            @if($service->image_path)
                            <div class="mb-3 overflow-hidden service-img-wrapper rounded-3">
                                <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->name }}" class="img-fluid w-100" style="height: 200px; object-fit: cover;">
                            </div>
                            @else
                            <i class="{{ $service->icon_class ?? 'fas fa-spa' }}"></i>
                            @endif
                            <h3>{{ $service->name }}</h3>
                            <p>{{ Str::limit($service->description, 90) }}</p>
                            <div class="service-price">
                                <span class="price">Rs {{ number_format($service->price, 2) }}</span>
                                <span class="duration">{{ $service->duration_minutes }} mins</span>
                            </div>
                            <div class="mt-3">
                                <a href="{{ isset($currentSalon) ? route('salon.booking', ['salon' => $currentSalon->slug]) : route('booking') }}" class="service-btn">Book Now</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center text-muted">
                        <p>No services currently available.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="py-5 gallery-section">
        <div class="container">
            <div class="section-title">
                <span class="subtitle">Our Gallery</span>
                <h2>Our Beautiful Transformations</h2>
                <p class="text-muted">
                    Witness the artistry of our expert beauticians
                </p>
            </div>

            <!-- Gallery Categories -->
            <div class="mb-5 gallery-filter justify-content-center d-flex">
                <ul
                    class="nav nav-pills justify-content-center"
                    id="gallery-tabs"
                    role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active"
                            id="all-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#all"
                            type="button"
                            role="tab">
                            <i class="fas fa-border-all me-2"></i>All Work
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="bridal-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#bridal"
                            type="button"
                            role="tab">
                            <i class="fas fa-crown me-2"></i>Bridal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="hair-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#hair"
                            type="button"
                            role="tab">
                            <i class="fas fa-cut me-2"></i>Hair Styling
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            id="makeup-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#makeup"
                            type="button"
                            role="tab">
                            <i class="fas fa-magic me-2"></i>Makeup
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Gallery Images -->
            <div class="tab-content" id="gallery-content">
                <!-- All Images Tab -->
                <div
                    class="tab-pane fade show active"
                    id="all"
                    role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/all-1.jpg') }}"
                                    alt="Gallery Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Bridal Makeup</h4>
                                        <p>Complete Bridal Look</p>
                                        <a
                                            href="img/gallery/all-1.jpg"
                                            class="view-btn"
                                            data-lightbox="gallery">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/all-2.jpg') }}"
                                    alt="Gallery Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Hair Styling</h4>
                                        <p>Modern and Trendy</p>
                                        <a
                                            href="{{ asset("assets/img/gallery/all-2.jpg") }}"
                                            class="view-btn"
                                            data-lightbox="gallery">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/all-3.jpg') }}"
                                    alt="Gallery Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Makeup Artistry</h4>
                                        <p>Creative and Elegant</p>
                                        <a
                                            href="img/gallery/all-3.jpg"
                                            class="view-btn"
                                            data-lightbox="gallery">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bridal Tab -->
                <div class="tab-pane fade" id="bridal" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/bridal-1.jpg') }}"
                                    alt="Bridal Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Traditional Bridal</h4>
                                        <p>Complete Bridal Package</p>
                                        <a
                                            href="img/gallery/bridal-1.jpg"
                                            class="view-btn"
                                            data-lightbox="bridal">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/bridal-2.jpg') }}"
                                    alt="Bridal Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Modern Bridal</h4>
                                        <p>Elegant and Stylish</p>
                                        <a
                                            href="img/gallery/bridal-2.jpg"
                                            class="view-btn"
                                            data-lightbox="bridal">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/bridal-3.jpg') }}"
                                    alt="Bridal Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Bridal Portrait</h4>
                                        <p>Captivating and Beautiful</p>
                                        <a
                                            href="img/gallery/bridal-3.jpg"
                                            class="view-btn"
                                            data-lightbox="bridal">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hair Styling Tab -->
                <div class="tab-pane fade" id="hair" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/hair-1.jpg') }}"
                                    alt="Hair Styling Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Classic Haircut</h4>
                                        <p>Timeless and Elegant</p>
                                        <a
                                            href="img/gallery/hair-1.jpg"
                                            class="view-btn"
                                            data-lightbox="hair">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/hair-2.jpg') }}"
                                    alt="Hair Styling Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Modern Haircut</h4>
                                        <p>Trendy and Stylish</p>
                                        <a
                                            href="img/gallery/hair-2.jpg"
                                            class="view-btn"
                                            data-lightbox="hair">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/hair-3.jpg') }}"
                                    alt="Hair Styling Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Hair Coloring</h4>
                                        <p>Vibrant and Beautiful</p>
                                        <a
                                            href="img/gallery/hair-3.jpg"
                                            class="view-btn"
                                            data-lightbox="hair">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Makeup Tab -->
                <div class="tab-pane fade" id="makeup" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/makeup-1.jpg') }}"
                                    alt="Makeup Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Natural Makeup</h4>
                                        <p>Fresh and Radiant</p>
                                        <a
                                            href="img/gallery/makeup-1.jpg"
                                            class="view-btn"
                                            data-lightbox="makeup">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/makeup-2.jpg') }}"
                                    alt="Makeup Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Bridal Makeup</h4>
                                        <p>Elegant and Timeless</p>
                                        <a
                                            href="img/gallery/makeup-2.jpg"
                                            class="view-btn"
                                            data-lightbox="makeup">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img
                                    src="{{ asset('assets/img/gallery/makeup-3.jpg') }}"
                                    alt="Makeup Image"
                                    class="img-fluid" />
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <h4>Party Makeup</h4>
                                        <p>Bold and Beautiful</p>
                                        <a
                                            href="img/gallery/makeup-3.jpg"
                                            class="view-btn"
                                            data-lightbox="makeup">
                                            <i
                                                class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 about-section">
        <div class="container">
            <!-- Main About Content -->
            <div class="mb-5 section-title">
                <span class="subtitle">About Us</span>
                <h2>Welcome to SalonJC</h2>
                <p class="text-muted">
                    Your Premier Beauty Destination in Pallawela
                </p>
            </div>

            <div class="mb-5 row align-items-center">
                <div class="mb-4 col-lg-6 mb-lg-0">
                    <div class="about-content">
                        <h3>Your Journey to Beauty</h3>
                        <p class="mb-4 lead text-gold">
                            Dedicated to elevating your natural beauty since
                            2025.
                        </p>
                        <p>
                            We are committed to providing exceptional beauty
                            services in a luxurious and welcoming
                            environment. Our team of skilled professionals
                            uses premium products and advanced techniques to
                            help you achieve your desired look.
                        </p>
                        <div class="gap-4 mt-4 d-flex">
                            <div class="achievement-box">
                                <i class="fas fa-award"></i>
                                <h4>10+</h4>
                                <p>Years Experience</p>
                            </div>
                            <div class="achievement-box">
                                <i class="fas fa-users"></i>
                                <h4>1000+</h4>
                                <p>Happy Clients</p>
                            </div>
                            <div class="achievement-box">
                                <i class="fas fa-certificate"></i>
                                <h4>100%</h4>
                                <p>Satisfaction</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-image">
                        <img
                            src="{{ asset('assets/img/about/salon-interior.jpg') }}"
                            alt="Salon Interior"
                            class="img-fluid rounded-3 main-image" />
                        <div class="mt-4 image-grid">
                            <img
                                src="{{ asset('assets/img/about/service-1.jpg') }}"
                                alt="Beauty Service"
                                class="img-fluid rounded-3" />
                            <img
                                src="{{ asset('assets/img/about/service-2.jpg') }}"
                                alt="Beauty Service"
                                class="img-fluid rounded-3" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Section -->
            <div class="mt-5 mb-4 section-title">
                <span class="subtitle">Our Team</span>
                <h2>Meet Our Experts</h2>
                <p class="text-muted">
                    Dedicated professionals ready to transform your look
                </p>
            </div>

            <div class="row team-section g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="team-member">
                        <div class="member-img">
                            <img
                                src="{{ asset('assets/img/team/stylist-1.jpg') }}"
                                alt="Team Member"
                                class="img-fluid" />
                            <div class="social-links">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Jessica Chen</h4>
                            <p>Master Stylist</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-member">
                        <div class="member-img">
                            <img
                                src="{{ asset('assets/img/team/stylist-2.jpg') }}"
                                alt="Team Member"
                                class="img-fluid" />
                            <div class="social-links">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Sarah Johnson</h4>
                            <p>Bridal Specialist</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-member">
                        <div class="member-img">
                            <img
                                src="{{ asset('assets/img/team/stylist-3.jpg') }}"
                                alt="Team Member"
                                class="img-fluid" />
                            <div class="social-links">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Maria Garcia</h4>
                            <p>Makeup Artist</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-member">
                        <div class="member-img">
                            <img
                                src="{{ asset('assets/img/team/stylist-4.jpg') }}"
                                alt="Team Member"
                                class="img-fluid" />
                            <div class="social-links">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Emily Taylor</h4>
                            <p>Color Specialist</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 contact-section">
        <div class="container">
            <div class="mb-5 section-title">
                <span class="subtitle">Contact Us</span>
                <h2>Get In Touch</h2>
                <p class="text-muted">We'd Love to Hear From You</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="contact-info-card">
                        <div class="icon-box">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Visit Us</h4>
                        <p>Kaloliya Rd, Pallawela<br />Sri Lanka</p>
                        <a
                            href="https://maps.google.com"
                            target="_blank"
                            class="direction-link">
                            <i class="fas fa-directions me-2"></i>Get
                            Directions
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="contact-info-card">
                        <div class="icon-box">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h4>Call Us</h4>
                        <p>071 414 7628</p>
                        <div class="business-hours">
                            <p class="mb-1">Mon - Sat: 9:00 AM - 8:00 PM</p>
                            <p>Sunday: 10:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="contact-info-card">
                        <div class="icon-box">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email Us</h4>
                        <p>salonjc2092@gmail.com</p>
                        <div class="mt-3 social-links">
                            <a href="#" class="me-3"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="me-3"><i class="fab fa-tiktok"></i></a>
                            <a href="#"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>
    @endsection
</x-app-layout>
