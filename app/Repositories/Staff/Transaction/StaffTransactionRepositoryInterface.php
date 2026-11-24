<?php

namespace App\Repositories\Staff\Transaction;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StaffTransactionRepositoryInterface
{
    public function getAll(?string $date = null, ?string $paymentMethod = null, ?string $amount = null): LengthAwarePaginator;

    public function getAllForTotals(): Collection;
}
