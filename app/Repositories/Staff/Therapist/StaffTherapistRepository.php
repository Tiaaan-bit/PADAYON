<?php

namespace App\Repositories\Staff\Therapist;

use App\Models\Therapists;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StaffTherapistRepository implements StaffTherapistRepositoryInterface
{
    public function getTherapists(?string $search = null, ?string $status = null, ?string $specialty = null, int $perPage = 6): LengthAwarePaginator
    {
        $query = Therapists::query();

        if ($search !== null && $search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($specialty !== null && $specialty !== '') {
            $query->where('specialty', 'like', '%' . $specialty . '%');
        }

        return $query->latest()->paginate($perPage);
    }
}
