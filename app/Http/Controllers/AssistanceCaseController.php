<?php

namespace App\Http\Controllers;

use App\Models\AssistanceCaseDetail;
use App\Models\DcfmsCase;
use Illuminate\Http\Request;

class AssistanceCaseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Assistance Services Workspace
    |--------------------------------------------------------------------------
    */

    public function show(DcfmsCase $case)
    {
        $this->authorizeAssistanceAccess(
            $case,
            false
        );

        $case->load([
            'assignments.assignedUser',
            'creator',
            'statusHistory.updatedBy',
        ]);

        $detail =
            AssistanceCaseDetail::firstOrCreate(
                [
                    'dcfms_case_id' =>
                        $case->id,
                ]
            );

        return view(
            'assistance.show',
            [
                'case' =>
                    $case,

                'detail' =>
                    $detail,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Assistance Services Workflow
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        DcfmsCase $case
    ) {
        $this->authorizeAssistanceAccess(
            $case,
            true
        );


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([
                'assistance_status' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'assistance_type' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'assistance_required' => [
                    'nullable',
                    'string',
                ],

                'assistance_provided' => [
                    'nullable',
                    'string',
                ],

                'referral_details' => [
                    'nullable',
                    'string',
                ],

                'follow_up_actions' => [
                    'nullable',
                    'string',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | Create or Load Assistance Detail
        |--------------------------------------------------------------------------
        */

        $detail =
            AssistanceCaseDetail::firstOrCreate(
                [
                    'dcfms_case_id' =>
                        $case->id,
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | Update Assistance Record
        |--------------------------------------------------------------------------
        */

        $detail->update([
            'assistance_status' =>
                $validated[
                    'assistance_status'
                ],

            'assistance_type' =>
                $validated[
                    'assistance_type'
                ]
                ?? null,

            'assistance_required' =>
                $validated[
                    'assistance_required'
                ]
                ?? null,

            'assistance_provided' =>
                $validated[
                    'assistance_provided'
                ]
                ?? null,

            'referral_details' =>
                $validated[
                    'referral_details'
                ]
                ?? null,

            'follow_up_actions' =>
                $validated[
                    'follow_up_actions'
                ]
                ?? null,

            'remarks' =>
                $validated[
                    'remarks'
                ]
                ?? null,

            'updated_by' =>
                auth()->id(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Shared Case Timeline
        |--------------------------------------------------------------------------
        |
        | Keep stored timeline text in English so case-history records stay
        | consistent regardless of the selected UI language.
        |
        */

        $case->statusHistory()
            ->create([
                'status' =>
                    $case->current_status,

                'action_taken' =>
                    'Assistance Services updated',

                'division' =>
                    'Assistance Services',

                'remarks' =>
                    $validated['remarks']
                    ??
                    'Assistance Services workflow updated.',

                'updated_by' =>
                    auth()->id(),
            ]);


        return redirect()
            ->route(
                'assistance.show',
                $case
            )
            ->with(
                'success',
                __('assistance_messages.workflow_updated_successfully')
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Assistance Access Rules
    |--------------------------------------------------------------------------
    |
    | There is NO Assistance Officer role.
    |
    | Assistance Services operational work is performed collaboratively by:
    | - Legal Officers
    | - Protection Officers
    |
    | Supervisory visibility:
    | - Legal Director
    | - Protection Director
    | - Director General
    | - Chairman (view only)
    |
    */

    private function authorizeAssistanceAccess(
        DcfmsCase $case,
        bool $requiresEdit
    ): void {
        $user =
            auth()->user();


        /*
        |--------------------------------------------------------------------------
        | Confirm Case is Routed to Assistance Services
        |--------------------------------------------------------------------------
        */

        $assistanceAssignment =
            $case->assignments()
                ->where(
                    'division',
                    'Assistance Services'
                )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        abort_unless(
            $assistanceAssignment,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Director General
        |--------------------------------------------------------------------------
        */

        if (
            $user->role ===
            'director_general'
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Chairman
        |--------------------------------------------------------------------------
        |
        | Chairman receives read-only oversight.
        |
        */

        if (
            $user->role ===
            'chairman'
        ) {

            abort_if(
                $requiresEdit,
                403
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Supervisory Directors
        |--------------------------------------------------------------------------
        */

        if (in_array(
            $user->role,
            [
                'legal_director',
                'protection_director',
            ],
            true
        )) {

            /*
             * For now, directors receive supervisory access.
             *
             * If you want directors to be strictly read-only later,
             * change this section accordingly.
             */

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Operational Assistance Work
        |--------------------------------------------------------------------------
        |
        | Only the officer assigned to this Assistance Services assignment
        | may update the assistance workflow.
        |
        */

        if (in_array(
            $user->role,
            [
                'legal_officer',
                'protection_officer',
            ],
            true
        )) {

            $isAssignedOfficer =
                (int)
                $assistanceAssignment
                    ->assigned_user_id
                ===
                (int)
                $user->id;


            abort_unless(
                $isAssignedOfficer,
                403
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | All Other Roles
        |--------------------------------------------------------------------------
        */

        abort(403);
    }
}
