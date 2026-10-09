<?php







namespace App\Http\Controllers;







use App\Enums\CaseStatus;



use App\Models\CaseAssignment;



use App\Models\CaseStatusHistory;



use App\Models\DcfmsCase;



use App\Models\User;



use App\Services\AuditLogService;



use App\Services\CaseAgingService;



use App\Services\CaseLifecycleService;



use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



use Illuminate\Http\Request;



use Illuminate\Support\Facades\DB;



use Illuminate\Validation\Rule;



class CaseController extends Controller



{



    use AuthorizesRequests;







    public function __construct(



        private readonly CaseLifecycleService $lifecycle,



        private readonly AuditLogService $audit,



        private readonly CaseAgingService $aging



    ) {



    }







    /*



    |--------------------------------------------------------------------------



    | Supported Operational Divisions



    |--------------------------------------------------------------------------



    */







    private const DIVISIONS = [



        'Law and Law Enforcement',



        'Protection Services',



        'Police Protection',



        'Assistance Services',



    ];





    /*



    |--------------------------------------------------------------------------



    | Institution-Wide Read Roles



    |--------------------------------------------------------------------------



    */







    private const INSTITUTION_WIDE_READ_ROLES = [



        'director_general',



        'board_secretary',



        'chairman',



        'policy_director',



    ];





    /*



    |--------------------------------------------------------------------------



    | Display Case Registry



    |--------------------------------------------------------------------------



    */







    public function index(Request $request)



    {



        $this->authorize(



            'viewAny',



            DcfmsCase::class



        );







        $user = auth()->user();







        abort_unless(



            $user,



            401



        );







        $query = DcfmsCase::query()



            ->with([



                'creator',



                'assignments.assignedUser',



                'threatAssessmentDirector',



                'statusHistory' => function ($query) {



                    $query->orderByDesc(

                        'created_at'

                    );



                },



            ]);





        /*



        |--------------------------------------------------------------------------



        | Role-Based Visibility



        |--------------------------------------------------------------------------



        */







        if (



            !in_array(



                $user->role,



                self::INSTITUTION_WIDE_READ_ROLES,



                true



            )



        ) {



            $this->applyRoleVisibility(



                $query,



                $user



            );



        }





        /*



        |--------------------------------------------------------------------------



        | Search



        |--------------------------------------------------------------------------



        */







        if (



            $request->filled(



                'search'



            )



        ) {



            $search = trim(



                (string) $request->input(



                    'search'



                )



            );







            $query->where(



                function ($subQuery) use ($search) {



                    $subQuery



                        ->where(



                            'case_number',



                            'ilike',



                            "%{$search}%"



                        )



                        ->orWhere(



                            'complainant_name',



                            'ilike',



                            "%{$search}%"



                        )



                        ->orWhere(



                            'complaint_summary',



                            'ilike',



                            "%{$search}%"



                        )



                        ->orWhere(



                            'complaint_category',



                            'ilike',



                            "%{$search}%"



                        );



                }



            );



        }





        /*



        |--------------------------------------------------------------------------



        | Filters



        |--------------------------------------------------------------------------



        */







        if (



            $request->filled(



                'status'



            )



        ) {



            $query->where(



                'current_status',



                $request->input(



                    'status'



                )



            );



        }





        if (



            $request->filled(



                'division'



            )



        ) {



            $division =



                $request->input(



                    'division'



                );







            if (



                in_array(



                    $division,



                    self::DIVISIONS,



                    true



                )



            ) {



                $query->whereHas(



                    'assignments',



                    function ($assignmentQuery) use ($division) {



                        $assignmentQuery



                            ->where(



                                'division',



                                $division



                            )



                            ->where(



                                'is_active',



                                true



                            );



                    }



                );



            }



        }





        if (



            $request->filled(



                'urgency'



            )



        ) {



            $query->where(



                'urgency',



                $request->input(



                    'urgency'



                )



            );



        }





        if (



            $request->filled(



                'threat_status'



            )



        ) {



            $query->where(



                'threat_assessment_status',



                $request->input(



                    'threat_status'



                )



            );



        }





        /*



        |--------------------------------------------------------------------------



        | Results



        |--------------------------------------------------------------------------



        */







        $cases = $query



            ->latest(



                'created_at'



            )



            ->paginate(15)



            ->withQueryString();





        /*

        |--------------------------------------------------------------------------

        | Attach SLA / Aging Summary to Registry Results

        |--------------------------------------------------------------------------

        */



        $cases

            ->getCollection()

            ->transform(

                function (DcfmsCase $case) {



                    $case->aging_summary =

                        $this->aging->summary(

                            $case

                        );



                    return $case;

                }

            );





        return view(



            'cases.index',



            compact(



                'cases'



            )



        );



    }





    /*



    |--------------------------------------------------------------------------



    | Show New Case Registration Form



    |--------------------------------------------------------------------------



    */







    public function create()



    {



        $this->authorize(



            'create',



            DcfmsCase::class



        );







        return view(



            'cases.create'



        );



    }





    /*



    |--------------------------------------------------------------------------



    | Store Newly Registered Case



    |--------------------------------------------------------------------------



    */







    public function store(Request $request)



    {



        $this->authorize(



            'create',



            DcfmsCase::class



        );







        $user = auth()->user();







        abort_unless(



            $user,



            401



        );





        $validated = $request->validate([



            'received_date' => [



                'required',



                'date',



            ],







            'complaint_source' => [



                'required',



                'string',



                'max:255',



            ],







            'complaint_mode' => [



                'nullable',



                'string',



                'max:255',



            ],







            'complainant_name' => [



                'nullable',



                'string',



                'max:255',



            ],







            'victim_witness_type' => [



                'nullable',



                'string',



                'max:255',



            ],







            'contact_number' => [



                'nullable',



                'string',



                'max:30',



            ],







            'email' => [



                'nullable',



                'email',



                'max:255',



            ],







            'complaint_summary' => [



                'required',



                'string',



            ],







            'complaint_category' => [



                'required',



                'string',



                'max:255',



            ],







            'urgency' => [



                'required',



                Rule::in([



                    'Normal',



                    'Urgent',



                    'Critical',



                ]),



            ],







            'primary_division' => [



                'nullable',



                Rule::in(



                    self::DIVISIONS



                ),



            ],



        ]);





        $case = DB::transaction(



            function () use (



                $validated,



                $user



            ) {



                $year =



                    now()->year;





                /*



                |--------------------------------------------------------------------------



                | Generate Case Number



                |--------------------------------------------------------------------------



                */







                $lastCase =



                    DcfmsCase::query()



                        ->whereYear(



                            'created_at',



                            $year



                        )



                        ->orderByDesc(



                            'id'



                        )



                        ->lockForUpdate()



                        ->first();





                $sequence = 1;





                if ($lastCase) {



                    $parts = explode(



                        '/',



                        $lastCase->case_number



                    );







                    $lastSequence =



                        (int) end(



                            $parts



                        );







                    $sequence =



                        $lastSequence + 1;



                }





                $caseNumber =



                    'NAPVCW/' .



                    $year .



                    '/' .



                    str_pad(



                        $sequence,



                        5,



                        '0',



                        STR_PAD_LEFT



                    );





                /*



                |--------------------------------------------------------------------------



                | Create Master Case



                |--------------------------------------------------------------------------



                */







                $case = DcfmsCase::create([



                    'case_number' =>



                        $caseNumber,







                    'received_date' =>



                        $validated[



                            'received_date'



                        ],







                    'complaint_source' =>



                        $validated[



                            'complaint_source'



                        ],







                    'complaint_mode' =>



                        $validated[



                            'complaint_mode'



                        ]



                        ?? null,







                    'complainant_name' =>



                        $validated[



                            'complainant_name'



                        ]



                        ?? null,







                    'victim_witness_type' =>



                        $validated[



                            'victim_witness_type'



                        ]



                        ?? null,







                    'contact_number' =>



                        $validated[



                            'contact_number'



                        ]



                        ?? null,







                    'email' =>



                        $validated[



                            'email'



                        ]



                        ?? null,







                    'complaint_summary' =>



                        $validated[



                            'complaint_summary'



                        ],







                    'complaint_category' =>



                        $validated[



                            'complaint_category'



                        ],







                    'urgency' =>



                        $validated[



                            'urgency'



                        ],







                    'primary_division' =>



                        $validated[



                            'primary_division'



                        ]



                        ?? null,







                    'current_status' =>



                        CaseStatus::REGISTERED->value,







                    'created_by' =>



                        $user->id,



                ]);





                /*



                |--------------------------------------------------------------------------



                | Human-Readable Timeline



                |--------------------------------------------------------------------------



                */







                CaseStatusHistory::create([



                    'dcfms_case_id' =>



                        $case->id,







                    'status' =>



                        CaseStatus::REGISTERED->value,







                    'action_taken' =>



                        'Case registered in DCFMS',







                    'remarks' =>



                        'Initial complaint record created.',







                    'division' =>



                        'Board Secretariat',







                    'updated_by' =>



                        $user->id,



                ]);





                /*



                |--------------------------------------------------------------------------



                | Structured Audit Log



                |--------------------------------------------------------------------------



                |



                | Intentionally excludes complaint summary, phone and email.



                |



                */







                $this->audit->record(



                    'CASE_REGISTERED',



                    $case,



                    [],



                    [



                        'case_number' =>



                            $case->case_number,







                        'received_date' =>



                            $case->received_date



                                ? $case->received_date->format(



                                    'Y-m-d'



                                )



                                : null,







                        'complaint_source' =>



                            $case->complaint_source,







                        'complaint_category' =>



                            $case->complaint_category,







                        'urgency' =>



                            $case->urgency,







                        'primary_division' =>



                            $case->primary_division,







                        'current_status' =>



                            $case->current_status,







                        'created_by' =>



                            $case->created_by,



                    ],



                    'New case registered in DCFMS.',



                    'Case Registry',



                    $case->id



                );





                return $case;



            }



        );





        return redirect()



            ->route(



                'cases.show',



                $case



            )



            ->with(



                'success',



                __('case_messages.registered_successfully')



            );



    }





    /*



    |--------------------------------------------------------------------------



    | Display One Master Case



    |--------------------------------------------------------------------------



    */







    public function show(DcfmsCase $case)



    {



        $this->authorize(



            'view',



            $case



        );





        $case->load([



            'creator',







            'assignments' => function ($query) {



                $query



                    ->orderByDesc(



                        'is_active'



                    )



                    ->orderBy(



                        'division'



                    );



            },







            'assignments.assignedUser',







            'statusHistory' => function ($query) {



                $query->orderByDesc(



                    'created_at'



                );



            },







            'statusHistory.updatedBy',







            'threatAssessmentDirector',







            'interimProtectionApprover',







            'finalProtectionApprover',



        ]);





        /*

        |--------------------------------------------------------------------------

        | Attach SLA / Aging Summary to Case Detail

        |--------------------------------------------------------------------------

        */



        $case->aging_summary =

            $this->aging->summary(

                $case

            );





        return view(



            'cases.show',



            compact(



                'case'



            )



        );



    }





    /*



    |--------------------------------------------------------------------------



    | Show Multi-Division Routing Form



    |--------------------------------------------------------------------------



    */







    public function routeForm(DcfmsCase $case)



    {



        $this->authorize(



            'route',



            $case



        );





        abort_if(



            $case->current_status ===



                CaseStatus::CLOSED->value,



            422,



            __('case_messages.closed_case_cannot_be_routed')



        );





        $divisions =



            self::DIVISIONS;





        $existingAssignments =



            $case



                ->assignments()



                ->where(



                    'is_active',



                    true



                )



                ->pluck(



                    'division'



                )



                ->filter()



                ->unique()



                ->values()



                ->toArray();





        $case->load([



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



        ]);





        return view(



            'cases.route',



            [



                'case' =>



                    $case,







                'divisions' =>



                    $divisions,







                'existingAssignments' =>



                    $existingAssignments,



            ]



        );



    }





    /*



    |--------------------------------------------------------------------------



    | Route Case to Operational Divisions



    |--------------------------------------------------------------------------



    */







    public function routeCase(



        Request $request,



        DcfmsCase $case



    ) {



        $this->authorize(



            'route',



            $case



        );







        $user = auth()->user();







        abort_unless(



            $user,



            401



        );





        abort_if(



            $case->current_status ===



                CaseStatus::CLOSED->value,



            422,



            __('case_messages.closed_case_cannot_be_routed')



        );





        $validated = $request->validate([



            'divisions' => [



                'required',



                'array',



                'min:1',



            ],







            'divisions.*' => [



                'required',



                Rule::in(



                    self::DIVISIONS



                ),



            ],







            'routing_note' => [



                'nullable',



                'string',



                'max:1000',



            ],



        ]);





        $selectedDivisions =



            collect(



                $validated[



                    'divisions'



                ]



            )



                ->unique()



                ->values();





        /*



        |--------------------------------------------------------------------------



        | Snapshot Existing Routing for Audit



        |--------------------------------------------------------------------------



        */







        $oldDivisions =



            $case



                ->assignments()



                ->where(



                    'is_active',



                    true



                )



                ->pluck(



                    'division'



                )



                ->filter()



                ->unique()



                ->sort()



                ->values()



                ->all();





        /*



        |--------------------------------------------------------------------------



        | Create New Division Assignments



        |--------------------------------------------------------------------------



        */







        DB::transaction(



            function () use (



                $validated,



                $selectedDivisions,



                $case,



                $user



            ) {



                foreach (



                    $selectedDivisions



                    as $division



                ) {



                    $exists =



                        CaseAssignment::query()



                            ->where(



                                'dcfms_case_id',



                                $case->id



                            )



                            ->where(



                                'division',



                                $division



                            )



                            ->where(



                                'is_active',



                                true



                            )



                            ->exists();





                    if ($exists) {



                        continue;



                    }





                    CaseAssignment::create([



                        'dcfms_case_id' =>



                            $case->id,







                        'division' =>



                            $division,







                        'assigned_user_id' =>



                            null,







                        'assigned_by' =>



                            $user->id,







                        'status' =>



                            'Assigned to Division',







                        'assigned_at' =>



                            now(),







                        'is_active' =>



                            true,



                    ]);





                    CaseStatusHistory::create([



                        'dcfms_case_id' =>



                            $case->id,







                        'status' =>



                            CaseStatus::ROUTED->value,







                        'action_taken' =>



                            "Case routed to {$division}",







                        'remarks' =>



                            $validated[



                                'routing_note'



                            ]



                            ?? null,







                        'division' =>



                            'Board Secretariat',







                        'updated_by' =>



                            $user->id,



                    ]);



                }





                /*



                |--------------------------------------------------------------------------



                | Set Primary Division If Missing



                |--------------------------------------------------------------------------



                */







                if (



                    empty(



                        $case->primary_division



                    )



                ) {



                    $case->update([



                        'primary_division' =>



                            $selectedDivisions->first(),



                    ]);



                }



            }



        );





        $case->refresh();





        /*



        |--------------------------------------------------------------------------



        | Audit Routing Change



        |--------------------------------------------------------------------------



        */







        $newDivisions =



            $case



                ->assignments()



                ->where(



                    'is_active',



                    true



                )



                ->pluck(



                    'division'



                )



                ->filter()



                ->unique()



                ->sort()



                ->values()



                ->all();





        if (



            $oldDivisions !==



            $newDivisions



        ) {



            $this->audit->record(



                'CASE_ROUTED',



                $case,



                [



                    'active_divisions' =>



                        $oldDivisions,



                ],



                [



                    'active_divisions' =>



                        $newDivisions,



                ],



                'Case routing updated.',



                'Case Routing',



                $case->id



            );



        }





        /*



        |--------------------------------------------------------------------------



        | Formal Lifecycle Transition



        |--------------------------------------------------------------------------



        */







        if (



            $case->current_status ===



            CaseStatus::REGISTERED->value



        ) {



            $case =



                $this->lifecycle->transition(



                    $case,



                    CaseStatus::ROUTED,



                    $user,



                    'Case routing completed',



                    'Case routed to one or more operational divisions.',



                    'Board Secretariat'



                );



        }





        return redirect()



            ->route(



                'cases.show',



                $case



            )



            ->with(



                'success',



                __('case_messages.routing_updated_successfully')



            );



    }





    /*



    |--------------------------------------------------------------------------



    | Show Officer Assignment Form



    |--------------------------------------------------------------------------



    */







    public function assignmentForm(



        DcfmsCase $case,



        CaseAssignment $assignment



    ) {



        abort_unless(



            $assignment->dcfms_case_id ===



                $case->id,



            404



        );





        abort_unless(



            $assignment->is_active,



            404



        );





        abort_if(



            $case->current_status ===



                CaseStatus::CLOSED->value,



            422,



            __('case_messages.closed_case_cannot_assign_officer')



        );





        $this->authorize(



            'assignOfficer',



            $assignment



        );





        $eligibleRoles =



            $this->eligibleRolesForDivision(



                $assignment->division



            );





        $officers =



            User::query()



                ->whereIn(



                    'role',



                    $eligibleRoles



                )



                ->where(



                    'is_active',



                    true



                )



                ->where(



                    'account_status',



                    'active'



                );





        if (



            $assignment->division !==



            'Assistance Services'



        ) {



            $officers->where(



                'division',



                $assignment->division



            );



        }





        $officers =



            $officers



                ->orderBy(



                    'name'



                )



                ->get();





        return view(



            'cases.assign-officer',



            compact(



                'case',



                'assignment',



                'officers'



            )



        );



    }





    /*



    |--------------------------------------------------------------------------



    | Assign Responsible Officer



    |--------------------------------------------------------------------------



    */







    public function assignOfficer(



        Request $request,



        DcfmsCase $case,



        CaseAssignment $assignment



    ) {



        abort_unless(



            $assignment->dcfms_case_id ===



                $case->id,



            404



        );





        abort_unless(



            $assignment->is_active,



            404



        );





        abort_if(



            $case->current_status ===



                CaseStatus::CLOSED->value,



            422,



            __('case_messages.closed_case_cannot_assign_officer')



        );





        $this->authorize(



            'assignOfficer',



            $assignment



        );





        $currentUser =



            auth()->user();







        abort_unless(



            $currentUser,



            401



        );





        $validated =



            $request->validate([



                'assigned_user_id' => [



                    'required',



                    'integer',



                    'exists:users,id',



                ],







                'assignment_note' => [



                    'nullable',



                    'string',



                    'max:1000',



                ],



            ]);





        $officer =



            User::findOrFail(



                $validated[



                    'assigned_user_id'



                ]



            );





        $eligibleRoles =



            $this->eligibleRolesForDivision(



                $assignment->division



            );





        abort_unless(



            in_array(



                $officer->role,



                $eligibleRoles,



                true



            ),



            422,



            __('case_messages.employee_not_eligible')



        );





        abort_unless(



            $officer->is_active &&



            $officer->account_status ===



                'active',



            422,



            __('case_messages.employee_account_not_active')



        );





        if (



            $assignment->division !==



            'Assistance Services'



        ) {



            abort_unless(



                $officer->division ===



                    $assignment->division,



                422,



                __('case_messages.employee_wrong_division')



            );



        }





        /*



        |--------------------------------------------------------------------------



        | Snapshot Existing Assignment for Audit



        |--------------------------------------------------------------------------



        */







        $oldAssignmentValues = [



            'assigned_user_id' =>



                $assignment->assigned_user_id,







            'assigned_by' =>



                $assignment->assigned_by,







            'status' =>



                $assignment->status,







            'assigned_at' =>



                $assignment->assigned_at



                    ? $assignment->assigned_at->toDateTimeString()



                    : null,



        ];





        /*



        |--------------------------------------------------------------------------



        | Perform Assignment



        |--------------------------------------------------------------------------



        */







        DB::transaction(



            function () use (



                $case,



                $assignment,



                $officer,



                $validated,



                $currentUser



            ) {



                $assignment->update([



                    'assigned_user_id' =>



                        $officer->id,







                    'assigned_by' =>



                        $currentUser->id,







                    'status' =>



                        'Officer Assigned',







                    'assigned_at' =>



                        now(),







                    'is_active' =>



                        true,



                ]);





                $assignment->refresh();





                CaseStatusHistory::create([



                    'dcfms_case_id' =>



                        $case->id,







                    'status' =>



                        'Officer Assigned',







                    'action_taken' =>



                        "Case assigned to {$officer->name}",







                    'remarks' =>



                        $validated[



                            'assignment_note'



                        ]



                        ?? null,







                    'division' =>



                        $assignment->division,







                    'updated_by' =>



                        $currentUser->id,



                ]);



            }



        );





        /*



        |--------------------------------------------------------------------------



        | Structured Assignment Audit



        |--------------------------------------------------------------------------



        */







        $this->audit->record(



            'OFFICER_ASSIGNED',



            $assignment,



            $oldAssignmentValues,



            [



                'assigned_user_id' =>



                    $assignment->assigned_user_id,







                'assigned_by' =>



                    $assignment->assigned_by,







                'status' =>



                    $assignment->status,







                'assigned_at' =>



                    $assignment->assigned_at



                        ? $assignment->assigned_at->toDateTimeString()



                        : null,



            ],



            "Responsible officer {$officer->name} assigned to {$assignment->division}.",



            'Case Assignment',



            $case->id



        );





        /*



        |--------------------------------------------------------------------------



        | Formal Lifecycle Transition



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



                    $currentUser,



                    'Case processing commenced',



                    "Responsible officer assigned for {$assignment->division}.",



                    $assignment->division



                );



        }





        return redirect()



            ->route(



                'cases.show',



                $case



            )



            ->with(



                'success',



                __('case_messages.officer_assigned_successfully')



            );



    }





    /*



    |--------------------------------------------------------------------------



    | Close Case



    |--------------------------------------------------------------------------



    */







    public function close(



        Request $request,



        DcfmsCase $case



    ) {



        $this->authorize(



            'close',



            $case



        );





        $user =



            auth()->user();







        abort_unless(



            $user,



            401



        );





        abort_if(



            $case->current_status ===



                CaseStatus::CLOSED->value,



            422,



            __('case_messages.case_already_closed')



        );





        $validated =



            $request->validate([



                'closure_reason' => [



                    'required',



                    'string',



                    'max:3000',



                ],



            ]);





        $case =



            $this->lifecycle->transition(



                $case,



                CaseStatus::CLOSED,



                $user,



                'Case closed',



                $validated[



                    'closure_reason'



                ],



                'Case Management'



            );





        return redirect()



            ->route(



                'cases.show',



                $case



            )



            ->with(



                'success',



                __('case_messages.closed_successfully')



            );



    }





    /*



    |--------------------------------------------------------------------------



    | Reopen Case



    |--------------------------------------------------------------------------



    */







    public function reopen(



        Request $request,



        DcfmsCase $case



    ) {



        $this->authorize(



            'reopen',



            $case



        );





        $user =



            auth()->user();







        abort_unless(



            $user,



            401



        );





        abort_unless(



            $case->current_status ===



                CaseStatus::CLOSED->value,



            422,



            __('case_messages.only_closed_can_reopen')



        );





        $validated =



            $request->validate([



                'reopen_reason' => [



                    'required',



                    'string',



                    'max:3000',



                ],



            ]);





        $case =



            $this->lifecycle->transition(



                $case,



                CaseStatus::UNDER_PROCESSING,



                $user,



                'Case reopened',



                $validated[



                    'reopen_reason'



                ],



                'Case Management'



            );





        return redirect()



            ->route(



                'cases.show',



                $case



            )



            ->with(



                'success',



                __('case_messages.reopened_successfully')



            );



    }





    /*



    |--------------------------------------------------------------------------



    | Apply Registry Visibility



    |--------------------------------------------------------------------------



    */







    private function applyRoleVisibility(



        $query,



        User $user



    ): void {



        /*



        |--------------------------------------------------------------------------



        | Division Leadership



        |--------------------------------------------------------------------------



        |



        | protection_director remains the internal role key for:



        | Assistant Director - Protection Services.



        |



        */







        $directorDivisions =



            match ($user->role) {



                'legal_director' => [



                    'Law and Law Enforcement',



                    'Assistance Services',



                ],







                'protection_director' => [



                    'Protection Services',



                    'Assistance Services',



                ],







                'police_protection_director' => [



                    'Police Protection',



                ],







                default => null,



            };





        if (



            $directorDivisions !==



            null



        ) {



            $query->whereHas(



                'assignments',



                function ($assignmentQuery) use (



                    $directorDivisions



                ) {



                    $assignmentQuery



                        ->whereIn(



                            'division',



                            $directorDivisions



                        )



                        ->where(



                            'is_active',



                            true



                        );



                }



            );







            return;



        }





        /*



        |--------------------------------------------------------------------------



        | Operational Officers



        |--------------------------------------------------------------------------



        */







        $officerDivisions =



            match ($user->role) {



                'legal_officer' => [



                    'Law and Law Enforcement',



                    'Assistance Services',



                ],







                'investigation_officer' => [



                    'Law and Law Enforcement',



                ],







                'protection_officer' => [



                    'Protection Services',



                    'Assistance Services',



                ],







                'police_protection_officer' => [



                    'Police Protection',



                ],







                default => null,



            };





        if (



            $officerDivisions !==



            null



        ) {



            $query->whereHas(



                'assignments',



                function ($assignmentQuery) use (



                    $officerDivisions,



                    $user



                ) {



                    $assignmentQuery



                        ->whereIn(



                            'division',



                            $officerDivisions



                        )



                        ->where(



                            'assigned_user_id',



                            $user->id



                        )



                        ->where(



                            'is_active',



                            true



                        );



                }



            );







            return;



        }





        /*



        |--------------------------------------------------------------------------



        | Unsupported Role



        |--------------------------------------------------------------------------



        */







        $query->whereRaw(



            '1 = 0'



        );



    }





    /*



    |--------------------------------------------------------------------------



    | Eligible Operational Roles per Division



    |--------------------------------------------------------------------------



    */







    private function eligibleRolesForDivision(



        string $division



    ): array {



        return match ($division) {



            'Law and Law Enforcement' => [



                'legal_officer',



                'investigation_officer',



            ],







            'Protection Services' => [



                'protection_officer',



            ],







            'Police Protection' => [



                'police_protection_officer',



            ],







            'Assistance Services' => [



                'legal_officer',



                'protection_officer',



            ],







            default => [],



        };



    }



}
