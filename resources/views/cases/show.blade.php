@extends('layouts.app')

@section('title', $case->case_number)

@section('page-title', $case->case_number)

@section(

    'page-description',

    __('cases_show.page_description')

)

@section('page-actions')

    @can('route', $case)

        @if($case->current_status !== \App\Enums\CaseStatus::CLOSED->value)
            <a

                href="{{ route('cases.route.form', $case) }}"

                class="inline-flex items-center gap-2

                       rounded-xl

                       bg-gradient-to-r from-blue-600 to-purple-600

                       px-5 py-2.5

                       text-sm font-semibold text-white

                       shadow-lg shadow-blue-950/20

                       transition hover:opacity-90"

            >

                <span>⇄</span>

                {{ __('cases_show.route_case') }}

            </a>

        @endif

    @endcan

    <a

        href="{{ route('cases.index') }}"

        class="inline-flex items-center gap-2

               rounded-xl

               border border-slate-700

               bg-[#111A2E]

               px-5 py-2.5

               text-sm font-medium text-slate-300

               transition hover:bg-[#152238] hover:text-white"

    >

        ← {{ __('cases_show.back_to_cases') }}

    </a>

@endsection

@section('content')

@php

    $user = auth()->user();

    $activeAssignments = $case->assignments

        ->where('is_active', true);

    $assignmentCount = $activeAssignments->count();

    $isClosed =
        $case->current_status ===
        \App\Enums\CaseStatus::CLOSED->value;

    $statusColor = match($case->current_status) {

        'Registered' => 'border-slate-600 bg-slate-500/10 text-slate-300',

        'Routed' => 'border-blue-500/20 bg-blue-500/10 text-blue-300',

        'Under Processing' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-300',

        'Awaiting Decision' => 'border-purple-500/20 bg-purple-500/10 text-purple-300',

        'Closed' => 'border-green-500/20 bg-green-500/10 text-green-300',

        default => 'border-blue-500/20 bg-blue-500/10 text-blue-300',

    };

    $urgencyColor = match($case->urgency) {

        'Critical' => 'border-red-500/20 bg-red-500/10 text-red-300',

        'Urgent' => 'border-orange-500/20 bg-orange-500/10 text-orange-300',

        default => 'border-slate-700 bg-slate-500/10 text-slate-300',

    };


    /*
    |--------------------------------------------------------------------------
    | Case Aging / Activity Monitoring
    |--------------------------------------------------------------------------
    */

    $aging =
        $case->aging_summary
        ?? null;

    $activityStyle =
        match(
            $aging['activity_status']
            ?? 'active'
        ) {
            'follow_up' =>
                'border-orange-500/20 bg-orange-500/[0.06] text-orange-300',

            'closed' =>
                'border-slate-700 bg-slate-500/[0.06] text-slate-400',

            default =>
                'border-green-500/20 bg-green-500/[0.06] text-green-300',
        };


    $statusLabel = match($case->current_status) {
        'Registered' => __('cases.statuses.registered'),
        'Routed' => __('cases.statuses.routed'),
        'Under Processing' => __('cases.statuses.under_processing'),
        'Awaiting Decision' => __('cases.statuses.awaiting_decision'),
        'Closed' => __('cases.statuses.closed'),
        default => $case->current_status,
    };

    $urgencyLabel = match($case->urgency) {
        'Critical' => __('cases.urgencies.critical'),
        'Urgent' => __('cases.urgencies.urgent'),
        'Normal' => __('cases.urgencies.normal'),
        default => $case->urgency,
    };

    $threatLabel = match($case->threat_assessment_status) {
        'Very High' => __('cases.threat_levels.very_high'),
        'High' => __('cases.threat_levels.high'),
        'Low' => __('cases.threat_levels.low'),
        'Very Low' => __('cases.threat_levels.very_low'),
        default => $case->threat_assessment_status,
    };

    $primaryDivisionLabel = match($case->primary_division) {
        'Law and Law Enforcement' => __('cases.divisions.law_enforcement'),
        'Protection Services' => __('cases.divisions.protection_services'),
        'Police Protection' => __('cases.divisions.police_protection'),
        'Assistance Services' => __('cases.divisions.assistance_services'),
        null => __('cases_show.not_initially_assigned'),
        default => $case->primary_division,
    };

    $activityStatusLabel = match($aging['activity_status'] ?? 'active') {
        'follow_up' => __('cases_show.follow_up_review_suggested'),
        'closed' => __('cases_show.closed'),
        default => __('cases_show.active'),
    };

@endphp

{{-- =========================================================

     CASE HERO

========================================================= --}}

<div

    class="relative overflow-hidden

           rounded-3xl

           border border-slate-800

           bg-gradient-to-br

           from-[#101A35]

           via-[#111A2E]

           to-[#0B1226]

           p-6 lg:p-7

           mb-6"

>

    <div

        class="absolute -top-20 -right-20

               h-60 w-60

               rounded-full

               bg-blue-600/10

               blur-3xl">

    </div>

    <div

        class="absolute -bottom-24 left-1/3

               h-60 w-60

               rounded-full

               bg-purple-600/10

               blur-3xl">

    </div>

    <div class="relative z-10">

        <div

            class="flex flex-col gap-5

                   xl:flex-row

                   xl:items-start

                   xl:justify-between"

        >

            <div class="max-w-4xl">

                <div class="flex flex-wrap items-center gap-2">

                    <span

                        class="rounded-full border

                               px-3 py-1

                               text-xs font-medium

                               {{ $statusColor }}"

                    >

                        {{ $statusLabel }}

                    </span>

                    <span

                        class="rounded-full

                               border border-slate-700

                               bg-[#0C1528]

                               px-3 py-1

                               text-xs text-slate-300"

                    >

                        {{ match($case->complaint_category) {
                            'Rights / Entitlement Violation' => __('cases_create.categories.rights_entitlement_violation'),
                            'Protection Request' => __('cases_create.categories.protection_request'),
                            'Assistance Request' => __('cases_create.categories.assistance_request'),
                            'Offence Information' => __('cases_create.categories.offence_information'),
                            'Court / Commission Order' => __('cases_create.categories.court_commission_order'),
                            'Police Request' => __('cases_create.categories.police_request'),
                            'Multi-Division Case' => __('cases_create.categories.multi_division_case'),
                            'Other' => __('cases_create.categories.other'),
                            default => $case->complaint_category,
                        } }}

                    </span>

                    <span

                        class="rounded-full border

                               px-3 py-1

                               text-xs font-medium

                               {{ $urgencyColor }}"

                    >

                        {{ $urgencyLabel }}

                    </span>

                    @if($assignmentCount > 1)

                        <span

                            class="rounded-full

                                   border border-purple-500/20

                                   bg-purple-500/10

                                   px-3 py-1

                                   text-xs text-purple-300"

                        >

                            {{ __('cases_show.multi_division_case') }}

                        </span>

                    @endif

                </div>

                <h2 class="mt-5 text-2xl lg:text-3xl font-semibold">

                    {{ __('cases_show.case_overview') }}

                </h2>

                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-400">

                    {{ $case->complaint_summary }}

                </p>

                <div

                    class="mt-6

                           grid grid-cols-2

                           md:grid-cols-4

                           gap-4"

                >

                    <div

                        class="rounded-xl

                               border border-slate-800

                               bg-[#0D172A]/80

                               p-4"

                    >

                        <p

                            class="text-[9px]

                                   uppercase

                                   tracking-[0.16em]

                                   text-slate-500"

                        >

                            {{ __('cases_show.received') }}

                        </p>

                        <p class="mt-2 text-sm font-medium text-white">

                            {{ $case->received_date->format('d M Y') }}

                        </p>

                    </div>

                    <div

                        class="rounded-xl

                               border border-slate-800

                               bg-[#0D172A]/80

                               p-4"

                    >

                        <p

                            class="text-[9px]

                                   uppercase

                                   tracking-[0.16em]

                                   text-slate-500"

                        >

                            {{ __('cases_show.source') }}

                        </p>

                        <p class="mt-2 text-sm font-medium text-white">

                            {{ match($case->complaint_source) {
                            'Direct Complaint' => __('cases_create.sources.direct_complaint'),
                            'Police Station' => __('cases_create.sources.police_station'),
                            'NAPVCW Police Protection Division' => __('cases_create.sources.napvcw_police_protection_division'),
                            'Court Order' => __('cases_create.sources.court_order'),
                            'Commission Order' => __('cases_create.sources.commission_order'),
                            'Other Institution' => __('cases_create.sources.other_institution'),
                            default => $case->complaint_source,
                        } }}

                        </p>

                    </div>

                    <div

                        class="rounded-xl

                               border border-slate-800

                               bg-[#0D172A]/80

                               p-4"

                    >

                        <p

                            class="text-[9px]

                                   uppercase

                                   tracking-[0.16em]

                                   text-slate-500"

                        >

                            {{ __('cases_show.divisions') }}

                        </p>

                        <p class="mt-2 text-sm font-medium text-white">

                            {{ $assignmentCount }}

                        </p>

                    </div>

                    <div

                        class="rounded-xl

                               border border-slate-800

                               bg-[#0D172A]/80

                               p-4"

                    >

                        <p

                            class="text-[9px]

                                   uppercase

                                   tracking-[0.16em]

                                   text-slate-500"

                        >

                            {{ __('cases_show.registered_by') }}

                        </p>

                        <p class="mt-2 text-sm font-medium text-white truncate">

                            {{ $case->creator->name }}

                        </p>

                    </div>

                </div>

            </div>

            <div

                class="shrink-0

                       rounded-2xl

                       border border-slate-700

                       bg-white/95

                       p-3

                       shadow-xl"

            >

                <img

                    src="{{ asset('images/napvcw-logo.png') }}"

                    alt="NAPVCW Logo"

                    class="h-20 w-20 object-contain"

                >

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     CASE AGING & ACTIVITY
========================================================= --}}

@if($aging)

    <div
        class="mb-6
               rounded-2xl
               border
               p-6
               {{ $activityStyle }}"
    >

        <div
            class="flex flex-col gap-4
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>

                <p
                    class="text-[10px]
                           font-semibold
                           uppercase
                           tracking-[0.18em]
                           opacity-70"
                >
                    {{ __('cases_show.case_aging_activity') }}
                </p>

                <h3 class="mt-2 text-xl font-semibold">
                    {{ $activityStatusLabel }}
                </h3>

                <p class="mt-1 text-xs text-slate-400">
                    {{ __('cases_show.activity_monitoring_desc') }}

                </p>

            </div>


            <div
                class="flex h-12 w-12
                       items-center justify-center
                       rounded-xl
                       border border-current/20
                       bg-black/10
                       text-xl"
            >
                ⏱
            </div>

        </div>


        <div
            class="mt-6
                   grid grid-cols-1 gap-3
                   sm:grid-cols-2
                   lg:grid-cols-4"
        >

            {{-- Case Age --}}
            <div
                class="rounded-xl
                       border border-slate-800/80
                       bg-[#0D172A]/60
                       p-4"
            >
                <p class="text-[10px] uppercase tracking-wider text-slate-500">
                    {{ __('cases_show.case_age') }}
                </p>

                <p class="mt-2 text-lg font-semibold text-white">
                    {{ $aging['age_days'] }}
                </p>

                <p class="text-[10px] text-slate-500">
                    {{ $aging['age_days'] === 1 ? __('cases_show.day') : __('cases_show.days') }}
                </p>
            </div>


            {{-- Last Activity --}}
            <div
                class="rounded-xl
                       border border-slate-800/80
                       bg-[#0D172A]/60
                       p-4"
            >
                <p class="text-[10px] uppercase tracking-wider text-slate-500">
                    {{ __('cases_show.last_activity') }}
                </p>

                @if($aging['last_activity_at'])

                    <p class="mt-2 text-sm font-semibold text-white">
                        {{ $aging['last_activity_at']->format('d M Y') }}
                    </p>

                    <p class="mt-1 text-[10px] text-slate-500">
                        {{ $aging['last_activity_at']->format('h:i A') }}
                    </p>

                @else

                    <p class="mt-2 text-sm font-semibold text-slate-400">
                        {{ __('cases_show.not_recorded') }}
                    </p>

                @endif
            </div>


            {{-- Inactive Period --}}
            <div
                class="rounded-xl
                       border border-slate-800/80
                       bg-[#0D172A]/60
                       p-4"
            >
                <p class="text-[10px] uppercase tracking-wider text-slate-500">
                    {{ __('cases_show.inactive_period') }}
                </p>

                <p class="mt-2 text-lg font-semibold text-white">
                    {{ $aging['inactive_days'] }}
                </p>

                <p class="text-[10px] text-slate-500">
                    {{ $aging['inactive_days'] === 1 ? __('cases_show.day') : __('cases_show.days') }}
                </p>
            </div>


            {{-- Activity Status --}}
            <div
                class="rounded-xl
                       border border-slate-800/80
                       bg-[#0D172A]/60
                       p-4"
            >
                <p class="text-[10px] uppercase tracking-wider text-slate-500">
                    {{ __('cases_show.activity_status') }}
                </p>

                <p class="mt-2 text-sm font-semibold text-white">
                    {{ $activityStatusLabel }}
                </p>
            </div>

        </div>


        {{-- Follow-Up Review Warning --}}
        @if($aging['requires_follow_up'])

            <div
                class="mt-5
                       flex items-start gap-3
                       rounded-xl
                       border border-orange-500/20
                       bg-orange-500/10
                       p-4"
            >

                <span class="text-orange-300">
                    ⚠
                </span>

                <div>

                    <p class="text-sm font-semibold text-orange-300">
                        {{ __('cases_show.follow_up_review_suggested') }}
                    </p>

                    <p class="mt-1 text-xs leading-relaxed text-orange-200/70">
                        {{ __('cases_show.no_activity_for') }}
                        {{ $aging['inactive_days'] }}
                        {{ $aging['inactive_days'] === 1 ? __('cases_show.day') : __('cases_show.days') }}.
                        {{ __('cases_show.consider_follow_up') }}
                    </p>

                </div>

            </div>

        @endif


        {{-- Closed Case Note --}}
        @if($case->current_status === \App\Enums\CaseStatus::CLOSED->value)

            <div
                class="mt-5
                       rounded-xl
                       border border-slate-700
                       bg-slate-500/[0.05]
                       p-4"
            >

                <p class="text-xs leading-relaxed text-slate-400">
                    {{ __('cases_show.closed_case_note') }}

                </p>

            </div>

        @endif

    </div>

@endif


{{-- =========================================================

     MAIN CONTENT GRID

========================================================= --}}

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- =====================================================

         LEFT / MAIN COLUMN

    ====================================================== --}}

    <div class="xl:col-span-2 space-y-6">

        {{-- Complaint / Party Information --}}

        <div

            class="rounded-2xl

                   border border-slate-800

                   bg-[#111A2E]

                   p-6"

        >

            <div

                class="flex items-center justify-between

                       border-b border-slate-800

                       pb-4"

            >

                <div>

                    <h3 class="text-lg font-semibold">

                        {{ __('cases_show.complaint_party_information') }}

                    </h3>

                    <p class="mt-1 text-sm text-slate-500">

                        {{ __('cases_show.complaint_party_desc') }}

                    </p>

                </div>

                <span

                    class="rounded-full

                           border border-slate-700

                           bg-[#0D172A]

                           px-3 py-1

                           text-[10px]

                           uppercase tracking-wider

                           text-slate-400"

                >

                    {{ __('cases_show.master_record') }}

                </span>

            </div>

            <div

                class="mt-5

                       grid grid-cols-1

                       md:grid-cols-2

                       gap-x-8 gap-y-5"

            >

                <div>

                    <p class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.complainant_name') }}

                    </p>

                    <p class="mt-1 text-sm text-slate-200">

                        {{ $case->complainant_name ?? __('cases_show.not_recorded') }}

                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.person_type') }}

                    </p>

                    <p class="mt-1 text-sm text-slate-200">

                        {{ match($case->victim_witness_type) {
                            'Victim' => __('cases_create.person_types.victim'),
                            'Witness' => __('cases_create.person_types.witness'),
                            'Representative' => __('cases_create.person_types.representative'),
                            'Other' => __('cases_create.person_types.other'),
                            null => __('cases_show.not_recorded'),
                            default => $case->victim_witness_type,
                        } }}

                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.contact_number') }}

                    </p>

                    <p class="mt-1 text-sm text-slate-200">

                        {{ $case->contact_number ?? __('cases_show.not_recorded') }}

                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.email') }}

                    </p>

                    <p class="mt-1 text-sm text-slate-200 break-all">

                        {{ $case->email ?? __('cases_show.not_recorded') }}

                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.complaint_mode') }}

                    </p>

                    <p class="mt-1 text-sm text-slate-200">

                        {{ match($case->complaint_mode) {
                            'By Hand' => __('cases_create.modes.by_hand'),
                            'Post' => __('cases_create.modes.post'),
                            'Courier' => __('cases_create.modes.courier'),
                            'Email' => __('cases_create.modes.email'),
                            'Fax' => __('cases_create.modes.fax'),
                            'Telephone / Hotline' => __('cases_create.modes.telephone_hotline'),
                            'Official Referral' => __('cases_create.modes.official_referral'),
                            null => __('cases_show.not_recorded'),
                            default => $case->complaint_mode,
                        } }}

                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.initial_division') }}

                    </p>

                    <p class="mt-1 text-sm text-slate-200">

                        {{ $primaryDivisionLabel }}

                    </p>

                </div>

            </div>

            <div class="mt-6">

                <p class="text-xs uppercase tracking-wider text-slate-500">

                    {{ __('cases_show.complaint_summary') }}

                </p>

                <div

                    class="mt-2

                           rounded-xl

                           border border-slate-800

                           bg-[#0D172A]

                           p-4"

                >

                    <p class="text-sm leading-7 text-slate-300 whitespace-pre-line">

                        {{ $case->complaint_summary }}

                    </p>

                </div>

            </div>

        </div>

        {{-- =====================================================

             DIVISION WORKFLOW OVERVIEW

        ====================================================== --}}

        <div

            class="rounded-2xl

                   border border-slate-800

                   bg-[#111A2E]

                   p-6"

        >

            <div

                class="flex flex-col gap-3

                       md:flex-row

                       md:items-center

                       md:justify-between"

            >

                <div>

                    <h3 class="text-lg font-semibold">

                        {{ __('cases_show.parallel_division_workflows') }}

                    </h3>

                    <p class="mt-1 text-sm text-slate-500">

                        {{ __('cases_show.parallel_division_desc') }}

                    </p>

                </div>

                <span

                    class="inline-flex w-fit items-center

                           rounded-full

                           border border-purple-500/20

                           bg-purple-500/10

                           px-3 py-1

                           text-xs text-purple-300"

                >

                    {{ $assignmentCount }}

                    {{ $assignmentCount === 1 ? __('cases_show.active_assignment') : __('cases_show.active_assignments') }}

                </span>

            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                @forelse($activeAssignments as $assignment)

                    @php

                        $divisionIcon = match($assignment->division) {

                            'Law and Law Enforcement' => '⚖',

                            'Protection Services' => '◈',

                            'Police Protection' => '⌖',

                            'Assistance Services' => '✦',

                            default => '▦',

                        };

                    @endphp

                    <div

                        class="rounded-2xl

                               border border-slate-800

                               bg-[#0D172A]

                               p-5

                               transition

                               hover:border-blue-500/20"

                    >

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex gap-3">

                                <div

                                    class="flex h-10 w-10 shrink-0

                                           items-center justify-center

                                           rounded-xl

                                           border border-blue-500/20

                                           bg-blue-500/10

                                           text-blue-300"

                                >

                                    {{ $divisionIcon }}

                                </div>

                                <div>

                                    <h4 class="text-sm font-semibold text-white">

                                        {{ match($assignment->division) {
                                            'Law and Law Enforcement' => __('cases.divisions.law_enforcement'),
                                            'Protection Services' => __('cases.divisions.protection_services'),
                                            'Police Protection' => __('cases.divisions.police_protection'),
                                            'Assistance Services' => __('cases.divisions.assistance_services'),
                                            default => $assignment->division,
                                        } }}

                                    </h4>

                                    <p class="mt-1 text-xs text-slate-500">

                                        @if($assignment->assignedUser)

                                            {{ $assignment->assignedUser->name }}

                                        @else

                                            {{ __('cases_show.awaiting_officer_assignment') }}

                                        @endif

                                    </p>
                                    @can('assignOfficer', $assignment)

                        <a

                            href="{{ route(

                                'cases.assignments.form',

                                [$case, $assignment]

                            ) }}"

                            class="mt-3

                                inline-flex

                                items-center

                                gap-2

                                text-xs

                                font-medium

                                 text-blue-400

                                 hover:text-blue-300"

                    >

                            {{ $assignment->assignedUser

                                ? 'Change Officer'

                                : 'Assign Officer'

                        }}

                         →

                        </a>

                @endcan

                                </div>

                            </div>

                            <span

                                class="rounded-full

                                       border border-blue-500/20

                                       bg-blue-500/10

                                       px-2.5 py-1

                                       text-[10px]

                                       text-blue-300"

                            >

                                {{ $assignment->status }}

                            </span>

                        </div>

                        <div

                            class="mt-4

                                   border-t border-slate-800

                                   pt-4

                                   grid grid-cols-2

                                   gap-4"

                        >

                            <div>

                                <p class="text-[9px] uppercase tracking-wider text-slate-600">

                                    {{ __('cases_show.assigned') }}

                                </p>

                                <p class="mt-1 text-xs text-slate-400">

                                    {{ $assignment->assigned_at

                                        ? $assignment->assigned_at->format('d M Y')

                                        : 'Not recorded'

                                    }}

                                </p>

                            </div>

                            <div>

                                <p class="text-[9px] uppercase tracking-wider text-slate-600">

                                    {{ __('cases_show.current_state') }}

                                </p>

                                <p class="mt-1 text-xs text-slate-400">

                                    {{ $assignment->is_active ? __('cases_show.active') : __('cases_show.inactive') }}

                                </p>

                            </div>

                        </div>

                    </div>

                @empty

                    <div

                        class="md:col-span-2

                               rounded-2xl

                               border border-dashed border-slate-700

                               bg-[#0D172A]

                               p-8

                               text-center"

                    >

                        <div

                            class="mx-auto

                                   flex h-12 w-12

                                   items-center justify-center

                                   rounded-xl

                                   bg-slate-800/60

                                   text-slate-500"

                        >

                            ⇄

                        </div>

                        <h4 class="mt-4 font-medium text-slate-300">

                            {{ __('cases_show.no_division_assignments') }}

                        </h4>

                        <p class="mt-2 text-sm text-slate-500">

                            {{ __('cases_show.not_routed_to_operational_division') }}

                        </p>

                        @if(in_array($user->role, [

                            'board_secretary',

                            'director_general',
                        ]))

                            <a

                                href="{{ route('cases.route.form', $case) }}"

                                class="mt-5

                                       inline-flex

                                       rounded-xl

                                       bg-blue-600

                                       px-4 py-2.5

                                       text-sm font-medium text-white

                                       hover:bg-blue-500"

                            >

                                {{ __('cases_show.route_this_case') }}

                            </a>

                        @endif

                    </div>

                @endforelse

            </div>

        </div>

        {{-- =====================================================

             SHARED CASE TIMELINE

        ====================================================== --}}

        <div

            class="rounded-2xl

                   border border-slate-800

                   bg-[#111A2E]

                   p-6"

        >

            <div

                class="flex flex-col gap-3

                       md:flex-row

                       md:items-center

                       md:justify-between"

            >

                <div>

                    <h3 class="text-lg font-semibold">

                        {{ __('cases_show.shared_case_timeline') }}

                    </h3>

                    <p class="mt-1 text-sm text-slate-500">

                        {{ __('cases_show.shared_timeline_desc') }}

                    </p>

                </div>

                <span

                    class="rounded-full

                           border border-slate-700

                           bg-[#0D172A]

                           px-3 py-1

                           text-xs text-slate-400"

                >

                    {{ $case->statusHistory->count() }}

                    {{ $case->statusHistory->count() === 1 ? __('cases_show.activity') : __('cases_show.activities') }}

                </span>

            </div>

            <div class="mt-7">

                @forelse($case->statusHistory as $history)

                    <div class="relative flex gap-4">

                        {{-- Timeline Line --}}

                        @if(!$loop->last)

                            <div

                                class="absolute

                                       left-[7px]

                                       top-5 bottom-0

                                       w-px

                                       bg-slate-800">

                            </div>

                        @endif

                        {{-- Timeline Dot --}}

                        <div class="relative z-10 mt-1">

                            <div

                                class="h-4 w-4

                                       rounded-full

                                       border-4 border-[#111A2E]

                                       bg-blue-500">

                            </div>

                        </div>

                        {{-- Activity --}}

                        <div

                            class="flex-1

                                   pb-7

                                   {{ !$loop->last ? 'border-b border-slate-800/60 mb-5' : '' }}"

                        >

                            <div

                                class="flex flex-col gap-2

                                       md:flex-row

                                       md:items-start

                                       md:justify-between"

                            >

                                <div>

                                    <p class="text-sm font-semibold text-white">

                                        {{ $history->action_taken }}

                                    </p>

                                    <div class="mt-2 flex flex-wrap gap-2">

                                        <span

                                            class="rounded-full

                                                   border border-blue-500/20

                                                   bg-blue-500/10

                                                   px-2.5 py-1

                                                   text-[10px]

                                                   text-blue-300"

                                        >

                                            {{ match($history->status) {
                                                'Registered' => __('cases.statuses.registered'),
                                                'Routed' => __('cases.statuses.routed'),
                                                'Under Processing' => __('cases.statuses.under_processing'),
                                                'Awaiting Decision' => __('cases.statuses.awaiting_decision'),
                                                'Closed' => __('cases.statuses.closed'),
                                                default => $history->status,
                                            } }}

                                        </span>

                                        @if($history->division)

                                            <span

                                                class="rounded-full

                                                       border border-slate-700

                                                       bg-[#0D172A]

                                                       px-2.5 py-1

                                                       text-[10px]

                                                       text-slate-400"

                                            >

                                                {{ match($history->division) {
                                                'Law and Law Enforcement' => __('cases.divisions.law_enforcement'),
                                                'Protection Services' => __('cases.divisions.protection_services'),
                                                'Police Protection' => __('cases.divisions.police_protection'),
                                                'Assistance Services' => __('cases.divisions.assistance_services'),
                                                default => $history->division,
                                            } }}

                                            </span>

                                        @endif

                                    </div>

                                </div>

                                <div class="md:text-right">

                                    <p class="text-xs text-slate-500">

                                        {{ $history->created_at->format('d M Y') }}

                                    </p>

                                    <p class="mt-1 text-[10px] text-slate-600">

                                        {{ $history->created_at->format('h:i A') }}

                                    </p>

                                </div>

                            </div>

                            @if($history->remarks)

                                <div

                                    class="mt-3

                                           rounded-xl

                                           border border-slate-800

                                           bg-[#0D172A]

                                           px-4 py-3"

                                >

                                    <p class="text-sm leading-relaxed text-slate-400">

                                        {{ $history->remarks }}

                                    </p>

                                </div>

                            @endif

                            <p class="mt-3 text-xs text-slate-600">

                                {{ __('cases_show.updated_by') }}

                                <span class="text-slate-400">

                                    {{ $history->updatedBy?->name ?? __('cases_show.system') }}

                                </span>

                            </p>

                        </div>

                    </div>

                @empty

                    <div

                        class="rounded-2xl

                               border border-dashed border-slate-700

                               bg-[#0D172A]

                               p-8 text-center"

                    >

                        <p class="text-sm text-slate-500">

                            {{ __('cases_show.no_timeline_activities') }}

                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>

    {{-- =====================================================

         RIGHT COLUMN

    ====================================================== --}}

    <div class="space-y-6">

        {{-- Case Information --}}

        <div

            class="rounded-2xl

                   border border-slate-800

                   bg-[#111A2E]

                   p-6"

        >

            <div

                class="flex items-center justify-between

                       border-b border-slate-800

                       pb-4"

            >

                <h3 class="font-semibold">

                    {{ __('cases_show.case_information') }}

                </h3>

                <span class="text-xs text-slate-600">

                    {{ __('cases_show.master') }}

                </span>

            </div>

            <dl class="mt-5 space-y-5 text-sm">

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.case_number') }}

                    </dt>

                    <dd class="mt-1 font-semibold text-blue-300">

                        {{ $case->case_number }}

                    </dd>

                </div>

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.received_date') }}

                    </dt>

                    <dd class="mt-1 text-slate-200">

                        {{ $case->received_date->format('d M Y') }}

                    </dd>

                </div>

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.complaint_source') }}

                    </dt>

                    <dd class="mt-1 text-slate-200">

                        {{ match($case->complaint_source) {
                            'Direct Complaint' => __('cases_create.sources.direct_complaint'),
                            'Police Station' => __('cases_create.sources.police_station'),
                            'NAPVCW Police Protection Division' => __('cases_create.sources.napvcw_police_protection_division'),
                            'Court Order' => __('cases_create.sources.court_order'),
                            'Commission Order' => __('cases_create.sources.commission_order'),
                            'Other Institution' => __('cases_create.sources.other_institution'),
                            default => $case->complaint_source,
                        } }}

                    </dd>

                </div>

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.category') }}

                    </dt>

                    <dd class="mt-1 text-slate-200">

                        {{ match($case->complaint_category) {
                            'Rights / Entitlement Violation' => __('cases_create.categories.rights_entitlement_violation'),
                            'Protection Request' => __('cases_create.categories.protection_request'),
                            'Assistance Request' => __('cases_create.categories.assistance_request'),
                            'Offence Information' => __('cases_create.categories.offence_information'),
                            'Court / Commission Order' => __('cases_create.categories.court_commission_order'),
                            'Police Request' => __('cases_create.categories.police_request'),
                            'Multi-Division Case' => __('cases_create.categories.multi_division_case'),
                            'Other' => __('cases_create.categories.other'),
                            default => $case->complaint_category,
                        } }}

                    </dd>

                </div>

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.current_status') }}

                    </dt>

                    <dd class="mt-2">

                        <span

                            class="inline-flex

                                   rounded-full

                                   border

                                   px-3 py-1

                                   text-xs

                                   {{ $statusColor }}"

                        >

                            {{ $statusLabel }}

                        </span>

                    </dd>

                </div>

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.urgency') }}

                    </dt>

                    <dd class="mt-2">

                        <span

                            class="inline-flex

                                   rounded-full

                                   border

                                   px-3 py-1

                                   text-xs

                                   {{ $urgencyColor }}"

                        >

                            {{ $urgencyLabel }}

                        </span>

                    </dd>

                </div>

                @if($case->threat_assessment_status)

    @php

        $threatColor = match(

            $case->threat_assessment_status

        ) {

            'Very High' =>

                'border-red-500/30 bg-red-500/10 text-red-300',

            'High' =>

                'border-orange-500/30 bg-orange-500/10 text-orange-300',

            'Low' =>

                'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',

            'Very Low' =>

                'border-green-500/30 bg-green-500/10 text-green-300',

            default =>

                'border-slate-700 bg-slate-500/10 text-slate-300',

        };

    @endphp

    <span

        class="rounded-full

               border

               px-3 py-1

               text-xs

               font-medium

               {{ $threatColor }}"

    >

        {{ __('cases_show.threat') }}:

        {{ $threatLabel }}

    </span>

@endif

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.primary_division') }}

                    </dt>

                    <dd class="mt-1 text-slate-200">

                        {{ $primaryDivisionLabel }}

                    </dd>

                </div>

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">

                        Registered By

                    </dt>

                    <dd class="mt-1 text-slate-200">

                        {{ $case->creator->name }}

                    </dd>

                </div>

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">

                        {{ __('cases_show.created_at') }}

                    </dt>

                    <dd class="mt-1 text-slate-200">

                        {{ $case->created_at->format('d M Y, h:i A') }}

                    </dd>

                </div>

                @if($case->closed_at)

                    <div>
                        <dt class="text-xs uppercase tracking-wider text-slate-500">
                            {{ __('cases_show.closed_at') }}
                        </dt>

                        <dd class="mt-1 text-green-300">
                            {{ $case->closed_at->format('d M Y, h:i A') }}
                        </dd>
                    </div>

                @endif

            </dl>

        </div>

        {{-- =========================================================
             PROTECTION DECISION SUMMARY
             Visible to all authorized users of this case
        ========================================================== --}}
        <div
            class="rounded-2xl
                   border border-slate-800
                   bg-[#111A2E]
                   p-6"
        >
            <div
                class="flex items-center justify-between
                       border-b border-slate-800
                       pb-4"
            >
                <div>
                    <h3 class="font-semibold text-white">
                        {{ __('cases_show.protection_decision') }}
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ __('cases_show.protection_decision_desc') }}
                    </p>
                </div>

                <span
                    class="rounded-full
                           border border-purple-500/20
                           bg-purple-500/10
                           px-3 py-1
                           text-[10px]
                           uppercase tracking-wider
                           text-purple-300"
                >
                    {{ __('cases_show.dg_approval') }}
                </span>
            </div>

            <div class="mt-5 space-y-5">

                {{-- Threat Assessment Recommendation --}}
                <div>
                    <p
                        class="text-[10px]
                               uppercase tracking-wider
                               text-slate-500"
                    >
                        {{ __('cases_show.threat_assessment_recommendation') }}
                    </p>

                    @if($case->threat_assessment_status)
                        @php
                            $protectionThreatColor = match($case->threat_assessment_status) {
                                'Very High' => 'border-red-500/30 bg-red-500/10 text-red-300',
                                'High' => 'border-orange-500/30 bg-orange-500/10 text-orange-300',
                                'Low' => 'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',
                                'Very Low' => 'border-green-500/30 bg-green-500/10 text-green-300',
                                default => 'border-slate-700 bg-slate-500/10 text-slate-300',
                            };
                        @endphp

                        <span
                            class="mt-2 inline-flex
                                   rounded-full border
                                   px-3 py-1
                                   text-xs font-medium
                                   {{ $protectionThreatColor }}"
                        >
                            {{ $threatLabel }}
                        </span>
                    @else
                        <p class="mt-2 text-xs text-slate-600">
                            {{ __('cases_show.threat_not_recorded') }}
                        </p>
                    @endif
                </div>

                {{-- Interim Protection --}}
                <div>
                    <p
                        class="text-[10px]
                               uppercase tracking-wider
                               text-slate-500"
                    >
                        {{ __('cases_show.interim_protection') }}
                    </p>

                    @if($case->interim_protection_approved)
                        <div class="mt-2">
                            <span
                                class="inline-flex items-center gap-2
                                       rounded-full
                                       border border-blue-500/20
                                       bg-blue-500/10
                                       px-3 py-1
                                       text-xs font-medium
                                       text-blue-300"
                            >
                                ✓ {{ __('cases_show.approved') }}
                            </span>

                            @if($case->interim_protection_approved_at)
                                <p class="mt-2 text-[10px] text-slate-600">
                                    Approved
                                    {{ $case->interim_protection_approved_at->format('d M Y, h:i A') }}
                                </p>
                            @endif
                        </div>
                    @else
                        <p class="mt-2 text-xs text-slate-600">
                            {{ __('cases_show.not_approved') }}
                        </p>
                    @endif
                </div>

                {{-- Final Protection Measures --}}
                <div>
                    <p
                        class="text-[10px]
                               uppercase tracking-wider
                               text-slate-500"
                    >
                        {{ __('cases_show.approved_protection_measures') }}
                    </p>

                    @if(!empty($case->approved_protection_types))
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($case->approved_protection_types as $type)
                                <span
                                    class="inline-flex items-center gap-1.5
                                           rounded-full
                                           border border-green-500/20
                                           bg-green-500/10
                                           px-3 py-1
                                           text-xs font-medium
                                           text-green-300"
                                >
                                    ✓ {{ match($type) {
                                        'Close Protection' => __('cases_show.protection_types.close_protection'),
                                        'Body-to-Body Protection' => __('cases_show.protection_types.body_to_body'),
                                        'On-site Protection' => __('cases_show.protection_types.on_site'),
                                        default => $type,
                                    } }}
                                </span>
                            @endforeach
                        </div>

                        @if($case->final_protection_approved_at)
                            <p class="mt-2 text-[10px] text-slate-600">
                                Approved
                                {{ $case->final_protection_approved_at->format('d M Y, h:i A') }}
                            </p>
                        @endif
                    @else
                        <p class="mt-2 text-xs text-slate-600">
                            {{ __('cases_show.no_final_measures') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- =========================================================
             DIRECTOR GENERAL - PROTECTION APPROVAL CONTROLS
        ========================================================== --}}
        @can('approveProtection', $case)

            {{-- Interim Protection Approval --}}
            <div
                class="rounded-2xl
                       border border-blue-500/20
                       bg-blue-500/[0.06]
                       p-6"
            >
                <div
                    class="border-b border-blue-500/10
                           pb-4"
                >
                    <p
                        class="text-[10px]
                               uppercase tracking-[0.18em]
                               text-blue-400"
                    >
                        {{ __('cases_show.dg_decision') }}
                    </p>

                    <h3 class="mt-2 font-semibold text-white">
                        {{ __('cases_show.interim_protection') }}
                    </h3>

                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        {{ __('cases_show.interim_protection') }} may be approved before the Police Protection
                        threat assessment recommendation is completed.
                    </p>
                </div>

                @if(!$case->threat_assessment_status)

                    <form
                        method="POST"
                        action="{{ route('protection.interim.update', ['case' => $case->id]) }}"
                        class="mt-5"
                    >
                        @csrf

                        <label
                            class="flex cursor-pointer
                                   items-center gap-3
                                   rounded-xl
                                   border border-slate-800
                                   bg-[#0D172A]
                                   px-4 py-4"
                        >
                            <input
                                type="checkbox"
                                name="interim_protection_approved"
                                value="1"
                                {{ $case->interim_protection_approved ? 'checked' : '' }}
                                class="h-5 w-5
                                       rounded
                                       border-slate-600
                                       bg-[#111A2E]
                                       text-blue-600
                                       focus:ring-blue-500"
                            >

                            <div>
                                <p class="text-sm font-medium text-slate-200">
                                    {{ __('cases_show.approve_interim_protection') }}
                                </p>

                                <p class="mt-1 text-[10px] text-slate-500">
                                    {{ __('cases_show.temporary_protection_desc') }}
                                </p>
                            </div>
                        </label>

                        <button
                            type="submit"
                            class="mt-4
                                   w-full
                                   rounded-xl
                                   bg-blue-600
                                   px-4 py-2.5
                                   text-sm font-semibold
                                   text-white
                                   transition
                                   hover:bg-blue-500"
                        >
                            {{ __('cases_show.save_interim_decision') }}
                        </button>
                    </form>

                @else

                    <div
                        class="mt-5
                               rounded-xl
                               border border-slate-700
                               bg-[#0D172A]
                               p-4"
                    >
                        <p class="text-sm text-slate-400">
                            The threat assessment recommendation has been completed.
                            {{ __('cases_show.interim_protection') }} approval is now locked.
                        </p>
                    </div>

                @endif
            </div>

            {{-- Final Protection Approval --}}
            <div
                class="rounded-2xl
                       border border-purple-500/20
                       bg-purple-500/[0.06]
                       p-6"
            >
                <div
                    class="border-b border-purple-500/10
                           pb-4"
                >
                    <p
                        class="text-[10px]
                               uppercase tracking-[0.18em]
                               text-purple-400"
                    >
                        {{ __('cases_show.dg_decision') }}
                    </p>

                    <h3 class="mt-2 font-semibold text-white">
                        {{ __('cases_show.final_protection_measures') }}
                    </h3>

                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        {{ __('cases_show.final_measures_desc') }}
                    </p>
                </div>

                @if($case->threat_assessment_status)

                    <div
                        class="mt-5
                               rounded-xl
                               border border-slate-800
                               bg-[#0D172A]
                               p-4"
                    >
                        <p
                            class="text-[10px]
                                   uppercase tracking-wider
                                   text-slate-500"
                        >
                            {{ __('cases_show.threat_assessment_recommendation') }}
                        </p>

                        <p class="mt-2 text-sm font-semibold text-orange-300">
                            {{ $threatLabel }}
                        </p>

                        @if($case->threat_assessment_at)
                            <p class="mt-1 text-[10px] text-slate-600">
                                {{ __('cases_show.recorded') }}
                                {{ $case->threat_assessment_at->format('d M Y, h:i A') }}
                            </p>
                        @endif
                    </div>

                    <form
                        method="POST"
                        action="{{ route('protection.final.update', ['case' => $case->id]) }}"
                        class="mt-5"
                    >
                        @csrf

                        <div class="space-y-3">

                            @foreach([
                                'Close Protection',
                                'Body-to-Body Protection',
                                'On-site Protection',
                            ] as $type)

                                <label
                                    class="flex cursor-pointer
                                           items-center gap-3
                                           rounded-xl
                                           border border-slate-800
                                           bg-[#0D172A]
                                           px-4 py-3
                                           transition
                                           hover:border-purple-500/30"
                                >
                                    <input
                                        type="checkbox"
                                        name="protection_types[]"
                                        value="{{ $type }}"
                                        {{ in_array(
                                            $type,
                                            $case->approved_protection_types ?? [],
                                            true
                                        ) ? 'checked' : '' }}
                                        class="h-5 w-5
                                               rounded
                                               border-slate-600
                                               bg-[#111A2E]
                                               text-purple-600
                                               focus:ring-purple-500"
                                    >

                                    <span class="text-sm text-slate-300">
                                        {{ match($type) {
                                            'Close Protection' => __('cases_show.protection_types.close_protection'),
                                            'Body-to-Body Protection' => __('cases_show.protection_types.body_to_body'),
                                            'On-site Protection' => __('cases_show.protection_types.on_site'),
                                            default => $type,
                                        } }}
                                    </span>
                                </label>

                            @endforeach

                        </div>

                        <button
                            type="submit"
                            class="mt-5
                                   w-full
                                   rounded-xl
                                   bg-gradient-to-r
                                   from-blue-600
                                   to-purple-600
                                   px-5 py-2.5
                                   text-sm font-semibold
                                   text-white
                                   transition
                                   hover:opacity-90"
                        >
                            {{ __('cases_show.approve_protection_measures') }}
                        </button>
                    </form>

                @else

                    <div
                        class="mt-5
                               rounded-xl
                               border border-yellow-500/20
                               bg-yellow-500/10
                               p-4"
                    >
                        <p class="text-sm font-medium text-yellow-300">
                            {{ __('cases_show.threat_assessment_required') }}
                        </p>

                        <p class="mt-2 text-xs leading-relaxed text-yellow-200/70">
                            {{ __('cases_show.threat_required_desc') }}
                        </p>
                    </div>

                @endif
            </div>

        @endcan

        {{-- Division Assignment Summary --}}

        <div

            class="rounded-2xl

                   border border-slate-800

                   bg-[#111A2E]

                   p-6"

        >

            <div

                class="flex items-center justify-between

                       border-b border-slate-800

                       pb-4"

            >

                <h3 class="font-semibold">

                    {{ __('cases_show.division_assignments') }}

                </h3>

                <span

                    class="flex h-7 min-w-7

                           items-center justify-center

                           rounded-full

                           bg-blue-500/10

                           px-2

                           text-xs

                           text-blue-300"

                >

                    {{ $assignmentCount }}

                </span>

            </div>

            <div class="mt-5 space-y-3">

                @forelse($activeAssignments as $assignment)

                    <div

                        class="rounded-xl

                               border border-slate-800

                               bg-[#0D172A]

                               p-4"

                    >

                        <div class="flex items-start justify-between gap-3">

                            <div>

                                <p class="text-sm font-medium text-white">

                                    {{ match($assignment->division) {
                                            'Law and Law Enforcement' => __('cases.divisions.law_enforcement'),
                                            'Protection Services' => __('cases.divisions.protection_services'),
                                            'Police Protection' => __('cases.divisions.police_protection'),
                                            'Assistance Services' => __('cases.divisions.assistance_services'),
                                            default => $assignment->division,
                                        } }}

                                </p>

                                <p class="mt-1 text-xs text-slate-500">

                                    @if($assignment->assignedUser)

                                        {{ $assignment->assignedUser->name }}

                                    @else

                                        {{ __('cases_show.officer_not_assigned') }}

                                    @endif

                                </p>

                            </div>

                            <span

                                class="rounded-full

                                       border border-blue-500/20

                                       bg-blue-500/10

                                       px-2 py-1

                                       text-[9px]

                                       text-blue-300"

                            >

                                Active

                            </span>

                        </div>

                        @if($assignment->assigned_at)

                            <p class="mt-3 text-[10px] text-slate-600">

                                Routed {{ $assignment->assigned_at->format('d M Y, h:i A') }}

                            </p>

                        @endif

                    </div>

                @empty

                    <div

                        class="rounded-xl

                               border border-dashed border-slate-700

                               p-5 text-center"

                    >

                        <p class="text-xs text-slate-500">

                            {{ __('cases_show.no_active_division_assignments') }}

                        </p>

                    </div>

                @endforelse

            </div>

            @if(in_array($user->role, [

                'board_secretary',

                'director_general',
            ]))

                <a

                    href="{{ route('cases.route.form', $case) }}"

                    class="mt-5

                           flex w-full

                           items-center justify-center gap-2

                           rounded-xl

                           border border-blue-500/20

                           bg-blue-500/10

                           px-4 py-2.5

                           text-sm font-medium

                           text-blue-300

                           transition

                           hover:bg-blue-500/20"

                >

                    ⇄ {{ __('cases_show.manage_routing') }}

                </a>

            @endif

        </div>

        @if(

            $activeAssignments

            ->where('division', 'Protection Services')

            ->isNotEmpty()

    &&

        in_array(auth()->user()->role, [

            'protection_director',

            'protection_officer',

            'director_general',
            ])

)

    <a

        href="{{ route('protection.show', $case) }}"

        class="flex w-full items-center justify-between

               rounded-xl border border-slate-800

               bg-[#0D172A] px-4 py-3

               text-sm text-slate-300

               hover:border-blue-500/20 hover:text-white"

    >

        <span>{{ __('cases_show.open_protection_workspace') }}</span>

        <span class="text-blue-400">→</span>

    </a>

        @endif

            {{-- =========================================================

     CASE ACTIONS

========================================================= --}}

        <div

            class="rounded-2xl

                   border border-slate-800

                   bg-[#111A2E]

                   p-6"

        >

            <div class="border-b border-slate-800 pb-4">

                <h3 class="font-semibold text-white">

                    {{ __('cases_show.case_actions') }}

                </h3>

                <p class="mt-1 text-xs text-slate-500">

                    {{ __('cases_show.case_actions_desc') }}

                </p>

            </div>

            <div class="mt-5 space-y-3">

                {{-- Route / Assign Divisions --}}

                @if(in_array($user->role, [

                    'board_secretary',

                    'director_general',
                ], true))

                    <a

                        href="{{ route('cases.route.form', ['case' => $case->id]) }}"

                        class="group flex w-full items-center justify-between

                               rounded-xl border border-slate-800

                               bg-[#0D172A] px-4 py-3.5

                               text-sm text-slate-300 transition

                               hover:border-blue-500/30

                               hover:bg-blue-500/[0.06]

                               hover:text-white"

                    >

                        <div class="flex items-center gap-3">

                            <span

                                class="flex h-9 w-9 shrink-0 items-center justify-center

                                       rounded-lg border border-blue-500/20

                                       bg-blue-500/10 text-blue-300"

                            >

                                ⇄

                            </span>

                            <div class="text-left">

                                <p class="font-medium">

                                    {{ __('cases_show.route_assign_divisions') }}

                                </p>

                                <p class="mt-1 text-[10px] text-slate-500">

                                    {{ __('cases_show.manage_division_routing') }}

                                </p>

                            </div>

                        </div>

                        <span class="text-blue-400 transition-transform group-hover:translate-x-1">

                            →

                        </span>

                    </a>

                @endif

                {{-- Legal Workspace --}}

                @if(

                    $activeAssignments

                        ->where('division', 'Law and Law Enforcement')

                        ->isNotEmpty()

                    &&

                    in_array($user->role, [

                        'legal_director',

                        'legal_officer',

                        'investigation_officer',

                        'director_general',
                    ], true)

                )

                    <a

                        href="{{ route('legal.show', ['case' => $case->id]) }}"

                        class="group flex w-full items-center justify-between

                               rounded-xl border border-slate-800

                               bg-[#0D172A] px-4 py-3.5

                               text-sm text-slate-300 transition

                               hover:border-blue-500/30

                               hover:bg-blue-500/[0.06]

                               hover:text-white"

                    >

                        <div class="flex items-center gap-3">

                            <span

                                class="flex h-9 w-9 shrink-0 items-center justify-center

                                       rounded-lg border border-blue-500/20

                                       bg-blue-500/10 text-blue-300"

                            >

                                ⚖

                            </span>

                            <div class="text-left">

                                <p class="font-medium">

                                    {{ __('cases_show.open_legal_workspace') }}

                                </p>

                                <p class="mt-1 text-[10px] text-slate-500">

                                    {{ __('cases_show.continue_legal_workflow') }}

                                </p>

                            </div>

                        </div>

                        <span class="text-blue-400">→</span>

                    </a>

                @endif

                {{-- Protection Workspace --}}

                @if(

                    $activeAssignments

                        ->where('division', 'Protection Services')

                        ->isNotEmpty()

                    &&

                    in_array($user->role, [

                        'protection_director',

                        'protection_officer',

                        'director_general',
                    ], true)

                )

                    <a

                        href="{{ route('protection.show', ['case' => $case->id]) }}"

                        class="group flex w-full items-center justify-between

                               rounded-xl border border-slate-800

                               bg-[#0D172A] px-4 py-3.5

                               text-sm text-slate-300 transition

                               hover:border-blue-500/30

                               hover:bg-blue-500/[0.06]

                               hover:text-white"

                    >

                        <div class="flex items-center gap-3">

                            <span

                                class="flex h-9 w-9 shrink-0 items-center justify-center

                                       rounded-lg border border-blue-500/20

                                       bg-blue-500/10 text-blue-300"

                            >

                                ◈

                            </span>

                            <div class="text-left">

                                <p class="font-medium">

                                    {{ __('cases_show.open_protection_workspace') }}

                                </p>

                                <p class="mt-1 text-[10px] text-slate-500">

                                    {{ __('cases_show.continue_protection_workflow') }}

                                </p>

                            </div>

                        </div>

                        <span class="text-blue-400">→</span>

                    </a>

                @endif

                {{-- Police Protection Workspace --}}

                @if(

                    $activeAssignments

                        ->where('division', 'Police Protection')

                        ->isNotEmpty()

                    &&

                    in_array($user->role, [

                        'police_protection_officer',

                        'director_general',
                    ], true)

                )

                    <a

                        href="{{ route('police-protection.show', ['case' => $case->id]) }}"

                        class="group flex w-full items-center justify-between

                               rounded-xl border border-slate-800

                               bg-[#0D172A] px-4 py-3.5

                               text-sm text-slate-300 transition

                               hover:border-blue-500/30

                               hover:bg-blue-500/[0.06]

                               hover:text-white"

                    >

                        <div class="flex items-center gap-3">

                            <span

                                class="flex h-9 w-9 shrink-0 items-center justify-center

                                       rounded-lg border border-blue-500/20

                                       bg-blue-500/10 text-blue-300"

                            >

                                ⌖

                            </span>

                            <div class="text-left">

                                <p class="font-medium">

                                    {{ __('cases_show.open_police_protection_workspace') }}

                                </p>

                                <p class="mt-1 text-[10px] text-slate-500">

                                    {{ __('cases_show.access_threat_assessment') }}

                                </p>

                            </div>

                        </div>

                        <span class="text-blue-400">→</span>

                    </a>

                @endif

                {{-- Assistance Workspace --}}

                @if(

                    $activeAssignments

                        ->where('division', 'Assistance Services')

                        ->isNotEmpty()

                    &&

                    in_array($user->role, [

                        'director_general',
                    ], true)

                )

                    <a

                        href="{{ route('assistance.show', ['case' => $case->id]) }}"

                        class="group flex w-full items-center justify-between

                               rounded-xl border border-slate-800

                               bg-[#0D172A] px-4 py-3.5

                               text-sm text-slate-300 transition

                               hover:border-blue-500/30

                               hover:bg-blue-500/[0.06]

                               hover:text-white"

                    >

                        <div class="flex items-center gap-3">

                            <span

                                class="flex h-9 w-9 shrink-0 items-center justify-center

                                       rounded-lg border border-blue-500/20

                                       bg-blue-500/10 text-blue-300"

                            >

                                ✦

                            </span>

                            <div class="text-left">

                                <p class="font-medium">

                                    {{ __('cases_show.open_assistance_workspace') }}

                                </p>

                                <p class="mt-1 text-[10px] text-slate-500">

                                    {{ __('cases_show.continue_assistance_workflow') }}

                                </p>

                            </div>

                        </div>

                        <span class="text-blue-400">→</span>

                    </a>

                @endif

                <div class="my-4 border-t border-slate-800"></div>

                @can('downloadReport', $case)

                {{-- Preview Case Summary PDF --}}

                <a

                    href="{{ route('cases.summary.preview', ['case' => $case->id]) }}"

                    target="_blank"

                    rel="noopener noreferrer"

                    class="group relative z-10 flex w-full cursor-pointer

                           items-center justify-between rounded-xl

                           border border-blue-500/30 bg-blue-500/10

                           px-4 py-4 text-blue-300 transition

                           hover:border-blue-400/50 hover:bg-blue-500/20"

                >

                    <div class="flex items-center gap-3">

                        <span

                            class="flex h-9 w-9 shrink-0 items-center justify-center

                                   rounded-lg bg-blue-500/10 text-blue-300"

                        >

                            ▧

                        </span>

                        <div class="text-left">

                            <p class="text-sm font-semibold">

                                {{ __('cases_show.preview_case_summary_pdf') }}

                            </p>

                            <p class="mt-1 text-[10px] text-slate-500">

                                {{ __('cases_show.preview_pdf_desc') }}

                            </p>

                        </div>

                    </div>

                    <span

                        class="text-lg text-blue-400 transition-transform

                               group-hover:-translate-y-0.5

                               group-hover:translate-x-0.5"

                    >

                        ↗

                    </span>

                </a>

                {{-- Download Case Summary PDF --}}

                <a

                    href="{{ route('cases.summary.pdf', ['case' => $case->id]) }}"

                    class="group relative z-10 flex w-full cursor-pointer

                           items-center justify-between rounded-xl

                           border border-green-500/30 bg-green-500/10

                           px-4 py-4 text-green-300 transition

                           hover:border-green-400/50 hover:bg-green-500/20"

                >

                    <div class="flex items-center gap-3">

                        <span

                            class="flex h-9 w-9 shrink-0 items-center justify-center

                                   rounded-lg bg-green-500/10 text-green-300"

                        >

                            ↓

                        </span>

                        <div class="text-left">

                            <p class="text-sm font-semibold">

                                {{ __('cases_show.download_case_summary_pdf') }}

                            </p>

                            <p class="mt-1 text-[10px] text-slate-500">

                                {{ __('cases_show.download_pdf_desc') }}

                            </p>

                        </div>

                    </div>

                    <span

                        class="text-lg text-green-400 transition-transform

                               group-hover:translate-y-0.5"

                    >

                        ↓

                    </span>

                </a>

                @endcan

            </div>

        </div>


        {{-- =========================================================
             CASE LIFECYCLE MANAGEMENT
        ========================================================== --}}

        @if(
            auth()->user()->can('close', $case) ||
            auth()->user()->can('reopen', $case)
        )

            <div
                class="rounded-2xl
                       border border-slate-800
                       bg-[#111A2E]
                       p-6"
            >
                <div class="border-b border-slate-800 pb-4">

                    <h3 class="font-semibold text-white">
                        {{ __('cases_show.case_lifecycle') }}
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ __('cases_show.case_lifecycle_desc') }}
                    </p>

                </div>

                <div class="mt-5">

                    @can('close', $case)

                        @if(!$isClosed)

                            <form
                                method="POST"
                                action="{{ route('cases.close', $case) }}"
                                onsubmit="return confirm('{{ __('cases_show.confirm_close') }}');"
                            >
                                @csrf

                                <div
                                    class="rounded-xl
                                           border border-red-500/20
                                           bg-red-500/[0.06]
                                           p-4"
                                >
                                    <div class="flex items-start gap-3">

                                        <span
                                            class="flex h-9 w-9 shrink-0
                                                   items-center justify-center
                                                   rounded-lg
                                                   border border-red-500/20
                                                   bg-red-500/10
                                                   text-red-300"
                                        >
                                            ✓
                                        </span>

                                        <div class="flex-1">

                                            <p class="text-sm font-semibold text-red-300">
                                                {{ __('cases_show.close_case') }}
                                            </p>

                                            <p class="mt-1 text-[10px] leading-relaxed text-slate-500">
                                                {{ __('cases_show.close_case_desc') }}
                                            </p>

                                            <textarea
                                                name="closure_reason"
                                                rows="3"
                                                maxlength="3000"
                                                required
                                                placeholder="{{ __('cases_show.closure_reason_placeholder') }}"
                                                class="mt-4 w-full
                                                       rounded-xl
                                                       border border-slate-700
                                                       bg-[#0D172A]
                                                       px-4 py-3
                                                       text-sm text-white
                                                       outline-none
                                                       placeholder:text-slate-600
                                                       focus:border-red-500/40"
                                            >{{ old('closure_reason') }}</textarea>

                                            @error('closure_reason')
                                                <p class="mt-2 text-xs text-red-400">
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                            <button
                                                type="submit"
                                                class="mt-3
                                                       inline-flex items-center gap-2
                                                       rounded-xl
                                                       border border-red-500/20
                                                       bg-red-500/10
                                                       px-4 py-2.5
                                                       text-sm font-semibold
                                                       text-red-300
                                                       transition
                                                       hover:bg-red-500/20"
                                            >
                                                {{ __('cases_show.close_case') }}
                                            </button>

                                        </div>

                                    </div>
                                </div>

                            </form>

                        @endif

                    @endcan


                    @can('reopen', $case)

                        @if($isClosed)

                            <form
                                method="POST"
                                action="{{ route('cases.reopen', $case) }}"
                                onsubmit="return confirm('{{ __('cases_show.confirm_reopen') }}');"
                            >
                                @csrf

                                <div
                                    class="rounded-xl
                                           border border-yellow-500/20
                                           bg-yellow-500/[0.06]
                                           p-4"
                                >
                                    <div class="flex items-start gap-3">

                                        <span
                                            class="flex h-9 w-9 shrink-0
                                                   items-center justify-center
                                                   rounded-lg
                                                   border border-yellow-500/20
                                                   bg-yellow-500/10
                                                   text-yellow-300"
                                        >
                                            ↻
                                        </span>

                                        <div class="flex-1">

                                            <p class="text-sm font-semibold text-yellow-300">
                                                {{ __('cases_show.reopen_case') }}
                                            </p>

                                            <p class="mt-1 text-[10px] leading-relaxed text-slate-500">
                                                {{ __('cases_show.reopen_case_desc') }}
                                            </p>

                                            <textarea
                                                name="reopen_reason"
                                                rows="3"
                                                maxlength="3000"
                                                required
                                                placeholder="{{ __('cases_show.reopen_reason_placeholder') }}"
                                                class="mt-4 w-full
                                                       rounded-xl
                                                       border border-slate-700
                                                       bg-[#0D172A]
                                                       px-4 py-3
                                                       text-sm text-white
                                                       outline-none
                                                       placeholder:text-slate-600
                                                       focus:border-yellow-500/40"
                                            >{{ old('reopen_reason') }}</textarea>

                                            @error('reopen_reason')
                                                <p class="mt-2 text-xs text-red-400">
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                            <button
                                                type="submit"
                                                class="mt-3
                                                       inline-flex items-center gap-2
                                                       rounded-xl
                                                       border border-yellow-500/20
                                                       bg-yellow-500/10
                                                       px-4 py-2.5
                                                       text-sm font-semibold
                                                       text-yellow-300
                                                       transition
                                                       hover:bg-yellow-500/20"
                                            >
                                                {{ __('cases_show.reopen_case') }}
                                            </button>

                                        </div>

                                    </div>
                                </div>

                            </form>

                        @endif

                    @endcan

                </div>

            </div>

        @endif

        {{-- Security / Confidentiality --}}

        <div

            class="rounded-2xl

                   border border-blue-500/20

                   bg-blue-500/[0.06]

                   p-5"

        >

            <div class="flex gap-3">

                <div

                    class="flex h-9 w-9 shrink-0

                           items-center justify-center

                           rounded-xl

                           bg-blue-500/10

                           text-blue-300"

                >

                    🔒

                </div>

                <div>

                    <p class="text-sm font-medium text-slate-200">

                        {{ __('cases_show.restricted_case_information') }}

                    </p>

                    <p class="mt-2 text-xs leading-relaxed text-slate-500">

                        Case information should only be accessed and updated by

                        authorized users according to their assigned role and division.

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection