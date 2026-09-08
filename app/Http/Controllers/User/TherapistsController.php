<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Therapists;
use App\Models\TherapistFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TherapistsController extends Controller
{
    public function index()
    {
        $therapists = Therapists::where('status', 'available')
            ->withCount('feedbacks')
            ->orderByDesc('feedbacks_count')
            ->paginate(9);

        return view('user.therapist', compact('therapists'));
    }

    public function storeFeedback(Request $request, Therapists $therapist)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existingFeedback = TherapistFeedback::where('user_id', Auth::id())
            ->where('therapist_id', $therapist->id)
            ->first();

        if ($existingFeedback) {
            return back()->withErrors(['error' => 'You have already submitted feedback for this therapist.']);
        }

        $hasAppointment = \App\Models\UsersAppointments::where('user_id', Auth::id())
            ->where('therapist_id', $therapist->id)
            ->where('status', 'confirm')
            ->exists();

        if (!$hasAppointment) {
            return back()->withErrors(['error' => 'You can only leave feedback after having an appointment with this therapist.']);
        }

        TherapistFeedback::create([
            'user_id' => Auth::id(),
            'therapist_id' => $therapist->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('user.therapists.index')
            ->with('success', 'Thank you for your feedback!');
    }
}