@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold">POS Sales History</h1>
            <p class="text-muted mb-0">Review all Point of Sale transactions and invoices.</p>
        </div>
        <a href="{{ $salon ? route('admin.pos', ['salon' => $salon->slug]) : route('admin.pos') }}" class="btn btn-warning btn-sm text-dark font-semibold d-flex align-items-center gap-1 shadow-sm">
            <i class="fas fa-cash-register"></i> Back to POS Console
        </a>
    </div>

    <!-- Filters -->
    @if(Auth::user()->isAdmin() && count($salons) > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ $salon ? route('admin.pos.history', ['salon' => $salon->slug]) : route('admin.pos.history') }}" class="row g-3 align-items-center">
                <div class="col-md-4 d-flex align-items-center gap-2">
                    <label for="salon_id" class="text-nowrap font-medium text-sm text-gray-700 mb-0">Filter by Salon:</label>
                    <select name="salon_id" id="salon_id" onchange="this.form.submit()" class="form-select form-select-sm border-gray-300 rounded shadow-sm">
                        <option value="">All Salons</option>
                        @foreach($salons as $s)
                            <option value="{{ $s->id }}" {{ request('salon_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->salon_name }} ({{ $s->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Sales Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            @if($sales->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-3x text-muted opacity-50 mb-3"></i>
                    <h5 class="font-semibold text-gray-600 mb-1">No Transactions Found</h5>
                    <p class="text-muted text-xs">Try processing some orders in the POS console.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-gray-700 text-xs font-semibold uppercase border-bottom border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Invoice No</th>
                                <th class="px-4 py-3">Date & Time</th>
                                @if(Auth::user()->isAdmin())
                                    <th class="px-4 py-3">Salon</th>
                                @endif
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Items Summary</th>
                                <th class="px-4 py-3 text-end">Subtotal</th>
                                <th class="px-4 py-3 text-end">Discount</th>
                                <th class="px-4 py-3 text-end">Tax (GST)</th>
                                <th class="px-4 py-3 text-end">Grand Total</th>
                                <th class="px-4 py-3 text-center">Payment</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-800 divide-y divide-gray-100">
                            @foreach($sales as $sale)
                                <tr>
                                    <td class="px-4 py-3.5 font-semibold text-gray-900">
                                        <code>{{ $sale->invoice_number }}</code>
                                    </td>
                                    <td class="px-4 py-3.5 text-muted">
                                        {{ $sale->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    @if(Auth::user()->isAdmin())
                                        <td class="px-4 py-3.5 font-semibold">
                                            {{ $sale->user ? $sale->user->salon_name : 'Global Admin' }}
                                        </td>
                                    @endif
                                    <td class="px-4 py-3.5">
                                        <div class="font-medium text-gray-900">{{ $sale->customer_name }}</div>
                                        @if($sale->customer_phone)
                                            <div class="text-xs text-muted"><i class="fas fa-phone-alt me-1"></i> {{ $sale->customer_phone }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-gray-600" style="max-width: 250px;">
                                        @php
                                            $itemNames = [];
                                            foreach($sale->items as $item) {
                                                $itemNames[] = $item['name'] . ' (x' . $item['quantity'] . ')';
                                            }
                                            $itemsStr = implode(', ', $itemNames);
                                        @endphp
                                        <span class="d-inline-block text-truncate w-100" title="{{ $itemsStr }}">
                                            {{ $itemsStr }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-end text-muted">
                                        Rs. {{ number_format($sale->subtotal, 2) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-end text-danger">
                                        Rs. {{ number_format($sale->discount, 2) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-end text-muted">
                                        Rs. {{ number_format($sale->tax, 2) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-end font-bold text-success">
                                        Rs. {{ number_format($sale->total, 2) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="mb-1">
                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle text-2xs px-2 py-0.5 rounded-pill font-semibold">
                                                {{ $sale->payment_method }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="badge {{ $sale->payment_status === 'Paid' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} text-2xs px-2 py-0.5 rounded-pill font-semibold">
                                                {{ $sale->payment_status }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <a href="{{ $salon ? route('admin.pos.invoice', ['salon' => $salon->slug, 'id' => $sale->id]) : route('admin.pos.invoice', $sale->id) }}" target="_blank" class="btn btn-outline-warning btn-xs py-1 px-2 text-dark font-medium d-inline-flex align-items-center gap-1 rounded shadow-xs">
                                            <i class="fas fa-print"></i> Invoice
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 border-top border-gray-100">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
