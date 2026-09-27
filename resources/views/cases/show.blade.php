@extends('layouts.app')

@section('title', $case->case_number)

@section('page-title', $case->case_number)

@section(
    'page-description',
    'Master case record, inter-divisional assignments, and shared case activity timeline.'
)

@section('page-actions')

    @if(in_array(auth()->user()->role, [
        'board_secretary',
        'director_general',
        'system_admin',
    ]))

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
            Route Case
        </a>

    @endif

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
        ← Back to Cases
    </a>

@endsection


@section('content')

@php
    $user = auth()->user();

    $activeAssignments = $case->assignments
        ->where('is_active', true);

    $assignmentCount = $activeAssignments->count();

    $statusColor = match($case->current_status) {
        'Registered' => 'border-slate-600 bg-slate-500/10 text-slate-300',
        'Routed' => 'border-blue-500/20 bg-blue-500/10 text-blue-300',
        'Under Processing' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-300',
        'Closed' => 'border-green-500/20 bg-green-500/10 text-green-300',
        default => 'border-blue-500/20 bg-blue-500/10 text-blue-300',
    };

    $urgencyColor = match($case->urgency) {
        'Critical' => 'border-red-500/20 bg-red-500/10 text-red-300',
        'Urgent' => 'border-orange-500/20 bg-orange-500/10 text-orange-300',
        default => 'border-slate-700 bg-slate-500/10 text-slate-300',
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
                        {{ $case->current_status }}
                    </span>

                    <span
                        class="rounded-full
                               border border-slate-700
                               bg-[#0C1528]
                               px-3 py-1
                               text-xs text-slate-300"
                    >
                        {{ $case->complaint_category }}
                    </span>

                    <span
                        class="rounded-full border
                               px-3 py-1
                               text-xs font-medium
                               {{ $urgencyColor }}"
                    >
                        {{ $case->urgency }}
                    </span>

                    @if($assignmentCount > 1)

                        <span
                            class="rounded-full
                                   border border-purple-500/20
                                   bg-purple-500/10
                                   px-3 py-1
                                   text-xs text-purple-300"
                        >
                            Multi-Division Case
                        </span>

                    @endif

                </div>


                <h2 class="mt-5 text-2xl lg:text-3xl font-semibold">
                    Case Overview
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
                            Received
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
                            Source
                        </p>

                        <p class="mt-2 text-sm font-medium text-white">
                            {{ $case->complaint_source }}
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
                            Divisions
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
                            Registered By
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
                        Complaint & Party Information
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Initial complaint and complainant details recorded at intake.
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
                    Master Record
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
                        Complainant Name
                    </p>

                    <p class="mt-1 text-sm text-slate-200">
                        {{ $case->complainant_name ?? 'Not recorded' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-500">
                        Person Type
                    </p>

                    <p class="mt-1 text-sm text-slate-200">
                        {{ $case->victim_witness_type ?? 'Not recorded' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-500">
                        Contact Number
                    </p>

                    <p class="mt-1 text-sm text-slate-200">
                        {{ $case->contact_number ?? 'Not recorded' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-500">
                        Email
                    </p>

                    <p class="mt-1 text-sm text-slate-200 break-all">
                        {{ $case->email ?? 'Not recorded' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-500">
                        Complaint Mode
                    </p>

                    <p class="mt-1 text-sm text-slate-200">
                        {{ $case->complaint_mode ?? 'Not recorded' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-500">
                        Initial Division
                    </p>

                    <p class="mt-1 text-sm text-slate-200">
                        {{ $case->primary_division ?? 'Not initially routed' }}
                    </p>
                </div>

            </div>


            <div class="mt-6">

                <p class="text-xs uppercase tracking-wider text-slate-500">
                    Complaint Summary
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
                        Parallel Division Workflows
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Each assigned division processes the same master case independently while sharing one case record.
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
                    {{ \Illuminate\Support\Str::plural('Active Assignment', $assignmentCount) }}
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
                                        {{ $assignment->division }}
                                    </h4>

                                    <p class="mt-1 text-xs text-slate-500">
                                        @if($assignment->assignedUser)
                                            {{ $assignment->assignedUser->name }}
                                        @else
                                            Awaiting officer assignment
                                        @endif
                                    </p>
                                    @php
                                        $canAssignOfficer = match($assignment->division) {

                                            'Law and Law Enforcement' => in_array($user->role, [
                                                'legal_director',
                                                'director_general',
                                                'system_admin',
                                            ]),

                                            'Protection Services' => in_array($user->role, [
                                                'protection_director',
                                                'director_general',
                                                'system_admin',
                                            ]),

                                            'Police Protection' => in_array($user->role, [
                                                'director_general',
                                                'system_admin',
                                                'board_secretary',
                                            ]),

                                            'Assistance Services' => in_array($user->role, [
                                                'director_general',
                                                'system_admin',
                                                'board_secretary',
                                            ]),

                                            default => false,
                    };
                    @endphp


                    @if($canAssignOfficer)

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

                @endif
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
                                    Assigned
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
                                    Current State
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $assignment->is_active ? 'Active' : 'Inactive' }}
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
                            No Division Assignments
                        </h4>

                        <p class="mt-2 text-sm text-slate-500">
                            This case has not yet been routed to an operational division.
                        </p>

                        @if(in_array($user->role, [
                            'board_secretary',
                            'director_general',
                            'system_admin',
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
                                Route this Case
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
                        Shared Case Timeline
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Chronological record of actions performed across all divisions.
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
                    {{ \Illuminate\Support\Str::plural('Activity', $case->statusHistory->count()) }}
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
                                            {{ $history->status }}
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
                                                {{ $history->division }}
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
                                Updated by
                                <span class="text-slate-400">
                                    {{ $history->updatedBy?->name ?? 'System' }}
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
                            No timeline activities are available for this case.
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
                    Case Information
                </h3>

                <span class="text-xs text-slate-600">
                    Master
                </span>

            </div>


            <dl class="mt-5 space-y-5 text-sm">

                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">
                        Case Number
                    </dt>

                    <dd class="mt-1 font-semibold text-blue-300">
                        {{ $case->case_number }}
                    </dd>

                </div>


                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">
                        Received Date
                    </dt>

                    <dd class="mt-1 text-slate-200">
                        {{ $case->received_date->format('d M Y') }}
                    </dd>

                </div>


                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">
                        Complaint Source
                    </dt>

                    <dd class="mt-1 text-slate-200">
                        {{ $case->complaint_source }}
                    </dd>

                </div>


                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">
                        Category
                    </dt>

                    <dd class="mt-1 text-slate-200">
                        {{ $case->complaint_category }}
                    </dd>

                </div>


                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">
                        Current Status
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
                            {{ $case->current_status }}
                        </span>

                    </dd>

                </div>


                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">
                        Urgency
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
                            {{ $case->urgency }}
                        </span>

                    </dd>

                </div>


                <div>

                    <dt class="text-xs uppercase tracking-wider text-slate-500">
                        Primary Division
                    </dt>

                    <dd class="mt-1 text-slate-200">
                        {{ $case->primary_division ?? 'Not initially assigned' }}
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
                        Created At
                    </dt>

                    <dd class="mt-1 text-slate-200">
                        {{ $case->created_at->format('d M Y, h:i A') }}
                    </dd>

                </div>

            </dl>

        </div>


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
                    Division Assignments
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
                                    {{ $assignment->division }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    @if($assignment->assignedUser)
                                        {{ $assignment->assignedUser->name }}
                                    @else
                                        Officer not assigned
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
                            No active division assignments.
                        </p>
                    </div>

                @endforelse

            </div>


            @if(in_array($user->role, [
                'board_secretary',
                'director_general',
                'system_admin',
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
                    ⇄ Manage Routing
                </a>

            @endif

        </div>


        {{-- Quick Actions --}}
        <div
            class="rounded-2xl
                   border border-slate-800
                   bg-[#111A2E]
                   p-6"
        >

            <h3 class="font-semibold">
                Case Actions
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Available actions based on your role.
            </p>


            <div class="mt-5 space-y-3">

                @if(in_array($user->role, [
                    'board_secretary',
                    'director_general',
                    'system_admin',
                ]))

                    <a
                        href="{{ route('cases.route.form', $case) }}"
                        class="flex w-full
                               items-center justify-between
                               rounded-xl
                               border border-slate-800
                               bg-[#0D172A]
                               px-4 py-3
                               text-sm text-slate-300
                               transition
                               hover:border-blue-500/20
                               hover:text-white"
                    >
                        <span>Route / Assign Divisions</span>
                        <span class="text-blue-400">→</span>
                    </a>

                @endif


                <button
                    type="button"
                    disabled
                    class="flex w-full
                           cursor-not-allowed
                           items-center justify-between
                           rounded-xl
                           border border-slate-800
                           bg-[#0D172A]/60
                           px-4 py-3
                           text-sm text-slate-600"
                >
                    <span>Generate Case Summary PDF</span>
                    <span>▧</span>
                </button>


                <button
                    type="button"
                    disabled
                    class="flex w-full
                           cursor-not-allowed
                           items-center justify-between
                           rounded-xl
                           border border-slate-800
                           bg-[#0D172A]/60
                           px-4 py-3
                           text-sm text-slate-600"
                >
                    <span>Close Case</span>
                    <span>✓</span>
                </button>

            </div>

        </div>


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
                        Restricted Case Information
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