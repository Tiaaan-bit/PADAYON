<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\WalkInCustomer;
use Illuminate\Http\Request;

class WalkInRegistrationController extends Controller
{
    /**
     * Open a walk-in registration invitation.
     */
    public function create(Request $request, string $token)
    {
        /*
        |--------------------------------------------------------------------------
        | Hash the token received from the email
        |--------------------------------------------------------------------------
        */

        $tokenHash = hash('sha256', $token);

        /*
        |--------------------------------------------------------------------------
        | Find walk-in customer
        |--------------------------------------------------------------------------
        */

        $walkInCustomer = WalkInCustomer::query()->where('registration_token', $tokenHash)->first();

        /*
        |--------------------------------------------------------------------------
        | Invalid token
        |--------------------------------------------------------------------------
        */

        if (!$walkInCustomer) {
            return redirect()->route('register')->with('error', 'This registration invitation is invalid or has already been used.');
        }

        /*
        |--------------------------------------------------------------------------
        | Already registered
        |--------------------------------------------------------------------------
        */

        if ($walkInCustomer->registered_user_id) {
            return redirect()->route('login')->with('info', 'This walk-in customer already has a Padayon account. Please log in.');
        }

        /*
        |--------------------------------------------------------------------------
        | Expired invitation
        |--------------------------------------------------------------------------
        */

        if (!$walkInCustomer->registration_token_expires_at || $walkInCustomer->registration_token_expires_at->isPast()) {
            return redirect()->route('register')->with('error', 'This registration invitation has expired. Please contact Padayon Massage Center for a new invitation.');
        }

        /*
        |--------------------------------------------------------------------------
        | Store RAW token in session
        |--------------------------------------------------------------------------
        |
        | Database:
        | SHA-256 hash
        |
        | Session:
        | Raw token
        |
        */

        session()->put('walk_in_registration_token', $token);

        /*
        |--------------------------------------------------------------------------
        | Go to registration page
        |--------------------------------------------------------------------------
        */

        return redirect()->route('register')->with('walk_in_registration', true);
    }
}
