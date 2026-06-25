<x-admin-layout>
    @push('styles')
    <style>
        .brands-page {
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
    </style>
    @endpush

    @section('content')
    <div class="brands-page">
        <div class="container">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-muted small uppercase fw-bold">Inventory Catalog</span>
                    <h1 class="h3 mb-0 fw-bold">Manage Brands</h1>
                </div>
                <div class="actions">
                    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary" style="background-color: #D4AF37; border-color: #D4AF37; color: #fff;">
                        <i class="fas fa-plus me-1"></i> Add New Brand
                    </a>
                </div>
            </div>

            <!-- Brands Table -->
            <div class="data-table">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                            <tr>
                                <td class="fw-bold">{{ $brand->name }}</td>
                                <td class="text-muted">{{ Str::limit($brand->description, 80) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $brand->status ? 'active' : 'inactive' }}">
                                        {{ $brand->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                            class="btn btn-sm btn-outline-primary action-btn me-2">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.brands.destroy', $brand->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this brand?');"
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
                                <td colspan="4" class="text-center text-muted py-4">No brands found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $brands->links() }}
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-admin-layout>
