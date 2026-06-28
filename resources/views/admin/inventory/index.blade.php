<x-admin-layout>
    @push('styles')
    <style>
        .admin-dashboard {
            padding: 40px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stat-icon.total-items {
            background: #e3f2fd;
            color: #1976d2;
        }

        .stat-icon.quantity {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .stat-icon.value {
            background: #fff3e0;
            color: #f57c00;
        }

        .stat-icon.low-stock {
            background: #ffebee;
            color: #c62828;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            margin: 10px 0 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
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

        .status-instock {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-lowstock {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-outstock {
            background: #ffebee;
            color: #c62828;
        }

        .action-btn {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 0.2s ease;
        }

        .btn-add-inventory {
            background-color: #D4AF37;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-inventory:hover {
            background-color: #C5A028;
            color: white;
            transform: translateY(-2px);
        }

        /* Custom Tooltip Styling */
        .tooltip {
            font-size: 0.75rem !important;
        }
        .tooltip-inner {
            background-color: #2C2C2C !important;
            color: #ffffff !important;
            border: 1px solid #D4AF37;
            padding: 8px 12px;
            border-radius: 6px;
            max-width: 300px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .bs-tooltip-top .tooltip-arrow::before, 
        .bs-tooltip-auto[data-popper-placement^="top"] .tooltip-arrow::before {
            border-top-color: #D4AF37 !important;
        }
        .bs-tooltip-bottom .tooltip-arrow::before, 
        .bs-tooltip-auto[data-popper-placement^="bottom"] .tooltip-arrow::before {
            border-bottom-color: #D4AF37 !important;
        }
        .bs-tooltip-start .tooltip-arrow::before, 
        .bs-tooltip-auto[data-popper-placement^="left"] .tooltip-arrow::before {
            border-left-color: #D4AF37 !important;
        }
        .bs-tooltip-end .tooltip-arrow::before, 
        .bs-tooltip-auto[data-popper-placement^="right"] .tooltip-arrow::before {
            border-right-color: #D4AF37 !important;
        }
    </style>
    @endpush

    @section('content')
    <div class="admin-dashboard">
        <div class="container-fluid px-4">
            <!-- Page Header -->
            <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-1 font-weight-bold">Inventory Management</h1>
                    <p class="text-muted mb-0">Track and manage salon stock items created by administrators and staff.</p>
                </div>
                <a href="{{ route('admin.inventory.create') }}" class="btn btn-add-inventory shadow-sm">
                    <i class="fas fa-plus me-2"></i> Add Inventory Item
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="mb-4 row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon total-items">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-value">{{ $stats['total_items'] }}</div>
                        <div class="stat-label">Unique Items</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon quantity">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="stat-value">{{ number_format($stats['total_quantity']) }}</div>
                        <div class="stat-label">Total Quantity in Stock</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon value">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="stat-value">Rs. {{number_format($stats['total_value'], 2)}}</div>
                        <div class="stat-label">Total Stock Value</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-icon low-stock">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-value">
                            {{ $stats['low_stock_count'] }}
                        </div>
                        <div class="stat-label">Low Stock Alerts</div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-card">
                <form action="{{ route('admin.inventory.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label text-muted small font-weight-bold">Search Items</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" id="search" class="form-control bg-light border-start-0" placeholder="Search by name or SKU..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="creator_id" class="form-label text-muted small font-weight-bold">Filter By Creator</label>
                        <select name="creator_id" id="creator_id" class="form-select bg-light">
                            <option value="">All Creators</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('creator_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->role }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 py-2">Filter</button>
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary w-100 py-2">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Inventory Table -->
            <div class="data-table">
                <h3>Stock Details</h3>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item Name</th>
                                <th>SKU</th>
                                <th>Brand</th>
                                <th>Quantity</th>
                                <th>Min Qty</th>
                                <th>MRP</th>
                                <th>Total Value</th>
                                <th>Status</th>
                                <th>Creator</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventories as $item)
                            <tr>
                                <td>
                                    @php
                                        $unitText = '';
                                        if ($item->unit && $item->unit_value) {
                                            $val = $item->unit_value;
                                            $formattedVal = $val % 1 == 0 ? (int)$val : $val;
                                            $unitText = " ({$formattedVal} {$item->unit})";
                                        } elseif ($item->unit) {
                                            $unitText = " ({$item->unit})";
                                        }
                                        $fullTooltip = $item->item_name . $unitText . ($item->description ? " - {$item->description}" : "");
                                    @endphp
                                    <div class="text-truncate" style="max-width:150px; cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $fullTooltip }}">
                                        <span class="fw-bold">{{ $item->item_name }}</span>
                                        @if($item->unit && $item->unit_value)
                                            @php
                                                $val = $item->unit_value;
                                                $formattedVal = $val % 1 == 0 ? (int)$val : $val;
                                            @endphp
                                            <span class="badge bg-light text-dark border ms-1" style="font-size: 10px; padding: 2px 4px;">{{ $formattedVal }} {{ $item->unit }}</span>
                                        @elseif($item->unit)
                                            <span class="badge bg-light text-dark border ms-1" style="font-size: 10px; padding: 2px 4px;">{{ $item->unit }}</span>
                                        @endif
                                        @if($item->description)
                                            <span class="text-muted ms-1" style="font-size: 12px;">- {{ $item->description }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td><code>{{ $item->sku ?? 'N/A' }}</code></td>
                                <td>
                                    @if($item->brand)
                                        <a href="{{ route('admin.brands.edit', $item->brand->id) }}" class="text-decoration-none fw-semibold text-truncate d-inline-block" style="color: #D4AF37; max-width: 120px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $item->brand->name }}">
                                            {{ $item->brand->name }}
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ $item->quantity }}</td>
                                <td>{{ $item->min_quantity }}</td>
                                <td>
                                    Rs. {{number_format($item->mrp ?? $item->price, 2)}}
                                    @if($item->unit && $item->unit_value)
                                        @php
                                            $val = $item->unit_value;
                                            $formattedVal = $val % 1 == 0 ? (int)$val : $val;
                                        @endphp
                                        <small class="text-muted d-block" style="font-size: 10px;">per {{ $formattedVal }} {{ $item->unit }}</small>
                                    @elseif($item->unit)
                                        <small class="text-muted d-block" style="font-size: 10px;">per {{ $item->unit }}</small>
                                    @endif
                                </td>
                                <td class="font-weight-bold">Rs. {{number_format($item->quantity * ($item->mrp ?? $item->price), 2)}}</td>
                                <td>
                                    @if($item->quantity == 0)
                                        <span class="status-badge status-outstock">Out of Stock</span>
                                    @elseif($item->quantity <= $item->min_quantity)
                                        <span class="status-badge status-lowstock">Low Stock</span>
                                    @else
                                        <span class="status-badge status-instock">In Stock</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark text-truncate" style="max-width: 100px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $item->creator->name ?? 'Deleted User' }}">
                                        {{ $item->creator->name ?? 'Deleted User' }}
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 10px;">{{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-sm btn-outline-dark action-btn">
                                            <i class="fas fa-edit me-1"></i>
                                        </a>
                                        <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this inventory item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger action-btn">
                                                <i class="fas fa-trash me-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-muted" style="opacity: 0.3;"></i>
                                    <p class="mb-0">No inventory items found. Click "Add Inventory Item" to get started.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $inventories->links() }}
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let searchTimeout;
            const searchInput = document.getElementById('search');

            function initTooltips() {
                // Remove any existing active tooltips to prevent orphans
                const existing = document.querySelectorAll('.tooltip');
                existing.forEach(el => el.remove());

                // Initialize new tooltips
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Initialize on page load
            initTooltips();
            
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    const query = e.target.value;
                    
                    searchTimeout = setTimeout(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.set('search', query);
                        url.searchParams.set('page', 1);
                        
                        const tableContainer = document.querySelector('.data-table');
                        if (tableContainer) {
                            tableContainer.style.opacity = '0.5';
                        }
                        
                        fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newTable = doc.querySelector('.data-table');
                            if (newTable && tableContainer) {
                                tableContainer.innerHTML = newTable.innerHTML;
                                tableContainer.style.opacity = '1';
                                // Re-initialize tooltips for newly loaded items
                                initTooltips();
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            if (tableContainer) tableContainer.style.opacity = '1';
                        });
                    }, 300);
                });
            }
        });
    </script>
    @endpush
</x-admin-layout>
