<?php

namespace App\Http\Controllers\User;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\TherapistFeedback;
use App\Models\Therapists;
use App\Models\UsersAppointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TherapistsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 4 therapists per page
        $therapists = Therapists::query()
            ->where('status', 'available')
            ->withCount('feedbacks')
            ->withAvg('feedbacks', 'rating')
            ->orderByDesc('feedbacks_count')
            ->orderBy('name')
            ->paginate(4, ['*'], 'therapists_page');

        $userFeedbacks = TherapistFeedback::query()->where('user_id', $userId)->whereIn('therapist_id', $therapists->pluck('id'))->get()->keyBy('therapist_id');

        $confirmedTherapistIds = UsersAppointments::query()->where('user_id', $userId)->where('status', AppointmentStatus::CONFIRMED)->whereIn('therapist_id', $therapists->pluck('id'))->pluck('therapist_id')->unique();

        // 5 reviews per page
        $allFeedbacks = TherapistFeedback::query()
            ->with(['user', 'therapist'])
            ->latest()
            ->paginate(5, ['*'], 'reviews_page');

        return view('user.therapist', compact('therapists', 'userFeedbacks', 'confirmedTherapistIds', 'allFeedbacks'));
    }

    public function storeFeedback(Request $request, Therapists $therapist)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if user already reviewed this therapist
        $existingFeedback = TherapistFeedback::query()->where('user_id', Auth::id())->where('therapist_id', $therapist->id)->exists();

        if ($existingFeedback) {
            return back()->withErrors([
                'error' => 'You have already submitted feedback for this therapist.',
            ]);
        }

        // Check if user has a confirmed appointment
        $hasAppointment = UsersAppointments::query()->where('user_id', Auth::id())->where('therapist_id', $therapist->id)->where('status', AppointmentStatus::CONFIRMED)->exists();

        if (!$hasAppointment) {
            return back()->withErrors([
                'error' => 'You can only leave feedback after having an appointment with this therapist.',
            ]);
        }

        // Create feedback
        TherapistFeedback::create([
            'user_id' => Auth::id(),
            'therapist_id' => $therapist->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('user.therapists.index')->with('success', 'Thank you for your feedback!');
    }
}
