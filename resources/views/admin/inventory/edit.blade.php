<x-admin-layout>
    @push('styles')
    <style>
        .admin-dashboard {
            padding: 40px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-title {
            color: #333;
            font-weight: 700;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .btn-submit {
            background-color: #D4AF37;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #C5A028;
            color: white;
            transform: translateY(-2px);
        }

        .btn-cancel {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
    </style>
    @endpush

    @section('content')
    <div class="admin-dashboard">
        <div class="container">
            <div class="form-card">
                <h2 class="form-title d-flex align-items-center">
                    <i class="fas fa-edit me-3 text-warning"></i>
                    Edit Inventory Item
                </h2>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Item Name -->
                        <div class="col-md-8">
                            <label for="item_name" class="form-label font-weight-bold">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="item_name" id="item_name" class="form-control" placeholder="e.g. Lavender Shampoo, Professional Scissors" value="{{ old('item_name', $inventory->item_name) }}" required>
                        </div>

                        <!-- SKU -->
                        <div class="col-md-4">
                            <label for="sku" class="form-label font-weight-bold">SKU (Stock Keeping Unit)</label>
                            <input type="text" name="sku" id="sku" class="form-control" placeholder="e.g. SH-LAV-001" value="{{ old('sku', $inventory->sku) }}">
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label for="description" class="form-label font-weight-bold">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control" placeholder="Add some notes about this item...">{{ old('description', $inventory->description) }}</textarea>
                        </div>

                        <!-- Unit -->
                        <div class="col-md-6">
                            <label for="unit" class="form-label font-weight-bold">Unit</label>
                            <select name="unit" id="unit" class="form-select">
                                <option value="">Select Unit (Optional)</option>
                                <option value="Gram" {{ old('unit', $inventory->unit) == 'Gram' ? 'selected' : '' }}>Gram</option>
                                <option value="Pack" {{ old('unit', $inventory->unit) == 'Pack' ? 'selected' : '' }}>Pack</option>
                                <option value="ML" {{ old('unit', $inventory->unit) == 'ML' ? 'selected' : '' }}>ML</option>
                                <option value="kg" {{ old('unit', $inventory->unit) == 'kg' ? 'selected' : '' }}>kg</option>
                                <option value="grm" {{ old('unit', $inventory->unit) == 'grm' ? 'selected' : '' }}>grm</option>
                            </select>
                        </div>

                        <!-- Unit Value / Qty -->
                        <div class="col-md-6">
                            <label for="unit_value" class="form-label font-weight-bold">Unit Value / Qty</label>
                            @php
                                $unitValue = $inventory->unit_value;
                                if ($unitValue !== null) {
                                    $unitValue = $unitValue % 1 == 0 ? (int)$unitValue : $unitValue;
                                }
                            @endphp
                            <input type="number" name="unit_value" id="unit_value" class="form-control" min="0" step="0.01" placeholder="e.g. 1, 100, 250, 500" value="{{ old('unit_value', $unitValue) }}">
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-4">
                            <label for="quantity" class="form-label font-weight-bold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="0" value="{{ old('quantity', $inventory->quantity) }}" required>
                        </div>

                        <!-- Min Quantity for Alert -->
                        <div class="col-md-4">
                            <label for="min_quantity" class="form-label font-weight-bold">Min Quantity (Low Stock Alert) <span class="text-danger">*</span></label>
                            <input type="number" name="min_quantity" id="min_quantity" class="form-control" min="0" value="{{ old('min_quantity', $inventory->min_quantity) }}" required>
                        </div>

                        <!-- Price per Unit -->
                        <div class="col-md-4">
                            <label for="price" class="form-label font-weight-bold">Unit Price (Rs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="price" id="price" class="form-control" min="0" step="0.01" value="{{ old('price', $inventory->price) }}" required>
                                <span class="input-group-text">Rs.</span>
                            </div>
                            <small id="price_help" class="form-text text-muted mt-1 d-block">Price according to unit.</small>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary btn-cancel">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-submit shadow-sm">
                            <i class="fas fa-save me-2"></i> Update Inventory Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const unitSelect = document.getElementById('unit');
            const unitVal = document.getElementById('unit_value');
            const priceHelp = document.getElementById('price_help');

            function updatePriceHelp() {
                const unit = unitSelect.value;
                const val = parseFloat(unitVal.value) || 0;
                
                if (unit && val > 0) {
                    const formattedVal = val % 1 === 0 ? parseInt(val) : val;
                    priceHelp.innerHTML = `<span class="text-success font-weight-bold"><i class="fas fa-info-circle me-1"></i> Price per ${formattedVal} ${unit}</span>`;
                } else if (unit) {
                    priceHelp.innerHTML = `<span class="text-success font-weight-bold"><i class="fas fa-info-circle me-1"></i> Price per ${unit}</span>`;
                } else {
                    priceHelp.textContent = 'Price according to unit.';
                }
            }

            unitSelect.addEventListener('change', updatePriceHelp);
            unitVal.addEventListener('input', updatePriceHelp);
            updatePriceHelp();
        });
    </script>
    @endpush
</x-admin-layout>
