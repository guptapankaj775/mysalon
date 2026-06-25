<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    /**
     * Display a listing of the vendors.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage_vendors');

        $query = Vendor::withCount('inventories');

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('tax_number', 'like', "%{$search}%");
            });
        }

        // Apply Status Filter
        if ($request->filled('status')) {
            $statusVal = $request->input('status') === 'active';
            $query->where('status', $statusVal);
        }

        $vendors = $query->orderBy('name')->paginate(10);

        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        Gate::authorize('manage_vendors');

        return view('admin.vendors.create');
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage_vendors');

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'contact_name'  => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:50',
            'website'       => 'nullable|string|max:255',
            'tax_number'    => 'nullable|string|max:100',
            'payment_terms' => 'nullable|string|in:Immediate,COD,Net 15,Net 30,Net 60',
            'bank_name'     => 'nullable|string|max:255',
            'bank_account'  => 'nullable|string|max:100',
            'bank_code'     => 'nullable|string|max:50',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address'       => 'nullable|string',
            'description'   => 'nullable|string',
            'status'        => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status', true);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('vendors', 'public');
            $validated['logo_path'] = $path;
        }

        Vendor::create($validated);

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        Gate::authorize('manage_vendors');

        return view('admin.vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        Gate::authorize('manage_vendors');

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'contact_name'  => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:50',
            'website'       => 'nullable|string|max:255',
            'tax_number'    => 'nullable|string|max:100',
            'payment_terms' => 'nullable|string|in:Immediate,COD,Net 15,Net 30,Net 60',
            'bank_name'     => 'nullable|string|max:255',
            'bank_account'  => 'nullable|string|max:100',
            'bank_code'     => 'nullable|string|max:50',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address'       => 'nullable|string',
            'description'   => 'nullable|string',
            'status'        => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status', false);

        if ($request->hasFile('logo')) {
            // Delete old logo file if it exists
            if ($vendor->logo_path) {
                Storage::disk('public')->delete($vendor->logo_path);
            }
            $path = $request->file('logo')->store('vendors', 'public');
            $validated['logo_path'] = $path;
        }

        $vendor->update($validated);

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified vendor from storage.
     */
    public function destroy(Vendor $vendor)
    {
        Gate::authorize('manage_vendors');

        // Delete associated logo if exists
        if ($vendor->logo_path) {
            Storage::disk('public')->delete($vendor->logo_path);
        }

        $vendor->delete();

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
