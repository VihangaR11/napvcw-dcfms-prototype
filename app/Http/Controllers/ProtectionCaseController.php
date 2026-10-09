<?php







namespace App\Http\Controllers;







use App\Enums\CaseStatus;



use App\Models\CaseStatusHistory;



use App\Models\DcfmsCase;



use App\Models\PoliceProtectionDetail;



use App\Models\ProtectionCaseDetail;



use App\Models\User;



use App\Services\AuditLogService;



use App\Services\CaseLifecycleService;



use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



use Illuminate\Http\Request;



use Illuminate\Support\Facades\DB;



use Illuminate\Validation\Rule;







class ProtectionCaseController extends Controller



{



    use AuthorizesRequests;







    public function __construct(



        private readonly CaseLifecycleService $lifecycle,



        private readonly AuditLogService $audit



    ) {



    }











    /*



    |--------------------------------------------------------------------------



    | Show Protection Services Workflow



    |--------------------------------------------------------------------------



    */







    public function show(DcfmsCase $case)



    {



        $this->authorize(



            'view',



            $case



        );







        $this->authorize(



            'updateProtection',



            $case



        );











        $protectionDetail =



            ProtectionCaseDetail::firstOrCreate(



                [



                    'dcfms_case_id' =>



                        $case->id,



                ],



                [



                    'protection_status' =>



                        'Protection Request Received',



                ]



            );











        $case->load([



            'creator',







            'assignments' => function ($query) {



                $query



                    ->where(



                        'is_active',



                        true



                    )



                    ->orderBy(



                        'division'



                    );



            },







            'assignments.assignedUser',







            'threatAssessmentDirector',

            'dgProtectionDecisionMaker',







            'interimProtectionApprover',







            'finalProtectionApprover',







            'statusHistory' => function ($query) {



                $query->orderByDesc(



                    'created_at'



                );



            },







            'statusHistory.updatedBy',



        ]);











        $protectionOfficers =



            User::query()



                ->where(



                    'division',



                    'Protection Services'



                )



                ->where(



                    'role',



                    'protection_officer'



                )



                ->where(



                    'is_active',



                    true



                )



                ->where(



                    'account_status',



                    'active'



                )



                ->orderBy(



                    'name'



                )



                ->get();











        return view(



            'protection.show',



            compact(



                'case',



                'protectionDetail',



                'protectionOfficers'



            )



        );



    }











    /*



    |--------------------------------------------------------------------------



    | Director General - Interim Protection Decision



    |--------------------------------------------------------------------------



    */







    public function updateInterimProtection(



        Request $request,



        DcfmsCase $case



    ) {



        $this->authorize(



            'approveProtection',



            $case



        );







        $user =



            auth()->user();







        abort_unless(



            $user,



            401



        );











        /*



        |--------------------------------------------------------------------------



        | Interim Protection Must Be Before Final Threat Recommendation



        |--------------------------------------------------------------------------



        */







        abort_if(



            filled(



                $case->threat_assessment_status



            ),



            422,



            __('protection_messages.interim_only_before_final_recommendation')



        );











        $approved =



            $request->boolean(



                'interim_protection_approved'



            );











        /*



        |--------------------------------------------------------------------------



        | Snapshot Previous Values for Audit



        |--------------------------------------------------------------------------



        */







        $oldValues = [



            'interim_protection_approved' =>



                $case->interim_protection_approved,







            'interim_protection_approved_by' =>



                $case->interim_protection_approved_by,







            'interim_protection_approved_at' =>



                $case->interim_protection_approved_at



                    ? $case->interim_protection_approved_at->toDateTimeString()



                    : null,



        ];











        /*



        |--------------------------------------------------------------------------



        | Persist Decision



        |--------------------------------------------------------------------------



        */







        DB::transaction(



            function () use (



                $case,



                $approved,



                $user



            ) {



                $case->update([



                    'interim_protection_approved' =>



                        $approved,







                    'interim_protection_approved_by' =>



                        $approved



                            ? $user->id



                            : null,







                    'interim_protection_approved_at' =>



                        $approved



                            ? now()



                            : null,



                ]);











                CaseStatusHistory::create([



                    'dcfms_case_id' =>



                        $case->id,







                    'status' =>



                        $case->current_status,







                    'action_taken' =>



                        $approved



                            ? 'Interim protection approved'



                            : 'Interim protection approval withdrawn',







                    'division' =>



                        'Director General',







                    'remarks' =>



                        $approved



                            ? 'Director General approved Interim Protection.'



                            : 'Director General withdrew the Interim Protection approval.',







                    'updated_by' =>



                        $user->id,



                ]);



            }



        );











        /*



        |--------------------------------------------------------------------------



        | Structured Audit Log



        |--------------------------------------------------------------------------



        */







        $case->refresh();







        $this->audit->record(



            $approved



                ? 'INTERIM_PROTECTION_APPROVED'



                : 'INTERIM_PROTECTION_WITHDRAWN',



            $case,



            $oldValues,



            [



                'interim_protection_approved' =>



                    $case->interim_protection_approved,







                'interim_protection_approved_by' =>



                    $case->interim_protection_approved_by,







                'interim_protection_approved_at' =>



                    $case->interim_protection_approved_at



                        ? $case->interim_protection_approved_at->toDateTimeString()



                        : null,



            ],



            $approved



                ? 'Director General approved Interim Protection.'



                : 'Director General withdrew Interim Protection approval.',



            'Protection Services',



            $case->id



        );











        return back()->with(



            'success',



            $approved



                ? __('protection_messages.interim_approved_successfully')



                : __('protection_messages.interim_withdrawn_successfully')



        );



    }











    /*



    |--------------------------------------------------------------------------



    | Director General - Final Protection Decision



    |--------------------------------------------------------------------------



    */







    public function updateFinalProtection(



        Request $request,



        DcfmsCase $case



    ) {



        $this->authorize(



            'approveProtection',



            $case



        );







        $user =



            auth()->user();







        abort_unless(



            $user,



            401



        );











        /*



        |--------------------------------------------------------------------------



        | Final Protection Requires Threat Recommendation



        |--------------------------------------------------------------------------



        */







        abort_unless(



            filled(



                $case->threat_assessment_status



            ),



            422,



            __('protection_messages.final_requires_threat_recommendation')



        );











        /*



        |--------------------------------------------------------------------------



        | Validate Protection Types



        |--------------------------------------------------------------------------



        */







        $validated =



            $request->validate([



                'protection_types' => [



                    'required',



                    'array',



                    'min:1',



                ],







                'protection_types.*' => [



                    'required',



                    Rule::in([



                        'Close Protection',



                        'Body-to-Body Protection',



                        'On-site Protection',



                    ]),



                ],



            ]);











        $protectionTypes =



            collect(



                $validated[



                    'protection_types'



                ]



            )



                ->unique()



                ->values()



                ->all();











        /*



        |--------------------------------------------------------------------------



        | Snapshot Previous Values for Audit



        |--------------------------------------------------------------------------



        */







        $oldValues = [



            'approved_protection_types' =>



                $case->approved_protection_types,







            'final_protection_approved_by' =>



                $case->final_protection_approved_by,







            'final_protection_approved_at' =>



                $case->final_protection_approved_at



                    ? $case->final_protection_approved_at->toDateTimeString()



                    : null,



        ];











        /*



        |--------------------------------------------------------------------------



        | Save Director General Decision



        |--------------------------------------------------------------------------



        */







        DB::transaction(



            function () use (



                $case,



                $protectionTypes,



                $user



            ) {



                $case->update([



                    'approved_protection_types' =>



                        $protectionTypes,







                    'final_protection_approved_by' =>



                        $user->id,







                    'final_protection_approved_at' =>



                        now(),



                ]);











                CaseStatusHistory::create([



                    'dcfms_case_id' =>



                        $case->id,







                    'status' =>



                        $case->current_status,







                    'action_taken' =>



                        'Final protection measures approved',







                    'division' =>



                        'Director General',







                    'remarks' =>



                        'Approved protection measures: ' .



                        implode(



                            ', ',



                            $protectionTypes



                        ) .



                        '. Threat assessment status: ' .



                        $case->threat_assessment_status .



                        '.',







                    'updated_by' =>



                        $user->id,



                ]);



            }



        );











        /*



        |--------------------------------------------------------------------------



        | Structured Audit Log



        |--------------------------------------------------------------------------



        */







        $case->refresh();







        $this->audit->record(



            'FINAL_PROTECTION_APPROVED',



            $case,



            $oldValues,



            [



                'approved_protection_types' =>



                    $case->approved_protection_types,







                'final_protection_approved_by' =>



                    $case->final_protection_approved_by,







                'final_protection_approved_at' =>



                    $case->final_protection_approved_at



                        ? $case->final_protection_approved_at->toDateTimeString()



                        : null,



            ],



            'Director General approved final protection measures.',



            'Protection Services',



            $case->id



        );











        /*



        |--------------------------------------------------------------------------



        | Formal Lifecycle Transition



        |--------------------------------------------------------------------------



        |



        | Awaiting Decision



        |       ↓



        | Under Processing



        |



        */







        if (



            $case->current_status ===



            CaseStatus::AWAITING_DECISION->value



        ) {



            $case =



                $this->lifecycle->transition(



                    $case,



                    CaseStatus::UNDER_PROCESSING,



                    $user,



                    'Protection implementation commenced',



                    'Director General approved the final protection measures. ' .



                    'The case has returned to active processing for implementation and monitoring.',



                    'Protection Services'



                );



        }











        return redirect()



            ->route(



                'protection.show',



                $case



            )



            ->with(



                'success',



                __('protection_messages.final_measures_approved_successfully')



            );



    }











    /*



    |--------------------------------------------------------------------------



    | Update Protection Services Workflow



    |--------------------------------------------------------------------------



    */








    /*
    |--------------------------------------------------------------------------
    | Director General - Protection Type Decision
    |--------------------------------------------------------------------------
    |
    | Exactly one DG protection type is selected after the Director - Police
    | Protection records the threat recommendation:
    | - Interim Protection
    | - Body-to-Body Protection
    | - Close Protection
    |
    */

    public function updateDgProtectionDecision(
        Request $request,
        DcfmsCase $case
    ) {
        $this->authorize(
            'approveProtection',
            $case
        );

        $user =
            auth()->user();

        abort_unless(
            $user,
            401
        );

        abort_unless(
            $user->role ===
                'director_general',
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Threat Recommendation Required
        |--------------------------------------------------------------------------
        */

        abort_unless(
            filled(
                $case->threat_assessment_status
            ),
            422,
            __('protection_messages.dg_decision_requires_threat_recommendation')
        );

        /*
        |--------------------------------------------------------------------------
        | Case Must Be Awaiting DG Decision
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $case->current_status ===
                CaseStatus::AWAITING_DECISION->value,
            422,
            __('protection_messages.dg_decision_requires_awaiting_decision')
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Single Protection Type
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([
                'dg_protection_type' => [
                    'required',
                    'string',
                    Rule::in([
                        'Interim Protection',
                        'Body-to-Body Protection',
                        'Close Protection',
                    ]),
                ],
            ]);

        $protectionType =
            $validated[
                'dg_protection_type'
            ];

        /*
        |--------------------------------------------------------------------------
        | Snapshot Previous Values
        |--------------------------------------------------------------------------
        */

        $oldValues = [
            'dg_protection_type' =>
                $case->dg_protection_type,

            'dg_protection_decided_by' =>
                $case->dg_protection_decided_by,

            'dg_protection_decided_at' =>
                $case->dg_protection_decided_at
                    ? $case->dg_protection_decided_at->toDateTimeString()
                    : null,

            'approved_protection_types' =>
                $case->approved_protection_types,

            'final_protection_approved_by' =>
                $case->final_protection_approved_by,

            'final_protection_approved_at' =>
                $case->final_protection_approved_at
                    ? $case->final_protection_approved_at->toDateTimeString()
                    : null,
        ];

        /*
        |--------------------------------------------------------------------------
        | Save DG Protection Decision
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $case,
                $user,
                $protectionType
            ) {
                $decisionTime =
                    now();

                $case->update([
                    'dg_protection_type' =>
                        $protectionType,

                    'dg_protection_decided_by' =>
                        $user->id,

                    'dg_protection_decided_at' =>
                        $decisionTime,

                    /*
                    |--------------------------------------------------------------------------
                    | Backward Compatibility With Existing Final-Approval Fields
                    |--------------------------------------------------------------------------
                    */

                    'approved_protection_types' =>
                        [
                            $protectionType,
                        ],

                    'final_protection_approved_by' =>
                        $user->id,

                    'final_protection_approved_at' =>
                        $decisionTime,
                ]);

                CaseStatusHistory::create([
                    'dcfms_case_id' =>
                        $case->id,

                    'status' =>
                        $case->current_status,

                    'action_taken' =>
                        'Director General protection type selected',

                    'division' =>
                        'Director General',

                    'remarks' =>
                        'Director General selected ' .
                        $protectionType .
                        ' as the protection type. ' .
                        'Threat assessment status: ' .
                        $case->threat_assessment_status .
                        '.',

                    'updated_by' =>
                        $user->id,
                ]);
            }
        );

        $case->refresh();

        /*
        |--------------------------------------------------------------------------
        | Structured Audit Log
        |--------------------------------------------------------------------------
        */

        $this->audit->record(
            'DG_PROTECTION_TYPE_SELECTED',
            $case,
            $oldValues,
            [
                'dg_protection_type' =>
                    $case->dg_protection_type,

                'dg_protection_decided_by' =>
                    $case->dg_protection_decided_by,

                'dg_protection_decided_at' =>
                    $case->dg_protection_decided_at
                        ? $case->dg_protection_decided_at->toDateTimeString()
                        : null,

                'approved_protection_types' =>
                    $case->approved_protection_types,

                'final_protection_approved_by' =>
                    $case->final_protection_approved_by,

                'final_protection_approved_at' =>
                    $case->final_protection_approved_at
                        ? $case->final_protection_approved_at->toDateTimeString()
                        : null,
            ],
            'Director General selected the final protection type.',
            'Protection Services',
            $case->id
        );

        /*
        |--------------------------------------------------------------------------
        | Return Case to Active Implementation
        |--------------------------------------------------------------------------
        */

        if (
            $case->current_status ===
                CaseStatus::AWAITING_DECISION->value
        ) {
            $case =
                $this->lifecycle->transition(
                    $case,
                    CaseStatus::UNDER_PROCESSING,
                    $user,
                    'Protection implementation commenced',
                    'Director General selected ' .
                    $protectionType .
                    '. The case returned to active processing for implementation and monitoring.',
                    'Protection Services'
                );
        }

        return redirect()
            ->route(
                'dashboard'
            )
            ->with(
                'success',
                __('protection_messages.dg_protection_type_saved', [
                    'type' =>
                        $protectionType,
                ])
            );
    }


    public function update(



        Request $request,



        DcfmsCase $case



    ) {



        $this->authorize(



            'updateProtection',



            $case



        );







        $user =



            auth()->user();







        abort_unless(



            $user,



            401



        );











        /*



        |--------------------------------------------------------------------------



        | Validate Workflow



        |--------------------------------------------------------------------------



        */







        $validated =



            $request->validate([



                'protection_officer_id' => [



                    'nullable',



                    'integer',



                    'exists:users,id',



                ],







                'protection_status' => [



                    'required',



                    'string',



                    'max:255',



                ],







                'threat_type' => [



                    'nullable',



                    'string',



                    'max:255',



                ],







                'threat_assessment_requested_date' => [



                    'nullable',



                    'date',



                ],







                'police_reference' => [



                    'nullable',



                    'string',



                    'max:255',



                ],







                'local_police_station' => [



                    'nullable',



                    'string',



                    'max:255',



                ],







                'interim_protection_requested_date' => [



                    'nullable',



                    'date',



                ],







                'threat_assessment_status' => [



                    'nullable',



                    'string',



                    'max:255',



                ],







                'threat_assessment_received_date' => [



                    'nullable',



                    'date',



                ],







                'threat_level' => [



                    'nullable',



                    'string',



                    'max:100',



                ],







                'threat_assessment_summary' => [



                    'nullable',



                    'string',



                    'max:5000',



                ],







                'protection_decision' => [



                    'nullable',



                    'string',



                    'max:5000',



                ],







                'protection_start_date' => [



                    'nullable',



                    'date',



                ],







                'review_date' => [



                    'nullable',



                    'date',



                    'after_or_equal:protection_start_date',



                ],







                'protection_outcome' => [



                    'nullable',



                    'string',



                    'max:5000',



                ],







                'remarks' => [



                    'nullable',



                    'string',



                    'max:3000',



                ],



            ]);











        /*



        |--------------------------------------------------------------------------



        | Validate Selected Protection Officer



        |--------------------------------------------------------------------------



        */







        if (



            !empty(



                $validated[



                    'protection_officer_id'



                ]



            )



        ) {



            $selectedOfficer =



                User::findOrFail(



                    $validated[



                        'protection_officer_id'



                    ]



                );











            abort_unless(



                $selectedOfficer->role ===



                    'protection_officer',



                422,



                __('protection_messages.selected_employee_not_protection_officer')



            );











            abort_unless(



                $selectedOfficer->division ===



                    'Protection Services',



                422,



                __('protection_messages.selected_employee_wrong_division')



            );











            abort_unless(



                $selectedOfficer->is_active &&



                $selectedOfficer->account_status ===



                    'active',



                422,



                __('protection_messages.selected_officer_account_inactive')



            );



        }











        /*



        |--------------------------------------------------------------------------



        | Ensure Detail Exists + Snapshot Previous Values



        |--------------------------------------------------------------------------



        */







        $detail =



            ProtectionCaseDetail::firstOrCreate(



                [



                    'dcfms_case_id' =>



                        $case->id,



                ]



            );











        $oldValues = [



            'protection_officer_id' =>



                $detail->protection_officer_id,







            'protection_status' =>



                $detail->protection_status,







            'threat_type' =>



                $detail->threat_type,







            'threat_assessment_requested_date' =>



                $detail->threat_assessment_requested_date



                    ? $detail->threat_assessment_requested_date->format('Y-m-d')



                    : null,







            'interim_protection_required' =>



                $detail->interim_protection_required,







            'threat_assessment_status' =>



                $detail->threat_assessment_status,







            'threat_assessment_received_date' =>



                $detail->threat_assessment_received_date



                    ? $detail->threat_assessment_received_date->format('Y-m-d')



                    : null,







            'threat_level' =>



                $detail->threat_level,







            'protection_start_date' =>



                $detail->protection_start_date



                    ? $detail->protection_start_date->format('Y-m-d')



                    : null,







            'review_date' =>



                $detail->review_date



                    ? $detail->review_date->format('Y-m-d')



                    : null,



        ];











        /*



        |--------------------------------------------------------------------------



        | Persist Workflow



        |--------------------------------------------------------------------------



        */







        DB::transaction(



            function () use (



                $request,



                $validated,



                $case,



                $user,



                $detail



            ) {



                $detail->update([



                    ...$validated,







                    'interim_protection_required' =>



                        $request->boolean(



                            'interim_protection_required'



                        ),



                ]);











                CaseStatusHistory::create([



                    'dcfms_case_id' =>



                        $case->id,







                    'status' =>



                        $validated[



                            'protection_status'



                        ],







                    'action_taken' =>



                        'Protection workflow updated',







                    'remarks' =>



                        $validated[



                            'remarks'



                        ]



                        ?? null,







                    'division' =>



                        'Protection Services',







                    'updated_by' =>



                        $user->id,



                ]);











                /*



                |--------------------------------------------------------------------------



                | Threat Assessment Request



                |--------------------------------------------------------------------------



                */







                if (



                    !empty(



                        $validated[



                            'threat_assessment_requested_date'



                        ]



                    )



                ) {



                    PoliceProtectionDetail::updateOrCreate(



                        [



                            'dcfms_case_id' =>



                                $case->id,



                        ],



                        [



                            'assessment_status' =>



                                'Request Received',







                            'request_received_date' =>



                                $validated[



                                    'threat_assessment_requested_date'



                                ],



                        ]



                    );



                }



            }



        );











        /*



        |--------------------------------------------------------------------------



        | Structured Workflow Audit



        |--------------------------------------------------------------------------



        |



        | Sensitive narrative fields such as assessment summary, protection



        | decision, outcome and remarks are deliberately not copied into the



        | old/new audit JSON.



        |



        */







        $detail->refresh();







        $this->audit->record(



            'PROTECTION_WORKFLOW_UPDATED',



            $detail,



            $oldValues,



            [



                'protection_officer_id' =>



                    $detail->protection_officer_id,







                'protection_status' =>



                    $detail->protection_status,







                'threat_type' =>



                    $detail->threat_type,







                'threat_assessment_requested_date' =>



                    $detail->threat_assessment_requested_date



                        ? $detail->threat_assessment_requested_date->format('Y-m-d')



                        : null,







                'interim_protection_required' =>



                    $detail->interim_protection_required,







                'threat_assessment_status' =>



                    $detail->threat_assessment_status,







                'threat_assessment_received_date' =>



                    $detail->threat_assessment_received_date



                        ? $detail->threat_assessment_received_date->format('Y-m-d')



                        : null,







                'threat_level' =>



                    $detail->threat_level,







                'protection_start_date' =>



                    $detail->protection_start_date



                        ? $detail->protection_start_date->format('Y-m-d')



                        : null,







                'review_date' =>



                    $detail->review_date



                        ? $detail->review_date->format('Y-m-d')



                        : null,



            ],



            'Protection Services workflow updated.',



            'Protection Services',



            $case->id



        );











        /*



        |--------------------------------------------------------------------------



        | Formal Lifecycle Fallback



        |--------------------------------------------------------------------------



        */







        $case->refresh();







        if (



            $case->current_status ===



            CaseStatus::ROUTED->value



        ) {



            $case =



                $this->lifecycle->transition(



                    $case,



                    CaseStatus::UNDER_PROCESSING,



                    $user,



                    'Protection Services processing commenced',



                    'Protection Services workflow activity has commenced.',



                    'Protection Services'



                );



        }











        return redirect()



            ->route(



                'protection.show',



                $case



            )



            ->with(



                'success',



                __('protection_messages.workflow_updated_successfully')



            );



    }



}
