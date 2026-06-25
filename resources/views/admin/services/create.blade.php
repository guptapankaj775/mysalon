<x-admin-layout>
    @push('styles')
    <style>
        .services-page {
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
            padding-bottom: 50px;
        }

        /* Sticky Action Bar like Magento 2 */
        .magento-sticky-header {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1000;
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
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
    </style>
    @endpush

    @section('content')
    <div class="services-page">
        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf

            <!-- Magento Sticky Action Bar -->
            <div class="magento-sticky-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small uppercase fw-bold">Service Catalog</span>
                    <h1 class="h3 mb-0 fw-bold">Add New Service</h1>
                </div>
                <div class="actions">
                    <a href="{{ route('admin.services') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-chevron-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-warning text-dark fw-bold px-4" style="background-color: #D4AF37; border-color: #D4AF37;">
                        <i class="fas fa-save me-1"></i> Save Service
                    </button>
                </div>
            </div>

            <div class="container">
                <div class="accordion magento-accordion" id="serviceFormAccordion">
                    
                    <!-- PANEL 1: General Information -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingGeneral">
                          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGeneral" aria-expanded="true" aria-controls="collapseGeneral">
                            <i class="fas fa-info-circle text-warning me-2"></i> General Information
                          </button>
                        </h2>
                        <div id="collapseGeneral" class="accordion-collapse collapse show" aria-labelledby="headingGeneral">
                          <div class="accordion-body">
                              <div class="row">
                                  <div class="col-md-6 mb-4">
                                      <label for="name" class="form-label fw-bold">Service Name</label>
                                      <input type="text" class="form-control border-gold-focus @error('name') is-invalid @enderror"
                                          id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Premium Haircut & Styling" required>
                                      @error('name')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-6 mb-4">
                                      <label for="category_id" class="form-label fw-bold">Category</label>
                                      <select class="form-select border-gold-focus @error('category_id') is-invalid @enderror"
                                          id="category_id" name="category_id" required>
                                          <option value="">Select Category</option>
                                          @foreach($categories as $category)
                                          <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                              {{ $category->name }}
                                          </option>
                                          @endforeach
                                      </select>
                                      @error('category_id')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-12 mb-4">
                                      <label for="description" class="form-label fw-bold">Description</label>
                                      <textarea class="form-control border-gold-focus @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4" placeholder="Describe the details and customer experience of this service..." required>{{ old('description') }}</textarea>
                                      @error('description')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-12">
                                      <label class="form-label fw-bold d-block">Active Status</label>
                                      <div class="form-check form-switch form-switch-gold ps-0 d-flex align-items-center">
                                          <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="status" name="status" value="1"
                                              {{ old('status', true) ? 'checked' : '' }}>
                                          <label class="form-check-label text-muted" for="status">Visible and available for online booking</label>
                                      </div>
                                  </div>
                              </div>
                          </div>
                        </div>
                    </div>

                    <!-- PANEL 2: Pricing & Duration -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPricing">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePricing" aria-expanded="false" aria-controls="collapsePricing">
                            <i class="fas fa-tags text-warning me-2"></i> Pricing & Duration
                          </button>
                        </h2>
                        <div id="collapsePricing" class="accordion-collapse collapse" aria-labelledby="headingPricing">
                          <div class="accordion-body">
                              <div class="row">
                                  <div class="col-md-6 mb-4">
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

                                  <div class="col-md-6 mb-4">
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
                              </div>
                          </div>
                        </div>
                    </div>

                    <!-- PANEL 3: Visuals & Identity -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingVisuals">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVisuals" aria-expanded="false" aria-controls="collapseVisuals">
                            <i class="fas fa-icons text-warning me-2"></i> Visuals & Identity
                          </button>
                        </h2>
                        <div id="collapseVisuals" class="accordion-collapse collapse" aria-labelledby="headingVisuals">
                          <div class="accordion-body">
                              <div class="row">
                                  <div class="col-md-12">
                                      <label for="icon" class="form-label fw-bold">Service Icon Class</label>
                                      <div class="input-group">
                                          <span class="input-group-text bg-light"><i class="fas fa-spa"></i></span>
                                          <input type="text" class="form-control border-gold-focus @error('icon') is-invalid @enderror"
                                              id="icon" name="icon" value="{{ old('icon') }}"
                                              placeholder="Enter Font Awesome icon class (e.g., fa-spa)" required>
                                      </div>
                                      <div class="form-text text-muted">Use standard Font Awesome 5 icons (e.g. <code>fa-spa</code>, <code>fa-cut</code>, <code>fa-hand-sparkles</code>, <code>fa-gem</code>).</div>
                                      @error('icon')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>
                              </div>
                          </div>
                        </div>
                    </div>

                    <!-- PANEL 4: Service Features / Highlights -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFeatures">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFeatures" aria-expanded="false" aria-controls="collapseFeatures">
                            <i class="fas fa-list-ul text-warning me-2"></i> Features & Highlights
                          </button>
                        </h2>
                        <div id="collapseFeatures" class="accordion-collapse collapse" aria-labelledby="headingFeatures">
                          <div class="accordion-body">
                              <div class="row">
                                  <div class="col-md-12">
                                      <label class="form-label fw-bold">Key Included Steps / Highlights</label>
                                      <div class="features-container mb-3">
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
                              </div>
                          </div>
                        </div>
                    </div>

                    <!-- PANEL 5: Consumable Inventory Items -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingInventory">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInventory" aria-expanded="false" aria-controls="collapseInventory">
                            <i class="fas fa-boxes text-warning me-2"></i> Consumable Inventory Items
                          </button>
                        </h2>
                        <div id="collapseInventory" class="accordion-collapse collapse" aria-labelledby="headingInventory">
                          <div class="accordion-body">
                              <div class="mb-3">
                                  <label class="form-label fw-bold">Search & Map Products Used</label>
                                  <div class="input-group">
                                      <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                      <input type="text" id="inventorySearch" class="form-control border-gold-focus border-start-0" placeholder="Type to filter inventory items by name, SKU or vendor...">
                                  </div>
                              </div>

                              <div class="card bg-light border-0 p-3" style="max-height: 250px; overflow-y: auto; border-radius: 8px;">
                                  @forelse($inventories as $inventory)
                                  <div class="form-check mb-2 inventory-item-row">
                                      <input class="form-check-input" type="checkbox" name="inventories[]" value="{{ $inventory->id }}"
                                             id="inventory_{{ $inventory->id }}"
                                             {{ is_array(old('inventories')) && in_array($inventory->id, old('inventories')) ? 'checked' : '' }}>
                                      <label class="form-check-label" for="inventory_{{ $inventory->id }}">
                                          <strong>{{ $inventory->item_name }}</strong> 
                                          @if($inventory->sku)<code class="ms-1">{{ $inventory->sku }}</code>@endif
                                          @if($inventory->unit && $inventory->unit_value)
                                              <span class="badge bg-white text-dark border ms-1">{{ $inventory->unit_value % 1 == 0 ? (int)$inventory->unit_value : $inventory->unit_value }} {{ $inventory->unit }}</span>
                                          @endif
                                          @if($inventory->vendor)
                                              <span class="text-muted small ms-1">(supplied by {{ $inventory->vendor->name }})</span>
                                          @endif
                                      </label>
                                  </div>
                                  @empty
                                  <div class="text-muted small">
                                      <i class="fas fa-info-circle me-1"></i>No inventory items registered. <a href="{{ route('admin.inventory.create') }}" target="_blank">Add inventory items first</a>.
                                  </div>
                                  @endforelse
                              </div>
                              <div class="form-text text-muted mt-2">Select the products that are used or consumed during this service.</div>
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

            // Auto-expand panels on validation errors
            @if ($errors->any())
                // Find all fields with error and expand their accordion parent panel
                const errorFields = document.querySelectorAll('.is-invalid, .invalid-feedback');
                errorFields.forEach(function(el) {
                    const accordionCollapse = el.closest('.accordion-collapse');
                    if (accordionCollapse) {
                        const bsCollapse = new bootstrap.Collapse(accordionCollapse, {
                            toggle: false
                        });
                        bsCollapse.show();
                    }
                });
            @endif
        });
    </script>
    @endpush
    @endsection
</x-admin-layout>
