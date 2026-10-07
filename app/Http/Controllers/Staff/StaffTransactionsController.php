<?php

namespace App\Http\Controllers\Staff;

use App\Actions\Admin\Transaction\MarkTransactionAsPaid;
use App\Http\Controllers\Controller;
use App\Repositories\Staff\Transaction\StaffTransactionRepositoryInterface;
use App\Models\UsersAppointments;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Enums\Admin\Appointment\AppointmentStatus;

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
        abort_unless($appointment->status === AppointmentStatus::CONFIRMED, 422, 'Only confirmed appointments can be marked as paid.');

        $totalAppointmentAmount = (float) $appointment->service_price + (float) $appointment->addons_price;

        $amountPaid = (float) ($appointment->amount_paid ?? 0);

        $remainingBalance = max(0, $totalAppointmentAmount - $amountPaid);

        abort_if($remainingBalance <= 0, 422, 'This appointment is already fully paid.');

        $markTransactionAsPaid->execute($appointment);

        return back()->with('success', 'Payment has been successfully marked as paid.');
    }
}
