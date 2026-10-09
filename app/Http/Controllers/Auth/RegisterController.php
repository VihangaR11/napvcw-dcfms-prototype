<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Available institutional designations.
     */
    private const DESIGNATIONS = [
        'Chairman',
        'Director General',
        'Board Secretary',
        'Director - Law and Law Enforcement Division',
        'Legal Officer',
        'Investigation Officer',
        'Assistant Director - Protection Services Division',
        'Protection Officer',
        'Director - Police Protection Division',
        'Police Protection Officer',
        'Director - Policy and Programs Division',
    ];

    /**
     * Show signup page.
     */
    public function showRegistrationForm()
    {
        return view('auth.register', [
            'designations' => self::DESIGNATIONS,
        ]);
    }

    /**
     * Register user.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'designation' => [
                'required',
                Rule::in(self::DESIGNATIONS),
            ],

            'employee_number' => [
                'required',
                'string',
                'max:50',
                'unique:users,employee_number',
            ],

            'password' => [
                'required',
                'string',
                'min:5',
                'confirmed',
            ],
        ]);

        [$role, $division] =
            $this->resolveRoleAndDivision(
                $validated['designation']
            );

        User::create([
            'name' => $validated['name'],

            'designation' =>
                $validated['designation'],

            'employee_number' =>
                strtoupper(
                    trim(
                        $validated['employee_number']
                    )
                ),

            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'role' =>
                $role,

            'division' =>
                $division,

            /*
             * New self-registered accounts remain inactive
             * until approved by an authorized administrator.
             */
            'is_active' =>
                false,

            'account_status' =>
                'pending',
        ]);

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Your account has been created successfully and is awaiting approval.'
            );
    }

    /**
     * Map designation to application role and division.
     */
    private function resolveRoleAndDivision(
        string $designation
    ): array {
        return match ($designation) {
   
        'Chairman' => [
            'chairman',
            'Board Secretariat',
        ],

        'Director General' => [
            'director_general',
            'Director General’s Bureau',
        ],

        'Board Secretary' => [
            'board_secretary',
            'Board Secretariat',
        ],

        'Director - Law and Law Enforcement Division' => [
            'legal_director',
            'Law and Law Enforcement',
        ],

        'Legal Officer' => [
            'legal_officer',
            'Law and Law Enforcement',
        ],

        'Investigation Officer' => [
            'investigation_officer',
            'Law and Law Enforcement',
        ],

        'Assistant Director - Protection Services Division' => [
            'protection_director',
            'Protection Services',
        ],

        'Protection Officer' => [
            'protection_officer',
            'Protection Services',
        ],

        'Director - Police Protection Division' => [
            'police_protection_director',
            'Police Protection',
        ],
        
        'Police Protection Officer' => [
            'police_protection_officer',
            'Police Protection',
        ],

        'Director - Policy and Programs Division' => [
            'policy_director',
            'Policy and Programs',
        ],

        default => [
                'staff',
                'Unassigned',
            ],
        };
    }
}