@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold">Point of Sale (POS)</h1>
            <p class="text-muted mb-0">Select services and products to checkout customers.</p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->isAdmin() && count($salons) > 0)
                <form method="GET" action="{{ $salon ? route('admin.pos', ['salon' => $salon->slug]) : route('admin.pos') }}" class="d-flex align-items-center gap-2">
                    <label for="salon_id" class="text-nowrap font-medium text-sm text-gray-700">Active Salon:</label>
                    <select name="salon_id" id="salon_id" onchange="this.form.submit()" class="form-select form-select-sm border-gray-300 rounded shadow-sm">
                        @foreach($salons as $s)
                            <option value="{{ $s->id }}" {{ $ownerId == $s->id ? 'selected' : '' }}>
                                {{ $s->salon_name }} ({{ $s->name }})
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
            <a href="{{ $salon ? route('admin.pos.history', ['salon' => $salon->slug]) : route('admin.pos.history') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="fas fa-history"></i> Sales History
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="row">
        <!-- Catalog Column -->
        <div class="col-lg-7 col-xl-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <!-- Search and Tabs -->
                        <div class="input-group input-group-sm max-w-xs">
                            <span class="input-group-text bg-light border-gray-300"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="search-input" placeholder="Search catalog..." class="form-control border-gray-300 bg-light">
                        </div>
                        <div class="nav nav-pills" id="catalog-tabs" role="tablist">
                            <button class="nav-link active btn-sm py-1.5 px-3 rounded-pill me-2" id="services-tab" data-bs-toggle="pill" data-bs-target="#services-pane" type="button" role="tab">
                                <i class="fas fa-cut me-1"></i> Services
                            </button>
                            <button class="nav-link btn-sm py-1.5 px-3 rounded-pill" id="products-tab" data-bs-toggle="pill" data-bs-target="#products-pane" type="button" role="tab">
                                <i class="fas fa-boxes me-1"></i> Products / Inventory
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0" style="max-height: 650px; overflow-y: auto;">
                    <div class="tab-content" id="catalog-tab-content">
                        <!-- Services Pane -->
                        <div class="tab-pane fade show active" id="services-pane" role="tabpanel">
                            @if($services->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-cut fa-3x text-muted opacity-50 mb-3"></i>
                                    <p class="text-gray-500 mb-0">No active services found for this salon.</p>
                                </div>
                            @else
                                <div class="row g-3 mt-1" id="services-grid">
                                    @foreach($services as $service)
                                        <div class="col-md-6 col-xl-4 catalog-item" data-name="{{ strtolower($service->name) }}">
                                            <div class="card h-100 border border-gray-100 shadow-xs hover-shadow transition rounded-3">
                                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5 rounded text-xs font-semibold">Service</span>
                                                            <span class="text-muted text-xs"><i class="far fa-clock me-1"></i> {{ $service->duration }} mins</span>
                                                        </div>
                                                        <h5 class="card-title font-semibold text-sm mb-1 text-gray-900">{{ $service->name }}</h5>
                                                        <p class="card-text text-muted text-xs line-clamp-2 mb-3">{{ $service->description }}</p>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-gray-50">
                                                        <span class="font-bold text-gray-900 text-sm">Rs. {{ number_format($service->price, 2) }}</span>
                                                        <button type="button" onclick="addToCart({{ $service->id }}, 'service', '{{ addslashes($service->name) }}', {{ $service->price }}, 3.0)" class="btn btn-warning btn-xs py-1 px-2.5 rounded-pill font-medium text-xs text-dark shadow-sm">
                                                            <i class="fas fa-plus"></i> Add
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Products Pane -->
                        <div class="tab-pane fade" id="products-pane" role="tabpanel">
                            @if($inventories->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-boxes fa-3x text-muted opacity-50 mb-3"></i>
                                    <p class="text-gray-500 mb-0">No active products or stock found.</p>
                                </div>
                            @else
                                <div class="row g-3 mt-1" id="products-grid">
                                    @foreach($inventories as $product)
                                        @php
                                            $price = $product->price ?: $product->mrp;
                                            $outOfStock = $product->manage_stock && $product->quantity <= 0;
                                        @endphp
                                        <div class="col-md-6 col-xl-4 catalog-item" data-name="{{ strtolower($product->item_name) }}">
                                            <div class="card h-100 border border-gray-100 shadow-xs hover-shadow transition rounded-3 {{ $outOfStock ? 'opacity-75' : '' }}">
                                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded text-xs font-semibold">Product</span>
                                                            <span class="text-xs {{ $outOfStock ? 'text-danger font-bold' : ($product->quantity <= $product->min_quantity ? 'text-warning font-semibold' : 'text-success') }}">
                                                                Stock: {{ $product->manage_stock ? $product->quantity : 'Unlimited' }}
                                                            </span>
                                                        </div>
                                                        <h5 class="card-title font-semibold text-sm mb-1 text-gray-900">{{ $product->item_name }}</h5>
                                                        <p class="card-text text-muted text-xs mb-1">SKU: {{ $product->sku ?: 'N/A' }}</p>
                                                        @if($product->brand)
                                                            <p class="card-text text-muted text-xs mb-3">Brand: {{ $product->brand->name }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-gray-50">
                                                        <span class="font-bold text-gray-900 text-sm">Rs. {{ number_format($price, 2) }}</span>
                                                        @if($outOfStock)
                                                            <button type="button" class="btn btn-secondary btn-xs py-1 px-2.5 rounded-pill font-medium text-xs text-white" disabled>
                                                                Out of Stock
                                                            </button>
                                                        @else
                                                            <button type="button" onclick="addToCart({{ $product->id }}, 'product', '{{ addslashes($product->item_name) }}', {{ $price }}, {{ $product->gst_percent ?: 3.0 }}, {{ $product->quantity }}, {{ $product->manage_stock ? 1 : 0 }})" class="btn btn-success btn-xs py-1 px-2.5 rounded-pill font-medium text-xs text-white shadow-sm">
                                                                <i class="fas fa-plus"></i> Add
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout / Cart Column -->
        <div class="col-lg-5 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 border-bottom border-gray-100">
                    <h5 class="card-title font-bold text-base mb-0 text-gray-900 d-flex align-items-center gap-2">
                        <i class="fas fa-shopping-cart text-warning"></i> Customer & Cart
                    </h5>
                </div>

                <div class="card-body d-flex flex-column justify-content-between">
                    <!-- Customer Details Forms -->
                    <div class="mb-4">
                        <div class="p-3 bg-light rounded-3 mb-3 border border-gray-200">
                            <h6 class="font-semibold text-xs text-gray-700 uppercase tracking-wider mb-3">Customer Details</h6>
                            <div class="mb-2.5">
                                <label for="customer_name" class="form-label text-xs font-semibold text-gray-600 mb-1">Full Name <span class="text-danger">*</span></label>
                                <input type="text" id="customer_name" class="form-control form-control-sm border-gray-300" placeholder="Walk-in Customer" required value="Walk-in Customer">
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="customer_phone" class="form-label text-xs font-semibold text-gray-600 mb-1">Mobile No</label>
                                    <input type="text" id="customer_phone" class="form-control form-control-sm border-gray-300" placeholder="e.g. 9876543210">
                                </div>
                                <div class="col-6">
                                    <label for="customer_email" class="form-label text-xs font-semibold text-gray-600 mb-1">Email ID</label>
                                    <input type="email" id="customer_email" class="form-control form-control-sm border-gray-300" placeholder="e.g. guest@mail.com">
                                </div>
                            </div>
                        </div>

                        <!-- Cart Items List -->
                        <h6 class="font-semibold text-xs text-gray-700 uppercase tracking-wider mb-2">Cart Items</h6>
                        <div id="cart-list" class="overflow-y-auto mb-3" style="max-height: 250px;">
                            <!-- Appended dynamically -->
                            <div class="text-center py-4 text-muted text-xs empty-cart-msg">
                                <i class="fas fa-shopping-basket fa-2x mb-2 opacity-50"></i>
                                <p class="mb-0">Cart is empty. Add items from the catalog.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt Summary -->
                    <div class="pt-3 border-top border-gray-200">
                        <div class="d-flex justify-content-between text-xs text-muted mb-1.5">
                            <span>Subtotal:</span>
                            <span id="summary-subtotal">Rs. 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between text-xs text-muted mb-1.5">
                            <span>Taxes (GST):</span>
                            <span id="summary-tax">Rs. 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-xs text-muted mb-2">
                            <span>Discount (Rs.):</span>
                            <div style="width: 100px;">
                                <input type="number" id="discount-input" min="0" step="1" value="0" class="form-control form-control-sm border-gray-300 py-0.5 text-end text-xs" oninput="recalculateSummary()">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center font-bold text-gray-900 border-top border-gray-200 pt-2 mb-3">
                            <span class="text-base">Grand Total:</span>
                            <span class="text-lg text-success" id="summary-total">Rs. 0.00</span>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label class="form-label text-xs font-semibold text-gray-600 mb-2">Payment Method</label>
                            <div class="row g-2" id="payment-methods">
                                <div class="col-3">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_cash" value="Cash" checked>
                                    <label class="btn btn-outline-warning w-100 py-2 btn-sm rounded-3 text-xs font-semibold d-flex flex-column align-items-center gap-1 text-dark" for="pay_cash">
                                        <i class="fas fa-money-bill-wave"></i> Cash
                                    </label>
                                </div>
                                <div class="col-3">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_card" value="Card">
                                    <label class="btn btn-outline-warning w-100 py-2 btn-sm rounded-3 text-xs font-semibold d-flex flex-column align-items-center gap-1 text-dark" for="pay_card">
                                        <i class="far fa-credit-card"></i> Card
                                    </label>
                                </div>
                                <div class="col-3">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_upi" value="UPI">
                                    <label class="btn btn-outline-warning w-100 py-2 btn-sm rounded-3 text-xs font-semibold d-flex flex-column align-items-center gap-1 text-dark" for="pay_upi">
                                        <i class="fas fa-qrcode"></i> UPI
                                    </label>
                                </div>
                                <div class="col-3">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_later" value="Pay Later">
                                    <label class="btn btn-outline-warning w-100 py-2 btn-sm rounded-3 text-xs font-semibold d-flex flex-column align-items-center gap-1 text-dark" for="pay_later">
                                        <i class="fas fa-clock"></i> Later
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Action -->
                        <button type="button" id="btn-checkout" onclick="processCheckout()" class="btn btn-warning w-100 py-2.5 rounded-3 font-bold text-sm text-dark shadow d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-cash-register"></i> Complete Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-shadow:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    .transition {
        transition: all 0.2s ease-in-out;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
    .catalog-item {
        transition: opacity 0.2s;
    }
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    .nav-pills .nav-link.active {
        background-color: #D4AF37 !important;
        color: #1a1a1a !important;
        font-weight: 600;
    }
    .nav-pills .nav-link {
        color: #4a5568;
        font-weight: 500;
        border: 1px solid #e2e8f0;
    }
    .btn-check:checked + .btn-outline-warning {
        background-color: #D4AF37 !important;
        border-color: #D4AF37 !important;
        color: #1a1a1a !important;
        box-shadow: 0 0 8px rgba(212, 175, 55, 0.3);
    }
    .btn-outline-warning {
        border-color: #e2e8f0;
        color: #4a5568 !important;
    }
    .btn-outline-warning:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
    }
</style>
@endpush

@push('scripts')
<script>
    let cart = [];

    // Search filter logic
    document.getElementById('search-input').addEventListener('input', function(e) {
        let query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.catalog-item').forEach(item => {
            let name = item.getAttribute('data-name');
            if (name.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    function addToCart(id, type, name, price, taxPercent, maxStock = null, manageStock = 0) {
        let existingItem = cart.find(item => item.id === id && item.type === type);

        if (existingItem) {
            if (manageStock && existingItem.quantity >= maxStock) {
                alert(`Cannot add more. Only ${maxStock} items available in inventory.`);
                return;
            }
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: id,
                type: type,
                name: name,
                price: price,
                taxPercent: taxPercent,
                quantity: 1,
                maxStock: maxStock,
                manageStock: manageStock
            });
        }

        renderCart();
    }

    function updateQuantity(id, type, delta) {
        let item = cart.find(item => item.id === id && item.type === type);
        if (item) {
            if (delta > 0 && item.manageStock && item.quantity >= item.maxStock) {
                alert(`Cannot add more. Only ${item.maxStock} items available in inventory.`);
                return;
            }
            item.quantity += delta;
            if (item.quantity <= 0) {
                removeFromCart(id, type);
                return;
            }
        }
        renderCart();
    }

    function removeFromCart(id, type) {
        cart = cart.filter(item => !(item.id === id && item.type === type));
        renderCart();
    }

    function renderCart() {
        const cartList = document.getElementById('cart-list');
        const emptyMsg = cartList.querySelector('.empty-cart-msg');
        
        // Remove old rows
        cartList.querySelectorAll('.cart-row').forEach(row => row.remove());

        if (cart.length === 0) {
            if (emptyMsg) emptyMsg.style.display = 'block';
            recalculateSummary();
            return;
        }

        if (emptyMsg) emptyMsg.style.display = 'none';

        cart.forEach(item => {
            const row = document.createElement('div');
            row.className = 'cart-row d-flex justify-content-between align-items-center p-2 mb-2 bg-white border border-gray-150 rounded shadow-xs';
            row.innerHTML = `
                <div class="flex-grow-1 min-w-0 me-2">
                    <span class="badge ${item.type === 'service' ? 'bg-warning-subtle text-warning border-warning-subtle' : 'bg-success-subtle text-success border-success-subtle'} px-1 py-0.5 rounded text-2xs font-semibold me-1">${item.type === 'service' ? 'S' : 'P'}</span>
                    <span class="text-xs font-semibold text-gray-800 text-truncate d-inline-block align-middle" style="max-width: 140px;" title="${item.name}">${item.name}</span>
                    <div class="text-2xs text-muted">Rs. ${item.price.toFixed(2)} each</div>
                </div>
                <div class="d-flex align-items-center gap-1.5">
                    <div class="input-group input-group-sm" style="width: 80px;">
                        <button type="button" class="btn btn-outline-secondary py-0 px-1.5 text-xs" onclick="updateQuantity(${item.id}, '${item.type}', -1)">-</button>
                        <span class="form-control form-control-sm text-center py-0 px-1 text-xs bg-light font-semibold">${item.quantity}</span>
                        <button type="button" class="btn btn-outline-secondary py-0 px-1.5 text-xs" onclick="updateQuantity(${item.id}, '${item.type}', 1)">+</button>
                    </div>
                    <button type="button" onclick="removeFromCart(${item.id}, '${item.type}')" class="btn btn-outline-danger btn-xs border-0 text-danger p-1">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;
            cartList.appendChild(row);
        });

        recalculateSummary();
    }

    function recalculateSummary() {
        let subtotal = 0;
        let tax = 0;

        cart.forEach(item => {
            let itemSubtotal = item.price * item.quantity;
            let itemTax = itemSubtotal * (item.taxPercent / 100);
            subtotal += itemSubtotal;
            tax += itemTax;
        });

        let discount = parseFloat(document.getElementById('discount-input').value) || 0;
        if (discount < 0) discount = 0;

        let total = (subtotal + tax) - discount;
        if (total < 0) total = 0;

        document.getElementById('summary-subtotal').innerText = `Rs. ${subtotal.toFixed(2)}`;
        document.getElementById('summary-tax').innerText = `Rs. ${tax.toFixed(2)}`;
        document.getElementById('summary-total').innerText = `Rs. ${total.toFixed(2)}`;
    }

    function processCheckout() {
        if (cart.length === 0) {
            alert('Your cart is empty. Add services or products before completing checkout.');
            return;
        }

        const name = document.getElementById('customer_name').value.trim();
        if (!name) {
            alert('Customer Name is required.');
            return;
        }

        const phone = document.getElementById('customer_phone').value.trim();
        const email = document.getElementById('customer_email').value.trim();
        const discount = parseFloat(document.getElementById('discount-input').value) || 0;
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const salonIdSelect = document.getElementById('salon_id');
        const salonId = salonIdSelect ? salonIdSelect.value : null;

        const btnCheckout = document.getElementById('btn-checkout');
        btnCheckout.disabled = true;
        btnCheckout.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        fetch("{{ route('admin.pos.checkout') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                customer_name: name,
                customer_phone: phone,
                customer_email: email,
                cart_items: cart,
                discount: discount,
                payment_method: paymentMethod,
                salon_id: salonId
            })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            btnCheckout.disabled = false;
            btnCheckout.innerHTML = '<i class="fas fa-cash-register"></i> Complete Checkout';

            if (res.status === 200 && res.body.success) {
                // Clear cart
                cart = [];
                renderCart();
                document.getElementById('customer_name').value = 'Walk-in Customer';
                document.getElementById('customer_phone').value = '';
                document.getElementById('customer_email').value = '';
                document.getElementById('discount-input').value = 0;
                
                alert('Sale processed successfully!');
                
                // Open invoice in new window/tab for printing
                window.open(res.body.invoice_url, '_blank');
            } else {
                alert('Checkout failed: ' + (res.body.message || 'Unknown error'));
            }
        })
        .catch(err => {
            btnCheckout.disabled = false;
            btnCheckout.innerHTML = '<i class="fas fa-cash-register"></i> Complete Checkout';
            alert('Checkout error: ' + err.message);
        });
    }
</script>
@endpush
@endsection
