<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon_io/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Fix Tailwind conflict with Bootstrap collapse */
        .collapse:not(.show):not(.navbar-collapse) {
            display: none !important;
        }
        .collapse.show:not(.navbar-collapse) {
            display: block !important;
            visibility: visible !important;
        }
        
        .navbar-collapse {
            visibility: visible !important;
        }

        /* Alert Styles */
        .alert {
            border-radius: 8px;
            border: 1px solid transparent;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #28A745;
            color: white;
            border-color: #1e7e34;
        }

        .alert-danger {
            background-color: #DC3545;
            color: white;
            border-color: #bd2130;
        }

        .btn-link {
            color: #00A3B1;
        }

        .btn-link:hover {
            color: #008C99;
        }

        /* Moroccanoil Premium Top Navbar Styles */
        .navbar-custom {
            background-color: #121A21 !important;
            border-bottom: 2px solid #00A3B1;
            padding: 10px 20px;
        }

        .navbar-custom .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 6px 12px !important;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link:focus {
            color: #00A3B1 !important;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .navbar-custom .nav-link.active {
            color: #00A3B1 !important;
            background-color: rgba(0, 163, 177, 0.15);
        }

        /* Dropdown custom dark premium styling */
        .dropdown-menu-custom {
            background-color: #121A21 !important;
            border: 1px solid rgba(0, 163, 177, 0.2) !important;
            border-radius: 8px;
            padding: 5px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
        }

        .dropdown-menu-custom .dropdown-item {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .dropdown-menu-custom .dropdown-item:hover {
            background-color: rgba(0, 163, 177, 0.15) !important;
            color: #00A3B1 !important;
        }

        .dropdown-menu-custom .dropdown-item.active {
            background-color: rgba(0, 163, 177, 0.2) !important;
            color: #00A3B1 !important;
        }

        .main-content {
            padding: 2px 30px 30px 30px;
            min-height: calc(100vh - 65px);
            background: #F4F8F9;
            transition: 0.3s;
        }

        /* Global Table Cell Font Size */
        table td, .table td, table th, .table th {
            font-size: 14px !important;
        }

        /* Moroccanoil Global Helper Classes */
        .border-gold-focus:focus {
            border-color: #00A3B1 !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 163, 177, 0.25) !important;
        }
        .text-gold {
            color: #00A3B1 !important;
        }
        .bg-gold {
            background-color: #00A3B1 !important;
        }
        .btn-warning, .btn-gold {
            background-color: #00A3B1 !important;
            border-color: #00A3B1 !important;
            color: #FFFFFF !important;
        }
        .btn-warning:hover, .btn-gold:hover {
            background-color: #008C99 !important;
            border-color: #008C99 !important;
            color: #FFFFFF !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container-fluid">
            <!-- Brand Logo -->
            <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : (Auth::user()->slug ? route('salon.dashboard', ['salon' => Auth::user()->slug]) : route('dashboard')) }}" class="navbar-brand d-flex align-items-center">
                <i class="fas fa-spa me-2" style="color: #00A3B1 !important;"></i>
                <span style="color: #00A3B1; font-weight: 700;">SalonJC</span>
                <span class="ms-2 text-white small opacity-75" style="font-size: 0.85rem;">
                    @if(Auth::user()->role === 'admin') Admin @else Portal @endif
                </span>
            </a>

            <!-- Mobile Hamburger Menu Button -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="topNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                    @if(Auth::user()->role === 'admin')
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>

                        <!-- POS -->
                        <li class="nav-item">
                            <a href="{{ route('admin.pos') }}" class="nav-link {{ request()->routeIs('admin.pos*') ? 'active' : '' }}">
                                <i class="fas fa-cash-register"></i> POS
                            </a>
                        </li>

                        <!-- services_dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.services*', 'admin.categories*') ? 'active' : '' }}" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cut"></i> Services
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-dark" aria-labelledby="servicesDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.services*') ? 'active' : '' }}" href="{{ route('admin.services') }}">
                                        <i class="fas fa-list"></i> Services List
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}">
                                        <i class="fas fa-th-list"></i> Service Categories
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Bookings -->
                        <li class="nav-item">
                            <a href="{{ route('admin.bookings') }}" class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-check"></i> Bookings
                            </a>
                        </li>

                        <!-- admin_inventory_dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.inventory-categories*', 'admin.inventory.index', 'admin.inventory.create', 'admin.inventory.edit', 'admin.vendors*') ? 'active' : '' }}" href="#" id="adminInventoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-boxes"></i> Inventory
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-dark" aria-labelledby="adminInventoryDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.inventory-categories*') ? 'active' : '' }}" href="{{ route('admin.inventory-categories.index') }}">
                                        <i class="fas fa-folder-open"></i> Inv Category
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.inventory.index', 'admin.inventory.create', 'admin.inventory.edit') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
                                        <i class="fas fa-list"></i> Inventory
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider bg-secondary"></li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.vendors*') ? 'active' : '' }}" href="{{ route('admin.vendors.index') }}">
                                        <i class="fas fa-truck"></i> Vendors
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- admin_team_dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('admin/users*') || request()->routeIs('admin.staff*', 'admin.roles*') ? 'active' : '' }}" href="#" id="teamDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-users"></i> Team
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-dark" aria-labelledby="teamDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                        <i class="fas fa-users-cog"></i> Users List
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.staff*') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">
                                        <i class="fas fa-user-tie"></i> Staff Members
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.roles*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                                        <i class="fas fa-shield-alt"></i> Permissions & Roles
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- admin_reports_dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.reports.sales*', 'admin.feedback*', 'admin.pos.history*') ? 'active' : '' }}" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-dark" aria-labelledby="reportsDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.pos.history*') ? 'active' : '' }}" href="{{ route('admin.pos.history') }}">
                                        <i class="fas fa-history text-warning"></i> POS History
                                    </a>
                                </li>
                                @can('view_sales_reports')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.reports.sales*') ? 'active' : '' }}" href="{{ Auth::user()->slug ? route('admin.reports.sales', ['salon' => Auth::user()->slug]) : route('admin.reports.sales') }}">
                                        <i class="fas fa-chart-line"></i> Sales Report
                                    </a>
                                </li>
                                @endcan
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.feedback*') ? 'active' : '' }}" href="{{ route('admin.feedback.index') }}">
                                        <i class="fas fa-comments"></i> Feedbacks
                                        @php
                                        $pendingCount = \App\Models\Feedback::where('is_published', false)->count();
                                        @endphp
                                        @if($pendingCount > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- admin_subscriptions_dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('admin/subscriptions*', 'admin/subscribers*', 'admin/subscription-settings*') ? 'active' : '' }}" href="#" id="adminSubscriptionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-layer-group"></i> Subscriptions
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-dark" aria-labelledby="adminSubscriptionsDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->is('admin/subscriptions*') ? 'active' : '' }}" href="{{ route('admin.subscriptions.index') }}">
                                        <i class="fas fa-layer-group"></i> Plans
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->is('admin/subscribers*') ? 'active' : '' }}" href="{{ route('admin.subscribers') }}">
                                        <i class="fas fa-id-card"></i> Subscribers
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->is('admin/subscription-settings*') ? 'active' : '' }}" href="{{ route('admin.subscription.settings') }}">
                                        <i class="fas fa-sliders-h"></i> Settings
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        @php
                            $activeTab = request()->query('tab', 'overview');
                            if (session('status') === 'profile-updated' || session('status') === 'password-updated' || $errors->any() || $errors->updatePassword->any()) {
                                $activeTab = 'profile';
                            }
                        @endphp
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ Auth::user()->slug ? route('salon.dashboard', ['salon' => Auth::user()->slug, 'tab' => 'overview']) : route('dashboard', ['tab' => 'overview']) }}" class="nav-link {{ (request()->routeIs('dashboard') || request()->routeIs('salon.dashboard')) && $activeTab === 'overview' ? 'active' : '' }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>

                        <!-- POS -->
                        <li class="nav-item">
                            <a href="{{ Auth::user()->slug ? route('admin.pos', ['salon' => Auth::user()->slug]) : route('admin.pos') }}" class="nav-link {{ request()->routeIs('admin.pos*') ? 'active' : '' }}">
                                <i class="fas fa-cash-register"></i> POS
                            </a>
                        </li>

                        <!-- merchant_services_dropdown -->
                        @if(Auth::user()->can('manage_services'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.services*', 'admin.categories*') ? 'active' : '' }}" href="#" id="merchantServicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cut"></i> Services
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-dark" aria-labelledby="merchantServicesDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.services*') ? 'active' : '' }}" href="{{ route('admin.services') }}">
                                        <i class="fas fa-list"></i> Services List
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}">
                                        <i class="fas fa-th-list"></i> Service Categories
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        <!-- merchant_bookings_dropdown -->
                        @can('manage_bookings')
                        <li class="nav-item">
                            <a href="{{ route('admin.bookings') }}" class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-check"></i> Bookings
                            </a>
                        </li>
                        @endcan

                        <!-- merchant_inventory_dropdown -->
                        @if(Auth::user()->can('manage_inventory') || Auth::user()->can('manage_vendors'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.inventory-categories*', 'admin.inventory.index', 'admin.inventory.create', 'admin.inventory.edit', 'admin.vendors*') ? 'active' : '' }}" href="#" id="merchantInventoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-boxes"></i> Inventory
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-dark" aria-labelledby="merchantInventoryDropdown">
                                @can('manage_inventory')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.inventory-categories*') ? 'active' : '' }}" href="{{ route('admin.inventory-categories.index') }}">
                                        <i class="fas fa-folder-open"></i> Inv Category
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.inventory.index', 'admin.inventory.create', 'admin.inventory.edit') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
                                        <i class="fas fa-list"></i> Inventory Items
                                    </a>
                                </li>
                                @endcan
                                @if(Auth::user()->can('manage_inventory') && Auth::user()->can('manage_vendors'))
                                <li><hr class="dropdown-divider bg-secondary"></li>
                                @endif
                                @can('manage_vendors')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.vendors*') ? 'active' : '' }}" href="{{ route('admin.vendors.index') }}">
                                        <i class="fas fa-truck"></i> Vendors
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif

                        <!-- merchant_team_dropdown -->
                        @if(Auth::user()->can('manage_users') || Auth::user()->can('manage_staff') || Auth::user()->can('manage_roles'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('admin/users*') || request()->routeIs('admin.staff*', 'admin.roles*') ? 'active' : '' }}" href="#" id="merchantTeamDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-users"></i> Team
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-dark" aria-labelledby="merchantTeamDropdown">
                                @can('manage_users')
                                <li>
                                    <a class="dropdown-item {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                        <i class="fas fa-users-cog"></i> Users List
                                    </a>
                                </li>
                                @endcan
                                @can('manage_staff')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.staff*') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">
                                        <i class="fas fa-user-tie"></i> Staff Members
                                    </a>
                                </li>
                                @endcan
                                @can('manage_roles')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.roles*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                                        <i class="fas fa-shield-alt"></i> Permissions & Roles
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif

                        <!-- merchant_reports_dropdown -->
                        @if(Auth::user()->can('view_sales_reports') || Auth::user()->can('manage_feedbacks') || Auth::user()->role === 'user')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.reports.sales*', 'admin.feedback*', 'admin.pos.history*') ? 'active' : '' }}" href="#" id="merchantReportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-dark" aria-labelledby="merchantReportsDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.pos.history*') ? 'active' : '' }}" href="{{ Auth::user()->slug ? route('admin.pos.history', ['salon' => Auth::user()->slug]) : route('admin.pos.history') }}">
                                        <i class="fas fa-history text-warning"></i> POS History
                                    </a>
                                </li>
                                @can('view_sales_reports')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.reports.sales*') ? 'active' : '' }}" href="{{ Auth::user()->slug ? route('admin.reports.sales', ['salon' => Auth::user()->slug]) : route('admin.reports.sales') }}">
                                        <i class="fas fa-chart-line"></i> Sales Report
                                    </a>
                                </li>
                                @endcan
                                @can('manage_feedbacks')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.feedback*') ? 'active' : '' }}" href="{{ route('admin.feedback.index') }}">
                                        <i class="fas fa-comments"></i> Feedbacks
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif

                        <!-- merchant_subscriptions_dropdown -->
                        @if(Auth::user()->role === 'merchant')
                        <li class="nav-item">
                            <a href="{{ Auth::user()->slug ? route('salon.subscription.index', ['salon' => Auth::user()->slug]) : route('subscription.index') }}" class="nav-link {{ (request()->routeIs('subscription*') || request()->routeIs('salon.subscription.index')) ? 'active' : '' }}">
                                <i class="fas fa-crown"></i> Subscriptions
                            </a>
                        </li>
                        @endif
                    @endif
                </ul>

                <!-- User Dropdown & Profile Settings -->
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle fs-5 me-1 text-warning"></i>
                            <span>{{ Auth::user()->name }}</span>
                            <span class="badge bg-warning text-dark ms-2 small" style="font-size: 0.75rem; text-transform: uppercase;">{{ ucfirst(Auth::user()->role) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-end dropdown-menu-dark" aria-labelledby="userDropdown">
                            @if(Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff')
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard', ['tab' => 'profile']) }}">
                                    <i class="fas fa-user-cog"></i> Profile Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider bg-secondary"></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ Auth::user()->slug ? route('salon.logout', ['salon' => Auth::user()->slug]) : route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start" style="cursor: pointer;">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
    <!-- JavaScript -->
</body>

</html>
