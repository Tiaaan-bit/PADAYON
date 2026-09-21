<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\Transaction\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTransactionsController extends Controller
{
    public function transactions(Request $request, TransactionRepositoryInterface $transactions): View
    {
        $transactionList = $transactions->getAll($request->input('date'), $request->input('payment_method'), $request->input('amount'));

        $totalServicePrice = $transactions->getTotalServicePrice();

        $totalAddOnPrice = $transactions->getTotalAddOnPrice();

        $totalAmountPaid = $transactions->getTotalAmountPaid();

        return view('admin.transactions', [
            'transactions' => $transactionList,
            'totalServicePrice' => $totalServicePrice,
            'totalAddOnPrice' => $totalAddOnPrice,
            'totalAmountPaid' => $totalAmountPaid,
        ]);
    }
}
