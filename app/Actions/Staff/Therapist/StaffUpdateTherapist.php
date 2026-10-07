<?php

namespace App\Actions\Staff\Therapist;

use App\Models\Therapists;

class StaffUpdateTherapist
{
    public function execute(Therapists $therapist, string $status): Therapists
    {
        $therapist->update([
            'status' => $status,
        ]);

        return $therapist->refresh();
    }
}
