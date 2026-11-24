<?php

namespace App\Repositories\Staff\Transaction;

use App\Models\UsersAppointments;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StaffTransactionRepository implements StaffTransactionRepositoryInterface
{
    public function getAll(?string $date = null, ?string $paymentMethod = null, ?string $amount = null): LengthAwarePaginator
    {
        $query = UsersAppointments::with(['user', 'walkInCustomer', 'service', 'therapist', 'addOn'])->latest();

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
}
