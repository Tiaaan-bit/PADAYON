<?php

namespace App\Repositories\Admin\Transaction;

use App\Models\UsersAppointments;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getAll(?string $date = null, ?string $paymentMethod = null, ?string $amount = null): LengthAwarePaginator
    {
        $query = UsersAppointments::with(['user', 'service', 'therapist', 'addOn']);

        if ($date) {
            $query->whereDate('appointment_date', $date);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($amount !== null && $amount !== '') {
            $query->where('amount_paid', $amount);
        }

        return $query->latest()->paginate(5)->withQueryString();
    }

    public function getAllForTotals(): Collection
    {
        return UsersAppointments::with(['service'])
            ->latest()
            ->get();
    }

    public function getTotalServicePrice(): float
    {
        return $this->getAllForTotals()->sum(fn($transaction) => $transaction->service->price ?? 0);
    }

    public function getTotalAddOnPrice(): float
    {
        return $this->getAllForTotals()->sum(fn($transaction) => $transaction->addons_price ?? 0);
    }

    public function getTotalAmountPaid(): float
    {
        return (float) $this->getAllForTotals()->sum('amount_paid');
    }
}
