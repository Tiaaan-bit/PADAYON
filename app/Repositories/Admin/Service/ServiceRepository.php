<?php

namespace App\Repositories\Admin\Service;

use App\Models\Services;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function getAll(?string $search = null): LengthAwarePaginator
    {
        $query = Services::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('duration_minutes', 'like', "%{$search}%");
            });
        }

        return $query
            ->latest()
            ->paginate(5)
            ->withQueryString();
    }

    public function getActiveCount(): int
    {
        return Services::where('status', 'active')->count();
    }

    public function getInactiveCount(): int
    {
        return Services::where('status', 'inactive')->count();
    }

    public function create(array $data): Services
    {
        return Services::create($data);
    }

    public function update(Services $service, array $data): Services
    {
        $service->update($data);

        return $service->refresh();
    }

    public function delete(Services $service): bool
    {
        return $service->delete();
    }
}