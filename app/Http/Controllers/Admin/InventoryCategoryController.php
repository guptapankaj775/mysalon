<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;

class InventoryCategoryController extends Controller
{
    public function index()
    {
        $query = InventoryCategory::query();
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        $categories = $query->orderBy('name')->paginate(10);
        return view('admin.inventory_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.inventory_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:inventory_categories,name',
            'description' => 'nullable|string',
            'status'      => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');
        $validated['user_id'] = auth()->id();

        InventoryCategory::create($validated);

        return redirect()->route('admin.inventory-categories.index')->with('success', 'Inventory category created successfully.');
    }

    public function edit(InventoryCategory $inventoryCategory)
    {
        if (!auth()->user()->isAdmin() && $inventoryCategory->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this category.');
        }
        return view('admin.inventory_categories.edit', compact('inventoryCategory'));
    }

    public function update(Request $request, InventoryCategory $inventoryCategory)
    {
        if (!auth()->user()->isAdmin() && $inventoryCategory->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this category.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:inventory_categories,name,' . $inventoryCategory->id,
            'description' => 'nullable|string',
            'status'      => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');

        $inventoryCategory->update($validated);

        return redirect()->route('admin.inventory-categories.index')->with('success', 'Inventory category updated successfully.');
    }

    public function destroy(InventoryCategory $inventoryCategory)
    {
        if (!auth()->user()->isAdmin() && $inventoryCategory->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this category.');
        }

        $inventoryCategory->delete();
        return redirect()->route('admin.inventory-categories.index')->with('success', 'Inventory category deleted successfully.');
    }
}
