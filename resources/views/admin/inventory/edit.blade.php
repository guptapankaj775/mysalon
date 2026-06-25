<x-admin-layout>
    @push('styles')
    <style>
        .inventory-page {
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
    <div class="inventory-page">
        <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Magento Sticky Action Bar -->
            <div class="magento-sticky-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small uppercase fw-bold">Inventory Catalog</span>
                    <h1 class="h3 mb-0 fw-bold">Edit Inventory Item: {{ $inventory->item_name }}</h1>
                </div>
                <div class="actions">
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-chevron-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-warning text-dark fw-bold px-4" style="background-color: #D4AF37; border-color: #D4AF37;">
                        <i class="fas fa-save me-1"></i> Save Item
                    </button>
                </div>
            </div>

            <div class="container">
                <div class="accordion magento-accordion" id="inventoryFormAccordion">
                    
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
                                      <label for="item_name" class="form-label fw-bold">Item Name <span class="text-danger">*</span></label>
                                      <input type="text" name="item_name" id="item_name" class="form-control border-gold-focus @error('item_name') is-invalid @enderror" placeholder="e.g. Lavender Shampoo" value="{{ old('item_name', $inventory->item_name) }}" required>
                                      @error('item_name')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="sku" class="form-label fw-bold">Item Code (SKU)</label>
                                      <input type="text" name="sku" id="sku" class="form-control border-gold-focus @error('sku') is-invalid @enderror" placeholder="e.g. SH-LAV-001" value="{{ old('sku', $inventory->sku) }}">
                                      @error('sku')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label class="form-label fw-bold d-block">Active Status</label>
                                      <div class="form-check form-switch form-switch-gold ps-0 d-flex align-items-center mt-1">
                                          <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="status" name="status" value="1"
                                              {{ old('status', $inventory->status) ? 'checked' : '' }}>
                                          <label class="form-check-label text-muted" for="status">Enabled</label>
                                      </div>
                                  </div>

                                  <div class="col-md-4 mb-4">
                                      <label for="brand_id" class="form-label fw-bold">Brand</label>
                                      <select name="brand_id" id="brand_id" class="form-select border-gold-focus @error('brand_id') is-invalid @enderror">
                                          <option value="">Select Brand</option>
                                          @foreach($brands as $brand)
                                              <option value="{{ $brand->id }}" {{ old('brand_id', $inventory->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                          @endforeach
                                      </select>
                                      @error('brand_id')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-4 mb-4">
                                      <label for="inventory_category_id" class="form-label fw-bold">Category</label>
                                      <select name="inventory_category_id" id="inventory_category_id" class="form-select border-gold-focus @error('inventory_category_id') is-invalid @enderror">
                                          <option value="">Select Category</option>
                                          @foreach($categories as $category)
                                              <option value="{{ $category->id }}" {{ old('inventory_category_id', $inventory->inventory_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                          @endforeach
                                      </select>
                                      @error('inventory_category_id')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-4 mb-4">
                                      <label for="division" class="form-label fw-bold">Division</label>
                                      <input type="text" name="division" id="division" class="form-control border-gold-focus @error('division') is-invalid @enderror" placeholder="e.g. Hair Care" value="{{ old('division', $inventory->division) }}">
                                      @error('division')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-12">
                                      <label for="description" class="form-label fw-bold">Description</label>
                                      <textarea name="description" id="description" rows="3" class="form-control border-gold-focus @error('description') is-invalid @enderror" placeholder="Add description notes...">{{ old('description', $inventory->description) }}</textarea>
                                      @error('description')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
                              </div>
                          </div>
                        </div>
                    </div>

                    <!-- PANEL 2: Pricing & Tax -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPricing">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePricing" aria-expanded="false" aria-controls="collapsePricing">
                            <i class="fas fa-tags text-warning me-2"></i> Pricing & Tax
                          </button>
                        </h2>
                        <div id="collapsePricing" class="accordion-collapse collapse" aria-labelledby="headingPricing">
                          <div class="accordion-body">
                              <div class="row">
                                  <div class="col-md-3 mb-4">
                                      <label for="price" class="form-label fw-bold">Sale Price (Rs.) <span class="text-danger">*</span></label>
                                      <div class="input-group">
                                          <input type="number" name="price" id="price" class="form-control border-gold-focus @error('price') is-invalid @enderror" min="0" step="0.01" value="{{ old('price', $inventory->price) }}" required>
                                          <span class="input-group-text bg-light">Rs.</span>
                                      </div>
                                      @error('price')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="mrp" class="form-label fw-bold">MRP (Rs.)</label>
                                      <div class="input-group">
                                          <input type="number" name="mrp" id="mrp" class="form-control border-gold-focus @error('mrp') is-invalid @enderror" min="0" step="0.01" value="{{ old('mrp', $inventory->mrp) }}">
                                          <span class="input-group-text bg-light">Rs.</span>
                                      </div>
                                      @error('mrp')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="discount_percent" class="form-label fw-bold">Disc. %</label>
                                      <div class="input-group">
                                          <input type="number" name="discount_percent" id="discount_percent" class="form-control border-gold-focus @error('discount_percent') is-invalid @enderror" min="0" max="100" step="0.01" value="{{ old('discount_percent', $inventory->discount_percent) }}">
                                          <span class="input-group-text bg-light">%</span>
                                      </div>
                                      @error('discount_percent')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="taxable_amount" class="form-label fw-bold">Taxable Amount (Rs.)</label>
                                      <div class="input-group">
                                          <input type="number" name="taxable_amount" id="taxable_amount" class="form-control border-gold-focus @error('taxable_amount') is-invalid @enderror" min="0" step="0.01" value="{{ old('taxable_amount', $inventory->taxable_amount) }}">
                                          <span class="input-group-text bg-light">Rs.</span>
                                      </div>
                                      @error('taxable_amount')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="gst_percent" class="form-label fw-bold">GST %</label>
                                      <div class="input-group">
                                          <input type="number" name="gst_percent" id="gst_percent" class="form-control border-gold-focus @error('gst_percent') is-invalid @enderror" min="0" max="100" step="0.01" value="{{ old('gst_percent', $inventory->gst_percent) }}">
                                          <span class="input-group-text bg-light">%</span>
                                      </div>
                                      @error('gst_percent')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="gst_input" class="form-label fw-bold">GST Input (Rs.)</label>
                                      <div class="input-group">
                                          <input type="number" name="gst_input" id="gst_input" class="form-control border-gold-focus @error('gst_input') is-invalid @enderror" min="0" step="0.01" value="{{ old('gst_input', $inventory->gst_input) }}">
                                          <span class="input-group-text bg-light">Rs.</span>
                                      </div>
                                      @error('gst_input')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="gst_output" class="form-label fw-bold">GST Output (Rs.)</label>
                                      <div class="input-group">
                                          <input type="number" name="gst_output" id="gst_output" class="form-control border-gold-focus @error('gst_output') is-invalid @enderror" min="0" step="0.01" value="{{ old('gst_output', $inventory->gst_output) }}">
                                          <span class="input-group-text bg-light">Rs.</span>
                                      </div>
                                      @error('gst_output')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="hsn_code" class="form-label fw-bold">HSN Code</label>
                                      <input type="text" name="hsn_code" id="hsn_code" class="form-control border-gold-focus @error('hsn_code') is-invalid @enderror" placeholder="e.g. 33051090" value="{{ old('hsn_code', $inventory->hsn_code) }}">
                                      @error('hsn_code')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
                              </div>
                          </div>
                        </div>
                    </div>

                    <!-- PANEL 3: Purchase Details -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPurchase">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePurchase" aria-expanded="false" aria-controls="collapsePurchase">
                            <i class="fas fa-shopping-cart text-warning me-2"></i> Purchase Details
                          </button>
                        </h2>
                        <div id="collapsePurchase" class="accordion-collapse collapse" aria-labelledby="headingPurchase">
                          <div class="accordion-body">
                              <div class="row">
                                  <div class="col-md-3 mb-4">
                                      <label for="cost" class="form-label fw-bold">Pur. Rate (Cost Price)</label>
                                      <div class="input-group">
                                          <input type="number" name="cost" id="cost" class="form-control border-gold-focus @error('cost') is-invalid @enderror" min="0" step="0.01" value="{{ old('cost', $inventory->cost) }}">
                                          <span class="input-group-text bg-light">Rs.</span>
                                      </div>
                                      @error('cost')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="purchase_discount_percent" class="form-label fw-bold">Pur Disc. %</label>
                                      <div class="input-group">
                                          <input type="number" name="purchase_discount_percent" id="purchase_discount_percent" class="form-control border-gold-focus @error('purchase_discount_percent') is-invalid @enderror" min="0" max="100" step="0.01" value="{{ old('purchase_discount_percent', $inventory->purchase_discount_percent) }}">
                                          <span class="input-group-text bg-light">%</span>
                                      </div>
                                      @error('purchase_discount_percent')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="additional_discount_percent" class="form-label fw-bold">Add. Disc. %</label>
                                      <div class="input-group">
                                          <input type="number" name="additional_discount_percent" id="additional_discount_percent" class="form-control border-gold-focus @error('additional_discount_percent') is-invalid @enderror" min="0" max="100" step="0.01" value="{{ old('additional_discount_percent', $inventory->additional_discount_percent) }}">
                                          <span class="input-group-text bg-light">%</span>
                                      </div>
                                      @error('additional_discount_percent')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="additional_discount_amount" class="form-label fw-bold">Add. Disc. (Value)</label>
                                      <div class="input-group">
                                          <input type="number" name="additional_discount_amount" id="additional_discount_amount" class="form-control border-gold-focus @error('additional_discount_amount') is-invalid @enderror" min="0" step="0.01" value="{{ old('additional_discount_amount', $inventory->additional_discount_amount) }}">
                                          <span class="input-group-text bg-light">Rs.</span>
                                      </div>
                                      @error('additional_discount_amount')
                                      <div class="invalid-feedback d-block">{{ $message }}</div>
                                      @enderror
                                  </div>
                              </div>
                          </div>
                        </div>
                    </div>

                    <!-- PANEL 4: Stock & Quantity -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingStock">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStock" aria-expanded="false" aria-controls="collapseStock">
                            <i class="fas fa-cubes text-warning me-2"></i> Stock & Quantity
                          </button>
                        </h2>
                        <div id="collapseStock" class="accordion-collapse collapse" aria-labelledby="headingStock">
                          <div class="accordion-body">
                              <div class="row">
                                  <div class="col-md-3 mb-4">
                                      <label class="form-label fw-bold d-block">Manage Stock</label>
                                      <div class="form-check form-switch form-switch-gold ps-0 d-flex align-items-center mt-2">
                                          <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="manage_stock" name="manage_stock" value="1"
                                              {{ old('manage_stock', $inventory->manage_stock) ? 'checked' : '' }}>
                                          <label class="form-check-label text-muted" for="manage_stock">Yes</label>
                                      </div>
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="stock_status" class="form-label fw-bold">Stock Status</label>
                                      <select name="stock_status" id="stock_status" class="form-select border-gold-focus @error('stock_status') is-invalid @enderror">
                                          <option value="1" {{ old('stock_status', $inventory->stock_status ? '1' : '0') == '1' ? 'selected' : '' }}>In Stock</option>
                                          <option value="0" {{ old('stock_status', $inventory->stock_status ? '1' : '0') == '0' ? 'selected' : '' }}>Out of Stock</option>
                                      </select>
                                      @error('stock_status')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="quantity" class="form-label fw-bold">Stock (Quantity) <span class="text-danger">*</span></label>
                                      <input type="number" name="quantity" id="quantity" class="form-control border-gold-focus @error('quantity') is-invalid @enderror" min="0" value="{{ old('quantity', $inventory->quantity) }}" required>
                                      @error('quantity')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="min_quantity" class="form-label fw-bold">MBQ (Low Stock Threshold) <span class="text-danger">*</span></label>
                                      <input type="number" name="min_quantity" id="min_quantity" class="form-control border-gold-focus @error('min_quantity') is-invalid @enderror" min="0" value="{{ old('min_quantity', $inventory->min_quantity) }}" required>
                                      @error('min_quantity')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
                              </div>
                          </div>
                        </div>
                    </div>

                    <!-- PANEL 5: Measurement & Specifications -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingMeasurement">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMeasurement" aria-expanded="false" aria-controls="collapseMeasurement">
                            <i class="fas fa-balance-scale text-warning me-2"></i> Measurement & Specifications
                          </button>
                        </h2>
                        <div id="collapseMeasurement" class="accordion-collapse collapse" aria-labelledby="headingMeasurement">
                          <div class="accordion-body">
                              <div class="row">
                                  <div class="col-md-3 mb-4">
                                      <label for="size" class="form-label fw-bold">Size</label>
                                      <input type="text" name="size" id="size" class="form-control border-gold-focus @error('size') is-invalid @enderror" placeholder="e.g. Large, 250ml" value="{{ old('size', $inventory->size) }}">
                                      @error('size')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="unit" class="form-label fw-bold">Unit</label>
                                      <select name="unit" id="unit" class="form-select border-gold-focus @error('unit') is-invalid @enderror">
                                          <option value="">Select Unit (Optional)</option>
                                          <option value="Gram" {{ old('unit', $inventory->unit) == 'Gram' ? 'selected' : '' }}>Gram</option>
                                          <option value="Pack" {{ old('unit', $inventory->unit) == 'Pack' ? 'selected' : '' }}>Pack</option>
                                          <option value="ML" {{ old('unit', $inventory->unit) == 'ML' ? 'selected' : '' }}>ML</option>
                                          <option value="kg" {{ old('unit', $inventory->unit) == 'kg' ? 'selected' : '' }}>kg</option>
                                          <option value="grm" {{ old('unit', $inventory->unit) == 'grm' ? 'selected' : '' }}>grm</option>
                                      </select>
                                      @error('unit')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="unit_value" class="form-label fw-bold">Quantity Value (Size)</label>
                                      <input type="number" name="unit_value" id="unit_value" class="form-control border-gold-focus @error('unit_value') is-invalid @enderror" min="0" step="0.01" placeholder="e.g. 250, 500" value="{{ old('unit_value', $inventory->unit_value) }}">
                                      @error('unit_value')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>

                                  <div class="col-md-3 mb-4">
                                      <label for="weight" class="form-label fw-bold">Weight (kg)</label>
                                      <input type="number" name="weight" id="weight" class="form-control border-gold-focus @error('weight') is-invalid @enderror" min="0" step="0.01" placeholder="e.g. 0.25" value="{{ old('weight', $inventory->weight) }}">
                                      @error('weight')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
                              </div>
                          </div>
                        </div>
                    </div>

                    <!-- PANEL 6: Supplier Association -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSupplier">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSupplier" aria-expanded="false" aria-controls="collapseSupplier">
                            <i class="fas fa-truck-loading text-warning me-2"></i> Supplier Association
                          </button>
                        </h2>
                        <div id="collapseSupplier" class="accordion-collapse collapse" aria-labelledby="headingSupplier">
                          <div class="accordion-body">
                              <div class="row">
                                  <div class="col-12">
                                      <label for="vendor_id" class="form-label fw-bold">Supplier / Vendor (Company Name)</label>
                                      <select name="vendor_id" id="vendor_id" class="form-select border-gold-focus @error('vendor_id') is-invalid @enderror">
                                          <option value="">Select Supplier (Optional)</option>
                                          @foreach($vendors as $vendor)
                                              <option value="{{ $vendor->id }}" data-gst="{{ $vendor->tax_number }}" {{ old('vendor_id', $inventory->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                                  {{ $vendor->name }} @if($vendor->contact_name) ({{ $vendor->contact_name }}) @endif
                                              </option>
                                          @endforeach
                                      </select>
                                      <div id="vendor_gst_wrapper" class="mt-2" style="display: none;">
                                          <span class="text-muted small"><i class="fas fa-file-invoice me-1 text-warning"></i> Supplier GST No: <strong id="vendor_gst_display" class="text-dark"></strong></span>
                                      </div>
                                      @error('vendor_id')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
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
            // Supplier GST number mapping listener
            const vendorSelect = document.getElementById('vendor_id');
            const gstWrapper = document.getElementById('vendor_gst_wrapper');
            const gstDisplay = document.getElementById('vendor_gst_display');

            function updateVendorGst() {
                if (!vendorSelect) return;
                const selectedOption = vendorSelect.options[vendorSelect.selectedIndex];
                const gst = selectedOption ? selectedOption.getAttribute('data-gst') : null;
                
                if (gst) {
                    gstDisplay.textContent = gst;
                    gstWrapper.style.display = 'block';
                } else {
                    gstWrapper.style.display = 'none';
                    gstDisplay.textContent = '';
                }
            }

            if (vendorSelect) {
                vendorSelect.addEventListener('change', updateVendorGst);
                updateVendorGst();
            }

            // Auto-expand panels on validation errors
            @if ($errors->any())
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
