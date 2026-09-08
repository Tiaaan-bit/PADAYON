<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UsersAppointments;
use Illuminate\Http\Request;

class AdminTransactionsController extends Controller
{
    public function transactions(Request $request)
    {
        $query = UsersAppointments::with(['user', 'service', 'therapist', 'addOn']);

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('amount')) {
            $query->where('amount_paid', $request->amount);
        }

        $transactions = $query->latest()->paginate(5)->withQueryString();

        $allTransactions = UsersAppointments::with(['user', 'service', 'therapist', 'addOn'])
            ->latest()
            ->get();

        $totalServicePrice = $allTransactions->sum(function ($transaction) {
            return $transaction->service->price ?? 0;
        });

        $totalAddOnPrice = $allTransactions->sum(function ($transaction) {
            return $transaction->addons_price ?? 0;
        });

        $totalAmountPaid = $allTransactions->sum('amount_paid');

        return view('admin.transactions', compact('transactions', 'totalServicePrice', 'totalAddOnPrice', 'totalAmountPaid'));
    }

    
}
