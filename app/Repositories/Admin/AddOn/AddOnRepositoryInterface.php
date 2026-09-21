<?php

namespace App\Repositories\Admin\AddOn;

use App\Models\AddOns;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AddOnRepositoryInterface
{
    public function getAll(?string $search = null): LengthAwarePaginator;

    public function create(array $data): AddOns;

    public function update(AddOns $addOn, array $data): AddOns;

    public function delete(AddOns $addOn): bool;
}
