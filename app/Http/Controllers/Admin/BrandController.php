<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $query = Brand::query();
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        $brands = $query->orderBy('name')->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'status'      => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');
        $validated['user_id'] = auth()->id();

        Brand::create($validated);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        if (!auth()->user()->isAdmin() && $brand->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this brand.');
        }
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        if (!auth()->user()->isAdmin() && $brand->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this brand.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'status'      => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');

        $brand->update($validated);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if (!auth()->user()->isAdmin() && $brand->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this brand.');
        }

        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}
