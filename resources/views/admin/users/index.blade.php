<x-admin-layout>
    @section('title', 'Salon & User Management')

    @push('styles')
    <style>
        .action-btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .action-btn-icon:hover {
            transform: translateY(-1px);
        }
        .salon-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #d4af37 0%, #aa7c11 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }
    </style>
    @endpush

    @section('content')
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h3 class="h4 text-gray-900 font-bold mb-0">Salon & User Management</h3>
                <p class="text-muted small mb-0">Super Admin Portal - Add, view, edit, verify, or remove salon accounts.</p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-warning font-semibold text-dark shadow-sm rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Add New Salon / User
                </a>
            </div>
        </div>

        @if(session('user_updated'))
        <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-xs mb-3" role="alert">
            <i class="fas fa-info-circle me-1"></i> {{ session('user_updated') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('user_created'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-3" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('user_created') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('user_deleted'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-xs mb-3" role="alert">
            <i class="fas fa-trash-alt me-1"></i> {{ session('user_deleted') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-xs mb-3" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Salons & Users Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-xs font-bold text-uppercase text-secondary">Salon Business</th>
                                <th class="py-3 text-xs font-bold text-uppercase text-secondary">Owner / Contact</th>
                                <th class="py-3 text-xs font-bold text-uppercase text-secondary">Role</th>
                                <th class="py-3 text-xs font-bold text-uppercase text-secondary">Status</th>
                                <th class="py-3 text-xs font-bold text-uppercase text-secondary">Registered</th>
                                <th class="pe-4 py-3 text-xs font-bold text-uppercase text-secondary text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $u)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="salon-avatar shadow-xs">
                                            {{ strtoupper(substr($u->salon_name ?: $u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-bold text-gray-900 text-sm">{{ $u->salon_name ?: 'Personal Account' }}</h6>
                                            @if($u->slug)
                                                <a href="{{ url('/'.$u->slug) }}" target="_blank" class="text-xs text-primary font-medium text-decoration-none">
                                                    <i class="fas fa-external-link-alt text-2xs me-1"></i> /{{ $u->slug }}
                                                </a>
                                            @else
                                                <span class="text-xs text-muted">No custom slug</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3">
                                    <div class="font-semibold text-sm text-gray-800">{{ $u->name }}</div>
                                    <div class="text-xs text-muted"><i class="far fa-envelope me-1"></i>{{ $u->email }}</div>
                                    @if($u->phone)
                                        <div class="text-xs text-muted"><i class="fas fa-phone-alt me-1"></i>{{ $u->phone }}</div>
                                    @endif
                                </td>

                                <td class="py-3">
                                    @if($u->isSuperAdmin())
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill text-xs font-bold">
                                            <i class="fas fa-crown me-1"></i> Super Admin
                                        </span>
                                    @elseif($u->role === 'admin')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill text-xs font-bold">
                                            <i class="fas fa-user-shield me-1"></i> Admin
                                        </span>
                                    @elseif($u->role === 'user')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill text-xs font-semibold">
                                            <i class="fas fa-store me-1"></i> Salon Owner
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill text-xs font-semibold">
                                            <i class="fas fa-user-tag me-1"></i> {{ ucfirst($u->role) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3">
                                    @if($u->is_verified)
                                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill text-xs font-semibold">
                                            <i class="fas fa-check-circle me-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill text-xs font-semibold">
                                            <i class="fas fa-clock me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 text-xs text-muted font-medium">
                                    {{ $u->created_at ? $u->created_at->format('M d, Y') : 'N/A' }}
                                </td>

                                <td class="pe-4 py-3 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <!-- View Details Modal Trigger -->
                                        <button type="button" class="btn btn-outline-info action-btn-icon" data-bs-toggle="modal" data-bs-target="#viewSalonModal{{ $u->id }}" title="View Salon Details">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Edit Salon Trigger -->
                                        <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-outline-warning action-btn-icon text-dark" title="Edit Salon Details">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Verification Toggle -->
                                        @if($u->id !== Auth::id())
                                        <form action="{{ route('admin.users.toggle-verification', $u->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-{{ $u->is_verified ? 'secondary' : 'success' }} action-btn-icon" title="{{ $u->is_verified ? 'Unverify Salon' : 'Verify Salon' }}">
                                                <i class="fas {{ $u->is_verified ? 'fa-user-clock' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        @endif

                                        <!-- Delete Trigger -->
                                        @if($u->id !== Auth::id())
                                        <button type="button" class="btn btn-outline-danger action-btn-icon" data-bs-toggle="modal" data-bs-target="#deleteSalonModal{{ $u->id }}" title="Delete Salon">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endif
                                    </div>

                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewSalonModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg text-start">
                                            <div class="modal-content border-0 shadow rounded-4">
                                                <div class="modal-header bg-light border-bottom py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="salon-avatar">
                                                            {{ strtoupper(substr($u->salon_name ?: $u->name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <h5 class="modal-title font-bold text-gray-900 mb-0">{{ $u->salon_name ?: 'Personal Account' }}</h5>
                                                            <span class="text-xs text-muted">Salon ID #{{ $u->id }}</span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <small class="text-muted d-block text-uppercase font-semibold text-2xs mb-1">Owner Name</small>
                                                                <span class="font-bold text-gray-900">{{ $u->name }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <small class="text-muted d-block text-uppercase font-semibold text-2xs mb-1">Owner Email</small>
                                                                <span class="font-bold text-gray-900">{{ $u->email }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <small class="text-muted d-block text-uppercase font-semibold text-2xs mb-1">Phone Number</small>
                                                                <span class="font-bold text-gray-900">{{ $u->phone ?: 'Not specified' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <small class="text-muted d-block text-uppercase font-semibold text-2xs mb-1">Salon Type</small>
                                                                <span class="font-bold text-gray-900">{{ $u->salon_type ?: 'Unisex Salon' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <small class="text-muted d-block text-uppercase font-semibold text-2xs mb-1">Salon URL Slug</small>
                                                                @if($u->slug)
                                                                    <a href="{{ url('/'.$u->slug) }}" target="_blank" class="font-bold text-primary text-decoration-none">
                                                                        {{ url('/'.$u->slug) }} <i class="fas fa-external-link-alt text-2xs ms-1"></i>
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted font-bold">N/A</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <small class="text-muted d-block text-uppercase font-semibold text-2xs mb-1">Verification Status</small>
                                                                <span class="font-bold {{ $u->is_verified ? 'text-success' : 'text-warning' }}">
                                                                    {{ $u->is_verified ? 'Verified Active' : 'Pending Verification' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="p-3 bg-light rounded-3">
                                                                <small class="text-muted d-block text-uppercase font-semibold text-2xs mb-1">Salon Address</small>
                                                                <span class="font-bold text-gray-900">
                                                                    {{ implode(', ', array_filter([$u->address, $u->city, $u->state, $u->zip])) ?: 'No address specified' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-top py-2">
                                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-warning btn-sm px-3 font-semibold text-dark">
                                                        <i class="fas fa-edit me-1"></i> Edit Salon
                                                    </a>
                                                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    @if($u->id !== Auth::id())
                                    <div class="modal fade" id="deleteSalonModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content border-0 shadow rounded-4">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title font-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Delete Salon Account</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    <p class="mb-1 text-gray-800 font-medium">Are you sure you want to permanently delete <strong>{{ $u->salon_name ?: $u->name }}</strong>?</p>
                                                    <p class="text-muted small mb-0">This action cannot be undone and will remove all associated services, bookings, and inventory mapped under this salon account.</p>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger btn-sm px-3 font-semibold">Yes, Delete Salon</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-store fa-3x opacity-50 mb-3 d-block"></i>
                                    No salon accounts found. Click "Add New Salon / User" to register a new salon.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                <div class="p-3 border-top">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endsection
</x-admin-layout>
