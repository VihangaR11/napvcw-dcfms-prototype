<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()
                ->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Authenticate employee using EPF number and password.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'employee_number' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);

        $employeeNumber = strtoupper(
            trim(
                $validated['employee_number']
            )
        );

        $credentials = [
            'employee_number' => $employeeNumber,
            'password' => $validated['password'],
        ];

        /*
        |--------------------------------------------------------------------------
        | Attempt Login
        |--------------------------------------------------------------------------
        */

        if (!Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            return back()
                ->withErrors([
                    'employee_number' =>
                        'The EPF number or password is incorrect.',
                ])
                ->onlyInput(
                    'employee_number'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Account Status Check
        |--------------------------------------------------------------------------
        */

        if (!$user->is_active) {

            $message = match ($user->account_status) {

                'pending' =>
                    'Your account registration is awaiting administrator approval.',

                'rejected' =>
                    'Your account registration request was not approved.',

                'suspended' =>
                    'Your account access has been suspended. Please contact the system administrator.',

                default =>
                    'Your account is currently inactive.',
            };

            /*
            |--------------------------------------------------------------------------
            | Logout Inactive User
            |--------------------------------------------------------------------------
            */

            Auth::logout();

            $request
                ->session()
                ->invalidate();

            $request
                ->session()
                ->regenerateToken();

            return back()
                ->withErrors([
                    'employee_number' => $message,
                ])
                ->onlyInput(
                    'employee_number'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Successful Login
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->intended(
                route('dashboard')
            );
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'You have been signed out successfully.'
            );
    }
}