<?php

namespace App\Http\Controllers;

use App\Models\CaseAssignment;
use App\Models\CaseStatusHistory;
use App\Models\DcfmsCase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaseController extends Controller
{
    /**
     * Display the case registry.
     *
     * Access rules:
     * - Board Secretary, DG, System Admin: all cases
     * - Division Directors: all active cases assigned to their division
     * - Operational Officers: only cases personally assigned to them
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = DcfmsCase::query()
            ->with([
                'creator',
                'assignments.assignedUser',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Role-based case visibility
        |--------------------------------------------------------------------------
        */

        if (!in_array($user->role, [
            'system_admin',
            'director_general',
            'board_secretary',
        ])) {

            $query->whereHas('assignments', function ($assignmentQuery) use ($user) {

                $assignmentQuery
                    ->where('division', $user->division)
                    ->where('is_active', true);

                /*
                |--------------------------------------------------------------------------
                | Operational officers only see cases assigned specifically to them.
                |--------------------------------------------------------------------------
                */

                if (in_array($user->role, [
                    'legal_officer',
                    'investigation_officer',
                    'protection_officer',
                    'police_protection_officer',
                    'assistance_officer',
                ])) {

                    $assignmentQuery->where(
                        'assigned_user_id',
                        $user->id
                    );
                }
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
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
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'current_status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Division filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('division')) {

            $division = $request->division;

            $query->whereHas('assignments', function ($assignmentQuery) use ($division) {

                $assignmentQuery
                    ->where('division', $division)
                    ->where('is_active', true);
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Urgency filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('urgency')) {
            $query->where(
                'urgency',
                $request->urgency
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Results
        |--------------------------------------------------------------------------
        */

        $cases = $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();


        return view(
            'cases.index',
            compact('cases')
        );
    }


    /**
     * Show new case registration form.
     */
    public function create()
    {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, [
                'board_secretary',
                'director_general',
                'system_admin',
            ]),
            403
        );

        return view('cases.create');
    }


    /**
     * Store a newly registered case.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, [
                'board_secretary',
                'director_general',
                'system_admin',
            ]),
            403
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
                'string',
                'in:Normal,Urgent,Critical',
            ],

            'primary_division' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);


        $case = DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Generate case number
            |--------------------------------------------------------------------------
            */

            $year = now()->year;

            $lastCase = DcfmsCase::query()
                ->whereYear('created_at', $year)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $sequence = 1;

            if ($lastCase) {

                $parts = explode('/', $lastCase->case_number);

                $lastSequence = (int) end($parts);

                $sequence = $lastSequence + 1;
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
            | Create master case record
            |--------------------------------------------------------------------------
            */

            $case = DcfmsCase::create([
                'case_number' => $caseNumber,
                'received_date' => $validated['received_date'],
                'complaint_source' => $validated['complaint_source'],
                'complaint_mode' => $validated['complaint_mode'] ?? null,
                'complainant_name' => $validated['complainant_name'] ?? null,
                'victim_witness_type' => $validated['victim_witness_type'] ?? null,
                'contact_number' => $validated['contact_number'] ?? null,
                'email' => $validated['email'] ?? null,
                'complaint_summary' => $validated['complaint_summary'],
                'complaint_category' => $validated['complaint_category'],
                'urgency' => $validated['urgency'],
                'primary_division' => $validated['primary_division'] ?? null,
                'current_status' => 'Registered',
                'created_by' => auth()->id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Initial timeline record
            |--------------------------------------------------------------------------
            */

            CaseStatusHistory::create([
                'dcfms_case_id' => $case->id,
                'status' => 'Registered',
                'action_taken' => 'Case registered in DCFMS',
                'remarks' => 'Initial complaint record created.',
                'division' => 'Board Secretariat',
                'updated_by' => auth()->id(),
            ]);


            return $case;
        });


        return redirect()
            ->route('cases.show', $case)
            ->with(
                'success',
                'Case registered successfully.'
            );
    }


    /**
     * Display one master case record.
     */
    public function show(DcfmsCase $case)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Access control
        |--------------------------------------------------------------------------
        */

        if (!in_array($user->role, [
            'system_admin',
            'director_general',
            'board_secretary',
        ])) {

            $hasAccess = $case->assignments()
                ->where('division', $user->division)
                ->where('is_active', true)
                ->when(
                    in_array($user->role, [
                        'legal_officer',
                        'investigation_officer',
                        'protection_officer',
                        'police_protection_officer',
                        'assistance_officer',
                    ]),
                    function ($query) use ($user) {
                        $query->where(
                            'assigned_user_id',
                            $user->id
                        );
                    }
                )
                ->exists();

            abort_unless(
                $hasAccess,
                403
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Load related data
        |--------------------------------------------------------------------------
        */

        $case->load([
            'creator',

            'assignments' => function ($query) {
                $query
                    ->orderByDesc('is_active')
                    ->orderBy('division');
            },

            'assignments.assignedUser',

            'statusHistory' => function ($query) {
                $query->orderByDesc('created_at');
            },

            'statusHistory.updatedBy',
        ]);


        return view(
            'cases.show',
            compact('case')
        );
    }


    /**
     * Show multi-division routing form.
     */
    public function routeForm(DcfmsCase $case)
    {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, [
                'board_secretary',
                'director_general',
                'system_admin',
            ]),
            403
        );


        $case->load('assignments');


        $divisions = [
            'Law and Law Enforcement',
            'Protection Services',
            'Police Protection',
            'Assistance Services',
        ];


        $existingAssignments = $case->assignments
            ->where('is_active', true)
            ->pluck('division')
            ->toArray();


        return view(
            'cases.route',
            compact(
                'case',
                'divisions',
                'existingAssignments'
            )
        );
    }


    /**
     * Route one master case to multiple operational divisions.
     */
    public function routeCase(
        Request $request,
        DcfmsCase $case
    ) {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, [
                'board_secretary',
                'director_general',
                'system_admin',
            ]),
            403
        );


        $validated = $request->validate([
            'divisions' => [
                'required',
                'array',
                'min:1',
            ],

            'divisions.*' => [
                'required',
                'string',
                'in:Law and Law Enforcement,Protection Services,Police Protection,Assistance Services',
            ],

            'routing_note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        DB::transaction(function () use (
            $validated,
            $case
        ) {

            foreach (
                $validated['divisions']
                as $division
            ) {

                /*
                |--------------------------------------------------------------------------
                | Prevent duplicate active assignments
                |--------------------------------------------------------------------------
                */

                $exists = CaseAssignment::query()
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


                /*
                |--------------------------------------------------------------------------
                | Create division assignment
                |--------------------------------------------------------------------------
                */

                CaseAssignment::create([
                    'dcfms_case_id' => $case->id,
                    'division' => $division,
                    'assigned_user_id' => null,
                    'assigned_by' => auth()->id(),
                    'status' => 'Assigned to Division',
                    'assigned_at' => now(),
                    'is_active' => true,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Shared timeline entry
                |--------------------------------------------------------------------------
                */

                CaseStatusHistory::create([
                    'dcfms_case_id' => $case->id,
                    'status' => 'Routed',
                    'action_taken' =>
                        "Case routed to {$division}",
                    'remarks' =>
                        $validated['routing_note'] ?? null,
                    'division' =>
                        'Board Secretariat',
                    'updated_by' =>
                        auth()->id(),
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Update overall case status
            |--------------------------------------------------------------------------
            */

            $case->update([
                'current_status' => 'Routed',
            ]);
        });


        return redirect()
            ->route(
                'cases.show',
                $case
            )
            ->with(
                'success',
                'Case routed successfully.'
            );
    }


    /**
     * Show officer assignment form for one division.
     */
    public function assignmentForm(
        DcfmsCase $case,
        CaseAssignment $assignment
    ) {
        /*
        |--------------------------------------------------------------------------
        | Ensure assignment belongs to this case
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $assignment->dcfms_case_id === $case->id,
            404
        );


        $currentUser = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | Determine who can assign officers
        |--------------------------------------------------------------------------
        */

        $allowed = match ($assignment->division) {

            'Law and Law Enforcement' =>
                in_array(
                    $currentUser->role,
                    [
                        'legal_director',
                        'director_general',
                        'system_admin',
                    ]
                ),

            'Protection Services' =>
                in_array(
                    $currentUser->role,
                    [
                        'protection_director',
                        'director_general',
                        'system_admin',
                    ]
                ),

            'Police Protection' =>
                in_array(
                    $currentUser->role,
                    [
                        'board_secretary',
                        'director_general',
                        'system_admin',
                    ]
                ),

            'Assistance Services' =>
                in_array(
                    $currentUser->role,
                    [
                        'board_secretary',
                        'director_general',
                        'system_admin',
                    ]
                ),

            default => false,
        };


        abort_unless(
            $allowed,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Eligible roles for each division
        |--------------------------------------------------------------------------
        */

        $eligibleRoles = match ($assignment->division) {

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
                'assistance_officer',
            ],

            default => [],
        };


        /*
        |--------------------------------------------------------------------------
        | Load eligible active officers
        |--------------------------------------------------------------------------
        */

        $officers = User::query()
            ->where(
                'division',
                $assignment->division
            )
            ->whereIn(
                'role',
                $eligibleRoles
            )
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
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


    /**
     * Assign responsible officer to a division workflow.
     */
    public function assignOfficer(
        Request $request,
        DcfmsCase $case,
        CaseAssignment $assignment
    ) {
        abort_unless(
            $assignment->dcfms_case_id === $case->id,
            404
        );


        $currentUser = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | Authorization
        |--------------------------------------------------------------------------
        */

        $allowed = match ($assignment->division) {

            'Law and Law Enforcement' =>
                in_array(
                    $currentUser->role,
                    [
                        'legal_director',
                        'director_general',
                        'system_admin',
                    ]
                ),

            'Protection Services' =>
                in_array(
                    $currentUser->role,
                    [
                        'protection_director',
                        'director_general',
                        'system_admin',
                    ]
                ),

            'Police Protection' =>
                in_array(
                    $currentUser->role,
                    [
                        'board_secretary',
                        'director_general',
                        'system_admin',
                    ]
                ),

            'Assistance Services' =>
                in_array(
                    $currentUser->role,
                    [
                        'board_secretary',
                        'director_general',
                        'system_admin',
                    ]
                ),

            default => false,
        };


        abort_unless(
            $allowed,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
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


        /*
        |--------------------------------------------------------------------------
        | Load officer
        |--------------------------------------------------------------------------
        */

        $officer = User::findOrFail(
            $validated['assigned_user_id']
        );


        /*
        |--------------------------------------------------------------------------
        | Ensure officer belongs to correct division
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $officer->division === $assignment->division,
            422
        );


        /*
        |--------------------------------------------------------------------------
        | Ensure officer has valid role
        |--------------------------------------------------------------------------
        */

        $eligibleRoles = match ($assignment->division) {

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
                'assistance_officer',
            ],

            default => [],
        };


        abort_unless(
            in_array(
                $officer->role,
                $eligibleRoles
            ),
            422
        );


        /*
        |--------------------------------------------------------------------------
        | Ensure officer account is active
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $officer->is_active,
            422
        );


        /*
        |--------------------------------------------------------------------------
        | Perform assignment
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $case,
            $assignment,
            $officer,
            $validated
        ) {

            $assignment->update([
                'assigned_user_id' =>
                    $officer->id,

                'assigned_by' =>
                    auth()->id(),

                'status' =>
                    'Officer Assigned',

                'assigned_at' =>
                    now(),

                'is_active' =>
                    true,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Add shared timeline entry
            |--------------------------------------------------------------------------
            */

            CaseStatusHistory::create([
                'dcfms_case_id' =>
                    $case->id,

                'status' =>
                    'Officer Assigned',

                'action_taken' =>
                    "Case assigned to {$officer->name}",

                'remarks' =>
                    $validated['assignment_note'] ?? null,

                'division' =>
                    $assignment->division,

                'updated_by' =>
                    auth()->id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Update overall case state
            |--------------------------------------------------------------------------
            */

            if (in_array(
                $case->current_status,
                [
                    'Registered',
                    'Routed',
                ]
            )) {

                $case->update([
                    'current_status' =>
                        'Under Processing',
                ]);
            }
        });


        return redirect()
            ->route(
                'cases.show',
                $case
            )
            ->with(
                'success',
                'Responsible officer assigned successfully.'
            );
    }
}