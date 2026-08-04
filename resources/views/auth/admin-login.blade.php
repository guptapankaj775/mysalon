<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Portal Login - {{ config('app.name', 'SalonJC') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            min-height: 100vh;
        }
        .admin-auth-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border-top: 4px solid #D4AF37;
            padding: 2.5rem;
            max-width: 440px;
            margin: 0 auto;
        }
        .admin-badge {
            background-color: #2d3748;
            color: #D4AF37;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <a href="{{ url('/') }}" class="back-to-home text-white">
        <i class="fas fa-arrow-left me-2"></i>Back to Website
    </a>
    <section class="auth-section d-flex align-items-center min-vh-100 py-5">
        <div class="container">
            <div class="admin-auth-card">
                <div class="text-center">
                    <span class="admin-badge"><i class="fas fa-shield-alt me-1"></i> System Admin Portal</span>
                    <h2 class="fw-bold mb-1" style="color: #1a202c;">Salon<span style="color: #D4AF37;">JC</span></h2>
                    <p class="text-muted small mb-4">Administrator Control Center Sign In</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                <div class="alert alert-info py-2 fs-6" role="alert">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Admin Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-user-shield"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}"
                                placeholder="admin@example.com" required autofocus>
                        </div>
                        @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Enter admin password"
                                required autocomplete="current-password">
                        </div>
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                        <label class="form-check-label text-muted small" for="remember_me">
                            {{ __('Keep me signed in') }}
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-dark py-2 fw-bold" style="background-color: #1a202c; border-color: #1a202c;">
                            <i class="fas fa-lock me-2 text-warning"></i>{{ __('Admin Login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.bundle.min.js"></script>
</body>

</html>
