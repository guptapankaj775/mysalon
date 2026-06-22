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
        $query = Inventory::with('creator');

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
        return view('admin.inventory.create');
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name'    => 'required|string|max:255',
            'sku'          => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'quantity'     => 'required|integer|min:0',
            'price'        => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:0',
            'unit_value'   => 'nullable|numeric|min:0',
            'unit'         => 'nullable|string|in:Gram,Pack,ML,kg,grm',
        ]);

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
        return view('admin.inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'item_name'    => 'required|string|max:255',
            'sku'          => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'quantity'     => 'required|integer|min:0',
            'price'        => 'required|numeric|min:0',
            'min_quantity' => 'required|integer|min:0',
            'unit_value'   => 'nullable|numeric|min:0',
            'unit'         => 'nullable|string|in:Gram,Pack,ML,kg,grm',
        ]);

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
