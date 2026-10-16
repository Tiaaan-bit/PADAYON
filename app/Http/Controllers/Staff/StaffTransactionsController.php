<?php

namespace App\Http\Controllers\Staff;

use App\Actions\Admin\Transaction\MarkTransactionAsPaid;
use App\Http\Controllers\Controller;
use App\Repositories\Staff\Transaction\StaffTransactionRepositoryInterface;
use App\Models\UsersAppointments;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;


class StaffTransactionsController extends Controller
{
    public function transactions(Request $request, StaffTransactionRepositoryInterface $transactions): View
    {
        $transactionList = $transactions->getAll($request->input('date'), $request->input('payment_method'), $request->input('amount'));

        $totalServicePrice = $transactions->getTotalServicePrice();

        $totalAddOnPrice = $transactions->getTotalAddOnPrice();

        $totalAmountPaid = $transactions->getTotalAmountPaid();

        return view('staff.transactions', [
            'transactions' => $transactionList,
            'totalServicePrice' => $totalServicePrice,
            'totalAddOnPrice' => $totalAddOnPrice,
            'totalAmountPaid' => $totalAmountPaid,
        ]);
    }

    public function markAsPaid(UsersAppointments $appointment, MarkTransactionAsPaid $markTransactionAsPaid)
    {
        Gate::authorize('staff.transaction.markPaid', $appointment);

        $markTransactionAsPaid->execute($appointment);

        return back()->with('success', 'Payment has been successfully marked as paid.');
    }
}
