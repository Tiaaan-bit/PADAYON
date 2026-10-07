<?php

namespace App\Repositories\Staff\Therapist;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StaffTherapistRepositoryInterface
{
    public function getTherapists(?string $search = null, ?string $status = null, ?string $specialty = null, int $perPage = 6): LengthAwarePaginator;
}
