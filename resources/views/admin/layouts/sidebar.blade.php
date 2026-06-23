<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <h3>Admin Panel</h3>
    </div>
    <ul class="list-unstyled components">
        <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <i class="fas fa-home"></i> hello
            </a>
        </li>

        <li class="{{ request()->is('admin/users*') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="nav-link">
                <i class="fas fa-users"></i> Users
            </a>
        </li>

        <li class="{{ request()->is('admin/staff*') ? 'active' : '' }}">
            <a href="{{ route('admin.staff.index') }}" class="nav-link">
                <i class="fas fa-user-tie"></i> Staff
            </a>
        </li>

        <li class="{{ request()->is('admin/services*') ? 'active' : '' }}">
            <a href="{{ route('admin.services') }}" class="nav-link">
                <i class="fas fa-cut"></i> Services
            </a>
        </li>

        <li class="{{ request()->is('admin/bookings*') ? 'active' : '' }}">
            <a href="{{ route('admin.bookings') }}" class="nav-link">
                <i class="fas fa-calendar"></i> Bookings
            </a>
        </li>

        <li class="{{ request()->is('admin/categories*') ? 'active' : '' }}">
            <a href="{{ route('admin.categories') }}" class="nav-link">
                <i class="fas fa-th-list"></i> Categories
            </a>
        </li>

        <li class="{{ request()->is('admin/inventory*') ? 'active' : '' }}">
            <a href="{{ route('admin.inventory.index') }}" class="nav-link">
                <i class="fas fa-boxes"></i> Inventory
            </a>
        </li>

        <li class="{{ request()->is('admin/feedbacks*') ? 'active' : '' }}">
            <a href="{{ route('admin.feedback.index') }}" class="nav-link">
                <i class="fas fa-star"></i> Feedback
            </a>
        </li>

        <!-- Subscription Section -->
        <li style="padding: 0.4rem 1rem 0.2rem; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.35); pointer-events: none; margin-top: 0.5rem;">
            Subscriptions
        </li>

        <li class="{{ request()->is('admin/subscriptions*') ? 'active' : '' }}">
            <a href="{{ route('admin.subscriptions.index') }}" class="nav-link">
                <i class="fas fa-layer-group"></i> Plans
            </a>
        </li>

        <li class="{{ request()->is('admin/subscribers*') ? 'active' : '' }}">
            <a href="{{ route('admin.subscribers') }}" class="nav-link">
                <i class="fas fa-id-card"></i> Subscribers
            </a>
        </li>

        <li class="{{ request()->is('admin/subscription-settings*') ? 'active' : '' }}">
            <a href="{{ route('admin.subscription.settings') }}" class="nav-link">
                <i class="fas fa-sliders-h"></i> Sub. Settings
            </a>
        </li>

        <li>
            <a href="{{ url('/') }}" class="nav-link" target="_blank">
                <i class="fas fa-globe"></i> Visit Website
            </a>
        </li>

        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="#" class="nav-link" onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </form>
        </li>
    </ul>
</nav>
