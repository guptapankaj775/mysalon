<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventory items.
     */
    public function index(Request $request)
    {
        $query = Inventory::with(['creator', 'vendor', 'brand', 'category']);

        // Apply Search Filter (Item Name or SKU)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Apply Creator Filter
        if ($request->filled('creator_id')) {
            $query->where('user_id', $request->creator_id);
        }

        // Statistics calculation before pagination
        $stats = [
            'total_items' => (clone $query)->count(),
            'total_quantity' => (clone $query)->sum('quantity'),
            'total_value' => (clone $query)->sum(DB::raw('quantity * price')),
            'low_stock_count' => (clone $query)->whereColumn('quantity', '<=', 'min_quantity')->count(),
        ];

        // Paginated results
        $inventories = $query->orderBy('created_at', 'desc')->paginate(10);

        // Fetch all users for the filter dropdown
        $users = User::orderBy('name')->get();

        return view('admin.inventory.index', compact('inventories', 'users', 'stats'));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        $vendors = \App\Models\Vendor::where('status', true)->orderBy('name')->get();
        $brands = \App\Models\Brand::where('status', true)->orderBy('name')->get();
        $categories = \App\Models\InventoryCategory::where('status', true)->orderBy('name')->get();
        return view('admin.inventory.create', compact('vendors', 'brands', 'categories'));
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name'                   => 'required|string|max:255',
            'status'                      => 'boolean',
            'brand_id'                    => 'nullable|exists:brands,id',
            'inventory_category_id'       => 'nullable|exists:inventory_categories,id',
            'division'                    => 'nullable|string|max:255',
            'sku'                         => 'nullable|string|max:100',
            'hsn_code'                    => 'nullable|string|max:100',
            'description'                 => 'nullable|string',
            'quantity'                    => 'required|integer|min:0',
            'stock_status'                => 'boolean',
            'manage_stock'                => 'boolean',
            'price'                       => 'required|numeric|min:0',
            'mrp'                         => 'nullable|numeric|min:0',
            'discount_percent'            => 'nullable|numeric|min:0|max:100',
            'cost'                        => 'nullable|numeric|min:0',
            'purchase_discount_percent'   => 'nullable|numeric|min:0|max:100',
            'additional_discount_percent' => 'nullable|numeric|min:0|max:100',
            'additional_discount_amount'  => 'nullable|numeric|min:0',
            'taxable_amount'              => 'nullable|numeric|min:0',
            'special_price'               => 'nullable|numeric|min:0',
            'tax_class'                   => 'nullable|string|in:None,Taxable Goods',
            'gst_percent'                 => 'nullable|numeric|min:0|max:100',
            'gst_input'                   => 'nullable|numeric|min:0',
            'gst_output'                  => 'nullable|numeric|min:0',
            'min_quantity'                => 'required|integer|min:0',
            'unit_value'                  => 'nullable|numeric|min:0',
            'size'                        => 'nullable|string|max:255',
            'weight'                      => 'nullable|numeric|min:0',
            'unit'                        => 'nullable|string|in:Gram,Pack,ML,kg,grm',
            'vendor_id'                   => 'nullable|exists:vendors,id',
        ]);

        $validated['status'] = $request->boolean('status');
        $validated['manage_stock'] = $request->boolean('manage_stock');
        $validated['stock_status'] = $request->boolean('stock_status');

        // Automatically set creator user_id to currently logged-in user
        $validated['user_id'] = Auth::id();

        Inventory::create($validated);

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(Inventory $inventory)
    {
        $vendors = \App\Models\Vendor::where('status', true)->orderBy('name')->get();
        $brands = \App\Models\Brand::where('status', true)->orderBy('name')->get();
        $categories = \App\Models\InventoryCategory::where('status', true)->orderBy('name')->get();
        return view('admin.inventory.edit', compact('inventory', 'vendors', 'brands', 'categories'));
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'item_name'                   => 'required|string|max:255',
            'status'                      => 'boolean',
            'brand_id'                    => 'nullable|exists:brands,id',
            'inventory_category_id'       => 'nullable|exists:inventory_categories,id',
            'division'                    => 'nullable|string|max:255',
            'sku'                         => 'nullable|string|max:100',
            'hsn_code'                    => 'nullable|string|max:100',
            'description'                 => 'nullable|string',
            'quantity'                    => 'required|integer|min:0',
            'stock_status'                => 'boolean',
            'manage_stock'                => 'boolean',
            'price'                       => 'required|numeric|min:0',
            'mrp'                         => 'nullable|numeric|min:0',
            'discount_percent'            => 'nullable|numeric|min:0|max:100',
            'cost'                        => 'nullable|numeric|min:0',
            'purchase_discount_percent'   => 'nullable|numeric|min:0|max:100',
            'additional_discount_percent' => 'nullable|numeric|min:0|max:100',
            'additional_discount_amount'  => 'nullable|numeric|min:0',
            'taxable_amount'              => 'nullable|numeric|min:0',
            'special_price'               => 'nullable|numeric|min:0',
            'tax_class'                   => 'nullable|string|in:None,Taxable Goods',
            'gst_percent'                 => 'nullable|numeric|min:0|max:100',
            'gst_input'                   => 'nullable|numeric|min:0',
            'gst_output'                  => 'nullable|numeric|min:0',
            'min_quantity'                => 'required|integer|min:0',
            'unit_value'                  => 'nullable|numeric|min:0',
            'size'                        => 'nullable|string|max:255',
            'weight'                      => 'nullable|numeric|min:0',
            'unit'                        => 'nullable|string|in:Gram,Pack,ML,kg,grm',
            'vendor_id'                   => 'nullable|exists:vendors,id',
        ]);

        $validated['status'] = $request->boolean('status');
        $validated['manage_stock'] = $request->boolean('manage_stock');
        $validated['stock_status'] = $request->boolean('stock_status');

        $inventory->update($validated);

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified inventory item from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }
}
