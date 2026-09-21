<?php

namespace App\Actions\Admin\Therapist;

use App\Models\Therapists;
use App\Repositories\Admin\Therapist\TherapistRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class DeleteTherapist
{
    public function __construct(protected TherapistRepositoryInterface $therapists) {}

    public function execute(Therapists $therapist): bool
    {
        if ($therapist->image) {
            Storage::disk('public')->delete($therapist->image);
        }

        return $this->therapists->delete($therapist);
    }
}
