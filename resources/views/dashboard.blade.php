@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section(
    'page-description',
    'Overview of your current case workload, priorities and available actions.'
)

@section('content')

@php
    $user = auth()->user();

    $roleLabel = ucwords(
        str_replace(
            '_',
            ' ',
            $user->role
        )
    );
@endphp


{{-- =========================================================
     POLICE PROTECTION OFFICER DASHBOARD
========================================================= --}}

@if(auth()->user()->role === 'police_protection_officer')

    @php
        $officerCases = $officerPoliceCases ?? collect();

        $officerPoliceTotalSafe =
            $officerCases->count();

        $officerPoliceAwaitingDirectorSafe =
            $officerCases
                ->whereNull('threat_assessment_status')
                ->count();

        $officerPoliceAssessedSafe =
            $officerCases
                ->whereNotNull('threat_assessment_status')
                ->count();
    @endphp

    <div class="mb-6 rounded-2xl border border-slate-800 bg-gradient-to-br from-[#101A35] via-[#111A2E] to-[#0B1226] p-6">

        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-blue-400">
            Police Protection Division
        </p>

        <h2 class="mt-2 text-2xl font-semibold text-white">
            My Threat Assessment Work
        </h2>

        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-400">
            Review assigned cases, update threat-assessment findings
            and monitor the Director's final threat recommendation.
        </p>

    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">

        <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">
            <p class="text-[10px] uppercase tracking-wider text-slate-500">
                My Assigned Cases
            </p>
            <p class="mt-2 text-3xl font-semibold text-white">
                {{ $officerPoliceTotalSafe }}
            </p>
        </div>

        <div class="rounded-2xl border border-yellow-500/20 bg-yellow-500/[0.06] p-5">
            <p class="text-[10px] uppercase tracking-wider text-yellow-400">
                Awaiting Director
            </p>
            <p class="mt-2 text-3xl font-semibold text-yellow-300">
                {{ $officerPoliceAwaitingDirectorSafe }}
            </p>
            <p class="mt-2 text-xs text-slate-600">
                No final threat recommendation yet
            </p>
        </div>

        <div class="rounded-2xl border border-green-500/20 bg-green-500/[0.06] p-5">
            <p class="text-[10px] uppercase tracking-wider text-green-400">
                Recommended
            </p>
            <p class="mt-2 text-3xl font-semibold text-green-300">
                {{ $officerPoliceAssessedSafe }}
            </p>
            <p class="mt-2 text-xs text-slate-600">
                Director recommendation recorded
            </p>
        </div>

    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-800 bg-[#111A2E]">

        <div class="border-b border-slate-800 p-6">
            <h3 class="text-lg font-semibold text-white">
                Assigned Threat Assessment Cases
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Only cases currently assigned to you are shown.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-slate-800 bg-[#0D172A]">
                    <tr class="text-[10px] uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-4">Case</th>
                        <th class="px-5 py-4">Case Status</th>
                        <th class="px-5 py-4">Director Threat Status</th>
                        <th class="px-5 py-4">Assigned</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($officerCases as $case)

                        @php
                            $assignment = $case->assignments->first();

                            $threatBadge = match($case->threat_assessment_status) {
                                'Very High' => 'border-red-500/30 bg-red-500/10 text-red-300',
                                'High' => 'border-orange-500/30 bg-orange-500/10 text-orange-300',
                                'Low' => 'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',
                                'Very Low' => 'border-green-500/30 bg-green-500/10 text-green-300',
                                default => 'border-slate-700 bg-slate-500/10 text-slate-400',
                            };
                        @endphp

                        <tr class="border-b border-slate-800/70 transition hover:bg-[#152238]">

                            <td class="px-5 py-4">
                                <a
                                    href="{{ route('cases.show', $case) }}"
                                    class="text-sm font-semibold text-blue-300 hover:text-blue-200"
                                >
                                    {{ $case->case_number }}
                                </a>

                                <p class="mt-1 max-w-xs truncate text-[10px] text-slate-600">
                                    {{ $case->complaint_category }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-400">
                                {{ $case->current_status }}
                            </td>

                            <td class="px-5 py-4">
                                @if($case->threat_assessment_status)
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-medium {{ $threatBadge }}">
                                        {{ $case->threat_assessment_status }}
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full border border-yellow-500/20 bg-yellow-500/10 px-2.5 py-1 text-[10px] text-yellow-300">
                                        Awaiting Director
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-xs text-slate-500">
                                {{ $assignment?->assigned_at?->format('d M Y') ?? 'Not recorded' }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                <a
                                    href="{{ route('police-protection.show', $case) }}"
                                    class="inline-flex items-center gap-2 rounded-lg border border-blue-500/20 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                                >
                                    Open Assessment →
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                No Police Protection cases are currently assigned to you.
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endif


@if(auth()->user()->role === 'legal_director')

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-gradient-to-br
               from-[#101A35]
               via-[#111A2E]
               to-[#0B1226]
               p-6"
    >

        <p
            class="text-[10px]
                   uppercase
                   tracking-[0.18em]
                   text-blue-400"
        >
            Law & Law Enforcement Division
        </p>

        <h2
            class="mt-2
                   text-2xl
                   font-semibold
                   text-white"
        >
            Legal Division Overview
        </h2>

        <p
            class="mt-2
                   text-sm
                   text-slate-400"
        >
            Review routed cases, monitor officer assignments
            and supervise Law & Law Enforcement activities.
        </p>

    </div>


    <div
        class="mb-6
               grid grid-cols-1
               gap-4
               md:grid-cols-3"
    >

        <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">

            <p class="text-[10px] uppercase tracking-wider text-slate-500">
                Legal Cases
            </p>

            <p class="mt-2 text-3xl font-semibold text-white">
                {{ $legalTotalCases ?? 0 }}
            </p>

        </div>


        <div class="rounded-2xl border border-yellow-500/20 bg-yellow-500/[0.06] p-5">

            <p class="text-[10px] uppercase tracking-wider text-yellow-400">
                Awaiting Officer
            </p>

            <p class="mt-2 text-3xl font-semibold text-yellow-300">
                {{ $legalUnassignedCases ?? 0 }}
            </p>

        </div>


        <div class="rounded-2xl border border-green-500/20 bg-green-500/[0.06] p-5">

            <p class="text-[10px] uppercase tracking-wider text-green-400">
                Assigned
            </p>

            <p class="mt-2 text-3xl font-semibold text-green-300">
                {{ $legalAssignedCases ?? 0 }}
            </p>

        </div>

    </div>


    <div
        class="overflow-hidden
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]"
    >

        <div class="border-b border-slate-800 p-6">

            <h3 class="text-lg font-semibold text-white">
                Law & Law Enforcement Cases
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Assign or review officers responsible for each case.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead class="border-b border-slate-800 bg-[#0D172A]">

                    <tr class="text-[10px] uppercase tracking-wider text-slate-500">

                        <th class="px-5 py-4">Case</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Assigned Officer</th>
                        <th class="px-5 py-4">Officer Role</th>
                        <th class="px-5 py-4 text-right">Action</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(($legalCases ?? collect()) as $case)

                        @php
                            $legalAssignment =
                                $case->assignments->first();
                        @endphp

                        <tr class="border-b border-slate-800/70 hover:bg-[#152238]">

                            <td class="px-5 py-4">

                                <a
                                    href="{{ route('cases.show', $case) }}"
                                    class="text-sm font-semibold text-blue-300 hover:text-blue-200"
                                >
                                    {{ $case->case_number }}
                                </a>

                            </td>


                            <td class="px-5 py-4 text-sm text-slate-400">
                                {{ $case->current_status }}
                            </td>


                            <td class="px-5 py-4 text-sm text-slate-300">

                                {{
                                    $legalAssignment?->assignedUser?->name
                                    ?? 'Not Assigned'
                                }}

                            </td>


                            <td class="px-5 py-4 text-xs text-slate-500">

                                @if($legalAssignment?->assignedUser)

                                    {{
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $legalAssignment
                                                    ->assignedUser
                                                    ->role
                                            )
                                        )
                                    }}

                                @else

                                    —

                                @endif

                            </td>


                            <td class="px-5 py-4 text-right">

                                @if($legalAssignment)

                                    <a
                                        href="{{ route(
                                            'cases.assignments.form',
                                            [
                                                $case,
                                                $legalAssignment
                                            ]
                                        ) }}"
                                        class="inline-flex
                                               rounded-lg
                                               border border-blue-500/20
                                               bg-blue-500/10
                                               px-3 py-2
                                               text-xs
                                               text-blue-300"
                                    >
                                        {{
                                            $legalAssignment->assignedUser
                                                ? 'Change Officer'
                                                : 'Assign Officer'
                                        }}
                                    </a>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-5 py-12 text-center text-slate-500"
                            >
                                No cases are currently routed to Law & Law Enforcement.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endif
@if(auth()->user()->role === 'legal_officer')

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]
               p-6"
    >

        <p class="text-[10px] uppercase tracking-[0.18em] text-blue-400">
            Legal Officer
        </p>

        <h2 class="mt-2 text-2xl font-semibold text-white">
            My Assigned Cases
        </h2>

        <p class="mt-2 text-sm text-slate-400">
            Manage legal work and assigned assistance activities.
        </p>

    </div>


    <div
        class="mb-6
               rounded-2xl
               border border-blue-500/20
               bg-blue-500/[0.06]
               p-5"
    >

        <p class="text-[10px] uppercase tracking-wider text-blue-400">
            Active Assignments
        </p>

        <p class="mt-2 text-3xl font-semibold text-blue-300">
            {{ $legalOfficerTotal ?? 0 }}
        </p>

    </div>


    <div class="space-y-3">

        @forelse(($legalOfficerCases ?? collect()) as $case)

            @php
                $myAssignment =
                    $case->assignments->first();
            @endphp

            <a
                href="{{
                    $myAssignment?->division === 'Assistance Services'
                        ? route('assistance.show', $case)
                        : route('legal.show', $case)
                }}"

                class="flex
                       items-center
                       justify-between
                       rounded-xl
                       border border-slate-800
                       bg-[#111A2E]
                       px-5 py-4
                       transition
                       hover:border-blue-500/30"
            >

                <div>

                    <p class="text-sm font-semibold text-white">
                        {{ $case->case_number }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ $myAssignment?->division }}
                    </p>

                </div>

                <span class="text-blue-400">
                    Open →
                </span>

            </a>

        @empty

            <div
                class="rounded-xl
                       border border-dashed border-slate-700
                       p-8
                       text-center
                       text-sm
                       text-slate-500"
            >
                No cases are currently assigned to you.
            </div>

        @endforelse

    </div>

@endif
@if(auth()->user()->role === 'investigation_officer')

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]
               p-6"
    >

        <p class="text-[10px] uppercase tracking-[0.18em] text-purple-400">
            Investigation Officer
        </p>

        <h2 class="mt-2 text-2xl font-semibold text-white">
            My Investigation Cases
        </h2>

        <p class="mt-2 text-sm text-slate-400">
            Review assigned cases and record investigation findings.
        </p>

    </div>


    <div
        class="mb-6
               rounded-2xl
               border border-purple-500/20
               bg-purple-500/[0.06]
               p-5"
    >

        <p class="text-[10px] uppercase tracking-wider text-purple-400">
            Active Investigations
        </p>

        <p class="mt-2 text-3xl font-semibold text-purple-300">
            {{ $investigationOfficerTotal ?? 0 }}
        </p>

    </div>


    <div class="space-y-3">

        @forelse(($investigationOfficerCases ?? collect()) as $case)

            <a
                href="{{ route('legal.show', $case) }}"

                class="flex
                       items-center
                       justify-between
                       rounded-xl
                       border border-slate-800
                       bg-[#111A2E]
                       px-5 py-4
                       hover:border-purple-500/30"
            >

                <div>

                    <p class="text-sm font-semibold text-white">
                        {{ $case->case_number }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ $case->complaint_category }}
                    </p>

                </div>

                <span class="text-purple-400">
                    Open Investigation →
                </span>

            </a>

        @empty

            <div
                class="rounded-xl
                       border border-dashed border-slate-700
                       p-8
                       text-center
                       text-sm
                       text-slate-500"
            >
                No investigation cases are currently assigned to you.
            </div>

        @endforelse

    </div>

@endif
{{-- =========================================================
     PROTECTION DIRECTOR DASHBOARD
========================================================= --}}


@if(auth()->user()->role === 'protection_director')

    @php
        $protectionCasesSafe =
            $protectionCases
            ?? collect();

        $protectionTotalSafe =
            $protectionTotalCases
            ?? $protectionCasesSafe->count();

        $protectionUnassignedSafe =
            $protectionUnassignedCases
            ?? $protectionCasesSafe
                ->filter(
                    fn ($case) =>
                        !$case->assignments
                            ->firstWhere(
                                'division',
                                'Protection Services'
                            )
                            ?->assigned_user_id
                )
                ->count();

        $protectionAssignedSafe =
            $protectionAssignedCases
            ?? max(
                $protectionTotalSafe
                - $protectionUnassignedSafe,
                0
            );
    @endphp


    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-gradient-to-br
               from-[#101A35]
               via-[#111A2E]
               to-[#0B1226]
               p-6"
    >

        <p
            class="text-[10px]
                   uppercase
                   tracking-[0.18em]
                   text-blue-400"
        >
            Protection Services Division
        </p>

        <h2
            class="mt-2
                   text-2xl
                   font-semibold
                   text-white"
        >
            Protection Services Overview
        </h2>

        <p
            class="mt-2
                   text-sm
                   text-slate-400"
        >
            Monitor protection cases, officer assignments,
            threat recommendations and protection activities.
        </p>

    </div>


    <div
        class="mb-6
               grid grid-cols-1
               gap-4
               md:grid-cols-3"
    >

        <div
            class="rounded-2xl
                   border border-slate-800
                   bg-[#111A2E]
                   p-5"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-slate-500"
            >
                Protection Cases
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-white"
            >
                {{ $protectionTotalSafe }}
            </p>

        </div>


        <div
            class="rounded-2xl
                   border border-yellow-500/20
                   bg-yellow-500/[0.06]
                   p-5"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-yellow-400"
            >
                Awaiting Officer
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-yellow-300"
            >
                {{ $protectionUnassignedSafe }}
            </p>

        </div>


        <div
            class="rounded-2xl
                   border border-green-500/20
                   bg-green-500/[0.06]
                   p-5"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-green-400"
            >
                Assigned
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-green-300"
            >
                {{ $protectionAssignedSafe }}
            </p>

        </div>

    </div>


    <div
        class="overflow-hidden
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]"
    >

        <div
            class="border-b
                   border-slate-800
                   p-6"
        >

            <h3 class="text-lg font-semibold text-white">
                Protection Cases
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Review assignments and monitor threat recommendations.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead
                    class="border-b
                           border-slate-800
                           bg-[#0D172A]"
                >

                    <tr
                        class="text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >

                        <th class="px-5 py-4">
                            Case
                        </th>

                        <th class="px-5 py-4">
                            Assigned Officer
                        </th>

                        <th class="px-5 py-4">
                            Threat Status
                        </th>

                        <th class="px-5 py-4">
                            Case Status
                        </th>

                        <th class="px-5 py-4 text-right">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($protectionCasesSafe as $case)

                        @php

                            $protectionAssignment =
                                $case->assignments->first();

                            $threatBadge = match(
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
                                    'border-slate-700 bg-slate-500/10 text-slate-400',
                            };

                        @endphp


                        <tr
                            class="border-b
                                   border-slate-800/70
                                   hover:bg-[#152238]"
                        >

                            <td class="px-5 py-4">

                                <a
                                    href="{{ route('cases.show', $case) }}"
                                    class="text-sm
                                           font-semibold
                                           text-blue-300
                                           hover:text-blue-200"
                                >
                                    {{ $case->case_number }}
                                </a>

                            </td>


                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-300"
                            >

                                {{
                                    $protectionAssignment
                                        ?->assignedUser
                                        ?->name
                                    ??
                                    'Not Assigned'
                                }}

                            </td>


                            <td class="px-5 py-4">

                                @if($case->threat_assessment_status)

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               border
                                               px-2.5 py-1
                                               text-[10px]
                                               font-medium
                                               {{ $threatBadge }}"
                                    >
                                        {{ $case->threat_assessment_status }}
                                    </span>

                                @else

                                    <span
                                        class="text-[10px]
                                               text-slate-600"
                                    >
                                        Not Assessed
                                    </span>

                                @endif

                            </td>


                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-400"
                            >
                                {{ $case->current_status }}
                            </td>


                            <td
                                class="px-5 py-4
                                       text-right"
                            >

                                @if($protectionAssignment)

                                    <a
                                        href="{{ route(
                                            'cases.assignments.form',
                                            [
                                                $case,
                                                $protectionAssignment
                                            ]
                                        ) }}"

                                        class="inline-flex
                                               rounded-lg
                                               border border-blue-500/20
                                               bg-blue-500/10
                                               px-3 py-2
                                               text-xs
                                               text-blue-300"
                                    >

                                        {{
                                            $protectionAssignment->assignedUser
                                                ? 'Change Officer'
                                                : 'Assign Officer'
                                        }}

                                    </a>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-5
                                       py-12
                                       text-center
                                       text-slate-500"
                            >
                                No cases are currently routed to Protection Services.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endif
{{-- =========================================================
     PROTECTION OFFICER DASHBOARD
========================================================= --}}

@if(auth()->user()->role === 'protection_officer')

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-gradient-to-br
               from-[#101A35]
               via-[#111A2E]
               to-[#0B1226]
               p-6"
    >

        <p
            class="text-[10px]
                   uppercase
                   tracking-[0.18em]
                   text-blue-400"
        >
            Protection Officer
        </p>

        <h2
            class="mt-2
                   text-2xl
                   font-semibold
                   text-white"
        >
            My Protection Work
        </h2>

        <p
            class="mt-2
                   text-sm
                   text-slate-400"
        >
            Manage assigned protection cases and assistance activities,
            and monitor the recommended threat status for each case.
        </p>

    </div>


    <div
        class="mb-6
               rounded-2xl
               border border-blue-500/20
               bg-blue-500/[0.06]
               p-5"
    >

        <p
            class="text-[10px]
                   uppercase
                   tracking-wider
                   text-blue-400"
        >
            Active Assignments
        </p>

        <p
            class="mt-2
                   text-3xl
                   font-semibold
                   text-blue-300"
        >
            {{ $protectionOfficerTotal ?? 0 }}
        </p>

    </div>


    <div class="space-y-3">

        @forelse(($protectionOfficerCases ?? collect()) as $case)

            @php

                $myAssignment =
                    $case->assignments->first();

                $threatBadge = match(
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
                        'border-slate-700 bg-slate-500/10 text-slate-400',
                };

            @endphp


            <div
                class="rounded-xl
                       border border-slate-800
                       bg-[#111A2E]
                       p-5"
            >

                <div
                    class="flex flex-col
                           gap-4
                           md:flex-row
                           md:items-center
                           md:justify-between"
                >

                    <div>

                        <a
                            href="{{ route('cases.show', $case) }}"
                            class="text-sm
                                   font-semibold
                                   text-blue-300
                                   hover:text-blue-200"
                        >
                            {{ $case->case_number }}
                        </a>


                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-500"
                        >
                            {{ $myAssignment?->division }}
                        </p>

                    </div>


                    <div
                        class="flex
                               flex-wrap
                               items-center
                               gap-3"
                    >

                        @if($case->threat_assessment_status)

                            <span
                                class="inline-flex
                                       rounded-full
                                       border
                                       px-2.5 py-1
                                       text-[10px]
                                       font-medium
                                       {{ $threatBadge }}"
                            >
                                Threat:
                                {{ $case->threat_assessment_status }}
                            </span>

                        @else

                            <span
                                class="inline-flex
                                       rounded-full
                                       border border-slate-700
                                       bg-slate-500/10
                                       px-2.5 py-1
                                       text-[10px]
                                       text-slate-500"
                            >
                                Threat Not Assessed
                            </span>

                        @endif


                        @if(
                            $myAssignment?->division ===
                            'Assistance Services'
                        )

                            <a
                                href="{{ route(
                                    'assistance.show',
                                    $case
                                ) }}"

                                class="inline-flex
                                       rounded-lg
                                       border border-purple-500/20
                                       bg-purple-500/10
                                       px-3 py-2
                                       text-xs
                                       text-purple-300"
                            >
                                Open Assistance
                            </a>

                        @else

                            <a
                                href="{{ route(
                                    'protection.show',
                                    $case
                                ) }}"

                                class="inline-flex
                                       rounded-lg
                                       border border-blue-500/20
                                       bg-blue-500/10
                                       px-3 py-2
                                       text-xs
                                       text-blue-300"
                            >
                                Open Protection
                            </a>

                        @endif

                    </div>

                </div>

            </div>

        @empty

            <div
                class="rounded-xl
                       border border-dashed
                       border-slate-700
                       p-8
                       text-center
                       text-sm
                       text-slate-500"
            >
                No protection or assistance cases are currently assigned to you.
            </div>

        @endforelse

    </div>

@endif
@if(auth()->user()->role === 'board_secretary')

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-gradient-to-br
               from-[#101A35]
               via-[#111A2E]
               to-[#0B1226]
               p-6"
    >

        <p
            class="text-[10px]
                   uppercase
                   tracking-[0.18em]
                   text-blue-400"
        >
            Board Secretariat
        </p>

        <h2
            class="mt-2
                   text-2xl
                   font-semibold
                   text-white"
        >
            Case Intake & Routing
        </h2>

        <p
            class="mt-2
                   text-sm
                   text-slate-400"
        >
            Register complaints, review newly created cases
            and route them to the appropriate operational divisions.
        </p>

    </div>


    <div
        class="mb-6
               grid grid-cols-2
               gap-4
               xl:grid-cols-4"
    >

        <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">
            <p class="text-[10px] uppercase text-slate-500">
                Total Cases
            </p>

            <p class="mt-2 text-3xl font-semibold text-white">
                {{ $boardTotalCases ?? 0 }}
            </p>
        </div>


        <div class="rounded-2xl border border-blue-500/20 bg-blue-500/[0.06] p-5">
            <p class="text-[10px] uppercase text-blue-400">
                Registered
            </p>

            <p class="mt-2 text-3xl font-semibold text-blue-300">
                {{ $boardRegisteredCases ?? 0 }}
            </p>
        </div>


        <div class="rounded-2xl border border-green-500/20 bg-green-500/[0.06] p-5">
            <p class="text-[10px] uppercase text-green-400">
                Routed
            </p>

            <p class="mt-2 text-3xl font-semibold text-green-300">
                {{ $boardRoutedCases ?? 0 }}
            </p>
        </div>


        <div class="rounded-2xl border border-yellow-500/20 bg-yellow-500/[0.06] p-5">
            <p class="text-[10px] uppercase text-yellow-400">
                Awaiting Routing
            </p>

            <p class="mt-2 text-3xl font-semibold text-yellow-300">
                {{ $boardUnroutedCases ?? 0 }}
            </p>
        </div>

    </div>


    <div class="mb-6 flex flex-wrap gap-3">

        <a
            href="{{ route('cases.create') }}"
            class="inline-flex
                   rounded-xl
                   bg-blue-600
                   px-5 py-3
                   text-sm
                   font-semibold
                   text-white"
        >
            + Register New Case
        </a>

        <a
            href="{{ route('cases.index') }}"
            class="inline-flex
                   rounded-xl
                   border border-slate-700
                   bg-[#111A2E]
                   px-5 py-3
                   text-sm
                   text-slate-300"
        >
            View All Cases
        </a>

    </div>


    <div
        class="overflow-hidden
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]"
    >

        <div class="border-b border-slate-800 p-6">

            <h3 class="text-lg font-semibold text-white">
                Recent Cases
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Newly registered cases requiring routing are prioritized.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead class="border-b border-slate-800 bg-[#0D172A]">

                    <tr class="text-[10px] uppercase tracking-wider text-slate-500">

                        <th class="px-5 py-4">Case</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Primary Division</th>
                        <th class="px-5 py-4">Threat</th>
                        <th class="px-5 py-4 text-right">Action</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(($boardCases ?? collect()) as $case)

                        <tr class="border-b border-slate-800/70 hover:bg-[#152238]">

                            <td class="px-5 py-4">
                                <a
                                    href="{{ route('cases.show', $case) }}"
                                    class="text-sm font-semibold text-blue-300"
                                >
                                    {{ $case->case_number }}
                                </a>
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-400">
                                {{ $case->current_status }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-400">
                                {{ $case->primary_division ?? 'Not Routed' }}
                            </td>

                            <td class="px-5 py-4 text-xs text-slate-500">
                                {{ $case->threat_assessment_status ?? 'Not Assessed' }}
                            </td>

                            <td class="px-5 py-4 text-right">

                                <a
                                    href="{{ route('cases.route.form', $case) }}"
                                    class="inline-flex
                                           rounded-lg
                                           border border-blue-500/20
                                           bg-blue-500/10
                                           px-3 py-2
                                           text-xs
                                           text-blue-300"
                                >
                                    Route / Review
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-5 py-12 text-center text-slate-500"
                            >
                                No cases have been registered.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endif
@if(auth()->user()->role === 'director_general')

    @php
        $dgSummary =
            $dgAnalytics['summary']
            ?? [
                'total' => 0,
                'registered' => 0,
                'routed' => 0,
                'under_processing' => 0,
                'awaiting_decision' => 0,
                'closed' => 0,
            ];

        $dgDimensionLabels = [
            'current_status' =>
                __('dashboard.dg.dimensions.current_status'),

            'urgency' =>
                __('dashboard.dg.dimensions.urgency'),

            'complaint_category' =>
                __('dashboard.dg.dimensions.complaint_category'),

            'primary_division' =>
                __('dashboard.dg.dimensions.primary_division'),

            'threat_assessment_status' =>
                __('dashboard.dg.dimensions.threat_assessment_status'),

            'dg_protection_type' =>
                __('dashboard.dg.dimensions.protection_type'),
        ];

        $dgMonthLabels = [
            1 => __('dashboard.months.january'),
            2 => __('dashboard.months.february'),
            3 => __('dashboard.months.march'),
            4 => __('dashboard.months.april'),
            5 => __('dashboard.months.may'),
            6 => __('dashboard.months.june'),
            7 => __('dashboard.months.july'),
            8 => __('dashboard.months.august'),
            9 => __('dashboard.months.september'),
            10 => __('dashboard.months.october'),
            11 => __('dashboard.months.november'),
            12 => __('dashboard.months.december'),
        ];
    @endphp


    {{-- =========================================================
         DIRECTOR GENERAL HERO
    ========================================================== --}}

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-gradient-to-br
               from-[#101A35]
               via-[#111A2E]
               to-[#0B1226]
               p-6"
    >
        <div
            class="flex flex-col gap-4
                   lg:flex-row
                   lg:items-center
                   lg:justify-between"
        >
            <div>
                <p
                    class="text-[10px]
                           font-semibold
                           uppercase
                           tracking-[0.18em]
                           text-blue-400"
                >
                    {{ __('dashboard.dg.role') }}
                </p>

                <h2
                    class="mt-2
                           text-2xl
                           font-semibold
                           text-white"
                >
                    {{ __('dashboard.dg.title') }}
                </h2>

                <p
                    class="mt-2
                           max-w-3xl
                           text-sm
                           leading-relaxed
                           text-slate-400"
                >
                    {{ __('dashboard.dg.description') }}
                </p>
            </div>

            <a
                href="{{ route('cases.index') }}"
                class="inline-flex
                       items-center
                       justify-center
                       rounded-xl
                       border border-blue-500/20
                       bg-blue-500/10
                       px-4 py-2.5
                       text-sm
                       font-medium
                       text-blue-300
                       transition
                       hover:bg-blue-500/20"
            >
                {{ __('dashboard.dg.open_case_registry') }}
            </a>
        </div>
    </div>


    {{-- =========================================================
         DG SUMMARY STATISTICS
    ========================================================== --}}

    <div
        class="mb-6
               grid grid-cols-2
               gap-4
               lg:grid-cols-3
               xl:grid-cols-6"
    >
        @foreach([
            [
                'label' => __('dashboard.dg.stats.total'),
                'value' => $dgSummary['total'] ?? 0,
                'class' => 'border-slate-800 bg-[#111A2E] text-white',
            ],
            [
                'label' => __('dashboard.dg.stats.registered'),
                'value' => $dgSummary['registered'] ?? 0,
                'class' => 'border-blue-500/20 bg-blue-500/[0.06] text-blue-300',
            ],
            [
                'label' => __('dashboard.dg.stats.routed'),
                'value' => $dgSummary['routed'] ?? 0,
                'class' => 'border-purple-500/20 bg-purple-500/[0.06] text-purple-300',
            ],
            [
                'label' => __('dashboard.dg.stats.processing'),
                'value' => $dgSummary['under_processing'] ?? 0,
                'class' => 'border-yellow-500/20 bg-yellow-500/[0.06] text-yellow-300',
            ],
            [
                'label' => __('dashboard.dg.stats.awaiting_decision'),
                'value' => $dgSummary['awaiting_decision'] ?? 0,
                'class' => 'border-orange-500/20 bg-orange-500/[0.06] text-orange-300',
            ],
            [
                'label' => __('dashboard.dg.stats.closed'),
                'value' => $dgSummary['closed'] ?? 0,
                'class' => 'border-green-500/20 bg-green-500/[0.06] text-green-300',
            ],
        ] as $stat)
            <div
                class="rounded-2xl
                       border
                       p-5
                       {{ $stat['class'] }}"
            >
                <p
                    class="text-[10px]
                           uppercase
                           tracking-wider
                           opacity-70"
                >
                    {{ $stat['label'] }}
                </p>

                <p
                    class="mt-2
                           text-3xl
                           font-semibold"
                >
                    {{ $stat['value'] }}
                </p>
            </div>
        @endforeach
    </div>


    {{-- =========================================================
         PROTECTION DECISIONS REQUIRING DG ACTION
    ========================================================== --}}

    <div
        class="mb-6
               overflow-hidden
               rounded-2xl
               border border-orange-500/20
               bg-[#111A2E]"
    >
        <div
            class="border-b
                   border-orange-500/10
                   p-6"
        >
            <div
                class="flex flex-col gap-2
                       sm:flex-row
                       sm:items-center
                       sm:justify-between"
            >
                <div>
                    <p
                        class="text-[10px]
                               font-semibold
                               uppercase
                               tracking-[0.18em]
                               text-orange-400"
                    >
                        {{ __('dashboard.dg.decision_queue.eyebrow') }}
                    </p>

                    <h3
                        class="mt-2
                               text-lg
                               font-semibold
                               text-white"
                    >
                        {{ __('dashboard.dg.decision_queue.title') }}
                    </h3>

                    <p
                        class="mt-1
                               text-sm
                               text-slate-500"
                    >
                        {{ __('dashboard.dg.decision_queue.description') }}
                    </p>
                </div>

                <span
                    class="inline-flex
                           w-fit
                           rounded-full
                           border border-orange-500/20
                           bg-orange-500/10
                           px-3 py-1
                           text-xs
                           font-semibold
                           text-orange-300"
                >
                    {{ ($dgDecisionCases ?? collect())->count() }}
                    {{ __('dashboard.dg.decision_queue.pending') }}
                </span>
            </div>
        </div>


        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead
                    class="border-b
                           border-slate-800
                           bg-[#0D172A]"
                >
                    <tr
                        class="text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >
                        <th class="px-5 py-4">
                            {{ __('dashboard.dg.decision_queue.case') }}
                        </th>

                        <th class="px-5 py-4">
                            {{ __('dashboard.dg.decision_queue.threat_status') }}
                        </th>

                        <th class="px-5 py-4">
                            {{ __('dashboard.dg.decision_queue.recommended_by') }}
                        </th>

                        <th class="px-5 py-4">
                            {{ __('dashboard.dg.decision_queue.protection_type') }}
                        </th>

                        <th class="px-5 py-4 text-right">
                            {{ __('dashboard.dg.decision_queue.action') }}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(($dgDecisionCases ?? collect()) as $case)

                        @php
                            $threatClass = match(
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
                                    'border-slate-700 bg-slate-500/10 text-slate-400',
                            };
                        @endphp

                        <tr
                            class="border-b
                                   border-slate-800/70
                                   align-top
                                   hover:bg-[#152238]"
                        >
                            <td class="px-5 py-4">
                                <a
                                    href="{{ route('cases.show', $case) }}"
                                    class="text-sm
                                           font-semibold
                                           text-blue-300
                                           hover:text-blue-200"
                                >
                                    {{ $case->case_number }}
                                </a>

                                <p
                                    class="mt-1
                                           max-w-[240px]
                                           truncate
                                           text-xs
                                           text-slate-500"
                                >
                                    {{
                                        $case->complainant_name
                                        ?? __('dashboard.not_recorded')
                                    }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex
                                           rounded-full
                                           border
                                           px-2.5 py-1
                                           text-[10px]
                                           font-medium
                                           {{ $threatClass }}"
                                >
                                    {{
                                        $case->threat_assessment_status
                                        ?? __('dashboard.not_assessed')
                                    }}
                                </span>
                            </td>

                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-300"
                            >
                                {{
                                    $case->threatAssessmentDirector?->name
                                    ?? __('dashboard.not_recorded')
                                }}
                            </td>

                            <td class="px-5 py-4">
                                <form
                                    id="dg-protection-form-{{ $case->id }}"
                                    method="POST"
                                    action="{{
                                        route(
                                            'cases.dg-protection-decision',
                                            $case
                                        )
                                    }}"
                                >
                                    @csrf

                                    <label
                                        for="dg-protection-type-{{ $case->id }}"
                                        class="sr-only"
                                    >
                                        {{ __('dashboard.dg.decision_queue.protection_type') }}
                                    </label>

                                    <select
                                        id="dg-protection-type-{{ $case->id }}"
                                        name="dg_protection_type"
                                        required
                                        class="min-w-[220px]
                                               rounded-xl
                                               border border-slate-700
                                               bg-[#0D172A]
                                               px-3 py-2.5
                                               text-sm
                                               text-slate-200
                                               outline-none
                                               transition
                                               focus:border-blue-500/50"
                                    >
                                        <option value="">
                                            {{ __('dashboard.dg.decision_queue.select_type') }}
                                        </option>

                                        <option value="Interim Protection">
                                            {{ __('dashboard.dg.protection_types.interim') }}
                                        </option>

                                        <option value="Body-to-Body Protection">
                                            {{ __('dashboard.dg.protection_types.body_to_body') }}
                                        </option>

                                        <option value="Close Protection">
                                            {{ __('dashboard.dg.protection_types.close') }}
                                        </option>
                                    </select>
                                </form>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <button
                                    type="submit"
                                    form="dg-protection-form-{{ $case->id }}"
                                    class="inline-flex
                                           rounded-xl
                                           border border-green-500/20
                                           bg-green-500/10
                                           px-4 py-2.5
                                           text-xs
                                           font-semibold
                                           text-green-300
                                           transition
                                           hover:bg-green-500/20"
                                >
                                    {{ __('dashboard.dg.decision_queue.confirm') }}
                                </button>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-6
                                       py-12
                                       text-center"
                            >
                                <div
                                    class="mx-auto
                                           flex h-12 w-12
                                           items-center
                                           justify-center
                                           rounded-xl
                                           border border-green-500/20
                                           bg-green-500/10
                                           text-green-300"
                                >
                                    ✓
                                </div>

                                <p
                                    class="mt-4
                                           text-sm
                                           font-medium
                                           text-slate-300"
                                >
                                    {{ __('dashboard.dg.decision_queue.empty_title') }}
                                </p>

                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    {{ __('dashboard.dg.decision_queue.empty_description') }}
                                </p>
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- =========================================================
         DG DECISION ANALYTICS
    ========================================================== --}}

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]
               p-6"
    >
        <div
            class="flex flex-col gap-4
                   xl:flex-row
                   xl:items-end
                   xl:justify-between"
        >
            <div>
                <p
                    class="text-[10px]
                           font-semibold
                           uppercase
                           tracking-[0.18em]
                           text-purple-400"
                >
                    {{ __('dashboard.dg.analytics.eyebrow') }}
                </p>

                <h3
                    class="mt-2
                           text-lg
                           font-semibold
                           text-white"
                >
                    {{ __('dashboard.dg.analytics.title') }}
                </h3>

                <p
                    class="mt-1
                           max-w-2xl
                           text-sm
                           text-slate-500"
                >
                    {{ __('dashboard.dg.analytics.description') }}
                </p>
            </div>


            <form
                method="GET"
                action="{{ route('dashboard') }}"
                class="grid
                       grid-cols-1
                       gap-3
                       sm:grid-cols-2
                       xl:grid-cols-4"
            >
                <div>
                    <label
                        for="dg-period"
                        class="mb-1.5
                               block
                               text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >
                        {{ __('dashboard.dg.analytics.period') }}
                    </label>

                    <select
                        id="dg-period"
                        name="dg_period"
                        class="w-full
                               rounded-xl
                               border border-slate-700
                               bg-[#0D172A]
                               px-3 py-2.5
                               text-sm
                               text-slate-200"
                    >
                        <option
                            value="all"
                            @selected(
                                ($dgAnalytics['period'] ?? 'all')
                                === 'all'
                            )
                        >
                            {{ __('dashboard.dg.analytics.all_time') }}
                        </option>

                        <option
                            value="year"
                            @selected(
                                ($dgAnalytics['period'] ?? 'all')
                                === 'year'
                            )
                        >
                            {{ __('dashboard.dg.analytics.year') }}
                        </option>

                        <option
                            value="month"
                            @selected(
                                ($dgAnalytics['period'] ?? 'all')
                                === 'month'
                            )
                        >
                            {{ __('dashboard.dg.analytics.month') }}
                        </option>
                    </select>
                </div>


                <div>
                    <label
                        for="dg-year"
                        class="mb-1.5
                               block
                               text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >
                        {{ __('dashboard.dg.analytics.year') }}
                    </label>

                    <select
                        id="dg-year"
                        name="dg_year"
                        class="w-full
                               rounded-xl
                               border border-slate-700
                               bg-[#0D172A]
                               px-3 py-2.5
                               text-sm
                               text-slate-200"
                    >
                        @forelse(
                            $dgAnalytics['available_years'] ?? []
                            as $year
                        )
                            <option
                                value="{{ $year }}"
                                @selected(
                                    (int) ($dgAnalytics['year'] ?? 0)
                                    === (int) $year
                                )
                            >
                                {{ $year }}
                            </option>
                        @empty
                            <option value="{{ now()->year }}">
                                {{ now()->year }}
                            </option>
                        @endforelse
                    </select>
                </div>


                <div>
                    <label
                        for="dg-month"
                        class="mb-1.5
                               block
                               text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >
                        {{ __('dashboard.dg.analytics.month') }}
                    </label>

                    <select
                        id="dg-month"
                        name="dg_month"
                        class="w-full
                               rounded-xl
                               border border-slate-700
                               bg-[#0D172A]
                               px-3 py-2.5
                               text-sm
                               text-slate-200"
                    >
                        @foreach($dgMonthLabels as $monthNumber => $monthLabel)
                            <option
                                value="{{ $monthNumber }}"
                                @selected(
                                    (int) ($dgAnalytics['month'] ?? 0)
                                    === (int) $monthNumber
                                )
                            >
                                {{ $monthLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div>
                    <label
                        for="dg-dimension"
                        class="mb-1.5
                               block
                               text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >
                        {{ __('dashboard.dg.analytics.variable') }}
                    </label>

                    <select
                        id="dg-dimension"
                        name="dg_dimension"
                        class="w-full
                               rounded-xl
                               border border-slate-700
                               bg-[#0D172A]
                               px-3 py-2.5
                               text-sm
                               text-slate-200"
                    >
                        @foreach($dgDimensionLabels as $key => $label)
                            <option
                                value="{{ $key }}"
                                @selected(
                                    ($dgAnalytics['dimension'] ?? '')
                                    === $key
                                )
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="sm:col-span-2 xl:col-span-4">
                    <button
                        type="submit"
                        class="inline-flex
                               rounded-xl
                               border border-blue-500/20
                               bg-blue-500/10
                               px-4 py-2.5
                               text-xs
                               font-semibold
                               text-blue-300
                               transition
                               hover:bg-blue-500/20"
                    >
                        {{ __('dashboard.dg.analytics.apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>


    <div
        class="mb-6
               grid grid-cols-1
               gap-6
               xl:grid-cols-2"
    >
        <div
            class="rounded-2xl
                   border border-slate-800
                   bg-[#111A2E]
                   p-6"
        >
            <h3
                class="text-base
                       font-semibold
                       text-white"
            >
                {{ __('dashboard.dg.analytics.status_pie') }}
            </h3>

            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                {{ __('dashboard.dg.analytics.status_pie_description') }}
            </p>

            <div class="mt-6 h-[320px]">
                <canvas
                    id="dgStatusPieChart"
                    class="h-full w-full"
                ></canvas>
            </div>

            <div
                id="dgStatusLegend"
                class="mt-5
                       grid grid-cols-1
                       gap-2
                       sm:grid-cols-2"
            ></div>
        </div>


        <div
            class="rounded-2xl
                   border border-slate-800
                   bg-[#111A2E]
                   p-6"
        >
            <h3
                class="text-base
                       font-semibold
                       text-white"
            >
                {{
                    __('dashboard.dg.analytics.bar_title', [
                        'variable' =>
                            $dgDimensionLabels[
                                $dgAnalytics['dimension']
                                ?? 'complaint_category'
                            ]
                            ?? __('dashboard.dg.analytics.variable'),
                    ])
                }}
            </h3>

            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                {{ __('dashboard.dg.analytics.bar_description') }}
            </p>

            <div class="mt-6 h-[360px]">
                <canvas
                    id="dgDimensionBarChart"
                    class="h-full w-full"
                ></canvas>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusLabels =
                @json(
                    $dgAnalytics['status_chart']['labels']
                    ?? []
                );

            const statusValues =
                @json(
                    $dgAnalytics['status_chart']['values']
                    ?? []
                );

            const dimensionLabels =
                @json(
                    $dgAnalytics['dimension_chart']['labels']
                    ?? []
                );

            const dimensionValues =
                @json(
                    $dgAnalytics['dimension_chart']['values']
                    ?? []
                );

            const palette = [
                '#3B82F6',
                '#8B5CF6',
                '#EAB308',
                '#F97316',
                '#22C55E',
                '#EF4444',
                '#06B6D4',
                '#EC4899',
                '#64748B',
                '#14B8A6',
                '#A855F7',
                '#84CC16'
            ];

            const textColor =
                '#94A3B8';

            const gridColor =
                'rgba(100, 116, 139, 0.18)';


            function setupCanvas(canvas) {
                const rect =
                    canvas.getBoundingClientRect();

                const dpr =
                    window.devicePixelRatio
                    || 1;

                canvas.width =
                    Math.max(
                        1,
                        Math.floor(
                            rect.width * dpr
                        )
                    );

                canvas.height =
                    Math.max(
                        1,
                        Math.floor(
                            rect.height * dpr
                        )
                    );

                const ctx =
                    canvas.getContext('2d');

                ctx.setTransform(
                    dpr,
                    0,
                    0,
                    dpr,
                    0,
                    0
                );

                return {
                    ctx,
                    width: rect.width,
                    height: rect.height
                };
            }


            function drawEmpty(
                ctx,
                width,
                height
            ) {
                ctx.clearRect(
                    0,
                    0,
                    width,
                    height
                );

                ctx.fillStyle =
                    textColor;

                ctx.font =
                    '13px sans-serif';

                ctx.textAlign =
                    'center';

                ctx.textBaseline =
                    'middle';

                ctx.fillText(
                    @json(
                        __('dashboard.dg.analytics.no_data')
                    ),
                    width / 2,
                    height / 2
                );
            }


            function drawPie() {
                const canvas =
                    document.getElementById(
                        'dgStatusPieChart'
                    );

                if (!canvas) {
                    return;
                }

                const {
                    ctx,
                    width,
                    height
                } =
                    setupCanvas(
                        canvas
                    );

                ctx.clearRect(
                    0,
                    0,
                    width,
                    height
                );

                const total =
                    statusValues.reduce(
                        (sum, value) =>
                            sum +
                            Number(value || 0),
                        0
                    );

                if (total <= 0) {
                    drawEmpty(
                        ctx,
                        width,
                        height
                    );

                    return;
                }

                const radius =
                    Math.min(
                        width,
                        height
                    ) * 0.34;

                const centerX =
                    width / 2;

                const centerY =
                    height / 2;

                let startAngle =
                    -Math.PI / 2;

                statusValues.forEach(
                    (value, index) => {
                        const numericValue =
                            Number(value || 0);

                        const angle =
                            (
                                numericValue
                                / total
                            )
                            * Math.PI
                            * 2;

                        ctx.beginPath();

                        ctx.moveTo(
                            centerX,
                            centerY
                        );

                        ctx.arc(
                            centerX,
                            centerY,
                            radius,
                            startAngle,
                            startAngle + angle
                        );

                        ctx.closePath();

                        ctx.fillStyle =
                            palette[
                                index
                                % palette.length
                            ];

                        ctx.fill();

                        startAngle +=
                            angle;
                    }
                );

                ctx.beginPath();

                ctx.arc(
                    centerX,
                    centerY,
                    radius * 0.52,
                    0,
                    Math.PI * 2
                );

                ctx.fillStyle =
                    '#111A2E';

                ctx.fill();

                ctx.fillStyle =
                    '#F8FAFC';

                ctx.font =
                    '600 28px sans-serif';

                ctx.textAlign =
                    'center';

                ctx.textBaseline =
                    'middle';

                ctx.fillText(
                    total.toString(),
                    centerX,
                    centerY - 6
                );

                ctx.fillStyle =
                    textColor;

                ctx.font =
                    '11px sans-serif';

                ctx.fillText(
                    @json(
                        __('dashboard.dg.analytics.cases')
                    ),
                    centerX,
                    centerY + 20
                );


                const legend =
                    document.getElementById(
                        'dgStatusLegend'
                    );

                if (legend) {
                    legend.innerHTML =
                        '';

                    statusLabels.forEach(
                        (label, index) => {
                            const item =
                                document.createElement(
                                    'div'
                                );

                            item.className =
                                'flex items-center justify-between gap-3 rounded-lg border border-slate-800 bg-[#0D172A] px-3 py-2 text-xs';

                            const left =
                                document.createElement(
                                    'div'
                                );

                            left.className =
                                'flex min-w-0 items-center gap-2';

                            const dot =
                                document.createElement(
                                    'span'
                                );

                            dot.className =
                                'h-2.5 w-2.5 shrink-0 rounded-full';

                            dot.style.backgroundColor =
                                palette[
                                    index
                                    % palette.length
                                ];

                            const labelSpan =
                                document.createElement(
                                    'span'
                                );

                            labelSpan.className =
                                'truncate text-slate-400';

                            labelSpan.textContent =
                                label;

                            const valueSpan =
                                document.createElement(
                                    'span'
                                );

                            valueSpan.className =
                                'font-semibold text-slate-200';

                            valueSpan.textContent =
                                statusValues[index]
                                ?? 0;

                            left.appendChild(
                                dot
                            );

                            left.appendChild(
                                labelSpan
                            );

                            item.appendChild(
                                left
                            );

                            item.appendChild(
                                valueSpan
                            );

                            legend.appendChild(
                                item
                            );
                        }
                    );
                }
            }


            function drawBars() {
                const canvas =
                    document.getElementById(
                        'dgDimensionBarChart'
                    );

                if (!canvas) {
                    return;
                }

                const {
                    ctx,
                    width,
                    height
                } =
                    setupCanvas(
                        canvas
                    );

                ctx.clearRect(
                    0,
                    0,
                    width,
                    height
                );

                if (
                    !dimensionValues.length
                    ||
                    Math.max(
                        ...dimensionValues.map(
                            value =>
                                Number(value || 0)
                        )
                    ) <= 0
                ) {
                    drawEmpty(
                        ctx,
                        width,
                        height
                    );

                    return;
                }

                const margin = {
                    top: 24,
                    right: 20,
                    bottom: 100,
                    left: 48
                };

                const chartWidth =
                    width
                    - margin.left
                    - margin.right;

                const chartHeight =
                    height
                    - margin.top
                    - margin.bottom;

                const maxValue =
                    Math.max(
                        ...dimensionValues.map(
                            value =>
                                Number(value || 0)
                        )
                    );

                const steps = 5;

                ctx.strokeStyle =
                    gridColor;

                ctx.fillStyle =
                    textColor;

                ctx.font =
                    '10px sans-serif';

                ctx.textAlign =
                    'right';

                ctx.textBaseline =
                    'middle';

                for (
                    let step = 0;
                    step <= steps;
                    step++
                ) {
                    const ratio =
                        step / steps;

                    const y =
                        margin.top
                        + chartHeight
                        - (
                            chartHeight
                            * ratio
                        );

                    ctx.beginPath();

                    ctx.moveTo(
                        margin.left,
                        y
                    );

                    ctx.lineTo(
                        width
                        - margin.right,
                        y
                    );

                    ctx.stroke();

                    const tick =
                        Math.round(
                            maxValue
                            * ratio
                        );

                    ctx.fillText(
                        tick.toString(),
                        margin.left - 8,
                        y
                    );
                }

                const slotWidth =
                    chartWidth
                    / Math.max(
                        dimensionValues.length,
                        1
                    );

                const barWidth =
                    Math.max(
                        10,
                        Math.min(
                            44,
                            slotWidth * 0.62
                        )
                    );

                dimensionValues.forEach(
                    (value, index) => {
                        const numericValue =
                            Number(value || 0);

                        const barHeight =
                            (
                                numericValue
                                / maxValue
                            )
                            * chartHeight;

                        const x =
                            margin.left
                            + (
                                slotWidth
                                * index
                            )
                            + (
                                slotWidth
                                - barWidth
                            ) / 2;

                        const y =
                            margin.top
                            + chartHeight
                            - barHeight;

                        ctx.fillStyle =
                            palette[
                                index
                                % palette.length
                            ];

                        ctx.fillRect(
                            x,
                            y,
                            barWidth,
                            barHeight
                        );

                        ctx.fillStyle =
                            '#E2E8F0';

                        ctx.font =
                            '600 10px sans-serif';

                        ctx.textAlign =
                            'center';

                        ctx.fillText(
                            numericValue.toString(),
                            x + barWidth / 2,
                            Math.max(
                                margin.top + 8,
                                y - 8
                            )
                        );

                        ctx.save();

                        ctx.translate(
                            x + barWidth / 2,
                            margin.top
                            + chartHeight
                            + 14
                        );

                        ctx.rotate(
                            -Math.PI / 4
                        );

                        ctx.fillStyle =
                            textColor;

                        ctx.font =
                            '10px sans-serif';

                        ctx.textAlign =
                            'right';

                        ctx.textBaseline =
                            'middle';

                        const label =
                            String(
                                dimensionLabels[
                                    index
                                ]
                                ?? ''
                            );

                        ctx.fillText(
                            label.length > 28
                                ? label.slice(
                                    0,
                                    25
                                ) + '...'
                                : label,
                            0,
                            0
                        );

                        ctx.restore();
                    }
                );
            }


            function drawDgCharts() {
                drawPie();
                drawBars();
            }

            drawDgCharts();

            let resizeTimer = null;

            window.addEventListener(
                'resize',
                function () {
                    clearTimeout(
                        resizeTimer
                    );

                    resizeTimer =
                        setTimeout(
                            drawDgCharts,
                            120
                        );
                }
            );
        });
    </script>

@endif

@if(auth()->user()->role === 'chairman')

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-gradient-to-br
               from-[#101A35]
               via-[#111A2E]
               to-[#0B1226]
               p-6"
    >

        <p class="text-[10px] uppercase tracking-[0.18em] text-purple-400">
            Chairman
        </p>

        <h2 class="mt-2 text-2xl font-semibold text-white">
            Executive Case Overview
        </h2>

        <p class="mt-2 text-sm text-slate-400">
            Read-only institutional overview of case progress
            and high-risk matters.
        </p>

    </div>


    <div
        class="mb-6
               grid grid-cols-2
               gap-4
               lg:grid-cols-4"
    >

        <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">

            <p class="text-[10px] uppercase text-slate-500">
                Total Cases
            </p>

            <p class="mt-2 text-3xl font-semibold text-white">
                {{ $chairmanTotalCases ?? 0 }}
            </p>

        </div>


        <div class="rounded-2xl border border-yellow-500/20 bg-yellow-500/[0.06] p-5">

            <p class="text-[10px] uppercase text-yellow-400">
                Under Processing
            </p>

            <p class="mt-2 text-3xl font-semibold text-yellow-300">
                {{ $chairmanProcessingCases ?? 0 }}
            </p>

        </div>


        <div class="rounded-2xl border border-green-500/20 bg-green-500/[0.06] p-5">

            <p class="text-[10px] uppercase text-green-400">
                Closed
            </p>

            <p class="mt-2 text-3xl font-semibold text-green-300">
                {{ $chairmanClosedCases ?? 0 }}
            </p>

        </div>


        <div class="rounded-2xl border border-red-500/20 bg-red-500/[0.06] p-5">

            <p class="text-[10px] uppercase text-red-400">
                Very High Threat
            </p>

            <p class="mt-2 text-3xl font-semibold text-red-300">
                {{ $chairmanVeryHighThreat ?? 0 }}
            </p>

        </div>

    </div>


    <div class="space-y-3">

        @forelse(($chairmanRecentCases ?? collect()) as $case)

            <a
                href="{{ route('cases.show', $case) }}"

                class="flex
                       items-center
                       justify-between
                       rounded-xl
                       border border-slate-800
                       bg-[#111A2E]
                       px-5 py-4
                       hover:border-purple-500/20"
            >

                <div>

                    <p class="text-sm font-semibold text-white">
                        {{ $case->case_number }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ $case->current_status }}
                    </p>

                </div>


                <div class="flex items-center gap-3">

                    <span class="text-xs text-slate-500">
                        {{ $case->threat_assessment_status ?? 'Not Assessed' }}
                    </span>

                    <span class="text-purple-400">
                        View →
                    </span>

                </div>

            </a>

        @empty

            <div class="rounded-xl border border-dashed border-slate-700 p-8 text-center text-slate-500">
                No case records are available.
            </div>

        @endforelse

    </div>

@endif
{{-- =========================================================
     POLICY & PROGRAMS DIRECTOR DASHBOARD
========================================================= --}}

@if(auth()->user()->role === 'policy_director')

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-gradient-to-br
               from-[#101A35]
               via-[#111A2E]
               to-[#0B1226]
               p-6"
    >

        <p
            class="text-[10px]
                   uppercase
                   tracking-[0.18em]
                   text-purple-400"
        >
            Policy & Programs Division
        </p>

        <h2
            class="mt-2
                   text-2xl
                   font-semibold
                   text-white"
        >
            Institutional Reporting Overview
        </h2>

        <p
            class="mt-2
                   max-w-3xl
                   text-sm
                   leading-relaxed
                   text-slate-400"
        >
            Monitor institution-wide case trends, case status,
            threat assessment distribution and operational workload
            for policy and programme planning.
        </p>

    </div>


    {{-- Case Status Statistics --}}
    <div
        class="mb-6
               grid grid-cols-2
               gap-4
               xl:grid-cols-4"
    >

        <div
            class="rounded-2xl
                   border border-slate-800
                   bg-[#111A2E]
                   p-5"
        >

            <p class="text-[10px] uppercase text-slate-500">
                Total Cases
            </p>

            <p class="mt-2 text-3xl font-semibold text-white">
                {{ $policyTotalCases ?? 0 }}
            </p>

        </div>


        <div
            class="rounded-2xl
                   border border-blue-500/20
                   bg-blue-500/[0.06]
                   p-5"
        >

            <p class="text-[10px] uppercase text-blue-400">
                Registered
            </p>

            <p class="mt-2 text-3xl font-semibold text-blue-300">
                {{ $policyRegisteredCases ?? 0 }}
            </p>

        </div>


        <div
            class="rounded-2xl
                   border border-yellow-500/20
                   bg-yellow-500/[0.06]
                   p-5"
        >

            <p class="text-[10px] uppercase text-yellow-400">
                Under Processing
            </p>

            <p class="mt-2 text-3xl font-semibold text-yellow-300">
                {{ $policyProcessingCases ?? 0 }}
            </p>

        </div>


        <div
            class="rounded-2xl
                   border border-green-500/20
                   bg-green-500/[0.06]
                   p-5"
        >

            <p class="text-[10px] uppercase text-green-400">
                Closed
            </p>

            <p class="mt-2 text-3xl font-semibold text-green-300">
                {{ $policyClosedCases ?? 0 }}
            </p>

        </div>

    </div>


    {{-- Threat Distribution --}}
    <div
        class="mb-6
               grid grid-cols-2
               gap-4
               xl:grid-cols-4"
    >

        <div
            class="rounded-2xl
                   border border-red-500/20
                   bg-red-500/[0.06]
                   p-5"
        >

            <p class="text-[10px] uppercase text-red-400">
                Very High Threat
            </p>

            <p class="mt-2 text-2xl font-semibold text-red-300">
                {{ $policyThreatVeryHigh ?? 0 }}
            </p>

        </div>


        <div
            class="rounded-2xl
                   border border-orange-500/20
                   bg-orange-500/[0.06]
                   p-5"
        >

            <p class="text-[10px] uppercase text-orange-400">
                High Threat
            </p>

            <p class="mt-2 text-2xl font-semibold text-orange-300">
                {{ $policyThreatHigh ?? 0 }}
            </p>

        </div>


        <div
            class="rounded-2xl
                   border border-yellow-500/20
                   bg-yellow-500/[0.06]
                   p-5"
        >

            <p class="text-[10px] uppercase text-yellow-400">
                Low Threat
            </p>

            <p class="mt-2 text-2xl font-semibold text-yellow-300">
                {{ $policyThreatLow ?? 0 }}
            </p>

        </div>


        <div
            class="rounded-2xl
                   border border-green-500/20
                   bg-green-500/[0.06]
                   p-5"
        >

            <p class="text-[10px] uppercase text-green-400">
                Very Low Threat
            </p>

            <p class="mt-2 text-2xl font-semibold text-green-300">
                {{ $policyThreatVeryLow ?? 0 }}
            </p>

        </div>

    </div>


    {{-- Recent Case Monitoring --}}
    <div
        class="overflow-hidden
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]"
    >

        <div
            class="border-b
                   border-slate-800
                   p-6"
        >

            <h3 class="text-lg font-semibold text-white">
                Recent Case Activity
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Read-only overview for policy and programme monitoring.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead
                    class="border-b
                           border-slate-800
                           bg-[#0D172A]"
                >

                    <tr
                        class="text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >

                        <th class="px-5 py-4">
                            Case
                        </th>

                        <th class="px-5 py-4">
                            Category
                        </th>

                        <th class="px-5 py-4">
                            Status
                        </th>

                        <th class="px-5 py-4">
                            Threat
                        </th>

                        <th class="px-5 py-4 text-right">
                            View
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(($policyRecentCases ?? collect()) as $case
                    )

                        <tr
                            class="border-b
                                   border-slate-800/70
                                   hover:bg-[#152238]"
                        >

                            <td class="px-5 py-4">

                                <a
                                    href="{{ route(
                                        'cases.show',
                                        $case
                                    ) }}"
                                    class="text-sm
                                           font-semibold
                                           text-blue-300"
                                >
                                    {{ $case->case_number }}
                                </a>

                            </td>


                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-400"
                            >
                                {{ $case->complaint_category }}
                            </td>


                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-400"
                            >
                                {{ $case->current_status }}
                            </td>


                            <td
                                class="px-5 py-4
                                       text-xs
                                       text-slate-500"
                            >
                                {{
                                    $case->threat_assessment_status
                                    ??
                                    'Not Assessed'
                                }}
                            </td>


                            <td
                                class="px-5 py-4
                                       text-right"
                            >

                                <a
                                    href="{{ route(
                                        'cases.show',
                                        $case
                                    ) }}"
                                    class="text-xs
                                           text-purple-300
                                           hover:text-purple-200"
                                >
                                    View →
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-5
                                       py-12
                                       text-center
                                       text-slate-500"
                            >
                                No case records are available.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endif
{{-- =========================================================
     SYSTEM ADMINISTRATOR DASHBOARD
========================================================= --}}

@if(auth()->user()->role === 'system_admin')

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-gradient-to-br
               from-[#101A35]
               via-[#111A2E]
               to-[#0B1226]
               p-6"
    >

        <div
            class="flex flex-col
                   gap-5
                   lg:flex-row
                   lg:items-center
                   lg:justify-between"
        >

            <div>

                <p
                    class="text-[10px]
                           uppercase
                           tracking-[0.18em]
                           text-purple-400"
                >
                    System Administration
                </p>

                <h2
                    class="mt-2
                           text-2xl
                           font-semibold
                           text-white"
                >
                    User & Access Management
                </h2>

                <p
                    class="mt-2
                           max-w-2xl
                           text-sm
                           text-slate-400"
                >
                    Review registration requests, approve authorized
                    personnel and manage DCFMS account access.
                </p>

            </div>


            <a
                href="{{ route(
                    'admin.users.index',
                    [
                        'status' => 'pending'
                    ]
                ) }}"

                class="inline-flex
                       items-center
                       justify-center
                       gap-2
                       rounded-xl
                       bg-gradient-to-r
                       from-blue-600
                       to-purple-600
                       px-5 py-3
                       text-sm
                       font-semibold
                       text-white"
            >
                Review Pending Accounts

                @if(($adminPendingUsers ?? 0) > 0)

                    <span
                        class="rounded-full
                               bg-white/20
                               px-2 py-0.5
                               text-[10px]"
                    >
                        {{ $adminPendingUsers ?? 0 }}
                    </span>

                @endif

            </a>

        </div>

    </div>


    {{-- Account Statistics --}}
    <div
        class="mb-6
               grid grid-cols-2
               gap-4
               xl:grid-cols-4"
    >

        <a
            href="{{ route(
                'admin.users.index',
                ['status' => 'pending']
            ) }}"
            class="rounded-2xl
                   border border-yellow-500/20
                   bg-yellow-500/[0.06]
                   p-5
                   transition
                   hover:bg-yellow-500/10"
        >

            <p class="text-[10px] uppercase text-yellow-400">
                Pending
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-yellow-300"
            >
                {{ $adminPendingUsers ?? 0 }}
            </p>

            <p class="mt-2 text-xs text-slate-600">
                Awaiting approval
            </p>

        </a>


        <a
            href="{{ route(
                'admin.users.index',
                ['status' => 'active']
            ) }}"
            class="rounded-2xl
                   border border-green-500/20
                   bg-green-500/[0.06]
                   p-5
                   transition
                   hover:bg-green-500/10"
        >

            <p class="text-[10px] uppercase text-green-400">
                Active
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-green-300"
            >
                {{ $adminActiveUsers ?? 0 }}
            </p>

            <p class="mt-2 text-xs text-slate-600">
                Authorized users
            </p>

        </a>


        <a
            href="{{ route(
                'admin.users.index',
                ['status' => 'rejected']
            ) }}"
            class="rounded-2xl
                   border border-red-500/20
                   bg-red-500/[0.06]
                   p-5
                   transition
                   hover:bg-red-500/10"
        >

            <p class="text-[10px] uppercase text-red-400">
                Rejected
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-red-300"
            >
                {{ $adminRejectedUsers ?? 0 }}
            </p>

            <p class="mt-2 text-xs text-slate-600">
                Rejected requests
            </p>

        </a>


        <a
            href="{{ route(
                'admin.users.index',
                ['status' => 'suspended']
            ) }}"
            class="rounded-2xl
                   border border-orange-500/20
                   bg-orange-500/[0.06]
                   p-5
                   transition
                   hover:bg-orange-500/10"
        >

            <p class="text-[10px] uppercase text-orange-400">
                Suspended
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-orange-300"
            >
                {{ $adminSuspendedUsers ?? 0 }}
            </p>

            <p class="mt-2 text-xs text-slate-600">
                Access disabled
            </p>

        </a>

    </div>


    {{-- Recent Account Requests --}}
    <div
        class="overflow-hidden
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]"
    >

        <div
            class="flex flex-col
                   gap-3
                   border-b
                   border-slate-800
                   p-6
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>

                <h3 class="text-lg font-semibold text-white">
                    Recent User Accounts
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Latest DCFMS registration activity.
                </p>

            </div>


            <a
                href="{{ route('admin.users.index') }}"
                class="text-xs
                       font-medium
                       text-blue-400
                       hover:text-blue-300"
            >
                Manage All Accounts →
            </a>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead
                    class="border-b
                           border-slate-800
                           bg-[#0D172A]"
                >

                    <tr
                        class="text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >

                        <th class="px-5 py-4">
                            Employee
                        </th>

                        <th class="px-5 py-4">
                            Designation
                        </th>

                        <th class="px-5 py-4">
                            EPF
                        </th>

                        <th class="px-5 py-4">
                            Status
                        </th>

                        <th class="px-5 py-4">
                            Registered
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(($adminRecentUsers ?? collect()) as $account
                    )

                        @php

                            $accountStatusBadge =
                                match(
                                    $account->account_status
                                ) {

                                    'active' =>
                                        'border-green-500/20 bg-green-500/10 text-green-300',

                                    'pending' =>
                                        'border-yellow-500/20 bg-yellow-500/10 text-yellow-300',

                                    'rejected' =>
                                        'border-red-500/20 bg-red-500/10 text-red-300',

                                    'suspended' =>
                                        'border-orange-500/20 bg-orange-500/10 text-orange-300',

                                    default =>
                                        'border-slate-700 bg-slate-500/10 text-slate-400',
                                };

                        @endphp


                        <tr
                            class="border-b
                                   border-slate-800/70
                                   hover:bg-[#152238]"
                        >

                            <td class="px-5 py-4">

                                <p
                                    class="text-sm
                                           font-semibold
                                           text-white"
                                >
                                    {{ $account->name }}
                                </p>

                            </td>


                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-400"
                            >
                                {{ $account->designation }}
                            </td>


                            <td
                                class="px-5 py-4
                                       text-xs
                                       text-blue-400"
                            >
                                {{ $account->employee_number }}
                            </td>


                            <td class="px-5 py-4">

                                <span
                                    class="inline-flex
                                           rounded-full
                                           border
                                           px-2.5 py-1
                                           text-[10px]
                                           font-medium
                                           {{ $accountStatusBadge }}"
                                >
                                    {{
                                        ucfirst(
                                            $account->account_status
                                        )
                                    }}
                                </span>

                            </td>


                            <td
                                class="px-5 py-4
                                       text-xs
                                       text-slate-500"
                            >
                                {{ $account->created_at->format('d M Y') }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-5
                                       py-12
                                       text-center
                                       text-slate-500"
                            >
                                No user accounts are available.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endif

@php
    /*
    |--------------------------------------------------------------------------
    | QUICK ACTIONS
    |--------------------------------------------------------------------------
    */

    $quickLinks = match ($user->role) {

        'board_secretary' => [
            [
                'label' => 'Register New Case',
                'desc' => 'Register a newly received complaint or referral.',
                'icon' => '＋',
                'url' => route('cases.create'),
            ],
            [
                'label' => 'All Cases',
                'desc' => 'Search and review all registered master cases.',
                'icon' => '▤',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Cases for Routing',
                'desc' => 'Review newly registered cases requiring division routing.',
                'icon' => '⇄',
                'url' => route('cases.index', [
                    'status' => 'Registered',
                ]),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select a case and download its generated case summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index'),
            ],
        ],

        'director_general' => [
            [
                'label' => 'All Cases',
                'desc' => 'Monitor cases across all divisions.',
                'icon' => '▤',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Active Cases',
                'desc' => 'Review cases currently under processing.',
                'icon' => '◎',
                'url' => route('cases.index', [
                    'status' => 'Under Processing',
                ]),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select any visible case and download its case summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index'),
            ],
        ],

        'system_admin' => [
            [
                'label' => 'All Cases',
                'desc' => 'Review the complete DCFMS case registry.',
                'icon' => '▤',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Register New Case',
                'desc' => 'Create a new case record.',
                'icon' => '＋',
                'url' => route('cases.create'),
            ],
            [
                'label' => 'Active Cases',
                'desc' => 'Review cases currently being processed.',
                'icon' => '◎',
                'url' => route('cases.index', [
                    'status' => 'Under Processing',
                ]),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select a case and download the generated case summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index'),
            ],
        ],

        'legal_director' => [
            [
                'label' => 'Legal Cases',
                'desc' => 'Review all cases routed to Law & Law Enforcement.',
                'icon' => '⚖',
                'url' => route('cases.index', [
                    'division' => 'Law and Law Enforcement',
                ]),
            ],
            [
                'label' => 'Active Legal Cases',
                'desc' => 'Monitor Legal Division cases currently under processing.',
                'icon' => '◎',
                'url' => route('cases.index', [
                    'division' => 'Law and Law Enforcement',
                    'status' => 'Under Processing',
                ]),
            ],
            [
                'label' => 'Search Legal Cases',
                'desc' => 'Search within the Legal Division case registry.',
                'icon' => '⌕',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select a Legal case and download its summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index', [
                    'division' => 'Law and Law Enforcement',
                ]),
            ],
        ],

        'legal_officer' => [
            [
                'label' => 'My Assigned Cases',
                'desc' => 'Open Legal cases currently assigned to you.',
                'icon' => '⚖',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Active Cases',
                'desc' => 'Continue working on cases currently under processing.',
                'icon' => '◎',
                'url' => route('cases.index', [
                    'status' => 'Under Processing',
                ]),
            ],
            [
                'label' => 'Search My Cases',
                'desc' => 'Search within your assigned Legal case workload.',
                'icon' => '⌕',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select one of your assigned cases and download its summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index'),
            ],
        ],

        'investigation_officer' => [
            [
                'label' => 'My Investigation Cases',
                'desc' => 'Review cases currently assigned to you.',
                'icon' => '◎',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Cases Under Processing',
                'desc' => 'Continue investigation-related case activities.',
                'icon' => '→',
                'url' => route('cases.index', [
                    'status' => 'Under Processing',
                ]),
            ],
            [
                'label' => 'Search My Cases',
                'desc' => 'Search within your assigned investigation workload.',
                'icon' => '⌕',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select an assigned case and download its summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index'),
            ],
        ],

        'protection_director' => [
            [
                'label' => 'Protection Cases',
                'desc' => 'Review all cases routed to Protection Services.',
                'icon' => '◈',
                'url' => route('cases.index', [
                    'division' => 'Protection Services',
                ]),
            ],
            [
                'label' => 'Active Protection Cases',
                'desc' => 'Monitor protection cases currently under processing.',
                'icon' => '◎',
                'url' => route('cases.index', [
                    'division' => 'Protection Services',
                    'status' => 'Under Processing',
                ]),
            ],
            [
                'label' => 'Search Protection Cases',
                'desc' => 'Search within Protection Services case records.',
                'icon' => '⌕',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select a Protection case and download its summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index', [
                    'division' => 'Protection Services',
                ]),
            ],
        ],

        'protection_officer' => [
            [
                'label' => 'My Protection Cases',
                'desc' => 'Open protection cases currently assigned to you.',
                'icon' => '◈',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Active Cases',
                'desc' => 'Review your protection cases under processing.',
                'icon' => '◎',
                'url' => route('cases.index', [
                    'status' => 'Under Processing',
                ]),
            ],
            [
                'label' => 'Threat Assessment Cases',
                'desc' => 'Review assigned cases requiring threat assessment follow-up.',
                'icon' => '⌖',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select one of your cases and download the generated summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index'),
            ],
        ],

        'police_protection_officer' => [
            [
                'label' => 'Assessment Requests',
                'desc' => 'Open cases assigned for threat assessment.',
                'icon' => '⌖',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Active Assessments',
                'desc' => 'Review threat assessments currently under processing.',
                'icon' => '◎',
                'url' => route('cases.index', [
                    'status' => 'Under Processing',
                ]),
            ],
            [
                'label' => 'Search My Cases',
                'desc' => 'Search within your assigned Police Protection workload.',
                'icon' => '⌕',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select an assigned case and download its summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index'),
            ],
        ],

        default => [
            [
                'label' => 'My Cases',
                'desc' => 'Review cases currently available to your account.',
                'icon' => '◎',
                'url' => route('cases.index'),
            ],
            [
                'label' => 'Case Summary Reports',
                'desc' => 'Select a visible case and download its summary PDF.',
                'icon' => '▧',
                'url' => route('cases.index'),
            ],
        ],
        
    };


    /*
    |--------------------------------------------------------------------------
    | VISIBLE CASE QUERY
    |--------------------------------------------------------------------------
    */

    $visibleCases = \App\Models\DcfmsCase::query();

    if (!in_array($user->role, [
        'system_admin',
        'director_general',
        'board_secretary',
    ], true)) {

        $visibleCases->whereHas(
            'assignments',
            function ($assignmentQuery) use ($user) {

                $assignmentQuery
                    ->where('division', $user->division)
                    ->where('is_active', true);

                if (in_array($user->role, [
                    'legal_officer',
                    'investigation_officer',
                    'protection_officer',
                    'police_protection_officer',
                ], true)) {

                    $assignmentQuery->where(
                        'assigned_user_id',
                        $user->id
                    );
                }
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD COUNTS
    |--------------------------------------------------------------------------
    */

    $totalCases = (clone $visibleCases)->count();

    $registeredCases = (clone $visibleCases)
        ->where('current_status', 'Registered')
        ->count();

    $routedCases = (clone $visibleCases)
        ->where('current_status', 'Routed')
        ->count();

    $activeCases = (clone $visibleCases)
        ->where('current_status', 'Under Processing')
        ->count();

    $closedCases = (clone $visibleCases)
        ->where('current_status', 'Closed')
        ->count();


    /*
    |--------------------------------------------------------------------------
    | RECENT CASES
    |--------------------------------------------------------------------------
    */

    $recentCasesQuery = \App\Models\DcfmsCase::query();

    if (!in_array($user->role, [
        'system_admin',
        'director_general',
        'board_secretary',
    ], true)) {

        $recentCasesQuery->whereHas(
            'assignments',
            function ($assignmentQuery) use ($user) {

                $assignmentQuery
                    ->where('division', $user->division)
                    ->where('is_active', true);

                if (in_array($user->role, [
                    'legal_officer',
                    'investigation_officer',
                    'protection_officer',
                    'police_protection_officer',
                ], true)) {

                    $assignmentQuery->where(
                        'assigned_user_id',
                        $user->id
                    );
                }
            }
        );
    }

    $recentCases = $recentCasesQuery
        ->latest('created_at')
        ->take(5)
        ->get();
@endphp


{{-- =========================================================
     POLICE PROTECTION DIRECTOR DASHBOARD
========================================================= --}}


@if(auth()->user()->role === 'police_protection_director')

    @php
        $policeCasesSafe =
            $policeCases
            ?? collect();

        $policeTotalCasesSafe =
            $policeCasesSafe->count();

        $policeNotAssessedSafe =
            $policeCasesSafe
                ->whereNull('threat_assessment_status')
                ->count();

        $policeVeryHighSafe =
            $policeCasesSafe
                ->where('threat_assessment_status', 'Very High')
                ->count();

        $policeHighSafe =
            $policeCasesSafe
                ->where('threat_assessment_status', 'High')
                ->count();

        $policeLowSafe =
            $policeCasesSafe
                ->where('threat_assessment_status', 'Low')
                ->count();

        $policeVeryLowSafe =
            $policeCasesSafe
                ->where('threat_assessment_status', 'Very Low')
                ->count();
    @endphp


    {{-- =====================================================
         SECTION HEADER
    ====================================================== --}}
    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-gradient-to-br
               from-[#101A35]
               via-[#111A2E]
               to-[#0B1226]
               p-6"
    >

        <div
            class="flex flex-col
                   gap-4
                   lg:flex-row
                   lg:items-center
                   lg:justify-between"
        >

            <div>

                <p
                    class="text-[10px]
                           font-semibold
                           uppercase
                           tracking-[0.18em]
                           text-blue-400"
                >
                    Police Protection Division
                </p>

                <h2
                    class="mt-2
                           text-2xl
                           font-semibold
                           text-white"
                >
                    Threat Assessment Overview
                </h2>

                <p
                    class="mt-2
                           max-w-2xl
                           text-sm
                           leading-relaxed
                           text-slate-400"
                >
                    Review Police Protection cases,
                    monitor threat assessments and provide
                    the Director's recommended threat status.
                </p>

            </div>


            <a
                href="{{ route(
                    'cases.index',
                    [
                        'division' =>
                            'Police Protection'
                    ]
                ) }}"

                class="inline-flex
                       items-center
                       justify-center
                       gap-2
                       rounded-xl
                       border border-blue-500/20
                       bg-blue-500/10
                       px-4 py-2.5
                       text-sm
                       font-medium
                       text-blue-300
                       transition
                       hover:bg-blue-500/20"
            >
                View Police Cases

                <span>
                    →
                </span>

            </a>

        </div>

    </div>



    {{-- =====================================================
         STATISTICS
    ====================================================== --}}
    <div
        class="grid
               grid-cols-2
               gap-4
               lg:grid-cols-3
               xl:grid-cols-6
               mb-6"
    >

        {{-- Total --}}
        <div
            class="rounded-2xl
                   border border-slate-800
                   bg-[#111A2E]
                   p-5"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-slate-500"
            >
                Total Cases
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-white"
            >
                {{ $policeTotalCasesSafe }}
            </p>

            <p
                class="mt-2
                       text-xs
                       text-slate-600"
            >
                Police Protection
            </p>

        </div>


        {{-- Awaiting Assessment --}}
        <div
            class="rounded-2xl
                   border border-slate-700
                   bg-[#111A2E]
                   p-5"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-slate-500"
            >
                Not Assessed
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-slate-300"
            >
                {{ $policeNotAssessedSafe }}
            </p>

            <p
                class="mt-2
                       text-xs
                       text-slate-600"
            >
                Awaiting recommendation
            </p>

        </div>


        {{-- Very High --}}
        <div
            class="rounded-2xl
                   border border-red-500/20
                   bg-red-500/[0.06]
                   p-5"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-red-400"
            >
                Very High
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-red-300"
            >
                {{ $policeVeryHighSafe }}
            </p>

            <p
                class="mt-2
                       text-xs
                       text-slate-600"
            >
                Threat status
            </p>

        </div>


        {{-- High --}}
        <div
            class="rounded-2xl
                   border border-orange-500/20
                   bg-orange-500/[0.06]
                   p-5"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-orange-400"
            >
                High
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-orange-300"
            >
                {{ $policeHighSafe }}
            </p>

            <p
                class="mt-2
                       text-xs
                       text-slate-600"
            >
                Threat status
            </p>

        </div>


        {{-- Low --}}
        <div
            class="rounded-2xl
                   border border-yellow-500/20
                   bg-yellow-500/[0.06]
                   p-5"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-yellow-400"
            >
                Low
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-yellow-300"
            >
                {{ $policeLowSafe }}
            </p>

            <p
                class="mt-2
                       text-xs
                       text-slate-600"
            >
                Threat status
            </p>

        </div>


        {{-- Very Low --}}
        <div
            class="rounded-2xl
                   border border-green-500/20
                   bg-green-500/[0.06]
                   p-5"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-green-400"
            >
                Very Low
            </p>

            <p
                class="mt-2
                       text-3xl
                       font-semibold
                       text-green-300"
            >
                {{ $policeVeryLowSafe }}
            </p>

            <p
                class="mt-2
                       text-xs
                       text-slate-600"
            >
                Threat status
            </p>

        </div>

    </div>



    {{-- =====================================================
         POLICE PROTECTION CASE TABLE
    ====================================================== --}}
    <div
        class="overflow-hidden
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]"
    >

        {{-- Header --}}
        <div
            class="flex flex-col
                   gap-3
                   border-b
                   border-slate-800
                   p-6
                   lg:flex-row
                   lg:items-center
                   lg:justify-between"
        >

            <div>

                <h3
                    class="text-lg
                           font-semibold
                           text-white"
                >
                    Police Protection Cases
                </h3>

                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Cases awaiting assessment are shown first.
                </p>

            </div>


            @if($policeNotAssessedSafe > 0)

                <span
                    class="inline-flex
                           w-fit
                           items-center
                           gap-2
                           rounded-full
                           border border-yellow-500/20
                           bg-yellow-500/10
                           px-3 py-1.5
                           text-xs
                           text-yellow-300"
                >

                    <span
                        class="h-2 w-2
                               rounded-full
                               bg-yellow-400"
                    ></span>

                    {{ $policeNotAssessedSafe }}
                    awaiting assessment

                </span>

            @endif

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead
                    class="border-b
                           border-slate-800
                           bg-[#0D172A]"
                >

                    <tr
                        class="text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >

                        <th class="px-5 py-4">
                            Case
                        </th>

                        <th class="px-5 py-4">
                            Status
                        </th>

                        <th class="px-5 py-4">
                            Threat Status
                        </th>

                        <th class="px-5 py-4">
                            Assigned Officer
                        </th>

                        <th class="px-5 py-4">
                            Updated
                        </th>

                        <th class="px-5 py-4 text-right">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(
                        $policeCasesSafe
                        as $case
                    )

                        @php

                            $policeAssignment =
                                $case->assignments
                                    ->firstWhere(
                                        'division',
                                        'Police Protection'
                                    );


                            $threatBadge =
                                match(
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
                                        'border-slate-700 bg-slate-500/10 text-slate-400',
                                };

                        @endphp


                        <tr
                            class="border-b
                                   border-slate-800/70
                                   transition
                                   hover:bg-[#152238]"
                        >

                            {{-- Case --}}
                            <td class="px-5 py-4">

                                <a
                                    href="{{ route(
                                        'cases.show',
                                        $case
                                    ) }}"

                                    class="text-sm
                                           font-semibold
                                           text-blue-300
                                           hover:text-blue-200"
                                >
                                    {{ $case->case_number }}
                                </a>

                                <p
                                    class="mt-1
                                           max-w-xs
                                           truncate
                                           text-[10px]
                                           text-slate-600"
                                >
                                    {{ $case->complaint_category }}
                                </p>

                            </td>


                            {{-- Case Status --}}
                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-400"
                            >
                                {{ $case->current_status }}
                            </td>


                            {{-- Threat --}}
                            <td class="px-5 py-4">

                                @if(
                                    $case->threat_assessment_status
                                )

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               border
                                               px-2.5 py-1
                                               text-[10px]
                                               font-medium
                                               {{ $threatBadge }}"
                                    >
                                        {{ $case->threat_assessment_status }}
                                    </span>

                                @else

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               border border-slate-700
                                               bg-slate-500/10
                                               px-2.5 py-1
                                               text-[10px]
                                               text-slate-400"
                                    >
                                        Not Assessed
                                    </span>

                                @endif

                            </td>


                            {{-- Assigned Officer --}}
                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-400"
                            >

                                {{
                                    $policeAssignment
                                        ?->assignedUser
                                        ?->name
                                    ??
                                    'Not Assigned'
                                }}

                            </td>


                            {{-- Assessment Time --}}
                            <td
                                class="px-5 py-4
                                       text-xs
                                       text-slate-500"
                            >

                                @if(
                                    $case->threat_assessment_at
                                )

                                    {{
                                        $case
                                            ->threat_assessment_at
                                            ->format(
                                                'd M Y'
                                            )
                                    }}

                                @else

                                    —

                                @endif

                            </td>


                            {{-- Action --}}
                            <td
                                class="px-5 py-4
                                       text-right"
                            >

                                <a
                                    href="{{ route(
                                        'police-protection.show',
                                        $case
                                    ) }}"

                                    class="inline-flex
                                           items-center
                                           gap-2
                                           rounded-lg
                                           border
                                           {{ $case->threat_assessment_status
                                                ? 'border-blue-500/20 bg-blue-500/10 text-blue-300'
                                                : 'border-yellow-500/20 bg-yellow-500/10 text-yellow-300'
                                           }}
                                           px-3 py-2
                                           text-xs
                                           font-medium
                                           transition"
                                >

                                    @if(
                                        $case->threat_assessment_status
                                    )

                                        Review

                                    @else

                                        Assess Threat

                                    @endif

                                    <span>
                                        →
                                    </span>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-5
                                       py-12
                                       text-center"
                            >

                                <p
                                    class="text-sm
                                           text-slate-400"
                                >
                                    No Police Protection cases are currently available.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endif



{{-- =========================================================
     WELCOME CARD
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
        class="absolute
               -top-20 -right-20
               h-60 w-60
               rounded-full
               bg-blue-600/10
               blur-3xl"
    ></div>

    <div
        class="absolute
               -bottom-24 left-1/3
               h-60 w-60
               rounded-full
               bg-purple-600/10
               blur-3xl"
    ></div>

    <div
        class="relative z-10
               flex flex-col gap-6
               lg:flex-row
               lg:items-center
               lg:justify-between"
    >

        <div class="max-w-3xl">

            <div
                class="inline-flex items-center gap-2
                       rounded-full
                       border border-blue-500/20
                       bg-blue-500/10
                       px-3 py-1
                       text-[10px]
                       font-semibold
                       uppercase
                       tracking-[0.18em]
                       text-blue-300"
            >
                NAPVCW Digital Case Flow Management System
            </div>

            <p class="mt-5 text-sm text-slate-400">
                Welcome back,
            </p>

            <h2
                class="mt-1
                       text-3xl
                       font-semibold
                       tracking-tight
                       text-white"
            >
                {{ $user->name }}
            </h2>

            <div class="mt-3 flex flex-wrap gap-2">

                <span
                    class="rounded-full
                           border border-blue-500/20
                           bg-blue-500/10
                           px-3 py-1
                           text-xs
                           text-blue-300"
                >
                    {{ $roleLabel }}
                </span>

                <span
                    class="rounded-full
                           border border-slate-700
                           bg-[#0C1528]
                           px-3 py-1
                           text-xs
                           text-slate-400"
                >
                    {{ $user->division }}
                </span>

                @if($user->is_active)

                    <span
                        class="inline-flex items-center gap-2
                               rounded-full
                               border border-green-500/20
                               bg-green-500/10
                               px-3 py-1
                               text-xs
                               text-green-300"
                    >
                        <span class="h-2 w-2 rounded-full bg-green-400"></span>
                        Active
                    </span>

                @endif

            </div>

            <p
                class="mt-5
                       max-w-2xl
                       text-sm
                       leading-relaxed
                       text-slate-400"
            >
                Review your case workload, continue workflow activities,
                monitor priorities and access frequently used case-management actions.
            </p>

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


{{-- =========================================================
     CASE STATISTICS
========================================================= --}}
<div
    class="grid grid-cols-1
           sm:grid-cols-2
           xl:grid-cols-5
           gap-4
           mb-6"
>

    <a
        href="{{ route('cases.index') }}"
        class="group rounded-2xl border border-slate-800
               bg-[#111A2E] p-5 transition
               hover:-translate-y-0.5
               hover:border-blue-500/30"
    >
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500">
                    Total Cases
                </p>

                <p class="mt-3 text-3xl font-semibold text-white">
                    {{ $totalCases ?? ($dgAnalytics['summary']['total'] ?? 0) }}
                </p>
            </div>

            <div
                class="flex h-10 w-10 items-center justify-center
                       rounded-xl border border-blue-500/20
                       bg-blue-500/10 text-blue-300"
            >
                ▤
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-500">
            Cases available to your account
        </p>
    </a>


    <a
        href="{{ route('cases.index', ['status' => 'Registered']) }}"
        class="group rounded-2xl border border-slate-800
               bg-[#111A2E] p-5 transition
               hover:-translate-y-0.5
               hover:border-slate-500/30"
    >
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500">
                    Registered
                </p>

                <p class="mt-3 text-3xl font-semibold text-white">
                    {{ $registeredCases ?? ($dgAnalytics['summary']['registered'] ?? 0) }}
                </p>
            </div>

            <div
                class="flex h-10 w-10 items-center justify-center
                       rounded-xl border border-slate-700
                       bg-[#0D172A] text-slate-400"
            >
                ◫
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-500">
            Newly registered cases
        </p>
    </a>


    <a
        href="{{ route('cases.index', ['status' => 'Routed']) }}"
        class="group rounded-2xl border border-slate-800
               bg-[#111A2E] p-5 transition
               hover:-translate-y-0.5
               hover:border-purple-500/30"
    >
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500">
                    Routed
                </p>

                <p class="mt-3 text-3xl font-semibold text-white">
                    {{ $routedCases ?? ($dgAnalytics['summary']['routed'] ?? 0) }}
                </p>
            </div>

            <div
                class="flex h-10 w-10 items-center justify-center
                       rounded-xl border border-purple-500/20
                       bg-purple-500/10 text-purple-300"
            >
                ⇄
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-500">
            Cases routed to divisions
        </p>
    </a>


    <a
        href="{{ route('cases.index', ['status' => 'Under Processing']) }}"
        class="group rounded-2xl border border-slate-800
               bg-[#111A2E] p-5 transition
               hover:-translate-y-0.5
               hover:border-yellow-500/30"
    >
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500">
                    Active
                </p>

                <p class="mt-3 text-3xl font-semibold text-white">
                    {{ $activeCases ?? ($dgAnalytics['summary']['under_processing'] ?? 0) }}
                </p>
            </div>

            <div
                class="flex h-10 w-10 items-center justify-center
                       rounded-xl border border-yellow-500/20
                       bg-yellow-500/10 text-yellow-300"
            >
                ◎
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-500">
            Cases currently under processing
        </p>
    </a>


    <a
        href="{{ route('cases.index', ['status' => 'Closed']) }}"
        class="group rounded-2xl border border-slate-800
               bg-[#111A2E] p-5 transition
               hover:-translate-y-0.5
               hover:border-green-500/30"
    >
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500">
                    Closed
                </p>

                <p class="mt-3 text-3xl font-semibold text-white">
                    {{ $closedCases ?? ($dgAnalytics['summary']['closed'] ?? 0) }}
                </p>
            </div>

            <div
                class="flex h-10 w-10 items-center justify-center
                       rounded-xl border border-green-500/20
                       bg-green-500/10 text-green-300"
            >
                ✓
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-500">
            Completed case records
        </p>
    </a>

</div>


{{-- =========================================================
     QUICK ACTIONS
========================================================= --}}
<div
    class="rounded-2xl
           border border-slate-800
           bg-[#111A2E]
           p-6
           mb-6"
>

    <div
        class="flex flex-col gap-3
               md:flex-row
               md:items-center
               md:justify-between"
    >

        <div>
            <h3 class="text-lg font-semibold text-white">
                Quick Actions
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Frequently used actions based on your role and responsibilities.
            </p>
        </div>

        <span
            class="w-fit rounded-full
                   border border-slate-700
                   bg-[#0D172A]
                   px-3 py-1
                   text-[10px]
                   uppercase tracking-wider
                   text-slate-400"
        >
            {{ $roleLabel }}
        </span>

    </div>


    <div
        class="mt-5
               grid grid-cols-1
               md:grid-cols-2
               xl:grid-cols-4
               gap-4"
    >

        @foreach($quickLinks as $action)

            <a
                href="{{ $action['url'] }}"

                class="group relative overflow-hidden
                       rounded-2xl
                       border border-slate-800
                       bg-[#0D172A]
                       p-5
                       transition-all duration-200
                       hover:-translate-y-0.5
                       hover:border-blue-500/30
                       hover:bg-[#101C34]
                       hover:shadow-lg
                       hover:shadow-blue-950/10"
            >

                <div
                    class="absolute
                           -right-10 -top-10
                           h-24 w-24
                           rounded-full
                           bg-blue-500/0
                           blur-2xl
                           transition
                           group-hover:bg-blue-500/10"
                ></div>

                <div class="relative z-10">

                    <div
                        class="flex h-11 w-11
                               items-center justify-center
                               rounded-xl
                               border border-blue-500/20
                               bg-blue-500/10
                               text-lg
                               text-blue-300
                               transition
                               group-hover:bg-blue-500/20"
                    >
                        {{ $action['icon'] }}
                    </div>

                    <h4
                        class="mt-4
                               text-sm
                               font-semibold
                               text-white"
                    >
                        {{ $action['label'] }}
                    </h4>

                    <p
                        class="mt-2
                               min-h-[40px]
                               text-xs
                               leading-relaxed
                               text-slate-500"
                    >
                        {{ $action['desc'] }}
                    </p>

                    <div
                        class="mt-4
                               flex items-center
                               justify-between"
                    >
                        <span
                            class="text-xs
                                   font-medium
                                   text-blue-400"
                        >
                            Open
                        </span>

                        <span
                            class="text-blue-400
                                   transition-transform
                                   group-hover:translate-x-1"
                        >
                            →
                        </span>
                    </div>

                </div>

            </a>

        @endforeach

    </div>

</div>


{{-- =========================================================
     WORKLOAD + RECENT CASES
========================================================= --}}
<div
    class="grid grid-cols-1
           xl:grid-cols-3
           gap-6"
>

    <div
        class="rounded-2xl
               border border-slate-800
               bg-[#111A2E]
               p-6"
    >

        <div class="border-b border-slate-800 pb-4">
            <h3 class="text-lg font-semibold text-white">
                Workload Summary
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Current distribution of your visible cases.
            </p>
        </div>

        @php
            $maxCases = max(
                $totalCases,
                1
            );

            $workloadItems = [
                [
                    'label' => 'Registered',
                    'value' => $registeredCases,
                    'class' => 'bg-slate-500',
                ],
                [
                    'label' => 'Routed',
                    'value' => $routedCases,
                    'class' => 'bg-purple-500',
                ],
                [
                    'label' => 'Under Processing',
                    'value' => $activeCases,
                    'class' => 'bg-blue-500',
                ],
                [
                    'label' => 'Closed',
                    'value' => $closedCases,
                    'class' => 'bg-green-500',
                ],
            ];
        @endphp

        <div class="mt-6 space-y-5">

            @foreach($workloadItems as $item)

                @php
                    $percentage =
                        ($item['value'] / $maxCases) * 100;
                @endphp

                <div>
                    <div
                        class="flex items-center
                               justify-between
                               text-sm"
                    >
                        <span class="text-slate-300">
                            {{ $item['label'] }}
                        </span>

                        <span class="font-medium text-white">
                            {{ $item['value'] }}
                        </span>
                    </div>

                    <div
                        class="mt-2
                               h-2
                               overflow-hidden
                               rounded-full
                               bg-slate-800"
                    >
                        <div
                            class="h-full rounded-full {{ $item['class'] }}"
                            style="width: {{ min($percentage, 100) }}%"
                        ></div>
                    </div>
                </div>

            @endforeach

        </div>

        <a
            href="{{ route('cases.index') }}"
            class="mt-6
                   flex w-full
                   items-center justify-center
                   rounded-xl
                   border border-slate-700
                   bg-[#0D172A]
                   px-4 py-3
                   text-sm font-medium
                   text-slate-300
                   transition
                   hover:border-blue-500/30
                   hover:text-white"
        >
            View Case Registry
        </a>

    </div>


    <div
        class="xl:col-span-2
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]
               overflow-hidden"
    >

        <div
            class="flex flex-col gap-3
                   border-b border-slate-800
                   p-6
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>
                <h3 class="text-lg font-semibold text-white">
                    Recent Cases
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Most recently registered cases available to your account.
                </p>
            </div>

            <a
                href="{{ route('cases.index') }}"
                class="text-sm
                       font-medium
                       text-blue-400
                       hover:text-blue-300"
            >
                View all →
            </a>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead
                    class="border-b border-slate-800
                           bg-[#0D172A]"
                >
                    <tr
                        class="text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >
                        <th class="px-6 py-4">
                            Case
                        </th>

                        <th class="px-6 py-4">
                            Category
                        </th>

                        <th class="px-6 py-4">
                            Status
                        </th>

                        <th class="px-6 py-4">
                            Received
                        </th>

                        <th class="px-6 py-4 text-right">
                            Action
                        </th>
                    </tr>
                </thead>


                <tbody>

                    @forelse(($recentCases ?? collect()) as $case)

                        @php
                            $caseStatusClass = match($case->current_status) {
                                'Registered' =>
                                    'border-slate-600 bg-slate-500/10 text-slate-300',

                                'Routed' =>
                                    'border-purple-500/20 bg-purple-500/10 text-purple-300',

                                'Under Processing' =>
                                    'border-blue-500/20 bg-blue-500/10 text-blue-300',

                                'Closed' =>
                                    'border-green-500/20 bg-green-500/10 text-green-300',

                                default =>
                                    'border-slate-700 bg-slate-500/10 text-slate-300',
                            };
                        @endphp

                        <tr
                            class="border-b
                                   border-slate-800/70
                                   transition
                                   hover:bg-[#152238]"
                        >

                            <td class="px-6 py-4">
                                <p
                                    class="text-sm
                                           font-semibold
                                           text-blue-300"
                                >
                                    {{ $case->case_number }}
                                </p>

                                <p
                                    class="mt-1
                                           max-w-[220px]
                                           truncate
                                           text-xs
                                           text-slate-500"
                                >
                                    {{ $case->complainant_name ?? 'Complainant not recorded' }}
                                </p>
                            </td>

                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-slate-300"
                            >
                                {{ $case->complaint_category }}
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex
                                           rounded-full
                                           border
                                           px-2.5 py-1
                                           text-[10px]
                                           {{ $caseStatusClass }}"
                                >
                                    {{ $case->current_status }}
                                </span>
                            </td>

                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-slate-400"
                            >
                                {{ $case->received_date?->format('d M Y') ?? __('dashboard.not_recorded') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a
                                    href="{{ route('cases.show', $case) }}"
                                    class="text-sm
                                           font-medium
                                           text-blue-400
                                           hover:text-blue-300"
                                >
                                    Open →
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-6 py-12 text-center"
                            >

                                <div
                                    class="mx-auto
                                           flex h-12 w-12
                                           items-center justify-center
                                           rounded-xl
                                           border border-slate-800
                                           bg-[#0D172A]
                                           text-slate-500"
                                >
                                    ▤
                                </div>

                                <p
                                    class="mt-4
                                           text-sm
                                           font-medium
                                           text-slate-300"
                                >
                                    No cases available
                                </p>

                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    Cases visible to your account will appear here.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection