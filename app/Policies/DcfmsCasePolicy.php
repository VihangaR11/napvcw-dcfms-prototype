<?php

namespace App\Policies;

use App\Models\DcfmsCase;
use App\Models\User;

class DcfmsCasePolicy
{
    /*
    |--------------------------------------------------------------------------
    | View Case Registry
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return in_array(
            $user->role,
            [
                'director_general',
                'board_secretary',
                'chairman',
                'policy_director',

                'legal_director',
                'legal_officer',
                'investigation_officer',

                'protection_director',
                'protection_officer',

                'police_protection_director',
                'police_protection_officer',
            ],
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | View Individual Case
    |--------------------------------------------------------------------------
    */

    public function view(
        User $user,
        DcfmsCase $case
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Institution-Wide Roles
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $user->role,
                [
                    'director_general',
                    'board_secretary',
                    'chairman',
                    'policy_director',
                ],
                true
            )
        ) {
            return true;
        }


        /*
        |--------------------------------------------------------------------------
        | System Administrator
        |--------------------------------------------------------------------------
        |
        | Technical administrators should not automatically gain access
        | to operational victim/witness case information.
        |
        */

        if (
            $user->role ===
            'system_admin'
        ) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | Law & Law Enforcement Director
        |--------------------------------------------------------------------------
        */

        if (
            $user->role ===
            'legal_director'
        ) {
            return $case
                ->assignments()
                ->whereIn(
                    'division',
                    [
                        'Law and Law Enforcement',
                        'Assistance Services',
                    ]
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        /*
        |--------------------------------------------------------------------------
        | Assistant Director - Protection Services
        |--------------------------------------------------------------------------
        |
        | Internal role key remains:
        |
        | protection_director
        |
        */

        if (
            $user->role ===
            'protection_director'
        ) {
            return $case
                ->assignments()
                ->whereIn(
                    'division',
                    [
                        'Protection Services',
                        'Assistance Services',
                    ]
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        /*
        |--------------------------------------------------------------------------
        | Director - Police Protection
        |--------------------------------------------------------------------------
        */

        if (
            $user->role ===
            'police_protection_director'
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Police Protection'
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        /*
        |--------------------------------------------------------------------------
        | Legal Officer
        |--------------------------------------------------------------------------
        */

        if (
            $user->role ===
            'legal_officer'
        ) {
            return $case
                ->assignments()
                ->whereIn(
                    'division',
                    [
                        'Law and Law Enforcement',
                        'Assistance Services',
                    ]
                )
                ->where(
                    'assigned_user_id',
                    $user->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        /*
        |--------------------------------------------------------------------------
        | Investigation Officer
        |--------------------------------------------------------------------------
        */

        if (
            $user->role ===
            'investigation_officer'
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Law and Law Enforcement'
                )
                ->where(
                    'assigned_user_id',
                    $user->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        /*
        |--------------------------------------------------------------------------
        | Protection Officer
        |--------------------------------------------------------------------------
        */

        if (
            $user->role ===
            'protection_officer'
        ) {
            return $case
                ->assignments()
                ->whereIn(
                    'division',
                    [
                        'Protection Services',
                        'Assistance Services',
                    ]
                )
                ->where(
                    'assigned_user_id',
                    $user->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        /*
        |--------------------------------------------------------------------------
        | Police Protection Officer
        |--------------------------------------------------------------------------
        */

        if (
            $user->role ===
            'police_protection_officer'
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Police Protection'
                )
                ->where(
                    'assigned_user_id',
                    $user->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Register Case
    |--------------------------------------------------------------------------
    */

    public function create(
        User $user
    ): bool {
        return in_array(
            $user->role,
            [
                'board_secretary',
                'director_general',
            ],
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Route Case
    |--------------------------------------------------------------------------
    */

    public function route(
        User $user,
        DcfmsCase $case
    ): bool {
        return in_array(
            $user->role,
            [
                'board_secretary',
                'director_general',
            ],
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Legal Workflow
    |--------------------------------------------------------------------------
    */

    public function updateLegal(
        User $user,
        DcfmsCase $case
    ): bool {

        if (
            $user->role ===
            'director_general'
        ) {
            return true;
        }


        if (
            $user->role ===
            'legal_director'
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Law and Law Enforcement'
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        if (
            in_array(
                $user->role,
                [
                    'legal_officer',
                    'investigation_officer',
                ],
                true
            )
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Law and Law Enforcement'
                )
                ->where(
                    'assigned_user_id',
                    $user->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Protection Services Workflow
    |--------------------------------------------------------------------------
    */

    public function updateProtection(
        User $user,
        DcfmsCase $case
    ): bool {

        if (
            $user->role ===
            'director_general'
        ) {
            return true;
        }


        if (
            $user->role ===
            'protection_director'
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Protection Services'
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        if (
            $user->role ===
            'protection_officer'
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Protection Services'
                )
                ->where(
                    'assigned_user_id',
                    $user->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Assistance Services Workflow
    |--------------------------------------------------------------------------
    */

    public function updateAssistance(
        User $user,
        DcfmsCase $case
    ): bool {

        if (
            $user->role ===
            'director_general'
        ) {
            return true;
        }


        if (
            in_array(
                $user->role,
                [
                    'legal_director',
                    'protection_director',
                ],
                true
            )
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Assistance Services'
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        if (
            in_array(
                $user->role,
                [
                    'legal_officer',
                    'protection_officer',
                ],
                true
            )
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Assistance Services'
                )
                ->where(
                    'assigned_user_id',
                    $user->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Police Protection Assessment
    |--------------------------------------------------------------------------
    */

    public function updatePoliceProtection(
        User $user,
        DcfmsCase $case
    ): bool {

        if (
            in_array(
                $user->role,
                [
                    'director_general',
                    'police_protection_director',
                ],
                true
            )
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Police Protection'
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        if (
            $user->role ===
            'police_protection_officer'
        ) {
            return $case
                ->assignments()
                ->where(
                    'division',
                    'Police Protection'
                )
                ->where(
                    'assigned_user_id',
                    $user->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->exists();
        }


        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Recommend Threat Status
    |--------------------------------------------------------------------------
    |
    | Only Director - Police Protection.
    |
    */

    public function recommendThreat(
        User $user,
        DcfmsCase $case
    ): bool {

        if (
            $user->role !==
            'police_protection_director'
        ) {
            return false;
        }


        return $case
            ->assignments()
            ->where(
                'division',
                'Police Protection'
            )
            ->where(
                'is_active',
                true
            )
            ->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | Director General Protection Approval
    |--------------------------------------------------------------------------
    */

    public function approveProtection(
        User $user,
        DcfmsCase $case
    ): bool {
        return $user->role ===
            'director_general';
    }


    /*
    |--------------------------------------------------------------------------
    | Download Case Summary Report
    |--------------------------------------------------------------------------
    */

    public function downloadReport(
        User $user,
        DcfmsCase $case
    ): bool {
        return $this->view(
            $user,
            $case
        );
    }

    public function close(
    User $user,
    DcfmsCase $case
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


    public function reopen(
    User $user,
    DcfmsCase $case
        ): bool {
             return $user->role ===
        'director_general';
        }
}