<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>
    <a href="{{ isset($currentSalon) ? route('salon.home', ['salon' => $currentSalon->slug]) : url('/') }}" class="back-to-home">
        <i class="fas fa-arrow-left me-2"></i>Back to Home
    </a>
    <section class="auth-section">
        <div class="container">
            <div class="auth-card">
                <div class="logo">
                    <h2>{{ isset($currentSalon) ? $currentSalon->salon_name : 'Salon' }}<span>{{ isset($currentSalon) ? '' : 'JC' }}</span></h2>
                </div>

                <div class="auth-header">
                    <h1>Create Account</h1>
                    <p>Join SalonJC to book your appointments</p>
                </div>

                <form method="POST" action="{{ isset($currentSalon) ? route('salon.register', ['salon' => $currentSalon->slug]) : route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}"
                                placeholder="Enter your full name" required autofocus>
                        </div>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Salon Name -->
                    <div class="mb-3">
                        <label for="salon_name" class="form-label">Salon Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-store"></i></span>
                            <input type="text" class="form-control @error('salon_name') is-invalid @enderror"
                                id="salon_name" name="salon_name" value="{{ old('salon_name') }}"
                                placeholder="e.g. Glamour Hair Studio" required>
                        </div>
                        @error('salon_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Salon Type -->
                    <div class="mb-3">
                        <label for="salon_type" class="form-label">Salon Type</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-spa"></i></span>
                            <select class="form-select @error('salon_type') is-invalid @enderror"
                                id="salon_type" name="salon_type" required>
                                <option value="" disabled {{ !old('salon_type') ? 'selected' : '' }}>Select Salon Type</option>
                                <option value="Mens Salon" {{ old('salon_type') === 'Mens Salon' ? 'selected' : '' }}>Mens Salon</option>
                                <option value="Female Salon" {{ old('salon_type') === 'Female Salon' ? 'selected' : '' }}>Female Salon</option>
                                <option value="Unisex Salon" {{ old('salon_type') === 'Unisex Salon' ? 'selected' : '' }}>Unisex Salon</option>
                                <option value="Makeup Studio" {{ old('salon_type') === 'Makeup Studio' ? 'selected' : '' }}>Makeup Studio</option>
                                <option value="Academy + Salon" {{ old('salon_type') === 'Academy + Salon' ? 'selected' : '' }}>Academy + Salon</option>
                                <option value="Academy" {{ old('salon_type') === 'Academy' ? 'selected' : '' }}>Academy</option>
                            </select>
                        </div>
                        @error('salon_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Salon Model -->
                    <div class="mb-3">
                        <label for="salon_model" class="form-label">Salon Model</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                            <select class="form-select @error('salon_model') is-invalid @enderror"
                                id="salon_model" name="salon_model" required>
                                <option value="" disabled {{ !old('salon_model') ? 'selected' : '' }}>Select Salon Model</option>
                                <option value="Franchisee" {{ old('salon_model') === 'Franchisee' ? 'selected' : '' }}>Franchisee</option>
                                <option value="Self Owned" {{ old('salon_model') === 'Self Owned' ? 'selected' : '' }}>Self Owned</option>
                            </select>
                        </div>
                        @error('salon_model')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}"
                                placeholder="name@example.com" required>
                        </div>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password"
                                placeholder="Create a password" required>
                        </div>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                id="password_confirmation" name="password_confirmation"
                                placeholder="Repeat your password" required>
                        </div>
                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="gap-2 mb-3 d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>{{ __('Create Account') }}
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0">{{ __('Already have an account?') }}
                            <a href="{{ isset($currentSalon) ? route('salon.login', ['salon' => $currentSalon->slug]) : route('login') }}">{{ __('Sign in') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
