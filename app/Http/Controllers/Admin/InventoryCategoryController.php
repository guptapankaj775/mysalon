<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;

class InventoryCategoryController extends Controller
{
    public function index()
    {
        $categories = InventoryCategory::orderBy('name')->paginate(10);
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

        InventoryCategory::create($validated);

        return redirect()->route('admin.inventory-categories.index')->with('success', 'Inventory category created successfully.');
    }

    public function edit(InventoryCategory $inventoryCategory)
    {
        return view('admin.inventory_categories.edit', compact('inventoryCategory'));
    }

    public function update(Request $request, InventoryCategory $inventoryCategory)
    {
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
        $inventoryCategory->delete();
        return redirect()->route('admin.inventory-categories.index')->with('success', 'Inventory category deleted successfully.');
    }
}
