<x-admin-layout>
    @push('styles')
    <style>
        .categories-page {
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
    <div class="categories-page">
        <div class="container">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-muted small uppercase fw-bold">Inventory Catalog</span>
                    <h1 class="h3 mb-0 fw-bold">Manage Inventory Categories</h1>
                </div>
                <div class="actions">
                    <a href="{{ route('admin.inventory-categories.create') }}" class="btn btn-primary" style="background-color: #D4AF37; border-color: #D4AF37; color: #fff;">
                        <i class="fas fa-plus me-1"></i> Add New Category
                    </a>
                </div>
            </div>

            <!-- Categories Table -->
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
                            @forelse($categories as $category)
                            <tr>
                                <td class="fw-bold">{{ $category->name }}</td>
                                <td class="text-muted">{{ Str::limit($category->description, 80) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $category->status ? 'active' : 'inactive' }}">
                                        {{ $category->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.inventory-categories.edit', $category->id) }}"
                                            class="btn btn-sm btn-outline-primary action-btn me-2">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.inventory-categories.destroy', $category->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this category?');"
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
                                <td colspan="4" class="text-center text-muted py-4">No categories found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-admin-layout>
