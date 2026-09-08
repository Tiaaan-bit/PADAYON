<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Services;
use Illuminate\Http\Request;

class AdminServicesController extends Controller
{
    public function services(Request $request)
    {
        $query = Services::query();
    
        if ($request->filled('search')) {
            $search = $request->search;
    
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('duration_minutes', 'like', "%{$search}%");
            });
        }
    
        // Total active services
        $totalActiveServices = Services::where('status', 'active')->count();
    
        // Total inactive services
        $totalInactiveServices = Services::where('status', 'inactive')->count();
    
        $services = $query
            ->latest()
            ->paginate(5)
            ->withQueryString();
    
        return view('admin.services', compact(
            'services',
            'totalActiveServices',
            'totalInactiveServices'
        ));
    }

    public function create() {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        Services::create($validated);

        return redirect()->route('admin.services')->with('success', 'Service added successfully.');
    }

    public function edit(Services $service)
    {
        $services = Services::latest()->paginate(5)->withQueryString();

        return view('admin.services', compact('services', 'service'));
    }

    public function update(Request $request, Services $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $service->update($validated);

        return redirect()->route('admin.services')->with('success', 'Service updated successfully.');
    }

    public function destroy(Services $service)
    {
        $service->delete();

        return redirect()->route('admin.services')->with('success', 'Service deleted successfully.');
    }
}
