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

        /*
    |--------------------------------------------------------------------------
    | STEP 1: USER / ADMIN / STAFF LOGIN
    |--------------------------------------------------------------------------
    */

        if (Auth::guard('web')->attempt(
            [
                'email' => $email,
                'password' => $password,
            ],
            $request->boolean('remember')
        )) {
            // Clear any previous therapist authentication.
            Auth::guard('therapist')->logout();

            $request->session()->regenerate();

            $user = Auth::guard('web')->user();

            if ($user->isAdmin()) {
                if (method_exists($user, 'markOnline')) {
                    $user->markOnline();
                }

                return redirect()->route('admin.dashboard');
            }

            if ($user->isStaff()) {
                if (method_exists($user, 'markOnline')) {
                    $user->markOnline();
                }

                return redirect()->route('staff.dashboard');
            }

            if (!$user->hasVerifiedEmail()) {
                Auth::guard('web')->logout();

                return redirect()
                    ->route('verification.notice')
                    ->with('info', 'Please verify your email before accessing your account.');
            }

            if (method_exists($user, 'markOnline')) {
                $user->markOnline();
            }

            return redirect()->route('user.dashboard');
        }

        /*
    |--------------------------------------------------------------------------
    | STEP 2: THERAPIST LOGIN
    |--------------------------------------------------------------------------
    */

        if (Auth::guard('therapist')->attempt(
            [
                'email' => $email,
                'password' => $password,
            ],
            true // Automatically enable Remember Me
        )) {
            // Clear any previous web authentication.
            Auth::guard('web')->logout();

            $request->session()->regenerate();

            $therapist = Auth::guard('therapist')->user();

            if (method_exists($therapist, 'markOnline')) {
                $therapist->markOnline();
            }

            return redirect()->intended(route('therapist.ai.index'));
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
    | WEB USER / ADMIN / STAFF
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
    | THERAPIST
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
    | DESTROY CURRENT SESSION
    |--------------------------------------------------------------------------
    */

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out.');
    }
}
