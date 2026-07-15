<x-admin-layout>
    @push('styles')
    <style>
        .services-page {
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
            padding-bottom: 15px;
        }

        /* Sticky Action Bar like Magento 2 */
        .magento-sticky-header {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1000;
            background: white;
            padding: 8px 20px;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        .magento-accordion .accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px !important;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            background: white;
        }

        .magento-accordion .accordion-button {
            background-color: #fafafa;
            color: #2d3748;
            font-weight: 600;
            border: none;
            outline: none;
            padding: 18px 24px;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
        }

        .magento-accordion .accordion-button:not(.collapsed) {
            background-color: #fdfaf2;
            color: #bfa13d;
            box-shadow: none;
            border-bottom: 1px solid #f7edd4;
        }

        .magento-accordion .accordion-button i {
            font-size: 1.15rem;
            width: 28px;
        }

        .magento-accordion .accordion-body {
            padding: 25px 30px;
            background: white;
        }

        /* Premium Gold Toggle Switch */
        .form-switch-gold .form-check-input {
            width: 3rem;
            height: 1.5rem;
            cursor: pointer;
        }

        .form-switch-gold .form-check-input:checked {
            background-color: #D4AF37;
            border-color: #D4AF37;
        }

        /* Gold highlights */
        .border-gold-focus:focus {
            border-color: #D4AF37 !important;
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25) !important;
        }

        /* Profile settings inputs matching styling */
        .form-control:not(textarea),
        .form-select,
        .input-group .btn {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            color: #1f2937 !important;
            font-size: 0.8rem !important;
            height: 24px !important;
            border-radius: 4px !important;
            box-sizing: border-box !important;
            transition: all 0.2s ease-in-out;
            padding: 2px 6px !important;
        }

        .form-select {
            padding: 0px 24px 0px 6px !important;
        }

        textarea.form-control {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            color: #1f2937 !important;
            padding: 4px 6px !important;
            font-size: 0.8rem !important;
            border-radius: 4px !important;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            background-color: #ffffff !important;
            border-color: #D4AF37 !important;
            box-shadow: none !important;
            color: #1f2937 !important;
            outline: none !important;
        }

        .input-group-text {
            height: 24px !important;
            font-size: 0.75rem !important;
            border-radius: 4px 0 0 4px !important;
            background-color: #f3f4f6 !important;
            border: 1px solid #e5e7eb !important;
            padding: 2px 6px !important;
        }

        .input-group .btn {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 24px !important;
            border-radius: 0 4px 4px 0 !important;
        }

        input[type="file"] {
            padding: 0px 4px !important;
            line-height: 22px !important;
        }

        input[type="file"]::file-selector-button {
            height: 22px !important;
            padding: 0px 6px !important;
            font-size: 0.75rem !important;
            margin-top: -1px !important;
            border: none !important;
            background: #e5e7eb !important;
            border-radius: 3px !important;
        }

        /* Adjust labels to match layout spacing */
        .form-label {
            font-size: 0.75rem !important;
            margin-bottom: 2px !important;
            color: #4b5563 !important;
        }

        /* Inventory list container flex layout for side-by-side items */
        .inventory-list-container {
            display: flex !important;
            flex-flow: row wrap !important;
            gap: 6px 12px !important;
            background-color: #f9fafb !important;
            max-height: 110px !important;
            overflow-y: auto;
            border-radius: 8px;
        }

        .inventory-item-row {
            display: flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
            flex: 0 0 auto !important;
        }

        /* Inventory item truncation */
        .inventory-label-container {
            cursor: pointer !important;
            display: inline-block !important;
            max-width: 180px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            vertical-align: middle !important;
            font-size: 0.95rem !important;
            color: #2d3748 !important;
        }

        /* Custom Tooltip Styling */
        .tooltip {
            font-size: 0.75rem !important;
            z-index: 999999 !important;
        }
        .tooltip-inner {
            background-color: #2C2C2C !important;
            color: #ffffff !important;
            border: 1px solid #D4AF37;
            padding: 8px 12px;
            border-radius: 6px;
            max-width: 300px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: left;
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
    <div class="services-page">
        <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Magento Sticky Action Bar -->
            <div class="magento-sticky-header d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="mb-0 fw-bold" style="font-size: 1.15rem; color: #2d3748;">Add New Service</h5>
                </div>
                <div class="actions d-flex gap-2">
                    <a href="{{ route('admin.services') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center" style="height: 30px; font-size: 0.85rem;">
                        <i class="fas fa-chevron-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-warning text-dark fw-bold btn-sm d-flex align-items-center" style="background-color: #D4AF37; border-color: #D4AF37; height: 30px; font-size: 0.85rem;">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <!-- Single Column Layout -->
                    <div class="col-lg-12" style="padding:unset;">
                        
                        <!-- SINGLE CARD: Service Information -->
                        <div class="card mb-1 border-0 shadow-sm" style="border-radius: 10px; overflow: hidden; background: white;">
                            <div class="card-body p-2">
                                <div class="row px-3 py-2">
                                    
                                    <!-- SECTION 1: General Info -->
                                    <div class="col-md-3 mb-2">
                                        <label for="name" class="form-label fw-bold">Service Name</label>
                                        <input type="text" class="form-control border-gold-focus @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Haircut & Styling" required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-2">
                                        <label for="category_id" class="form-label fw-bold">Category</label>
                                        <div class="input-group">
                                            <select class="form-select border-gold-focus @error('category_id') is-invalid @enderror"
                                                id="category_id" name="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-outline-warning" type="button" id="addCategoryBtn" data-bs-toggle="modal" data-bs-target="#quickAddCategoryModal" style="border-color: #ced4da;">
                                                <i class="fas fa-plus text-warning"></i>
                                            </button>
                                        </div>
                                        @error('category_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-2">
                                        <label for="icon" class="form-label fw-bold">Service Icon (SVG/PNG)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-upload text-muted"></i></span>
                                            <input type="file" class="form-control border-gold-focus @error('icon') is-invalid @enderror"
                                                id="icon" name="icon" accept="image/png, image/svg+xml" required>
                                        </div>
                                        @error('icon')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-2">
                                        <label class="form-label fw-bold d-block">Active Status</label>
                                        <div class="form-check form-switch form-switch-gold ps-0 d-flex align-items-center" style="height: 30px;">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="status" name="status" value="1"
                                                {{ old('status', true) ? 'checked' : '' }}>
                                            <label class="form-check-label text-muted small" for="status">Visible and available</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <label for="description" class="form-label fw-bold">Description</label>
                                        <textarea class="form-control border-gold-focus @error('description') is-invalid @enderror"
                                            id="description" name="description" rows="2" placeholder="Describe the details and customer experience of this service..." required>{{ old('description') }}</textarea>
                                        @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- SECTION 2: Pricing & Duration -->
                                    <div class="col-md-6 mb-2">
                                        <label for="price" class="form-label fw-bold">Price (Rs.)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rs.</span>
                                            <input type="number" class="form-control border-gold-focus @error('price') is-invalid @enderror"
                                                id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" placeholder="0.00" required>
                                            @error('price')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label for="duration" class="form-label fw-bold">Duration (minutes)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control border-gold-focus @error('duration') is-invalid @enderror"
                                                id="duration" name="duration" value="{{ old('duration') }}" min="1" placeholder="e.g. 45" required>
                                            <span class="input-group-text bg-light">mins</span>
                                            @error('duration')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- SECTION 3: Features & Highlights -->
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label fw-bold">Key Included Steps / Highlights</label>
                                        <div class="features-container mb-2">
                                            <div class="input-group mb-2 feature-row">
                                                <input type="text" class="form-control border-gold-focus" name="features[]" placeholder="e.g. Deep conditioning hair mask" required>
                                                <button type="button" class="btn btn-outline-danger remove-feature" style="display:none;"><i class="fas fa-trash-alt"></i></button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-warning btn-sm add-feature text-dark"><i class="fas fa-plus me-1"></i> Add Highlight</button>
                                        @error('features')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- SECTION 4: Consumable Inventory Items -->
                                    <div class="col-md-12 mb-2">
                                        <div class="mb-2">
                                            <label class="form-label fw-bold">Search & Map Products Used</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                                <input type="text" id="inventorySearch" class="form-control border-gold-focus border-start-0" placeholder="Type to filter inventory...">
                                            </div>
                                        </div>

                                        <div class="card border-0 p-2 inventory-list-container">
                                            @forelse($inventories as $inventory)
                                            <div class="form-check inventory-item-row">
                                                <input class="form-check-input border-2" type="checkbox" name="inventories[]" value="{{ $inventory->id }}"
                                                       id="inventory_{{ $inventory->id }}"
                                                       {{ is_array(old('inventories')) && in_array($inventory->id, old('inventories')) ? 'checked' : '' }}>
                                                <label class="ml-1 mt-1 form-check-label inventory-label-container" style="font-size: 12px !important;" for="inventory_{{ $inventory->id }}"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="{{ $inventory->item_name }}{{ $inventory->sku ? ' ['.$inventory->sku.']' : '' }}{{ ($inventory->unit_value && $inventory->unit) ? ' ('.($inventory->unit_value % 1 == 0 ? (int)$inventory->unit_value : $inventory->unit_value).' '.$inventory->unit.')' : '' }}{{ $inventory->description ? ' - '.$inventory->description : '' }}">
                                                    <strong>{{ $inventory->item_name }}</strong> 
                                                    <span class="inventory-details">
                                                        @if($inventory->sku)<code class="ms-1">{{ $inventory->sku }}</code>@endif
                                                        @if($inventory->unit && $inventory->unit_value)
                                                            <span class="badge bg-white text-dark border ms-1">{{ $inventory->unit_value % 1 == 0 ? (int)$inventory->unit_value : $inventory->unit_value }} {{ $inventory->unit }}</span>
                                                        @endif
                                                    </span>
                                                </label>
                                            </div>
                                            @empty
                                            <div class="text-muted small">
                                                <i class="fas fa-info-circle me-1"></i>No inventory items registered. <a href="{{ route('admin.inventory.create') }}" target="_blank">Add inventory items first</a>.
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Features / Highlights Handler
            const container = document.querySelector('.features-container');
            const addButton = document.querySelector('.add-feature');

            function updateRemoveButtons() {
                const rows = container.querySelectorAll('.feature-row');
                rows.forEach((row, idx) => {
                    const removeBtn = row.querySelector('.remove-feature');
                    if (rows.length === 1) {
                        removeBtn.style.display = 'none';
                    } else {
                        removeBtn.style.display = 'inline-block';
                    }
                });
            }

            addButton.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'input-group mb-2 feature-row';
                newRow.innerHTML = `
                    <input type="text" class="form-control border-gold-focus" name="features[]" placeholder="Highlight detail..." required>
                    <button type="button" class="btn btn-outline-danger remove-feature"><i class="fas fa-trash-alt"></i></button>
                `;
                container.appendChild(newRow);
                updateRemoveButtons();
            });

            container.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-feature');
                if (btn) {
                    btn.closest('.feature-row').remove();
                    updateRemoveButtons();
                }
            });

            // Initial check
            updateRemoveButtons();

            // Client-side Inventory Filter
            const searchInput = document.getElementById('inventorySearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const filter = this.value.toLowerCase();
                    const items = document.querySelectorAll('.inventory-item-row');
                    items.forEach(function(item) {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(filter)) {
                            item.style.setProperty('display', 'block', 'important');
                        } else {
                            item.style.setProperty('display', 'none', 'important');
                        }
                    });
                });
            }

            // Quick Add Category Form Handler
            const quickAddForm = document.getElementById('quickAddCategoryForm');
            if (quickAddForm) {
                quickAddForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitBtn = document.getElementById('quickAddCategorySubmitBtn');
                    const alertDiv = document.getElementById('quickAddCategoryAlert');
                    
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
                    alertDiv.classList.add('d-none');
                    
                    const formData = new FormData(quickAddForm);
                    
                    fetch("{{ route('admin.categories.store') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(res => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Save Category';
                        
                        if (res.status === 200 || res.status === 201) {
                            const newCategory = res.body.category;
                            
                            // Append option to Category select dropdown
                            const categorySelect = document.getElementById('category_id');
                            const newOption = new Option(newCategory.name, newCategory.id, true, true);
                            categorySelect.add(newOption);
                            
                            // Close modal
                            const modalEl = document.getElementById('quickAddCategoryModal');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            modal.hide();
                            
                            // Clear form
                            quickAddForm.reset();
                        } else {
                            // Validation or other errors
                            let errorMsg = 'An error occurred. Please try again.';
                            if (res.body.errors) {
                                errorMsg = Object.values(res.body.errors).flat().join('<br>');
                            } else if (res.body.message) {
                                errorMsg = res.body.message;
                            }
                            alertDiv.innerHTML = errorMsg;
                            alertDiv.classList.remove('d-none');
                        }
                    })
                    .catch(err => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Save Category';
                        alertDiv.innerHTML = 'An unexpected error occurred. Please check your network connection.';
                        alertDiv.classList.remove('d-none');
                        console.error('Error adding category:', err);
                    });
                });
            }

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    @endpush

    <!-- Quick Add Category Modal -->
    <div class="modal fade" id="quickAddCategoryModal" tabindex="-1" aria-labelledby="quickAddCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4" style="background-color: #fafafa;">
                    <h5 class="modal-title fw-bold text-dark" id="quickAddCategoryModalLabel">
                        <i class="fas fa-th-list text-warning me-2"></i> Quick Add Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickAddCategoryForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div id="quickAddCategoryAlert" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="modal_category_name" class="form-label fw-bold text-muted small uppercase">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-gold-focus" id="modal_category_name" name="name" required placeholder="e.g. Nail Art Services">
                        </div>
                        <div class="mb-0">
                            <label for="modal_category_description" class="form-label fw-bold text-muted small uppercase">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control border-gold-focus" id="modal_category_description" name="description" rows="3" required placeholder="Describe what this category includes..."></textarea>
                        </div>
                        <input type="hidden" name="status" value="1">
                    </div>
                    <div class="modal-footer border-top bg-light py-3 px-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-dark fw-bold px-4" style="background-color: #D4AF37; border-color: #D4AF37;" id="quickAddCategorySubmitBtn">
                            <i class="fas fa-save me-1"></i> Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
</x-admin-layout>
