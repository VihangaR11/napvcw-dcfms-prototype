@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section(
    'page-description',
    'Operational overview of the Digital Case Flow Management System.'
)

@section('content')

@php
    $user = auth()->user();

    $roleLabel = ucwords(str_replace('_', ' ', $user->role));

    $quickLinks = match ($user->role) {
        'board_secretary' => [
            ['label' => 'Register New Case', 'desc' => 'Create a new complaint or case record.', 'icon' => '＋'],
            ['label' => 'Route Cases', 'desc' => 'Forward cases to relevant operational divisions.', 'icon' => '⇄'],
            ['label' => 'All Cases', 'desc' => 'Review and monitor registered cases.', 'icon' => '▤'],
        ],

        'legal_director', 'legal_officer', 'investigation_officer' => [
            ['label' => 'Legal Cases', 'desc' => 'Review cases routed to Law & Law Enforcement.', 'icon' => '⚖'],
            ['label' => 'Assigned Cases', 'desc' => 'View cases currently assigned to you.', 'icon' => '◎'],
            ['label' => 'Legal Workflow', 'desc' => 'Track investigations, observations and recommendations.', 'icon' => '→'],
        ],

        'protection_director', 'protection_officer' => [
            ['label' => 'Protection Cases', 'desc' => 'Review protection-related complaints and requests.', 'icon' => '◈'],
            ['label' => 'Threat Assessments', 'desc' => 'Track requests sent to Police Protection.', 'icon' => '⌖'],
            ['label' => 'Follow-up Cases', 'desc' => 'Monitor ongoing protection actions.', 'icon' => '↻'],
        ],

        'police_protection_officer' => [
            ['label' => 'Assessment Requests', 'desc' => 'Review threat assessment requests.', 'icon' => '⌖'],
            ['label' => 'Pending Assessments', 'desc' => 'View assessments currently in progress.', 'icon' => '◷'],
            ['label' => 'Completed Assessments', 'desc' => 'Review completed threat assessment records.', 'icon' => '✓'],
        ],

        'assistance_officer' => [
            ['label' => 'Assistance Cases', 'desc' => 'Review cases requiring victim or witness assistance.', 'icon' => '✦'],
            ['label' => 'Assigned Cases', 'desc' => 'View assistance cases assigned to you.', 'icon' => '◎'],
            ['label' => 'Follow-up Actions', 'desc' => 'Track assistance and referral progress.', 'icon' => '↻'],
        ],

        'director_general', 'system_admin' => [
            ['label' => 'All Cases', 'desc' => 'Monitor the complete institutional case flow.', 'icon' => '▤'],
            ['label' => 'Division Overview', 'desc' => 'Review progress across all operational divisions.', 'icon' => '▦'],
            ['label' => 'Case Reports', 'desc' => 'Access case summaries and operational reports.', 'icon' => '▧'],
        ],

        default => [
            ['label' => 'My Cases', 'desc' => 'View cases assigned to your account.', 'icon' => '◎'],
        ],
    };
@endphp


{{-- =========================================================
     HERO / WELCOME
========================================================= --}}
<div
    class="relative overflow-hidden rounded-3xl
           border border-slate-800
           bg-gradient-to-br from-[#101A35] via-[#111A2E] to-[#0B1226]
           p-7 mb-6"
>

    <div
        class="absolute -top-16 -right-16
               h-56 w-56 rounded-full
               bg-blue-600/10 blur-3xl">
    </div>

    <div
        class="absolute -bottom-20 left-1/3
               h-56 w-56 rounded-full
               bg-purple-600/10 blur-3xl">
    </div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

        <div class="max-w-3xl">

            <div
                class="inline-flex items-center gap-2
                       rounded-full border border-blue-500/20
                       bg-blue-500/10
                       px-3 py-1
                       text-[10px] font-semibold uppercase tracking-[0.18em]
                       text-blue-300"
            >
                NAPVCW Digital Service
            </div>

            <p class="mt-5 text-sm text-slate-400">
                Welcome back,
            </p>

            <h2 class="mt-1 text-3xl font-semibold tracking-tight">
                {{ $user->name }}
            </h2>

            <div class="mt-3 flex flex-wrap items-center gap-3">

                <span
                    class="rounded-full border border-blue-500/20
                           bg-blue-500/10
                           px-3 py-1
                           text-xs text-blue-300"
                >
                    {{ $roleLabel }}
                </span>

                <span
                    class="rounded-full border border-slate-700
                           bg-[#0C1528]
                           px-3 py-1
                           text-xs text-slate-400"
                >
                    {{ $user->division }}
                </span>

                @if($user->is_active)
                    <span
                        class="rounded-full border border-green-500/20
                               bg-green-500/10
                               px-3 py-1
                               text-xs text-green-300"
                    >
                        Active Account
                    </span>
                @endif

            </div>

            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-slate-400">
                Use this workspace to register, route, review and track case
                activities across the core operational divisions of NAPVCW.
            </p>

        </div>


        <div
            class="shrink-0 rounded-3xl
                   border border-slate-700
                   bg-white/95
                   p-4 shadow-xl"
        >
            <img
                src="{{ asset('images/napvcw-logo.png') }}"
                alt="NAPVCW Logo"
                class="h-24 w-24 object-contain"
            >
        </div>

    </div>

</div>


{{-- =========================================================
     USER / ACCESS SUMMARY
========================================================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">
        <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500">
            Employee Number
        </p>

        <p class="mt-3 text-2xl font-semibold text-white">
            {{ $user->employee_number }}
        </p>

        <p class="mt-1 text-xs text-slate-500">
            Authorized staff identifier
        </p>
    </div>


    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">
        <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500">
            Role
        </p>

        <p class="mt-3 text-lg font-semibold text-white">
            {{ $roleLabel }}
        </p>

        <p class="mt-1 text-xs text-slate-500">
            Role-based system access
        </p>
    </div>


    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">
        <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500">
            Division
        </p>

        <p class="mt-3 text-lg font-semibold text-white">
            {{ $user->division }}
        </p>

        <p class="mt-1 text-xs text-slate-500">
            Current operational workspace
        </p>
    </div>


    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">
        <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500">
            Account Status
        </p>

        <div class="mt-3">

            @if($user->is_active)

                <span
                    class="inline-flex items-center gap-2
                           rounded-full
                           border border-green-500/20
                           bg-green-500/10
                           px-3 py-1.5
                           text-sm text-green-300"
                >
                    <span class="h-2 w-2 rounded-full bg-green-400"></span>
                    Active
                </span>

            @else

                <span
                    class="inline-flex items-center gap-2
                           rounded-full
                           border border-red-500/20
                           bg-red-500/10
                           px-3 py-1.5
                           text-sm text-red-300"
                >
                    <span class="h-2 w-2 rounded-full bg-red-400"></span>
                    Inactive
                </span>

            @endif

        </div>

        <p class="mt-2 text-xs text-slate-500">
            System access availability
        </p>
    </div>

</div>


{{-- =========================================================
     QUICK ACTIONS + SYSTEM OVERVIEW
========================================================= --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">

    {{-- Quick Actions --}}
    <div class="xl:col-span-2 rounded-2xl border border-slate-800 bg-[#111A2E] p-6">

        <div class="flex items-center justify-between">

            <div>
                <h3 class="text-lg font-semibold">
                    Quick Actions
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Frequently used functions for your assigned role.
                </p>
            </div>

            <span
                class="rounded-full border border-slate-700
                       bg-[#0C1528]
                       px-3 py-1
                       text-[10px] uppercase tracking-wider text-slate-400"
            >
                Role Based
            </span>

        </div>


        <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">

            @foreach($quickLinks as $action)

                <a
                    href="#"
                    class="group rounded-2xl
                           border border-slate-800
                           bg-[#0D172A]
                           p-5
                           transition
                           hover:-translate-y-0.5
                           hover:border-blue-500/30
                           hover:bg-[#101C34]"
                >

                    <div
                        class="flex h-10 w-10 items-center justify-center
                               rounded-xl border border-blue-500/20
                               bg-blue-500/10
                               text-lg text-blue-300"
                    >
                        {{ $action['icon'] }}
                    </div>

                    <h4 class="mt-4 font-semibold text-white">
                        {{ $action['label'] }}
                    </h4>

                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        {{ $action['desc'] }}
                    </p>

                    <div class="mt-4 text-xs font-medium text-blue-400">
                        Open module →
                    </div>

                </a>

            @endforeach

        </div>

    </div>


    {{-- Institutional Identity --}}
    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">

        <div class="flex items-center gap-4">

            <div class="h-14 w-14 rounded-xl bg-white p-1.5">
                <img
                    src="{{ asset('images/gov-logo.png') }}"
                    alt="Government of Sri Lanka"
                    class="h-full w-full object-contain"
                >
            </div>

            <div class="h-14 w-14 rounded-full bg-white p-1.5">
                <img
                    src="{{ asset('images/napvcw-logo.png') }}"
                    alt="NAPVCW"
                    class="h-full w-full object-contain"
                >
            </div>

        </div>

        <h3 class="mt-5 text-lg font-semibold">
            Institutional System
        </h3>

        <p class="mt-2 text-sm leading-relaxed text-slate-400">
            Digital Case Flow Management System of the National Authority
            for the Protection of Victims of Crime and Witnesses.
        </p>

        <div class="mt-5 border-t border-slate-800 pt-5 space-y-3 text-sm">

            <div class="flex items-center justify-between">
                <span class="text-slate-500">System Type</span>
                <span class="text-slate-300">Internal Prototype</span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-slate-500">Hosting Model</span>
                <span class="text-slate-300">Cloud Ready</span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-slate-500">Access Model</span>
                <span class="text-slate-300">Role Based</span>
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     OPERATIONAL MODULES
========================================================= --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">

        <div class="flex items-center justify-between">

            <div>
                <h3 class="text-lg font-semibold">
                    Operational Modules
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Core divisions included in the current DCFMS scope.
                </p>
            </div>

        </div>


        <div class="mt-5 space-y-3">

            <div
                class="flex items-center justify-between
                       rounded-xl border border-slate-800
                       bg-[#0D172A]
                       px-4 py-3"
            >
                <div>
                    <p class="text-sm font-medium">Board Secretariat</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Complaint intake, registration and case routing
                    </p>
                </div>

                <span class="text-blue-400">→</span>
            </div>


            <div
                class="flex items-center justify-between
                       rounded-xl border border-slate-800
                       bg-[#0D172A]
                       px-4 py-3"
            >
                <div>
                    <p class="text-sm font-medium">Law & Law Enforcement</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Legal review, inquiry, investigation and recommendation
                    </p>
                </div>

                <span class="text-blue-400">→</span>
            </div>


            <div
                class="flex items-center justify-between
                       rounded-xl border border-slate-800
                       bg-[#0D172A]
                       px-4 py-3"
            >
                <div>
                    <p class="text-sm font-medium">Protection Services</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Protection requests, threat assessment and follow-up
                    </p>
                </div>

                <span class="text-blue-400">→</span>
            </div>


            <div
                class="flex items-center justify-between
                       rounded-xl border border-slate-800
                       bg-[#0D172A]
                       px-4 py-3"
            >
                <div>
                    <p class="text-sm font-medium">Police Protection</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Threat assessment and protection coordination
                    </p>
                </div>

                <span class="text-blue-400">→</span>
            </div>


            <div
                class="flex items-center justify-between
                       rounded-xl border border-slate-800
                       bg-[#0D172A]
                       px-4 py-3"
            >
                <div>
                    <p class="text-sm font-medium">Assistance Services</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Victim and witness assistance workflow
                    </p>
                </div>

                <span class="text-blue-400">→</span>
            </div>

        </div>

    </div>


    {{-- Current Development Scope --}}
    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">

        <h3 class="text-lg font-semibold">
            Prototype Capability Status
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Current development progress of the DCFMS prototype.
        </p>


        <div class="mt-6 space-y-5">

            <div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-300">Employee Authentication</span>
                    <span class="text-green-400">Completed</span>
                </div>

                <div class="mt-2 h-1.5 rounded-full bg-slate-800">
                    <div class="h-1.5 w-full rounded-full bg-green-500"></div>
                </div>
            </div>


            <div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-300">Role-Based Interface</span>
                    <span class="text-green-400">Completed</span>
                </div>

                <div class="mt-2 h-1.5 rounded-full bg-slate-800">
                    <div class="h-1.5 w-full rounded-full bg-green-500"></div>
                </div>
            </div>


            <div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-300">Case Registration</span>
                    <span class="text-blue-400">Next</span>
                </div>

                <div class="mt-2 h-1.5 rounded-full bg-slate-800">
                    <div class="h-1.5 w-1/4 rounded-full bg-blue-500"></div>
                </div>
            </div>


            <div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-300">Division Workflows</span>
                    <span class="text-slate-500">Pending</span>
                </div>

                <div class="mt-2 h-1.5 rounded-full bg-slate-800">
                    <div class="h-1.5 w-[8%] rounded-full bg-slate-600"></div>
                </div>
            </div>


            <div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-300">Case Summary PDF</span>
                    <span class="text-slate-500">Pending</span>
                </div>

                <div class="mt-2 h-1.5 rounded-full bg-slate-800">
                    <div class="h-1.5 w-[5%] rounded-full bg-slate-600"></div>
                </div>
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     SECURITY / SCOPE NOTE
========================================================= --}}
<div
    class="rounded-2xl border border-blue-500/20
           bg-blue-500/[0.06]
           p-5"
>

    <div class="flex items-start gap-4">

        <div
            class="flex h-10 w-10 shrink-0
                   items-center justify-center
                   rounded-xl bg-blue-500/10
                   text-blue-300"
        >
            🔒
        </div>

        <div>

            <h4 class="font-semibold text-slate-200">
                Restricted Government Case Management Environment
            </h4>

            <p class="mt-2 text-sm leading-relaxed text-slate-500">
                Access to case information is controlled according to assigned
                roles and divisions. The current prototype is intended for
                authorized internal testing and demonstration using non-sensitive
                sample data only.
            </p>

        </div>

    </div>

</div>

@endsection