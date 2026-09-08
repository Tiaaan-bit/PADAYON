<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UsersAppointments;
use Illuminate\Support\Facades\Auth;

class PaymentHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $paymentsQuery = UsersAppointments::with(['service', 'therapist', 'addOn'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        $payments = (clone $paymentsQuery)->paginate(5)->withQueryString();

        $totalServicePrice = (clone $paymentsQuery)->sum('service_price');

        $totalAddOnPrice = (clone $paymentsQuery)->sum('addons_price');

        $totalAmountPaid = (clone $paymentsQuery)->sum('amount_paid');

        $totalPending = (clone $paymentsQuery)->where('status', 'pending')->count();

        $totalConfirmed = (clone $paymentsQuery)->where('status', 'confirm')->count();

        return view('user.paymenthistory', compact('payments', 'totalServicePrice', 'totalAddOnPrice', 'totalAmountPaid', 'totalPending', 'totalConfirmed'));
    }
}
