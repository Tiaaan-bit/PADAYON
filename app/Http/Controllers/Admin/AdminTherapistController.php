<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Therapist\CreateTherapist;
use App\Actions\Admin\Therapist\DeleteTherapist;
use App\Actions\Admin\Therapist\UpdateTherapist;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TherapistStoreRequest;
use App\Http\Requests\Admin\TherapistUpdateRequest;
use App\Models\Therapists;
use App\Repositories\Admin\Therapist\TherapistRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminTherapistController extends Controller
{
    public function index(Request $request, TherapistRepositoryInterface $therapists): View
    {
        $therapistList = $therapists->getAll($request->input('search'), $request->input('status'), $request->input('specialty'));

        return view('admin.therapist', [
            'therapists' => $therapistList,
        ]);
    }

    public function store(TherapistStoreRequest $request, CreateTherapist $createTherapist): RedirectResponse
    {
        Gate::authorize('create', Therapists::class);

        $createTherapist->execute($request->validated());

        return redirect()->route('admin.therapists')->with('success', 'Therapist account created successfully.');
    }

    public function update(TherapistUpdateRequest $request, Therapists $therapist, UpdateTherapist $updateTherapist): RedirectResponse
    {
        Gate::authorize('update', $therapist);

        $updateTherapist->execute($therapist, $request->validated());

        return redirect()->route('admin.therapists')->with('success', 'Therapist updated successfully.');
    }

    public function destroy(Therapists $therapist, DeleteTherapist $deleteTherapist): RedirectResponse
    {
        Gate::authorize('delete', $therapist);

        $deleteTherapist->execute($therapist);

        return redirect()->route('admin.therapists')->with('success', 'Therapist deleted successfully.');
    }
}
