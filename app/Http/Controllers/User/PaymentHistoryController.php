<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UsersAppointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $paymentsQuery = UsersAppointments::with(['service', 'therapist', 'addOn'])->where('user_id', $user->id);


        if ($request->filled('date')) {
            $paymentsQuery->whereDate('appointment_date', $request->date);
        }


        if ($request->filled('payment_method')) {
            $paymentsQuery->where('payment_method', $request->payment_method);
        }

        if ($request->filled('payment_status')) {
            $paymentsQuery->where('payment_status', $request->payment_status);
        }

        if ($request->filled('amount')) {
            $paymentsQuery->where('amount_paid', $request->amount);
        }


        $payments = (clone $paymentsQuery)->orderByDesc('created_at')->paginate(5)->withQueryString();

        $totalServicePrice = (clone $paymentsQuery)->sum('service_price');

        $totalAddOnPrice = (clone $paymentsQuery)->sum('addons_price');

        $totalAmountPaid = (clone $paymentsQuery)->sum('amount_paid');
    
        $totalPending = (clone $paymentsQuery)->where('payment_status', 'pending')->count();

        $totalConfirmed = (clone $paymentsQuery)->where('status', 'confirm')->count();

        return view('user.paymenthistory', compact('payments', 'totalServicePrice', 'totalAddOnPrice', 'totalAmountPaid', 'totalPending', 'totalConfirmed'));
    }
}
