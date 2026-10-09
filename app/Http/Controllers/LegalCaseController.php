<?php

namespace App\Http\Controllers;

use App\Models\CaseStatusHistory;
use App\Models\DcfmsCase;
use App\Models\LegalCaseDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LegalCaseController extends Controller
{
    public function show(DcfmsCase $case)
    {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, [
                'legal_director',
                'legal_officer',
                'investigation_officer',
                'director_general',
                'system_admin',
            ]),
            403
        );

        $isAssignedToLegal = $case->assignments()
            ->where('division', 'Law and Law Enforcement')
            ->where('is_active', true)
            ->exists();

        abort_unless($isAssignedToLegal, 404);

        $legalDetail = LegalCaseDetail::firstOrCreate(
            [
                'dcfms_case_id' => $case->id,
            ],
            [
                'legal_status' => 'Received by Legal Division',
            ]
        );

        $legalOfficers = User::query()
            ->where('division', 'Law and Law Enforcement')
            ->where('role', 'legal_officer')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $investigationOfficers = User::query()
            ->where('division', 'Law and Law Enforcement')
            ->where('role', 'investigation_officer')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'legal.show',
            compact(
                'case',
                'legalDetail',
                'legalOfficers',
                'investigationOfficers'
            )
        );
    }

    public function update(Request $request, DcfmsCase $case)
    {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, [
                'legal_director',
                'legal_officer',
                'investigation_officer',
                'director_general',
                'system_admin',
            ]),
            403
        );

        $legalDetail = LegalCaseDetail::firstOrCreate([
            'dcfms_case_id' => $case->id,
        ]);

        $validated = $request->validate([
            're_number' => ['nullable', 'string', 'max:100'],

            'legal_officer_id' => [
                'nullable',
                'exists:users,id',
            ],

            'investigation_officer_id' => [
                'nullable',
                'exists:users,id',
            ],

            'legal_status' => [
                'required',
                'string',
                'max:255',
            ],

            'inquiry_started_date' => [
                'nullable',
                'date',
            ],

            'observation_requested_date' => [
                'nullable',
                'date',
            ],

            'observation_due_date' => [
                'nullable',
                'date',
            ],

            'first_reminder_date' => [
                'nullable',
                'date',
            ],

            'second_reminder_date' => [
                'nullable',
                'date',
            ],

            'field_visit_required' => [
                'nullable',
                'boolean',
            ],

            'field_visit_date' => [
                'nullable',
                'date',
            ],

            'io_findings' => [
                'nullable',
                'string',
            ],

            'legal_recommendation' => [
                'nullable',
                'string',
            ],

            'case_conference_required' => [
                'nullable',
                'boolean',
            ],

            'case_conference_date' => [
                'nullable',
                'date',
            ],

            'case_conference_outcome' => [
                'nullable',
                'string',
                'max:255',
            ],

            'board_submission_required' => [
                'nullable',
                'boolean',
            ],

            'board_submission_date' => [
                'nullable',
                'date',
            ],

            'board_decision' => [
                'nullable',
                'string',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use (
            $legalDetail,
            $validated,
            $case
        ) {
            $legalDetail->update([
                ...$validated,

                'field_visit_required' =>
                    request()->boolean('field_visit_required'),

                'case_conference_required' =>
                    request()->boolean('case_conference_required'),

                'board_submission_required' =>
                    request()->boolean('board_submission_required'),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Shared Case Timeline
            |--------------------------------------------------------------------------
            |
            | Keep timeline/audit-style records in English so the stored case
            | history remains consistent regardless of the user's UI locale.
            |
            */

            CaseStatusHistory::create([
                'dcfms_case_id' => $case->id,
                'status' => $validated['legal_status'],
                'action_taken' => 'Legal workflow updated',
                'remarks' => $validated['remarks'] ?? null,
                'division' => 'Law and Law Enforcement',
                'updated_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Master Case Status
            |--------------------------------------------------------------------------
            */

            $case->update([
                'current_status' => 'Under Processing',
            ]);
        });

        return redirect()
            ->route('legal.show', $case)
            ->with(
                'success',
                __('legal_messages.workflow_updated_successfully')
            );
    }
}
