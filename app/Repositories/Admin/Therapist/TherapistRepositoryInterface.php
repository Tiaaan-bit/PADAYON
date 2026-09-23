<?php

namespace App\Repositories\Admin\Therapist;

use App\Models\Therapists;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TherapistRepositoryInterface
{
    public function getAll(?string $search = null, ?string $status = null, ?string $specialty = null): LengthAwarePaginator;

    public function create(array $data): Therapists;

    public function update(Therapists $therapist, array $data): Therapists;

    public function delete(Therapists $therapist): bool;

    public function getFeedback(Therapists $therapist): Collection;
}
