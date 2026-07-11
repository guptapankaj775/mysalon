<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $query = ServiceCategory::withCount('services');
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        $categories = $query->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'boolean'
        ]);

        $validated['user_id'] = auth()->id();
        $category = ServiceCategory::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Category created successfully'
            ]);
        }

        return redirect()->route('admin.categories')->with('success', 'Category created successfully');
    }

    public function edit(ServiceCategory $category)
    {
        if (!auth()->user()->isAdmin() && $category->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this category.');
        }
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, ServiceCategory $category)
    {
        if (!auth()->user()->isAdmin() && $category->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this category.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'boolean'
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully');
    }

    public function destroy(ServiceCategory $category)
    {
        if (!auth()->user()->isAdmin() && $category->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this category.');
        }

        $category->delete();
        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully');
    }
}
