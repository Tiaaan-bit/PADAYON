<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show login form.
     */
    public function showForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $email = $credentials['email'];
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        /*
        |--------------------------------------------------------------------------
        | Clear Existing Authentication
        |--------------------------------------------------------------------------
        |
        | Your application has two guards:
        |
        | web       = users + admins
        | therapist = therapists
        |
        | Make sure an old session does not remain authenticated.
        |
        */

        Auth::guard('web')->logout();
        Auth::guard('therapist')->logout();

        /*
        |--------------------------------------------------------------------------
        | STEP 1: LOGIN USER / ADMIN
        |--------------------------------------------------------------------------
        */

        if (Auth::guard('web')->attempt([
            'email' => $email,
            'password' => $password,
        ], $remember)) {

            $request->session()->regenerate();

            $user = Auth::guard('web')->user();

            /*
            |--------------------------------------------------------------------------
            | ADMIN
            |--------------------------------------------------------------------------
            */

            if ($user->isAdmin()) {

                if (method_exists($user, 'markOnline')) {
                    $user->markOnline();
                }

                return redirect()->route('admin.dashboard');
            }

            /*
            |--------------------------------------------------------------------------
            | Staff
            |--------------------------------------------------------------------------
            */

            if ($user->isStaff()) {

                if (method_exists($user, 'markOnline')) {
                    $user->markOnline();
                }

                return redirect()->route('staff.dashboard');
            }

            

            /*
            |--------------------------------------------------------------------------
            | NORMAL USER
            |--------------------------------------------------------------------------
            */

            if (!$user->hasVerifiedEmail()) {

                Auth::guard('web')->logout();

                return redirect()
                    ->route('verification.notice')
                    ->with(
                        'info',
                        'Please verify your email before logging in.'
                    );
            }

            if (method_exists($user, 'markOnline')) {
                $user->markOnline();
            }

            return redirect()->route('user.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2: LOGIN THERAPIST
        |--------------------------------------------------------------------------
        */

        if (Auth::guard('therapist')->attempt([
            'email' => $email,
            'password' => $password,
        ], $remember)) {

            $request->session()->regenerate();

            $therapist = Auth::guard('therapist')->user();

            /*
            |--------------------------------------------------------------------------
            | OPTIONAL: CHECK THERAPIST STATUS
            |--------------------------------------------------------------------------
            */

            if (
                isset($therapist->status) &&
                $therapist->status !== 'available'
            ) {

                Auth::guard('therapist')->logout();

                return back()
                    ->withErrors([
                        'email' => 'Your therapist account is not active.',
                    ])
                    ->onlyInput('email');
            }

            if (method_exists($therapist, 'markOnline')) {
                $therapist->markOnline();
            }

            return redirect()->route('therapist.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3: INVALID LOGIN
        |--------------------------------------------------------------------------
        */

        return back()
            ->withErrors([
                'email' => 'Invalid email or password.',
            ])
            ->onlyInput('email');
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Logout Web User / Admin
        |--------------------------------------------------------------------------
        */

        if (Auth::guard('web')->check()) {

            $user = Auth::guard('web')->user();

            if ($user && method_exists($user, 'markOffline')) {
                $user->markOffline();
            }

            Auth::guard('web')->logout();
        }

        /*
        |--------------------------------------------------------------------------
        | Logout Therapist
        |--------------------------------------------------------------------------
        */

        if (Auth::guard('therapist')->check()) {

            $therapist = Auth::guard('therapist')->user();

            if ($therapist && method_exists($therapist, 'markOffline')) {
                $therapist->markOffline();
            }

            Auth::guard('therapist')->logout();
        }

        /*
        |--------------------------------------------------------------------------
        | Destroy Session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out.');
    }
}