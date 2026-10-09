<?php

namespace App\Policies;

use App\Models\CaseAssignment;
use App\Models\User;

class CaseAssignmentPolicy
{
    /*
    |--------------------------------------------------------------------------
    | View Assignment
    |--------------------------------------------------------------------------
    */

    public function view(
        User $user,
        CaseAssignment $assignment
    ): bool {
        return match ($assignment->division) {

            'Law and Law Enforcement' =>
                in_array(
                    $user->role,
                    [
                        'director_general',
                        'legal_director',
                        'legal_officer',
                        'investigation_officer',
                    ],
                    true
                ),

            'Protection Services' =>
                in_array(
                    $user->role,
                    [
                        'director_general',
                        'protection_director',
                        'protection_officer',
                    ],
                    true
                ),

            'Police Protection' =>
                in_array(
                    $user->role,
                    [
                        'director_general',
                        'police_protection_director',
                        'police_protection_officer',
                    ],
                    true
                ),

            'Assistance Services' =>
                in_array(
                    $user->role,
                    [
                        'director_general',
                        'legal_director',
                        'legal_officer',
                        'protection_director',
                        'protection_officer',
                    ],
                    true
                ),

            default =>
                false,
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Assign / Reassign Responsible Officer
    |--------------------------------------------------------------------------
    */

    public function assignOfficer(
        User $user,
        CaseAssignment $assignment
    ): bool {
        return match ($assignment->division) {

            'Law and Law Enforcement' =>
                in_array(
                    $user->role,
                    [
                        'legal_director',
                        'director_general',
                    ],
                    true
                ),

            'Protection Services' =>
                in_array(
                    $user->role,
                    [
                        'protection_director',
                        'director_general',
                    ],
                    true
                ),

            'Police Protection' =>
                in_array(
                    $user->role,
                    [
                        'police_protection_director',
                        'director_general',
                    ],
                    true
                ),

            'Assistance Services' =>
                in_array(
                    $user->role,
                    [
                        'legal_director',
                        'protection_director',
                        'director_general',
                    ],
                    true
                ),

            default =>
                false,
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Remove / Deactivate Assignment
    |--------------------------------------------------------------------------
    */

    public function deactivate(
        User $user,
        CaseAssignment $assignment
    ): bool {
        return in_array(
            $user->role,
            [
                'director_general',
                'board_secretary',
            ],
            true
        );
    }
}