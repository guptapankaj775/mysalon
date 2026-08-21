<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ isset($currentSalon) ? $currentSalon->salon_name . ' - Beauty Salon' : 'SalonJC - Beauty Salon' }}</title>

    <!-- favicon -->
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="{{ asset('assets/img/favicon_io/apple-touch-icon.png') }}" />
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="{{ asset('assets/img/favicon_io/favicon-32x32.png') }}" />
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="./img/favicon_io/favicon-16x16.png" />
    <link rel="manifest" href="{{ asset('assets/img/favicon_io/site.webmanifest') }}" />
    <!-- Google Fonts for Moroccanoil Luxury Editorial Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <!-- Font Awesome for icons -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/nav.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/footer.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/gallery.css') }}" />
    <!-- jQuery UI CSS -->
    <link
        rel="stylesheet"
        href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
    <!-- Lightbox2 CSS -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css"
        rel="stylesheet" />

    @stack('styles')
</head>

<body>
    @include('layouts.navigation')

    @yield('content')

    <!-- Footer Section - Exact Moroccanoil Style -->
    <footer class="footer-section">
        <div class="container py-3">
            <div class="row g-4">
                <!-- Col 1: ABOUT SALONJC -->
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <div class="footer-info">
                        <h5 class="footer-heading">ABOUT {{ isset($currentSalon) ? strtoupper($currentSalon->salon_name) : 'SALONJC' }}</h5>
                        <p class="mt-3 text-white-75">
                            Your premier beauty destination, offering professional services, premium argan care, and exceptional experience.
                        </p>
                        <ul class="list-unstyled footer-nav-list mt-3">
                            <li><a href="{{ isset($currentSalon) ? route('salon.about', ['salon' => $currentSalon->slug]) : route('about') }}">Our Story</a></li>
                            <li><a href="{{ isset($currentSalon) ? route('salon.services', ['salon' => $currentSalon->slug]) : route('services') }}">Argan Oil & Care</a></li>
                            <li><a href="#about">Sustainability</a></li>
                        </ul>
                        <div class="social-links mt-4">
                            <a href="#" class="me-3"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="me-3"><i class="fab fa-tiktok"></i></a>
                            <a href="#"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Col 2: QUICK LINKS -->
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5 class="footer-heading">QUICK LINKS</h5>
                        <ul class="list-unstyled footer-nav-list mt-3">
                            <li><a href="{{ isset($currentSalon) ? route('salon.home', ['salon' => $currentSalon->slug]) : route('home') }}">Home</a></li>
                            <li><a href="{{ isset($currentSalon) ? route('salon.services', ['salon' => $currentSalon->slug]) : route('services') }}">Services</a></li>
                            <li><a href="{{ isset($currentSalon) ? route('salon.about', ['salon' => $currentSalon->slug]) : route('about') }}">About Us</a></li>
                            <li><a href="{{ isset($currentSalon) ? route('salon.booking', ['salon' => $currentSalon->slug]) : route('booking') }}">Book Now</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Col 3: SUPPORT & SALON LOCATOR -->
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <div class="footer-services">
                        <h5 class="footer-heading">SUPPORT</h5>
                        <ul class="list-unstyled footer-nav-list mt-3">
                            <li><a href="#contact">Contact Us</a></li>
                            <li><a href="#faq">FAQ</a></li>
                            <li><a href="#booking-info">Booking Information</a></li>
                            <li><a href="#locator">Salon Locator</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Col 4: SIGN UP TO OUR NEWSLETTER (Exact Moroccanoil Style) -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-newsletter">
                        <h5 class="footer-heading">— SIGN UP TO OUR NEWSLETTER</h5>
                        <p class="newsletter-desc mt-3">
                            Stay in the loop about exclusive offers and the latest product & service information with our newsletter.
                        </p>
                        <form action="#" method="POST" class="newsletter-form mt-4" onsubmit="event.preventDefault();">
                            @csrf
                            <label class="text-uppercase fw-bold text-white small d-block mb-1" style="font-size: 11px; letter-spacing: 1.5px;">EMAIL ADDRESS</label>
                            <div class="newsletter-input-group">
                                <input type="email" class="newsletter-input" placeholder="Enter Email" required>
                                <button type="submit" class="newsletter-submit-btn" aria-label="Subscribe">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                            <div class="form-check mt-3 newsletter-consent">
                                <input class="form-check-input border-white bg-transparent me-2" type="checkbox" id="newsletterConsent" required>
                                <label class="form-check-label text-white-75" for="newsletterConsent" style="font-size: 11px; line-height: 1.5;">
                                    By signing up, I agree to receive promotional offers and understand that my data may be used to enhance marketing efforts. <a href="#privacy" class="text-white text-decoration-underline">Privacy Policy</a>.
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom White Bar (Exact Moroccanoil Style) -->
        <div class="footer-bottom-bar bg-white text-dark py-2">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3" style="font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">
                    <div class="text-secondary">
                        &copy; 2026 {{ isset($currentSalon) ? strtoupper($currentSalon->salon_name) : 'SALONJC' }}. ALL RIGHTS RESERVED.
                    </div>
                    <div class="d-flex flex-wrap gap-4 footer-legal-links">
                        <a href="#privacy" class="text-dark text-decoration-none">PRIVACY POLICY</a>
                        <a href="#terms" class="text-dark text-decoration-none">TERMS OF SERVICE</a>
                        <a href="#sustainability" class="text-dark text-decoration-none">SUSTAINABILITY</a>
                        <a href="#accessibility" class="text-dark text-decoration-none">ACCESSIBILITY STATEMENT</a>
                    </div>
                </div>
            </div>
        </div>
    <!-- Footer Section Ends -->


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    <!-- Typed.js -->
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('scripts')
</body>

</html>
