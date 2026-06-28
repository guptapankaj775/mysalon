<x-admin-layout>
    @push('styles')
    <style>
        .admin-dashboard {
            padding: 40px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
        }

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

        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .data-table {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .data-table h3 {
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-inactive {
            background: #f4f6f9;
            color: #6c757d;
        }

        .text-gold {
            color: #D4AF37;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.02);
        }

        .vendor-logo {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #D4AF37;
        }
    </style>
    @endpush

    @section('title', 'Vendor Management')

    @section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h1 class="h2 text-dark fw-bold">Vendor Management</h1>
                <p class="text-muted mb-0">Manage product suppliers, tax compliance, and purchase contracts.</p>
            </div>
            <div>
                <a href="{{ route('admin.vendors.create') }}" class="btn btn-gold px-4 py-2">
                    <i class="fas fa-plus me-2"></i>Add New Vendor
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card border-0">
            <form action="{{ route('admin.vendors.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5 col-sm-12">
                    <label for="search" class="form-label small fw-semibold text-muted">Search Vendor</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by name, tax ID, contact or email...">
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="status" class="form-label small fw-semibold text-muted">Status</label>
                    <select class="form-select bg-light" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-4 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-gold flex-grow-1">Filter</button>
                    <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary px-3">Clear</a>
                </div>
            </form>
        </div>

        <!-- Vendors Table -->
        <div class="data-table border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="table-light">
                            <th class="ps-3 py-3" style="width: 5%;">#</th>
                            <th class="py-3" style="width: 30%;">Vendor / Brand</th>
                            <th class="py-3" style="width: 15%;">Contact Person</th>
                            <th class="py-3" style="width: 15%;">Contact Details</th>
                            <th class="text-center py-3" style="width: 10%;">Group</th>
                            <th class="text-center py-3" style="width: 10%;">Items Supplied</th>
                            <th class="text-center py-3" style="width: 10%;">Status</th>
                            <th class="text-end pe-3 py-3" style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr>
                            <td class="ps-3 fw-semibold text-muted">{{ $loop->iteration + ($vendors->currentPage() - 1) * $vendors->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($vendor->logo_path)
                                        <img src="{{ asset('storage/' . $vendor->logo_path) }}" class="vendor-logo" alt="logo">
                                    @else
                                        <div class="vendor-logo"><i class="fas fa-store"></i></div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $vendor->name }}</div>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            @if($vendor->tax_number)
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis border px-2 py-0.5" style="font-size: 0.65rem;">
                                                    GST/VAT: {{ $vendor->tax_number }}
                                                </span>
                                            @endif
                                            @if($vendor->website)
                                                <a href="{{ $vendor->website }}" target="_blank" class="text-decoration-none small text-muted" style="font-size: 0.75rem;">
                                                    <i class="fas fa-external-link-alt me-1"></i>Visit Site
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($vendor->contact_name)
                                    <span>{{ $vendor->contact_name }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($vendor->email)
                                    <div class="small text-truncate" style="max-width: 180px;" title="{{ $vendor->email }}">
                                        <i class="fas fa-envelope me-2 text-muted"></i>{{ $vendor->email }}
                                    </div>
                                @endif
                                @if($vendor->phone)
                                    <div class="small"><i class="fas fa-phone me-2 text-muted"></i>{{ $vendor->phone }}</div>
                                @endif
                                @if(!$vendor->email && !$vendor->phone)
                                    <span class="text-muted small">No contact info</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-3">{{ $vendor->group ?? 'Creditor' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-3">{{ $vendor->inventories_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge {{ $vendor->status ? 'status-active' : 'status-inactive' }}">
                                    {{ $vendor->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-sm btn-outline-info me-1 rounded-circle" title="Edit Vendor">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                onclick="return confirm('Are you sure you want to delete this vendor? This will un-assign any associated inventory items and permanently delete the vendor logo.')"
                                                title="Delete Vendor">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open d-block fs-3 mb-2 text-muted"></i>
                                No vendors found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($vendors->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $vendors->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
    @endsection
</x-admin-layout>
