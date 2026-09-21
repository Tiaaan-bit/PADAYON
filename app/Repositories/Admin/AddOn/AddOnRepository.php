<?php

namespace App\Repositories\Admin\AddOn;

use App\Models\AddOns;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddOnRepository implements AddOnRepositoryInterface
{
    public function getAll(?string $search = null): LengthAwarePaginator
    {
        $query = AddOns::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('duration_minutes', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(5)->withQueryString();
    }

    public function create(array $data): AddOns
    {
        return AddOns::create($data);
    }

    public function update(AddOns $addOn, array $data): AddOns
    {
        $addOn->update($data);

        return $addOn->refresh();
    }

    public function delete(AddOns $addOn): bool
    {
        return $addOn->delete();
    }
}
