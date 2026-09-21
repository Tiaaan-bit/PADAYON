<?php

namespace App\Repositories\Admin\Therapist;

use App\Models\Therapists;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TherapistRepository implements TherapistRepositoryInterface
{
    public function getAll(?string $search = null, ?string $status = null, ?string $specialty = null): LengthAwarePaginator
    {
        $query = Therapists::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($specialty) {
            $query->where('specialty', 'like', "%{$specialty}%");
        }

        return $query->latest()->paginate(6)->withQueryString();
    }

    public function create(array $data): Therapists
    {
        return Therapists::create($data);
    }

    public function update(Therapists $therapist, array $data): Therapists
    {
        $therapist->update($data);

        return $therapist->refresh();
    }

    public function delete(Therapists $therapist): bool
    {
        return $therapist->delete();
    }
}
