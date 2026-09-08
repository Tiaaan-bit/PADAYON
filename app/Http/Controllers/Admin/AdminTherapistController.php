<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Therapists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminTherapistController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Therapists::query();

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by specialty
        if ($request->filled('specialty')) {
            $query->where('specialty', 'like', '%' . $request->specialty . '%');
        }

        $therapists = $query->latest()->paginate(6)->withQueryString();

        return view('admin.therapist', compact('therapists'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:therapists,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'description' => ['nullable', 'string'],
            'specialty' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:available,unavailable'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        
        $validated['password'] = Hash::make($validated['password']);


        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('therapists', 'public');
        }

    
        Therapists::create($validated);

        return redirect()->route('admin.therapists')->with('success', 'Therapist account created successfully.');
    }


    public function update(Request $request, Therapists $therapist)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('therapists', 'email')->ignore($therapist->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'description' => ['nullable', 'string'],
            'specialty' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:available,unavailable'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

    
        if ($request->hasFile('image')) {
            // Delete old image
            if ($therapist->image) {
                Storage::disk('public')->delete($therapist->image);
            }

            // Save new image
            $validated['image'] = $request->file('image')->store('therapists', 'public');
        }

    
        $therapist->update($validated);

        return redirect()->route('admin.therapists')->with('success', 'Therapist updated successfully.');
    }


    public function destroy(Therapists $therapist)
    {
        
        if ($therapist->image) {
            Storage::disk('public')->delete($therapist->image);
        }

        $therapist->delete();

        return redirect()->route('admin.therapists')->with('success', 'Therapist deleted successfully.');
    }
}
