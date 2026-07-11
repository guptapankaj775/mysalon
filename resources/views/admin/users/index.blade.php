<x-admin-layout>


    @section('title', 'User Management')

    @push('styles')
    <style>
        .action-btn {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 0.2s ease;
        }
    </style>
    @endpush

    @section('content')
    <div class="container-fluid" style="padding: unset !important;">
        <div class="flex-wrap pb-2 mb-2 d-flex justify-content-between flex-md-nowrap align-items-center border-bottom">
            <h4 class="h4">User Management</h4>
            <div class="actions">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New User
                </a>
            </div>
        </div> 
        @if(session('user_updated'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('user_updated') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if(session('user_created'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('user_created') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if(session('user_deleted'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('user_deleted') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Users Table -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'staff' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->is_verified ? 'success' : 'warning' }}">
                                        {{ $user->is_verified ? 'Verified' : 'Pending' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($user->id !== Auth::id())
                                    <form action="{{ route('admin.users.toggle-verification', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <!-- <button type="submit" class="btn btn-sm btn-{{ $user->is_verified ? 'warning' : 'success' }} me-2">
                                            {{ $user->is_verified ? 'Unverify' : 'Verify' }}
                                        </button> -->
                                    </form>
                                    @endif
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-dark action-btn">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger action-btn" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all modals
            var editModals = document.querySelectorAll('.modal');
            editModals.forEach(function(modal) {
                new bootstrap.Modal(modal);
            });

            // Clear form errors when modal is closed
            editModals.forEach(function(modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    var form = modal.querySelector('form');
                    if (form) {
                        var errorElements = form.querySelectorAll('.is-invalid');
                        errorElements.forEach(function(element) {
                            element.classList.remove('is-invalid');
                        });
                        var feedbackElements = form.querySelectorAll('.invalid-feedback');
                        feedbackElements.forEach(function(element) {
                            element.remove();
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-admin-layout>
