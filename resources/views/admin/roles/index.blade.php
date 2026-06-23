<x-admin-layout>
    @section('title', 'Permissions Matrix')

    @section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h1 class="h2 text-dark fw-bold">Permissions Matrix</h1>
                <p class="text-muted mb-0">Configure role-based access control and capabilities for each user tier.</p>
            </div>
        </div>

        <form action="{{ route('admin.roles.update') }}" method="POST">
            @csrf

            <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                <div class="card-header bg-dark text-white p-3 d-flex align-items-center justify-content-between">
                    <span class="h5 mb-0 text-white"><i class="fas fa-user-shield me-2 text-gold"></i>Role-Permission Matrix</span>
                    <button type="submit" class="btn btn-gold px-4 py-2">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 40%;">Permission Capability</th>
                                    @foreach($roles as $role)
                                        <th class="text-center py-3" style="width: 20%;">
                                            <span class="text-uppercase tracking-wider font-semibold text-xs py-1 px-3 rounded bg-{{ $role === 'admin' ? 'dark text-gold' : ($role === 'staff' ? 'secondary text-white' : 'light text-dark') }}" style="{{ $role === 'admin' ? 'color: #D4AF37; background-color: #212529;' : '' }}">
                                                {{ ucfirst($role) }}
                                            </span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $key => $label)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-semibold text-dark">{{ $label }}</div>
                                            <div class="text-muted small">Ability to access: <code>{{ $key }}</code></div>
                                        </td>
                                        @foreach($roles as $role)
                                            <td class="text-center py-3">
                                                @if($role === 'admin')
                                                    <!-- Admin role always gets full access, keeping it locked to checked -->
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" type="checkbox" checked disabled id="admin_{{ $key }}">
                                                        <input type="hidden" name="permissions[admin][{{ $key }}]" value="1">
                                                    </div>
                                                    <span class="badge bg-dark-subtle text-dark-emphasis rounded-pill px-2 py-1 ms-1 d-block d-md-inline-block text-xxs">Bypass</span>
                                                @else
                                                    @php
                                                        $hasPerm = isset($rolePermissions[$role]) && in_array($key, $rolePermissions[$role]);
                                                    @endphp
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input role-perm-checkbox" type="checkbox" 
                                                               name="permissions[{{ $role }}][{{ $key }}]" 
                                                               value="1" 
                                                               {{ $hasPerm ? 'checked' : '' }}
                                                               id="{{ $role }}_{{ $key }}">
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small"><i class="fas fa-info-circle me-1 text-gold"></i>Note: Admin permissions bypass the database mappings via direct gates.</span>
                    <button type="submit" class="btn btn-gold px-4">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
    <style>
        .btn-gold {
            background-color: #D4AF37;
            color: #fff;
            border-color: #D4AF37;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            background-color: #bfa130;
            border-color: #bfa130;
            color: #fff;
            box-shadow: 0 4px 6px rgba(212, 175, 55, 0.2);
        }
        .form-check-input:checked {
            background-color: #D4AF37;
            border-color: #D4AF37;
        }
        .form-check-input:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
        .text-gold {
            color: #D4AF37;
        }
        .bg-dark-subtle {
            background-color: #e2e3e5;
        }
        .text-dark-emphasis {
            color: #1a1d20;
        }
        .text-xxs {
            font-size: 0.65rem;
        }
        /* Custom hover effect on table rows */
        .table-hover tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.03);
        }
    </style>
    @endpush
    @endsection
</x-admin-layout>
