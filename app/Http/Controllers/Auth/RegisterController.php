<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\WalkInCustomer;
use App\Models\UsersAppointments;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show registration form.
     */
    public function showForm()
    {
        $walkInRegistration = session()->has('walk_in_registration_token');
        $walkInCustomer = null;

        if ($walkInRegistration) {
            $token = session('walk_in_registration_token');
            $tokenHash = hash('sha256', $token);

            $walkInCustomer = WalkInCustomer::query()->where('registration_token', $tokenHash)->first();

            // Validate invitation again.
            if (!$walkInCustomer || $walkInCustomer->registered_user_id || !$walkInCustomer->registration_token_expires_at || $walkInCustomer->registration_token_expires_at->isPast()) {
                session()->forget('walk_in_registration_token');

                $walkInCustomer = null;
                $walkInRegistration = false;
            }
        }

        return view('auth.register', [
            'walkInRegistration' => $walkInRegistration,
            'walkInCustomer' => $walkInCustomer,
        ]);
    }

    /**
     * Process registration.
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Find walk-in invitation
        |--------------------------------------------------------------------------
        */

        $walkInCustomer = null;

        $walkInToken = session('walk_in_registration_token');

        if ($walkInToken) {
            $tokenHash = hash('sha256', $walkInToken);

            $walkInCustomer = WalkInCustomer::query()->where('registration_token', $tokenHash)->first();

            /*
            |--------------------------------------------------------------------------
            | Validate invitation
            |--------------------------------------------------------------------------
            */

            if (!$walkInCustomer || $walkInCustomer->registered_user_id || !$walkInCustomer->registration_token_expires_at || $walkInCustomer->registration_token_expires_at->isPast()) {
                session()->forget('walk_in_registration_token');

                return redirect()->route('register')->with('error', 'Your walk-in registration invitation is invalid or has expired.');
            }

            /*
            |--------------------------------------------------------------------------
            | Email must match walk-in booking
            |--------------------------------------------------------------------------
            */

            if (strtolower($data['email']) !== strtolower($walkInCustomer->email)) {
                return back()->withInput()->with('error', 'The email address must match the email address used for your walk-in booking.');
            }

            /*
            |--------------------------------------------------------------------------
            | Phone must match walk-in booking
            |--------------------------------------------------------------------------
            */

            if ($data['phone'] !== $walkInCustomer->phone) {
                return back()->withInput()->with('error', 'The phone number must match the phone number used for your walk-in booking.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Create user + link walk-in customer + appointments
        |--------------------------------------------------------------------------
        */

        [$user, $linkedWalkInCustomer] = DB::transaction(function () use ($data, $walkInCustomer) {
            /*
                |--------------------------------------------------------------------------
                | Create user account
                |--------------------------------------------------------------------------
                */

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => 'user',
                'status' => 'pending',
            ]);

            /*
                |--------------------------------------------------------------------------
                | Normal registration
                |--------------------------------------------------------------------------
                */

            if (!$walkInCustomer) {
                return [$user, null];
            }

            /*
                |--------------------------------------------------------------------------
                | Re-fetch walk-in customer inside transaction
                |--------------------------------------------------------------------------
                */

            $customer = WalkInCustomer::query()->where('id', $walkInCustomer->id)->lockForUpdate()->first();

            if (!$customer) {
                throw new \RuntimeException('Walk-in customer record could not be found.');
            }

            /*
                |--------------------------------------------------------------------------
                | Prevent duplicate registration
                |--------------------------------------------------------------------------
                */

            if ($customer->registered_user_id) {
                throw new \RuntimeException('This walk-in customer is already linked to an account.');
            }

            /*
                |--------------------------------------------------------------------------
                | Link walk-in customer to newly created user
                |--------------------------------------------------------------------------
                */

            $customer->registered_user_id = $user->id;
            $customer->registration_token = null;
            $customer->registration_token_expires_at = null;

            $customer->save();

            /*
                |--------------------------------------------------------------------------
                | Link existing walk-in appointments to the new user
                |--------------------------------------------------------------------------
                |
                | Before registration:
                |
                | user_id = NULL
                | walk_in_customer_id = 7
                |
                | After registration:
                |
                | user_id = 21
                | walk_in_customer_id = 7
                |
                | This allows the appointment to appear in the user's
                | normal appointment history.
                |
                */

            UsersAppointments::query()
                ->where('walk_in_customer_id', $customer->id)
                ->whereNull('user_id')
                ->update([
                    'user_id' => $user->id,
                ]);

            /*
                |--------------------------------------------------------------------------
                | Refresh customer
                |--------------------------------------------------------------------------
                */

            $customer->refresh();

            return [$user, $customer];
        });

        /*
        |--------------------------------------------------------------------------
        | Remove invitation token from session
        |--------------------------------------------------------------------------
        */

        if ($linkedWalkInCustomer) {
            session()->forget('walk_in_registration_token');
        }

        /*
        |--------------------------------------------------------------------------
        | Existing email verification flow
        |--------------------------------------------------------------------------
        */

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', 'Registration successful! Please check your email to verify your account.');
    }
}
