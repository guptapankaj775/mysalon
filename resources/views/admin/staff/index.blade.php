<x-admin-layout>
    @push('styles')
    <style>
        .staff-page {
            padding: 40px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
        }

        .data-table {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .action-btn {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-inactive {
            background: #ffebee;
            color: #c62828;
        }

        .staff-photo-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }

        .staff-photo-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
    </style>
    @endpush

    @section('content')
    <div class="staff-page">
        <div class="container">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Staff Management</h1>
                <div class="actions">
                    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Staff
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Staff Table -->
            <div class="data-table">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Bio</th>
                                <th>Services Mapped</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff as $member)
                            <tr>
                                <td>
                                    @if($member->image_path)
                                        <img src="{{ asset('storage/' . $member->image_path) }}" alt="{{ $member->name }}" class="staff-photo-img">
                                    @else
                                        <div class="staff-photo-placeholder">
                                            <i class="fas fa-user fa-lg"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $member->name }}</td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($member->bio ?? 'No bio provided', 60) }}</span>
                                </td>
                                <td>
                                    @if($member->services->count() > 0)
                                        <span class="badge bg-secondary">{{ $member->services->count() }} services</span>
                                    @else
                                        <span class="badge bg-light text-dark">No services</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $member->status ? 'active' : 'inactive' }}">
                                        {{ $member->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.staff.edit', $member->id) }}"
                                            class="btn btn-sm btn-outline-primary action-btn me-2">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.staff.destroy', $member->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this staff member?');"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger action-btn">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No staff members found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $staff->links() }}
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-admin-layout>
