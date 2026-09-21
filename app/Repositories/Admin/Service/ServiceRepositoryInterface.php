<?php

namespace App\Repositories\Admin\Service;

use App\Models\Services;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface
{
    public function getAll(?string $search = null): LengthAwarePaginator;

    public function getActiveCount(): int;

    public function getInactiveCount(): int;

    public function create(array $data): Services;

    public function update(Services $service, array $data): Services;

    public function delete(Services $service): bool;
}