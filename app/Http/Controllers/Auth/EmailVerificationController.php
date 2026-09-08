<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\EmailVerificationService;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('user.dashboard');
        }

        return view('auth.verify-email');
    }

    public function resend(Request $request, EmailVerificationService $service)
    {
        if (!$service->resend($request->user())) {
            return redirect()->route('user.dashboard');
        }

        return back()->with('success', 'A verification code has been sent.');
    }

    public function verifyCode(EmailVerificationRequest $request, EmailVerificationService $service)
    {
        $success = $service->verify($request->user(), $request->code);

        if (!$success) {
            return back()->withErrors([
                'code' => 'Invalid or expired verification code.',
            ]);
        }

        return redirect()->route('user.dashboard')->with('success', 'Email verified successfully!');
    }
}
