<?php

namespace App\Repositories\Admin\Transaction;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface
{
    public function getAll(?string $date = null, ?string $paymentMethod = null, ?string $amount = null): LengthAwarePaginator;

    public function getAllForTotals(): Collection;

    public function getTotalServicePrice(): float;

    public function getTotalAddOnPrice(): float;

    public function getTotalAmountPaid(): float;
}
