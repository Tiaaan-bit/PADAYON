<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOns;
use Illuminate\Http\Request;

class AdminAddonsController extends Controller
{
    public function addons(Request $request)
    {
        $query = AddOns::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $addOns = $query
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.addons', compact('addOns'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        AddOns::create($validated);

        return redirect()->route('admin.addons')->with('success', 'Add-on added successfully.');
    }



    public function edit(AddOns $addOn)
    {
        return view('admin.addons', compact('addOn'));
    }

    public function update(Request $request, AddOns $addOn)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $addOn->update($validated);

        return redirect()->route('admin.addons')->with('success', 'Add-on updated successfully.');
    }

    public function destroy(AddOns $addOn)
    {
        $addOn->delete($addOn->id);

        return redirect()->route('admin.addons')->with('success', 'Add-on deleted successfully.');
    }
}