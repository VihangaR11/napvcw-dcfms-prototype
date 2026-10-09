<?php



namespace App\Http\Controllers;



use App\Models\CaseAssignment;



use App\Models\DcfmsCase;



use App\Models\User;



use App\Services\CaseAgingService;
use Illuminate\Http\Request;



class DashboardController extends Controller

{

    public function __construct(

        private readonly CaseAgingService $aging

    ) {

    }



    /*



    |--------------------------------------------------------------------------



    | Dashboard



    |--------------------------------------------------------------------------



    */



    public function index(Request $request)
    {



        $user = auth()->user();



        abort_unless(



            $user,



            401



        );



        /*



        |--------------------------------------------------------------------------



        | Shared Dashboard Data



        |--------------------------------------------------------------------------



        */



        $dashboardAlerts = [];



        $pendingActions = collect();

        /*
        |--------------------------------------------------------------------------
        | Director General Analytics Defaults
        |--------------------------------------------------------------------------
        */

        $dgDecisionCases = collect();

        $dgAnalytics = [
            'period' => 'all',
            'year' => null,
            'month' => null,
            'dimension' => 'complaint_category',
            'available_years' => [],
            'summary' => [
                'total' => 0,
                'registered' => 0,
                'routed' => 0,
                'under_processing' => 0,
                'awaiting_decision' => 0,
                'closed' => 0,
            ],
            'status_chart' => [
                'labels' => [],
                'values' => [],
            ],
            'dimension_chart' => [
                'labels' => [],
                'values' => [],
            ],
        ];

        $canRegisterCase =
            $user->role !==
            'director_general';




        /*



        |--------------------------------------------------------------------------



        | Board Secretary



        |--------------------------------------------------------------------------



        |



        | Primary responsibility:



        | - newly registered cases



        | - routing



        |



        */



        if (



            $user->role ===



            'board_secretary'



        ) {



            $registeredCases =



                DcfmsCase::query()



                    ->where(



                        'current_status',



                        'Registered'



                    )



                    ->latest()



                    ->take(10)



                    ->get();



            foreach (



                $registeredCases



                as $case



            ) {



                $pendingActions->push([



                    'priority' =>



                        $case->urgency === 'Critical'



                            ? 'critical'



                            : (



                                $case->urgency === 'Urgent'



                                    ? 'high'



                                    : 'normal'



                            ),



                    'type' =>



                        'routing',



                    'title' =>



                        'Case awaiting routing',



                    'description' =>



                        $case->case_number .



                        ' has been registered and requires division routing.',



                    'case_number' =>



                        $case->case_number,



                    'case_id' =>



                        $case->id,



                    'action_label' =>



                        'Route Case',



                    'action_url' =>



                        route(



                            'cases.route.form',



                            $case



                        ),



                ]);



            }



            $dashboardAlerts[



                'awaiting_routing'



            ] =



                DcfmsCase::query()



                    ->where(



                        'current_status',



                        'Registered'



                    )



                    ->count();



        }



        /*



        |--------------------------------------------------------------------------



        | Director General



        |--------------------------------------------------------------------------



        |



        | Primary decision queue:



        | - final protection decisions



        | - interim protection requests



        |



        */
        if (
            $user->role ===
            'director_general'
        ) {

            /*
            |--------------------------------------------------------------------------
            | DG Protection Decision Queue
            |--------------------------------------------------------------------------
            |
            | A case appears here after the Director - Police Protection records
            | the final threat recommendation and before the DG records the
            | protection type decision.
            |
            */

            $dgDecisionCases =
                DcfmsCase::query()
                    ->with([
                        'threatAssessmentDirector',
                        'protectionDetail',
                    ])
                    ->where(
                        'current_status',
                        'Awaiting Decision'
                    )
                    ->whereNotNull(
                        'threat_assessment_status'
                    )
                    ->whereNull(
                        'dg_protection_type'
                    )
                    ->latest(
                        'updated_at'
                    )
                    ->take(25)
                    ->get();

            foreach (
                $dgDecisionCases
                as $case
            ) {
                $pendingActions->push([
                    'priority' =>
                        in_array(
                            $case->threat_assessment_status,
                            [
                                'Very High',
                                'High',
                            ],
                            true
                        )
                            ? 'critical'
                            : 'high',

                    'type' =>
                        'protection_decision',

                    'title' =>
                        'Protection decision required',

                    'description' =>
                        $case->case_number .
                        ' has a Director - Police Protection threat recommendation of ' .
                        $case->threat_assessment_status .
                        ' and requires a Director General protection decision.',

                    'case_number' =>
                        $case->case_number,

                    'case_id' =>
                        $case->id,

                    'action_label' =>
                        'Decide Protection',

                    'action_url' =>
                        route(
                            'cases.show',
                            $case
                        ),
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Interim Protection Requests
            |--------------------------------------------------------------------------
            */

            $interimCases =
                DcfmsCase::query()
                    ->where(
                        'interim_protection_approved',
                        false
                    )
                    ->whereNull(
                        'threat_assessment_status'
                    )
                    ->whereHas(
                        'protectionDetail',
                        function ($query) {
                            $query->where(
                                'interim_protection_required',
                                true
                            );
                        }
                    )
                    ->latest()
                    ->take(10)
                    ->get();

            foreach (
                $interimCases
                as $case
            ) {
                $pendingActions->push([
                    'priority' =>
                        'critical',

                    'type' =>
                        'interim_protection',

                    'title' =>
                        'Interim protection decision required',

                    'description' =>
                        $case->case_number .
                        ' has an interim protection request awaiting Director General decision.',

                    'case_number' =>
                        $case->case_number,

                    'case_id' =>
                        $case->id,

                    'action_label' =>
                        'Review Protection',

                    'action_url' =>
                        route(
                            'cases.show',
                            $case
                        ),
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Director General Decision Analytics
            |--------------------------------------------------------------------------
            |
            | The filters affect charts only. They never modify case data.
            |
            */

            $allowedPeriods = [
                'all',
                'year',
                'month',
            ];

            $allowedDimensions = [
                'current_status',
                'urgency',
                'complaint_category',
                'primary_division',
                'threat_assessment_status',
                'dg_protection_type',
            ];

            $period =
                in_array(
                    $request->string(
                        'dg_period'
                    )->toString(),
                    $allowedPeriods,
                    true
                )
                    ? $request->string(
                        'dg_period'
                    )->toString()
                    : 'all';

            $dimension =
                in_array(
                    $request->string(
                        'dg_dimension'
                    )->toString(),
                    $allowedDimensions,
                    true
                )
                    ? $request->string(
                        'dg_dimension'
                    )->toString()
                    : 'complaint_category';

            $availableYears =
                DcfmsCase::query()
                    ->whereNotNull(
                        'created_at'
                    )
                    ->selectRaw(
                        'EXTRACT(YEAR FROM created_at)::int AS year'
                    )
                    ->distinct()
                    ->orderByDesc(
                        'year'
                    )
                    ->pluck(
                        'year'
                    )
                    ->map(
                        fn ($year) =>
                            (int) $year
                    )
                    ->values()
                    ->all();

            $selectedYear =
                $request->integer(
                    'dg_year'
                );

            if (
                !$selectedYear ||
                !in_array(
                    $selectedYear,
                    $availableYears,
                    true
                )
            ) {
                $selectedYear =
                    $availableYears[0]
                    ?? (int) now()->year;
            }

            $selectedMonth =
                $request->integer(
                    'dg_month'
                );

            if (
                $selectedMonth < 1 ||
                $selectedMonth > 12
            ) {
                $selectedMonth =
                    (int) now()->month;
            }

            $analyticsQuery =
                DcfmsCase::query();

            if (
                $period ===
                'year'
            ) {
                $analyticsQuery
                    ->whereYear(
                        'created_at',
                        $selectedYear
                    );
            }

            if (
                $period ===
                'month'
            ) {
                $analyticsQuery
                    ->whereYear(
                        'created_at',
                        $selectedYear
                    )
                    ->whereMonth(
                        'created_at',
                        $selectedMonth
                    );
            }

            $summaryQuery =
                clone $analyticsQuery;

            $dgSummary = [
                'total' =>
                    (clone $summaryQuery)
                        ->count(),

                'registered' =>
                    (clone $summaryQuery)
                        ->where(
                            'current_status',
                            'Registered'
                        )
                        ->count(),

                'routed' =>
                    (clone $summaryQuery)
                        ->where(
                            'current_status',
                            'Routed'
                        )
                        ->count(),

                'under_processing' =>
                    (clone $summaryQuery)
                        ->where(
                            'current_status',
                            'Under Processing'
                        )
                        ->count(),

                'awaiting_decision' =>
                    (clone $summaryQuery)
                        ->where(
                            'current_status',
                            'Awaiting Decision'
                        )
                        ->count(),

                'closed' =>
                    (clone $summaryQuery)
                        ->where(
                            'current_status',
                            'Closed'
                        )
                        ->count(),
            ];

            $statusRows =
                (clone $analyticsQuery)
                    ->selectRaw(
                        "COALESCE(current_status, 'Not Recorded') AS label, COUNT(*) AS total"
                    )
                    ->groupBy(
                        'current_status'
                    )
                    ->orderByDesc(
                        'total'
                    )
                    ->get();

            $dimensionRows =
                (clone $analyticsQuery)
                    ->selectRaw(
                        "COALESCE({$dimension}, 'Not Recorded') AS label, COUNT(*) AS total"
                    )
                    ->groupBy(
                        $dimension
                    )
                    ->orderByDesc(
                        'total'
                    )
                    ->get();

            $dgAnalytics = [
                'period' =>
                    $period,

                'year' =>
                    $selectedYear,

                'month' =>
                    $selectedMonth,

                'dimension' =>
                    $dimension,

                'available_years' =>
                    $availableYears,

                'summary' =>
                    $dgSummary,

                'status_chart' => [
                    'labels' =>
                        $statusRows
                            ->pluck(
                                'label'
                            )
                            ->values()
                            ->all(),

                    'values' =>
                        $statusRows
                            ->pluck(
                                'total'
                            )
                            ->map(
                                fn ($value) =>
                                    (int) $value
                            )
                            ->values()
                            ->all(),
                ],

                'dimension_chart' => [
                    'labels' =>
                        $dimensionRows
                            ->pluck(
                                'label'
                            )
                            ->values()
                            ->all(),

                    'values' =>
                        $dimensionRows
                            ->pluck(
                                'total'
                            )
                            ->map(
                                fn ($value) =>
                                    (int) $value
                            )
                            ->values()
                            ->all(),
                ],
            ];

            $dashboardAlerts[
                'awaiting_decision'
            ] =
                $dgDecisionCases
                    ->count();

            $dashboardAlerts[
                'interim_protection_requests'
            ] =
                $interimCases
                    ->count();
        }


        /*



        |--------------------------------------------------------------------------



        | Legal Director



        |--------------------------------------------------------------------------



        */



        if (



            $user->role ===



            'legal_director'



        ) {



            $assignments =



                CaseAssignment::query()



                    ->with('case')



                    ->where(



                        'division',



                        'Law and Law Enforcement'



                    )



                    ->where(



                        'is_active',



                        true



                    )



                    ->whereNull(



                        'assigned_user_id'



                    )



                    ->latest()



                    ->take(10)



                    ->get();



            foreach (



                $assignments



                as $assignment



            ) {



                if (



                    !$assignment->case



                ) {



                    continue;



                }



                $pendingActions->push([



                    'priority' =>



                        $this->casePriority(



                            $assignment->case



                        ),



                    'type' =>



                        'officer_assignment',



                    'title' =>



                        'Legal officer assignment required',



                    'description' =>



                        $assignment->case->case_number .



                        ' has been routed to Law & Law Enforcement but has no responsible officer.',



                    'case_number' =>



                        $assignment->case->case_number,



                    'case_id' =>



                        $assignment->case->id,



                    'action_label' =>



                        'Assign Officer',



                    'action_url' =>



                        route(



                            'cases.assignments.form',



                            [



                                $assignment->case,



                                $assignment,



                            ]



                        ),



                ]);



            }



            $dashboardAlerts[



                'unassigned_cases'



            ] =



                CaseAssignment::query()



                    ->where(



                        'division',



                        'Law and Law Enforcement'



                    )



                    ->where(



                        'is_active',



                        true



                    )



                    ->whereNull(



                        'assigned_user_id'



                    )



                    ->count();



        }



        /*



        |--------------------------------------------------------------------------



        | Assistant Director - Protection Services



        |--------------------------------------------------------------------------



        |



        | Internal role key remains protection_director.



        |



        */



        if (



            $user->role ===



            'protection_director'



        ) {



            $assignments =



                CaseAssignment::query()



                    ->with('case')



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



                    ->whereNull(



                        'assigned_user_id'



                    )



                    ->latest()



                    ->take(10)



                    ->get();



            foreach (



                $assignments



                as $assignment



            ) {



                if (



                    !$assignment->case



                ) {



                    continue;



                }



                $pendingActions->push([



                    'priority' =>



                        $this->casePriority(



                            $assignment->case



                        ),



                    'type' =>



                        'officer_assignment',



                    'title' =>



                        'Protection assignment required',



                    'description' =>



                        $assignment->case->case_number .



                        ' requires an officer assignment for ' .



                        $assignment->division .



                        '.',



                    'case_number' =>



                        $assignment->case->case_number,



                    'case_id' =>



                        $assignment->case->id,



                    'action_label' =>



                        'Assign Officer',



                    'action_url' =>



                        route(



                            'cases.assignments.form',



                            [



                                $assignment->case,



                                $assignment,



                            ]



                        ),



                ]);



            }



            $dashboardAlerts[



                'unassigned_cases'



            ] =



                CaseAssignment::query()



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



                    ->whereNull(



                        'assigned_user_id'



                    )



                    ->count();



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



            /*



            |--------------------------------------------------------------------------



            | Completed Assessment But No Threat Recommendation



            |--------------------------------------------------------------------------



            */



            $cases =



                DcfmsCase::query()



                    ->whereHas(



                        'assignments',



                        function ($query) {



                            $query



                                ->where(



                                    'division',



                                    'Police Protection'



                                )



                                ->where(



                                    'is_active',



                                    true



                                );



                        }



                    )



                    ->whereHas(



                        'policeProtectionDetail',



                        function ($query) {



                            $query->where(



                                'assessment_status',



                                'Assessment Completed'



                            );



                        }



                    )



                    ->whereNull(



                        'threat_assessment_status'



                    )



                    ->latest()



                    ->take(10)



                    ->get();



            foreach (



                $cases



                as $case



            ) {



                $pendingActions->push([



                    'priority' =>



                        'high',



                    'type' =>



                        'threat_recommendation',



                    'title' =>



                        'Threat recommendation required',



                    'description' =>



                        $case->case_number .



                        ' has a completed assessment but no Director threat recommendation.',



                    'case_number' =>



                        $case->case_number,



                    'case_id' =>



                        $case->id,



                    'action_label' =>



                        'Assess Threat',



                    'action_url' =>



                        route(



                            'police-protection.show',



                            $case



                        ),



                ]);



            }



            $dashboardAlerts[



                'pending_recommendations'



            ] =



                $cases->count();



        }



        /*



        |--------------------------------------------------------------------------



        | Operational Officers



        |--------------------------------------------------------------------------



        */



        if (



            in_array(



                $user->role,



                [



                    'legal_officer',



                    'investigation_officer',



                    'protection_officer',



                    'police_protection_officer',



                ],



                true



            )



        ) {



            $assignments =



                CaseAssignment::query()



                    ->with('case')



                    ->where(



                        'assigned_user_id',



                        $user->id



                    )



                    ->where(



                        'is_active',



                        true



                    )



                    ->whereHas(



                        'case',



                        function ($query) {



                            $query->where(



                                'current_status',



                                '!=',



                                'Closed'



                            );



                        }



                    )



                    ->latest()



                    ->take(10)



                    ->get();



            foreach (



                $assignments



                as $assignment



            ) {



                if (



                    !$assignment->case



                ) {



                    continue;



                }



                $pendingActions->push([



                    'priority' =>



                        $this->casePriority(



                            $assignment->case



                        ),



                    'type' =>



                        'assigned_case',



                    'title' =>



                        'Assigned case requires attention',



                    'description' =>



                        $assignment->case->case_number .



                        ' is assigned to you for ' .



                        $assignment->division .



                        '.',



                    'case_number' =>



                        $assignment->case->case_number,



                    'case_id' =>



                        $assignment->case->id,



                    'action_label' =>



                        'Open Case',



                    'action_url' =>



                        route(



                            'cases.show',



                            $assignment->case



                        ),



                ]);



            }



            $dashboardAlerts[



                'my_active_cases'



            ] =



                $assignments->count();



        }



        /*



        |--------------------------------------------------------------------------



        | System Administrator



        |--------------------------------------------------------------------------



        */



        if (



            $user->role ===



            'system_admin'



        ) {



            $pendingUsers =



                User::query()



                    ->where(



                        'account_status',



                        'pending'



                    )



                    ->where(



                        'role',



                        '!=',



                        'system_admin'



                    )



                    ->latest()



                    ->take(10)



                    ->get();



            foreach (



                $pendingUsers



                as $pendingUser



            ) {



                $pendingActions->push([



                    'priority' =>



                        'normal',



                    'type' =>



                        'account_approval',



                    'title' =>



                        'Account approval required',



                    'description' =>



                        $pendingUser->name .



                        ' (' .



                        $pendingUser->employee_number .



                        ') is awaiting account approval.',



                    'case_number' =>



                        null,



                    'case_id' =>



                        null,



                    'action_label' =>



                        'Review Account',



                    'action_url' =>



                        route(



                            'admin.users.index',



                            [



                                'status' =>



                                    'pending',



                            ]



                        ),



                ]);



            }



            $dashboardAlerts[



                'pending_accounts'



            ] =



                User::query()



                    ->where(



                        'account_status',



                        'pending'



                    )



                    ->where(



                        'role',



                        '!=',



                        'system_admin'



                    )



                    ->count();



        }



        /*



        |--------------------------------------------------------------------------



        | Cases Requiring Follow-Up Review



        |--------------------------------------------------------------------------



        |



        | This is an activity-monitoring indicator only.



        | It is NOT an SLA, deadline, overdue determination, or completion



        | target.



        |



        */



        if (



            in_array(



                $user->role,



                [



                    'director_general',



                    'board_secretary',



                    'legal_director',



                    'protection_director',



                    'police_protection_director',



                ],



                true



            )



        ) {



            $monitoringQuery =



                DcfmsCase::query()



                    ->where(



                        'current_status',



                        '!=',



                        'Closed'



                    )



                    ->with([



                        'statusHistory' => function ($query) {



                            $query->orderByDesc(



                                'created_at'



                            );



                        },



                        'assignments' => function ($query) {



                            $query->where(



                                'is_active',



                                true



                            );



                        },



                    ]);



            /*



            |--------------------------------------------------------------------------



            | Restrict Division Directors to Their Operational Scope



            |--------------------------------------------------------------------------



            */



            if (



                $user->role ===



                'legal_director'



            ) {



                $monitoringQuery->whereHas(



                    'assignments',



                    function ($query) {



                        $query



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



                            );



                    }



                );



            }



            if (



                $user->role ===



                'protection_director'



            ) {



                $monitoringQuery->whereHas(



                    'assignments',



                    function ($query) {



                        $query



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



                            );



                    }



                );



            }



            if (



                $user->role ===



                'police_protection_director'



            ) {



                $monitoringQuery->whereHas(



                    'assignments',



                    function ($query) {



                        $query



                            ->where(



                                'division',



                                'Police Protection'



                            )



                            ->where(



                                'is_active',



                                true



                            );



                    }



                );



            }



            /*



            |--------------------------------------------------------------------------



            | Evaluate Activity Monitoring



            |--------------------------------------------------------------------------



            */



            $casesForMonitoring =



                $monitoringQuery



                    ->latest(



                        'updated_at'



                    )



                    ->get();



            $followUpReviewCases =



                $casesForMonitoring



                    ->filter(



                        function (



                            DcfmsCase $case



                        ) {



                            return



                                $this->aging



                                    ->requiresFollowUp(



                                        $case



                                    );



                        }



                    )



                    ->values();



            $dashboardAlerts[



                'follow_up_review_cases'



            ] =



                $followUpReviewCases->count();



            /*



            |--------------------------------------------------------------------------



            | Add Follow-Up Items to Pending Actions



            |--------------------------------------------------------------------------



            */



            foreach (



                $followUpReviewCases



                    ->take(10)



                as $case



            ) {



                $summary =



                    $this->aging->summary(



                        $case



                    );



                $inactiveDays =



                    $summary[



                        'inactive_days'



                    ];



                $pendingActions->push([



                    'priority' =>



                        'normal',



                    'type' =>



                        'follow_up_review',



                    'title' =>



                        'Follow-up review suggested',



                    'description' =>



                        $case->case_number .



                        ' has no recorded activity for ' .



                        $inactiveDays .



                        (



                            $inactiveDays === 1



                                ? ' day.'



                                : ' days.'



                        ),



                    'case_number' =>



                        $case->case_number,



                    'case_id' =>



                        $case->id,



                    'action_label' =>



                        'Review Case',



                    'action_url' =>



                        route(



                            'cases.show',



                            $case



                        ),



                ]);



            }



        }



        /*



        |--------------------------------------------------------------------------



        | Priority Ordering



        |--------------------------------------------------------------------------



        */



        $priorityOrder = [



            'critical' => 1,



            'high' => 2,



            'normal' => 3,



        ];



        $pendingActions =



            $pendingActions



                ->sortBy(



                    fn ($item) =>



                        $priorityOrder[



                            $item['priority']



                        ]



                        ?? 99



                )



                ->values();



        return view(



            'dashboard',



            compact(
                'dashboardAlerts',
                'pendingActions',
                'dgDecisionCases',
                'dgAnalytics',
                'canRegisterCase'
            )



        );



    }



    /*



    |--------------------------------------------------------------------------



    | Determine Operational Priority



    |--------------------------------------------------------------------------



    */



    private function casePriority(



        DcfmsCase $case



    ): string {



        if (



            $case->urgency ===



            'Critical'



        ) {



            return 'critical';



        }



        if (



            $case->urgency ===



            'Urgent'



        ) {



            return 'high';



        }



        if (



            in_array(



                $case->threat_assessment_status,



                [



                    'Very High',



                    'High',



                ],



                true



            )



        ) {



            return 'critical';



        }



        return 'normal';



    }



}