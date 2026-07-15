  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top">
      <div class="container">
          <a class="navbar-brand" href="{{ isset($currentSalon) ? route('salon.home', ['salon' => $currentSalon->slug]) : route('home') }}">
              {{ isset($currentSalon) ? $currentSalon->salon_name : 'Salon' }}<span>{{ isset($currentSalon) ? '' : 'JC' }}</span>
          </a>
          <button
              class="navbar-toggler"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#navbarNav">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav ms-auto align-items-center">
                  <li class="nav-item">
                      <a class="nav-link {{ (request()->routeIs('home') || request()->routeIs('salon.home')) ? 'active' : '' }}" href="{{ isset($currentSalon) ? route('salon.home', ['salon' => $currentSalon->slug]) : route('home') }}">Home</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link {{ (request()->routeIs('services') || request()->routeIs('salon.services')) ? 'active' : '' }}" href="{{ isset($currentSalon) ? route('salon.services', ['salon' => $currentSalon->slug]) : route('services') }}">Services</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link {{ (request()->routeIs('about') || request()->routeIs('salon.about')) ? 'active' : '' }}" href="{{ isset($currentSalon) ? route('salon.about', ['salon' => $currentSalon->slug]) : route('about') }}">About Us</a>
                  </li>
                  @guest
                  <li class="nav-item">
                      <a class="nav-link" href="{{ isset($currentSalon) ? route('salon.login', ['salon' => $currentSalon->slug]) : route('login') }}">Login</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="{{ isset($currentSalon) ? route('salon.register', ['salon' => $currentSalon->slug]) : route('register') }}"><i class="fa-solid fa-user-plus"></i></a>
                  </li>
                  @endguest
                  @auth
                  <!-- Show admin dashboard link for admin users -->
                  @if(Auth::user()->role === 'admin')
                  <li class="nav-item">
                      <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Admin Dashboard</a>
                  </li>
                  @else
                  <li class="nav-item">
                      <a href="{{ Auth::user()->slug ? route('salon.dashboard', ['salon' => Auth::user()->slug]) : route('dashboard') }}" class="nav-link {{ (request()->routeIs('dashboard') || request()->routeIs('salon.dashboard')) ? 'active' : '' }}">Dashboard</a>
                  </li>
                  @endif
                  <li class="nav-item">
                      <form action="{{ isset($currentSalon) ? route('salon.logout', ['salon' => $currentSalon->slug]) : route('logout') }}" method="post">
                          @csrf
                          <button type="submit" class="nav-link">Logout</button>
                      </form>
                  </li>
                  @endauth
                  <li class="nav-item ms-lg-2">
                      <a href="{{ isset($currentSalon) ? route('salon.booking', ['salon' => $currentSalon->slug]) : route('services') }}" class="nav-link book-now-nav">Book Now</a>
                  </li>
              </ul>
          </div>
      </div>
  </nav>
