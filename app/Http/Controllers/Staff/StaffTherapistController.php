<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Therapists;
use Illuminate\Http\Request;

class StaffTherapistController extends Controller
{
    public function index(Request $request)
    {
        $query = Therapists::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('specialty')) {
            $query->where('specialty', 'like', '%' . $request->specialty . '%');
        }

        $therapists = $query->latest()->paginate(6)->withQueryString();

        return view('staff.therapist', compact('therapists'));
    }

    public function update(Request $request, Therapists $therapist)
    {
        $validated = $request->validate([
            
            'status' => 'required|in:available,unavailable',
            
        ]);

        $therapist->update($validated);

        return redirect()->route('staff.therapists')->with('success', 'Therapist updated successfully.');
    }
}
