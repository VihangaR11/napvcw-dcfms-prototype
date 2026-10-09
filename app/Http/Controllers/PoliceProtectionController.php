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



class PoliceProtectionController extends Controller

{

    use AuthorizesRequests;



    public function __construct(

        private readonly CaseLifecycleService $lifecycle,

        private readonly AuditLogService $audit

    ) {

    }





    /*

    |--------------------------------------------------------------------------

    | Show Police Protection Workflow

    |--------------------------------------------------------------------------

    */



    public function show(DcfmsCase $case)

    {

        /*

        |--------------------------------------------------------------------------

        | Centralized RBAC

        |--------------------------------------------------------------------------

        */



        $this->authorize(

            'view',

            $case

        );



        $this->authorize(

            'updatePoliceProtection',

            $case

        );





        /*

        |--------------------------------------------------------------------------

        | Ensure Detail Record Exists

        |--------------------------------------------------------------------------

        */



        $detail =

            PoliceProtectionDetail::firstOrCreate(

                [

                    'dcfms_case_id' =>

                        $case->id,

                ],

                [

                    'assessment_status' =>

                        'Awaiting Request',

                ]

            );





        /*

        |--------------------------------------------------------------------------

        | Load Related Case Information

        |--------------------------------------------------------------------------

        */



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



            'interimProtectionApprover',



            'finalProtectionApprover',



            'statusHistory' => function ($query) {

                $query->orderByDesc(

                    'created_at'

                );

            },



            'statusHistory.updatedBy',

        ]);





        /*

        |--------------------------------------------------------------------------

        | Eligible Police Protection Officers

        |--------------------------------------------------------------------------

        */



        $officers =

            User::query()

                ->where(

                    'role',

                    'police_protection_officer'

                )

                ->where(

                    'division',

                    'Police Protection'

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

            'police-protection.show',

            [

                'case' =>

                    $case,



                'detail' =>

                    $detail,



                'officers' =>

                    $officers,

            ]

        );

    }





    /*

    |--------------------------------------------------------------------------

    | Director - Police Protection Threat Recommendation

    |--------------------------------------------------------------------------

    */



    public function updateThreatStatus(

        Request $request,

        DcfmsCase $case

    ) {

        /*

        |--------------------------------------------------------------------------

        | Centralized RBAC

        |--------------------------------------------------------------------------

        */



        $this->authorize(

            'recommendThreat',

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

        | Validate Threat Recommendation

        |--------------------------------------------------------------------------

        */



        $validated =

            $request->validate([

                'threat_assessment_status' => [

                    'required',

                    Rule::in([

                        'Very High',

                        'High',

                        'Low',

                        'Very Low',

                    ]),

                ],

            ]);





        /*

        |--------------------------------------------------------------------------

        | Snapshot Previous Values for Audit

        |--------------------------------------------------------------------------

        */



        $oldValues = [

            'threat_assessment_status' =>

                $case->threat_assessment_status,



            'threat_assessment_by' =>

                $case->threat_assessment_by,



            'threat_assessment_at' =>

                $case->threat_assessment_at

                    ? $case->threat_assessment_at->toDateTimeString()

                    : null,

        ];





        /*

        |--------------------------------------------------------------------------

        | Save Recommendation

        |--------------------------------------------------------------------------

        |

        | This does NOT change the case lifecycle status. After the operational

        | assessment is completed, the case remains "Awaiting Decision" until

        | the Director General makes the final protection decision.

        |

        */



        DB::transaction(

            function () use (

                $case,

                $validated,

                $user

            ) {

                $case->update([

                    'threat_assessment_status' =>

                        $validated[

                            'threat_assessment_status'

                        ],



                    'threat_assessment_by' =>

                        $user->id,



                    'threat_assessment_at' =>

                        now(),

                ]);





                CaseStatusHistory::create([

                    'dcfms_case_id' =>

                        $case->id,



                    'status' =>

                        $case->current_status,



                    'action_taken' =>

                        'Threat assessment status recommended',



                    'division' =>

                        'Police Protection',



                    'remarks' =>

                        'Threat assessment status recommended as ' .

                        $validated[

                            'threat_assessment_status'

                        ] .

                        ' by the Director - Police Protection.',



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

            'THREAT_LEVEL_RECOMMENDED',

            $case,

            $oldValues,

            [

                'threat_assessment_status' =>

                    $case->threat_assessment_status,



                'threat_assessment_by' =>

                    $case->threat_assessment_by,



                'threat_assessment_at' =>

                    $case->threat_assessment_at

                        ? $case->threat_assessment_at->toDateTimeString()

                        : null,

            ],

            'Director - Police Protection recorded the threat assessment recommendation.',

            'Police Protection',

            $case->id

        );





        return redirect()

            ->route(

                'police-protection.show',

                $case

            )

            ->with(

                'success',

                __('police_protection_messages.recommendation_updated_successfully')

            );

    }





    /*

    |--------------------------------------------------------------------------

    | Update Operational Police Protection Assessment

    |--------------------------------------------------------------------------

    */



    public function update(

        Request $request,

        DcfmsCase $case

    ) {

        /*

        |--------------------------------------------------------------------------

        | Centralized RBAC

        |--------------------------------------------------------------------------

        */



        $this->authorize(

            'updatePoliceProtection',

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

        | Validate Assessment Input

        |--------------------------------------------------------------------------

        */



        $validated =

            $request->validate([

                'police_protection_officer_id' => [

                    'nullable',

                    'integer',

                    'exists:users,id',

                ],



                'assessment_status' => [

                    'required',

                    Rule::in([

                        'Awaiting Request',

                        'Request Received',

                        'Assessment Started',

                        'Assessment In Progress',

                        'Assessment Completed',

                    ]),

                ],



                'request_received_date' => [

                    'nullable',

                    'date',

                ],



                'assessment_started_date' => [

                    'nullable',

                    'date',

                ],



                'assessment_completed_date' => [

                    'nullable',

                    'date',

                    'after_or_equal:assessment_started_date',

                ],



                'threat_level' => [

                    'nullable',

                    'string',

                    'max:100',

                ],



                'assessment_summary' => [

                    'nullable',

                    'string',

                    'max:5000',

                ],



                'recommendation' => [

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

        | Validate Selected Police Protection Officer

        |--------------------------------------------------------------------------

        */



        if (

            !empty(

                $validated[

                    'police_protection_officer_id'

                ]

            )

        ) {

            $selectedOfficer =

                User::findOrFail(

                    $validated[

                        'police_protection_officer_id'

                    ]

                );





            abort_unless(

                $selectedOfficer->role ===

                    'police_protection_officer',

                422,

                __('police_protection_messages.selected_employee_not_officer')

            );





            abort_unless(

                $selectedOfficer->division ===

                    'Police Protection',

                422,

                __('police_protection_messages.selected_employee_wrong_division')

            );





            abort_unless(

                $selectedOfficer->is_active &&

                $selectedOfficer->account_status ===

                    'active',

                422,

                __('police_protection_messages.selected_officer_account_inactive')

            );

        }





        /*

        |--------------------------------------------------------------------------

        | Ensure Detail Exists + Capture Previous Values

        |--------------------------------------------------------------------------

        */



        $detail =

            PoliceProtectionDetail::firstOrCreate(

                [

                    'dcfms_case_id' =>

                        $case->id,

                ],

                [

                    'assessment_status' =>

                        'Awaiting Request',

                ]

            );





        $oldValues = [

            'police_protection_officer_id' =>

                $detail->police_protection_officer_id,



            'assessment_status' =>

                $detail->assessment_status,



            'request_received_date' =>

                $detail->request_received_date

                    ? $detail->request_received_date->format('Y-m-d')

                    : null,



            'assessment_started_date' =>

                $detail->assessment_started_date

                    ? $detail->assessment_started_date->format('Y-m-d')

                    : null,



            'assessment_completed_date' =>

                $detail->assessment_completed_date

                    ? $detail->assessment_completed_date->format('Y-m-d')

                    : null,



            'threat_level' =>

                $detail->threat_level,



            'recommendation' =>

                $detail->recommendation,

        ];





        /*

        |--------------------------------------------------------------------------

        | Persist Assessment

        |--------------------------------------------------------------------------

        */



        DB::transaction(

            function () use (

                $case,

                $validated,

                $user,

                $detail

            ) {

                $detail->update(

                    $validated

                );





                /*

                |--------------------------------------------------------------------------

                | Human-Readable Timeline Entry

                |--------------------------------------------------------------------------

                */



                CaseStatusHistory::create([

                    'dcfms_case_id' =>

                        $case->id,



                    'status' =>

                        $validated[

                            'assessment_status'

                        ],



                    'action_taken' =>

                        'Police Protection assessment updated',



                    'remarks' =>

                        $validated[

                            'remarks'

                        ]

                        ?? null,



                    'division' =>

                        'Police Protection',



                    'updated_by' =>

                        $user->id,

                ]);





                /*

                |--------------------------------------------------------------------------

                | Push Completed Assessment to Protection Services

                |--------------------------------------------------------------------------

                */



                if (

                    $validated[

                        'assessment_status'

                    ] ===

                    'Assessment Completed'

                ) {

                    ProtectionCaseDetail::updateOrCreate(

                        [

                            'dcfms_case_id' =>

                                $case->id,

                        ],

                        [

                            'threat_assessment_status' =>

                                'Assessment Received',



                            'threat_assessment_received_date' =>

                                $validated[

                                    'assessment_completed_date'

                                ]

                                ?? now(),



                            'threat_level' =>

                                $validated[

                                    'threat_level'

                                ]

                                ?? null,



                            'threat_assessment_summary' =>

                                $validated[

                                    'assessment_summary'

                                ]

                                ?? null,

                        ]

                    );

                }

            }

        );





        /*

        |--------------------------------------------------------------------------

        | Structured Operational Assessment Audit

        |--------------------------------------------------------------------------

        |

        | The potentially sensitive free-text assessment summary and remarks are

        | deliberately not duplicated into AuditLog old/new JSON.

        |

        */



        $detail->refresh();



        $this->audit->record(

            'THREAT_ASSESSMENT_UPDATED',

            $detail,

            $oldValues,

            [

                'police_protection_officer_id' =>

                    $detail->police_protection_officer_id,



                'assessment_status' =>

                    $detail->assessment_status,



                'request_received_date' =>

                    $detail->request_received_date

                        ? $detail->request_received_date->format('Y-m-d')

                        : null,



                'assessment_started_date' =>

                    $detail->assessment_started_date

                        ? $detail->assessment_started_date->format('Y-m-d')

                        : null,



                'assessment_completed_date' =>

                    $detail->assessment_completed_date

                        ? $detail->assessment_completed_date->format('Y-m-d')

                        : null,



                'threat_level' =>

                    $detail->threat_level,



                'recommendation' =>

                    $detail->recommendation,

            ],

            'Police Protection operational threat assessment updated.',

            'Police Protection',

            $case->id

        );





        /*

        |--------------------------------------------------------------------------

        | Formal Lifecycle Transition

        |--------------------------------------------------------------------------

        |

        | Under Processing

        |        ↓

        | Awaiting Decision

        |

        */



        $case->refresh();





        if (

            $validated[

                'assessment_status'

            ] ===

                'Assessment Completed' &&

            $case->current_status ===

                CaseStatus::UNDER_PROCESSING->value

        ) {

            $case =

                $this->lifecycle->transition(

                    $case,

                    CaseStatus::AWAITING_DECISION,

                    $user,

                    'Threat assessment completed',

                    'Police Protection threat assessment completed. ' .

                    'Case is awaiting the Director - Police Protection ' .

                    'recommendation and subsequent protection decision.',

                    'Police Protection'

                );

        }





        return redirect()

            ->route(

                'police-protection.show',

                $case

            )

            ->with(

                'success',

                __('police_protection_messages.assessment_updated_successfully')

            );

    }

}
