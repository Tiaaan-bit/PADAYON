<?php

namespace App\Http\Controllers\Staff;

use App\Actions\Staff\Therapist\StaffUpdateTherapist;
use App\Http\Controllers\Controller;
use App\Models\Therapists;
use App\Repositories\Staff\Therapist\StaffTherapistRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StaffTherapistController extends Controller
{
    public function __construct(
        private readonly StaffTherapistRepositoryInterface $therapistRepository,
        private readonly StaffUpdateTherapist $updateTherapistStatusAction
    ) {}

    /**
     * Display the therapist list.
     */
    public function index(Request $request)
    {
        Gate::authorize('staff.therapist.viewAny');

        $therapists = $this->therapistRepository->getTherapists(
            search: $request->input('search'),
            status: $request->input('status'),
            specialty: $request->input('specialty')
        );

        $therapists->withQueryString();

        return view('staff.therapist', compact('therapists'));
    }

    /**
     * Update therapist availability/status.
     */
    public function update(
        Request $request,
        Therapists $therapist
    ) {
        Gate::authorize(
            'staff.therapist.update',
            $therapist
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:available,unavailable',
            ],
        ]);

        $this->updateTherapistStatusAction->execute(
            $therapist,
            $validated['status']
        );

        return redirect()
            ->route('staff.therapists')
            ->with(
                'success',
                'Therapist updated successfully.'
            );
    }
}