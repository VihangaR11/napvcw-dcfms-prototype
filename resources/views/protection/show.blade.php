@extends('layouts.app')



@section('title', __('protection.title'))



@section('page-title', __('protection.page_title'))



@section(

    'page-description',

    __('protection.page_description')

)



@section('page-actions')

    <a

        href="{{ route('cases.show', $case) }}"

        class="inline-flex items-center gap-2 rounded-xl border border-slate-700

               bg-[#111A2E] px-5 py-2.5 text-sm text-slate-300

               hover:bg-[#152238]"

    >

        ← {{ __('protection.master_case') }}

    </a>

@endsection



@section('content')



@php

    $statusColor = match($protectionDetail->protection_status) {

        'Protection Request {{ __('protection.received') }}' => 'border-slate-600 bg-slate-500/10 text-slate-300',

        '{{ __('protection.protection_officer') }} Assigned' => 'border-blue-500/20 bg-blue-500/10 text-blue-300',

        'Threat {{ __('protection.assessment') }} Requested' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-300',

        'Awaiting Threat {{ __('protection.assessment') }}' => 'border-orange-500/20 bg-orange-500/10 text-orange-300',

        'Threat {{ __('protection.assessment') }} {{ __('protection.received') }}' => 'border-cyan-500/20 bg-cyan-500/10 text-cyan-300',

        '{{ __('protection.protection_decision') }} Pending' => 'border-purple-500/20 bg-purple-500/10 text-purple-300',

        'Protection Active' => 'border-green-500/20 bg-green-500/10 text-green-300',

        'Protection Under Review' => 'border-blue-500/20 bg-blue-500/10 text-blue-300',

        'Protection Continued' => 'border-green-500/20 bg-green-500/10 text-green-300',

        'Protection Withdrawn' => 'border-orange-500/20 bg-orange-500/10 text-orange-300',

        'Protection Terminated' => 'border-red-500/20 bg-red-500/10 text-red-300',

        'Closed' => 'border-green-500/20 bg-green-500/10 text-green-300',

        default => 'border-blue-500/20 bg-blue-500/10 text-blue-300',

    };


    $protectionStatusLabel = match($protectionDetail->protection_status) {
        'Protection Request {{ __('protection.received') }}' => __('protection.statuses.protection_request_received'),
        '{{ __('protection.protection_officer') }} Assigned' => __('protection.statuses.protection_officer_assigned'),
        'Threat {{ __('protection.assessment') }} Requested' => __('protection.statuses.threat_assessment_requested'),
        'Awaiting Threat {{ __('protection.assessment') }}' => __('protection.statuses.awaiting_threat_assessment'),
        'Threat {{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.statuses.threat_assessment_received'),
        '{{ __('protection.protection_decision') }} Pending' => __('protection.statuses.protection_decision_pending'),
        'Protection Active' => __('protection.statuses.protection_active'),
        'Protection Under Review' => __('protection.statuses.protection_under_review'),
        'Protection Continued' => __('protection.statuses.protection_continued'),
        'Protection Withdrawn' => __('protection.statuses.protection_withdrawn'),
        'Protection Terminated' => __('protection.statuses.protection_terminated'),
        'Closed' => __('protection.statuses.closed'),
        default => $protectionDetail->protection_status,
    };

    $assessmentStatusLabel = match($protectionDetail->threat_assessment_status) {
        'Not Requested' => __('protection.assessment_statuses.not_requested'),
        'Request Sent' => __('protection.assessment_statuses.request_sent'),
        'Request {{ __('protection.received') }}' => __('protection.assessment_statuses.request_received'),
        '{{ __('protection.assessment') }} In Progress' => __('protection.assessment_statuses.assessment_in_progress'),
        '{{ __('protection.assessment') }} Completed' => __('protection.assessment_statuses.assessment_completed'),
        '{{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.assessment_statuses.assessment_received'),
        default => $protectionDetail->threat_assessment_status,
    };

    $threatLevelLabel = match($protectionDetail->threat_level) {
        'Low' => __('protection.threat_levels.low'),
        'Moderate' => __('protection.threat_levels.moderate'),
        'High' => __('protection.threat_levels.high'),
        'Critical' => __('protection.threat_levels.critical'),
        null => __('protection.pending'),
        default => $protectionDetail->threat_level,
    };

@endphp



<form

    method="POST"

    action="{{ route('protection.update', $case) }}"

    class="space-y-6"

>

    @csrf



    <div

        class="relative overflow-hidden rounded-3xl

               border border-slate-800

               bg-gradient-to-br from-[#101A35] via-[#111A2E] to-[#0B1226]

               p-6 lg:p-7"

    >

        <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-blue-600/10 blur-3xl"></div>

        <div class="absolute -bottom-24 left-1/3 h-60 w-60 rounded-full bg-purple-600/10 blur-3xl"></div>



        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">



            <div class="max-w-4xl">



                <div class="flex flex-wrap gap-2">

                    <span class="rounded-full border px-3 py-1 text-xs font-medium {{ $statusColor }}">

                        {{ $protectionStatusLabel }}

                    </span>



                    <span

                        class="rounded-full border border-slate-700 bg-[#0C1528]

                               px-3 py-1 text-xs text-slate-300"

                    >

                        {{ __('protection.division_name') }}

                    </span>

                </div>



                <p class="mt-5 text-xs uppercase tracking-wider text-slate-500">

                    {{ __('protection.master_case') }}

                </p>



                <h2 class="mt-1 text-3xl font-semibold text-blue-300">

                    {{ $case->case_number }}

                </h2>



                <p class="mt-4 text-sm leading-relaxed text-slate-400">

                    {{ $case->complaint_summary }}

                </p>



                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">

                    <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                        <p class="text-[9px] uppercase tracking-wider text-slate-500">

                            {{ __('protection.received') }}

                        </p>

                        <p class="mt-2 text-sm font-medium">

                            {{ $case->received_date->format('d M Y') }}

                        </p>

                    </div>



                    <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                        <p class="text-[9px] uppercase tracking-wider text-slate-500">

                            {{ __('protection.threat_type') }}

                        </p>

                        <p class="mt-2 text-sm font-medium">

                            {{ $protectionDetail->threat_type ?: __('protection.not_recorded') }}

                        </p>

                    </div>



                    <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                        <p class="text-[9px] uppercase tracking-wider text-slate-500">

                            {{ __('protection.threat_level') }}

                        </p>

                        <p class="mt-2 text-sm font-medium">

                            {{ $threatLevelLabel }}

                        </p>

                    </div>



                    <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                        <p class="text-[9px] uppercase tracking-wider text-slate-500">

                            {{ __('protection.assessment') }}

                        </p>

                        <p class="mt-2 text-sm font-medium">

                            {{ $assessmentStatusLabel }}

                        </p>

                    </div>

                </div>

            </div>



            <div class="shrink-0 rounded-2xl border border-slate-700 bg-white/95 p-3">

                <img

                    src="{{ asset('images/napvcw-logo.png') }}"

                    alt="NAPVCW"

                    class="h-20 w-20 object-contain"

                >

            </div>

        </div>

    </div>



    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">

        <div class="border-b border-slate-800 pb-4">

            <h3 class="text-lg font-semibold">

                {{ __('protection.registration_assignment') }}

            </h3>



            <p class="mt-1 text-sm text-slate-500">

                {{ __('protection.registration_assignment_desc') }}

            </p>

        </div>



        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.protection_officer') }}

                </label>



                <select

                    name="protection_officer_id"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

                    <option value="">{{ __('protection.select_protection_officer') }}</option>



                    @foreach($protectionOfficers as $officer)

                        <option

                            value="{{ $officer->id }}"

                            @selected(

                                old(

                                    'protection_officer_id',

                                    $protectionDetail->protection_officer_id

                                ) == $officer->id

                            )

                        >

                            {{ $officer->name }} — {{ $officer->employee_number }}

                        </option>

                    @endforeach

                </select>

            </div>



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.workflow_status') }} *

                </label>



                <select

                    name="protection_status"

                    required

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

                    @foreach([

                        'Protection Request {{ __('protection.received') }}',

                        '{{ __('protection.protection_officer') }} Assigned',

                        'Threat {{ __('protection.assessment') }} Requested',

                        'Awaiting Threat {{ __('protection.assessment') }}',

                        'Threat {{ __('protection.assessment') }} {{ __('protection.received') }}',

                        '{{ __('protection.protection_decision') }} Pending',

                        'Protection Active',

                        'Protection Under Review',

                        'Protection Continued',

                        'Protection Withdrawn',

                        'Protection Terminated',

                        'Closed',

                    ] as $status)

                        <option

                            value="{{ match($status) {
                                'Protection Request {{ __('protection.received') }}' => __('protection.statuses.protection_request_received'),
                                '{{ __('protection.protection_officer') }} Assigned' => __('protection.statuses.protection_officer_assigned'),
                                'Threat {{ __('protection.assessment') }} Requested' => __('protection.statuses.threat_assessment_requested'),
                                'Awaiting Threat {{ __('protection.assessment') }}' => __('protection.statuses.awaiting_threat_assessment'),
                                'Threat {{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.statuses.threat_assessment_received'),
                                '{{ __('protection.protection_decision') }} Pending' => __('protection.statuses.protection_decision_pending'),
                                'Protection Active' => __('protection.statuses.protection_active'),
                                'Protection Under Review' => __('protection.statuses.protection_under_review'),
                                'Protection Continued' => __('protection.statuses.protection_continued'),
                                'Protection Withdrawn' => __('protection.statuses.protection_withdrawn'),
                                'Protection Terminated' => __('protection.statuses.protection_terminated'),
                                'Closed' => __('protection.statuses.closed'),
                                'Not Requested' => __('protection.assessment_statuses.not_requested'),
                                'Request Sent' => __('protection.assessment_statuses.request_sent'),
                                'Request {{ __('protection.received') }}' => __('protection.assessment_statuses.request_received'),
                                '{{ __('protection.assessment') }} In Progress' => __('protection.assessment_statuses.assessment_in_progress'),
                                '{{ __('protection.assessment') }} Completed' => __('protection.assessment_statuses.assessment_completed'),
                                '{{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.assessment_statuses.assessment_received'),
                                default => $status,
                            } }}"

                            @selected(

                                old(

                                    'protection_status',

                                    $protectionDetail->protection_status

                                ) === $status

                            )

                        >

                            {{ match($status) {
                                'Protection Request {{ __('protection.received') }}' => __('protection.statuses.protection_request_received'),
                                '{{ __('protection.protection_officer') }} Assigned' => __('protection.statuses.protection_officer_assigned'),
                                'Threat {{ __('protection.assessment') }} Requested' => __('protection.statuses.threat_assessment_requested'),
                                'Awaiting Threat {{ __('protection.assessment') }}' => __('protection.statuses.awaiting_threat_assessment'),
                                'Threat {{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.statuses.threat_assessment_received'),
                                '{{ __('protection.protection_decision') }} Pending' => __('protection.statuses.protection_decision_pending'),
                                'Protection Active' => __('protection.statuses.protection_active'),
                                'Protection Under Review' => __('protection.statuses.protection_under_review'),
                                'Protection Continued' => __('protection.statuses.protection_continued'),
                                'Protection Withdrawn' => __('protection.statuses.protection_withdrawn'),
                                'Protection Terminated' => __('protection.statuses.protection_terminated'),
                                'Closed' => __('protection.statuses.closed'),
                                'Not Requested' => __('protection.assessment_statuses.not_requested'),
                                'Request Sent' => __('protection.assessment_statuses.request_sent'),
                                'Request {{ __('protection.received') }}' => __('protection.assessment_statuses.request_received'),
                                '{{ __('protection.assessment') }} In Progress' => __('protection.assessment_statuses.assessment_in_progress'),
                                '{{ __('protection.assessment') }} Completed' => __('protection.assessment_statuses.assessment_completed'),
                                '{{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.assessment_statuses.assessment_received'),
                                default => $status,
                            } }}

                        </option>

                    @endforeach

                </select>

            </div>



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.threat_type') }}

                </label>



                <input

                    type="text"

                    name="threat_type"

                    value="{{ old('threat_type', $protectionDetail->threat_type) }}"

                    placeholder="{{ __('protection.threat_type_placeholder') }}"



                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.local_police_station') }}

                </label>



                <input

                    type="text"

                    name="local_police_station"

                    value="{{ old(

                        'local_police_station',

                        $protectionDetail->local_police_station

                    ) }}"



                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>



        </div>

    </div>



    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <div class="border-b border-slate-800 pb-4">

            <h3 class="text-lg font-semibold">

                {{ __('protection.threat_assessment_coordination') }}

            </h3>



            <p class="mt-1 text-sm text-slate-500">

                {{ __('protection.threat_assessment_coordination_desc') }}

            </p>

        </div>



        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.threat_assessment_requested_date') }}

                </label>



                <input

                    type="date"

                    name="threat_assessment_requested_date"

                    value="{{ old(

                        'threat_assessment_requested_date',

                        optional($protectionDetail->threat_assessment_requested_date)->format('Y-m-d')

                    ) }}"



                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.police_reference') }}

                </label>



                <input

                    type="text"

                    name="police_reference"

                    value="{{ old(

                        'police_reference',

                        $protectionDetail->police_reference

                    ) }}"



                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.threat_assessment_status') }}

                </label>



                <select

                    name="threat_assessment_status"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

                    @foreach([

                        'Not Requested',

                        'Request Sent',

                        'Request {{ __('protection.received') }}',

                        '{{ __('protection.assessment') }} In Progress',

                        '{{ __('protection.assessment') }} Completed',

                        '{{ __('protection.assessment') }} {{ __('protection.received') }}',

                    ] as $status)

                        <option

                            value="{{ match($status) {
                                'Protection Request {{ __('protection.received') }}' => __('protection.statuses.protection_request_received'),
                                '{{ __('protection.protection_officer') }} Assigned' => __('protection.statuses.protection_officer_assigned'),
                                'Threat {{ __('protection.assessment') }} Requested' => __('protection.statuses.threat_assessment_requested'),
                                'Awaiting Threat {{ __('protection.assessment') }}' => __('protection.statuses.awaiting_threat_assessment'),
                                'Threat {{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.statuses.threat_assessment_received'),
                                '{{ __('protection.protection_decision') }} Pending' => __('protection.statuses.protection_decision_pending'),
                                'Protection Active' => __('protection.statuses.protection_active'),
                                'Protection Under Review' => __('protection.statuses.protection_under_review'),
                                'Protection Continued' => __('protection.statuses.protection_continued'),
                                'Protection Withdrawn' => __('protection.statuses.protection_withdrawn'),
                                'Protection Terminated' => __('protection.statuses.protection_terminated'),
                                'Closed' => __('protection.statuses.closed'),
                                'Not Requested' => __('protection.assessment_statuses.not_requested'),
                                'Request Sent' => __('protection.assessment_statuses.request_sent'),
                                'Request {{ __('protection.received') }}' => __('protection.assessment_statuses.request_received'),
                                '{{ __('protection.assessment') }} In Progress' => __('protection.assessment_statuses.assessment_in_progress'),
                                '{{ __('protection.assessment') }} Completed' => __('protection.assessment_statuses.assessment_completed'),
                                '{{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.assessment_statuses.assessment_received'),
                                default => $status,
                            } }}"

                            @selected(

                                old(

                                    'threat_assessment_status',

                                    $protectionDetail->threat_assessment_status

                                ) === $status

                            )

                        >

                            {{ match($status) {
                                'Protection Request {{ __('protection.received') }}' => __('protection.statuses.protection_request_received'),
                                '{{ __('protection.protection_officer') }} Assigned' => __('protection.statuses.protection_officer_assigned'),
                                'Threat {{ __('protection.assessment') }} Requested' => __('protection.statuses.threat_assessment_requested'),
                                'Awaiting Threat {{ __('protection.assessment') }}' => __('protection.statuses.awaiting_threat_assessment'),
                                'Threat {{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.statuses.threat_assessment_received'),
                                '{{ __('protection.protection_decision') }} Pending' => __('protection.statuses.protection_decision_pending'),
                                'Protection Active' => __('protection.statuses.protection_active'),
                                'Protection Under Review' => __('protection.statuses.protection_under_review'),
                                'Protection Continued' => __('protection.statuses.protection_continued'),
                                'Protection Withdrawn' => __('protection.statuses.protection_withdrawn'),
                                'Protection Terminated' => __('protection.statuses.protection_terminated'),
                                'Closed' => __('protection.statuses.closed'),
                                'Not Requested' => __('protection.assessment_statuses.not_requested'),
                                'Request Sent' => __('protection.assessment_statuses.request_sent'),
                                'Request {{ __('protection.received') }}' => __('protection.assessment_statuses.request_received'),
                                '{{ __('protection.assessment') }} In Progress' => __('protection.assessment_statuses.assessment_in_progress'),
                                '{{ __('protection.assessment') }} Completed' => __('protection.assessment_statuses.assessment_completed'),
                                '{{ __('protection.assessment') }} {{ __('protection.received') }}' => __('protection.assessment_statuses.assessment_received'),
                                default => $status,
                            } }}

                        </option>

                    @endforeach

                </select>

            </div>



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.threat_assessment_received_date') }}

                </label>



                <input

                    type="date"

                    name="threat_assessment_received_date"

                    value="{{ old(

                        'threat_assessment_received_date',

                        optional($protectionDetail->threat_assessment_received_date)->format('Y-m-d')

                    ) }}"



                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>



        </div>



        <div class="mt-5 rounded-xl border border-slate-800 bg-[#0D172A] p-4">

            <label class="flex items-center gap-3">



                <input

                    type="checkbox"

                    name="interim_protection_required"

                    value="1"

                    @checked(

                        old(

                            'interim_protection_required',

                            $protectionDetail->interim_protection_required

                        )

                    )

                >



                <div>

                    <p class="text-sm font-medium text-slate-300">

                        {{ __('protection.interim_protection_required') }}

                    </p>



                    <p class="mt-1 text-xs text-slate-500">

                        {{ __('protection.interim_protection_required_desc') }}

                    </p>

                </div>

            </label>

        </div>



        <div class="mt-5">

            <label class="block mb-2 text-sm text-slate-300">

                {{ __('protection.interim_protection_requested_date') }}

            </label>



            <input

                type="date"

                name="interim_protection_requested_date"

                value="{{ old(

                    'interim_protection_requested_date',

                    optional($protectionDetail->interim_protection_requested_date)->format('Y-m-d')

                ) }}"



                class="w-full md:w-1/2 rounded-xl border border-slate-700

                       bg-[#0D172A] px-4 py-3 text-white"

            >

        </div>

    </div>



    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <div class="border-b border-slate-800 pb-4">

            <h3 class="text-lg font-semibold">

                {{ __('protection.threat_assessment_result') }}

            </h3>



            <p class="mt-1 text-sm text-slate-500">

                {{ __('protection.threat_assessment_result_desc') }}

            </p>

        </div>



        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.threat_level') }}

                </label>



                <select

                    name="threat_level"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

                    <option value="">{{ __('protection.not_determined') }}</option>



                    @foreach([

                        'Low',

                        'Moderate',

                        'High',

                        'Critical',

                    ] as $level)

                        <option

                            value="{{ match($level) {
                                'Low' => __('protection.threat_levels.low'),
                                'Moderate' => __('protection.threat_levels.moderate'),
                                'High' => __('protection.threat_levels.high'),
                                'Critical' => __('protection.threat_levels.critical'),
                                default => $level,
                            } }}"

                            @selected(

                                old(

                                    'threat_level',

                                    $protectionDetail->threat_level

                                ) === $level

                            )

                        >

                            {{ match($level) {
                                'Low' => __('protection.threat_levels.low'),
                                'Moderate' => __('protection.threat_levels.moderate'),
                                'High' => __('protection.threat_levels.high'),
                                'Critical' => __('protection.threat_levels.critical'),
                                default => $level,
                            } }}

                        </option>

                    @endforeach

                </select>

            </div>



        </div>



        <div class="mt-5">

            <label class="block mb-2 text-sm text-slate-300">

                {{ __('protection.threat_assessment_summary') }}

            </label>



            <textarea

                name="threat_assessment_summary"

                rows="5"

                class="w-full rounded-xl border border-slate-700

                       bg-[#0D172A] px-4 py-3 text-white"

            >{{ old(

                'threat_assessment_summary',

                $protectionDetail->threat_assessment_summary

            ) }}</textarea>

        </div>

    </div>



    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <div class="border-b border-slate-800 pb-4">

            <h3 class="text-lg font-semibold">

                {{ __('protection.protection_decision_review') }}

            </h3>

        </div>



        <div class="mt-6 space-y-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('protection.protection_decision') }}

                </label>



                <textarea

                    name="protection_decision"

                    rows="5"

                    placeholder="{{ __('protection.protection_decision_placeholder') }}"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >{{ old(

                    'protection_decision',

                    $protectionDetail->protection_decision

                ) }}</textarea>

            </div>



            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">



                <div>

                    <label class="block mb-2 text-sm text-slate-300">

                        {{ __('protection.protection_start_date') }}

                    </label>



                    <input

                        type="date"

                        name="protection_start_date"

                        value="{{ old(

                            'protection_start_date',

                            optional($protectionDetail->protection_start_date)->format('Y-m-d')

                        ) }}"

                        class="w-full rounded-xl border border-slate-700

                               bg-[#0D172A] px-4 py-3 text-white"

                    >

                </div>



                <div>

                    <label class="block mb-2 text-sm text-slate-300">

                        {{ __('protection.review_date') }}

                    </label>



                    <input

                        type="date"

                        name="review_date"

                        value="{{ old(

                            'review_date',

                            optional($protectionDetail->review_date)->format('Y-m-d')

                        ) }}"

                        class="w-full rounded-xl border border-slate-700

                               bg-[#0D172A] px-4 py-3 text-white"

                    >

                </div>



                <div>

                    <label class="block mb-2 text-sm text-slate-300">

                        {{ __('protection.protection_outcome') }}

                    </label>



                    <select

                        name="protection_outcome"

                        class="w-full rounded-xl border border-slate-700

                               bg-[#0D172A] px-4 py-3 text-white"

                    >

                        <option value="">{{ __('protection.not_determined') }}</option>



                        @foreach([

                            'Protection Provided',

                            'Protection Continued',

                            'Protection Withdrawn',

                            'Protection Terminated',

                            'Transferred / Referred',

                            'Closed',

                        ] as $outcome)

                            <option

                                value="{{ match($outcome) {
                                    'Protection Provided' => __('protection.outcomes.protection_provided'),
                                    'Protection Continued' => __('protection.outcomes.protection_continued'),
                                    'Protection Withdrawn' => __('protection.outcomes.protection_withdrawn'),
                                    'Protection Terminated' => __('protection.outcomes.protection_terminated'),
                                    'Transferred / Referred' => __('protection.outcomes.transferred_referred'),
                                    'Closed' => __('protection.outcomes.closed'),
                                    default => $outcome,
                                } }}"

                                @selected(

                                    old(

                                        'protection_outcome',

                                        $protectionDetail->protection_outcome

                                    ) === $outcome

                                )

                            >

                                {{ match($outcome) {
                                    'Protection Provided' => __('protection.outcomes.protection_provided'),
                                    'Protection Continued' => __('protection.outcomes.protection_continued'),
                                    'Protection Withdrawn' => __('protection.outcomes.protection_withdrawn'),
                                    'Protection Terminated' => __('protection.outcomes.protection_terminated'),
                                    'Transferred / Referred' => __('protection.outcomes.transferred_referred'),
                                    'Closed' => __('protection.outcomes.closed'),
                                    default => $outcome,
                                } }}

                            </option>

                        @endforeach

                    </select>

                </div>



            </div>

        </div>

    </div>



    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <label class="block mb-2 text-sm text-slate-300">

            {{ __('protection.workflow_remarks') }}

        </label>



        <textarea

            name="remarks"

            rows="4"

            placeholder="{{ __('protection.remarks_placeholder') }}"

            class="w-full rounded-xl border border-slate-700

                   bg-[#0D172A] px-4 py-3 text-white"

        >{{ old(

            'remarks',

            $protectionDetail->remarks

        ) }}</textarea>



    </div>



    <div

        class="sticky bottom-4 z-10 rounded-2xl

               border border-slate-700

               bg-[#0B1226]/95

               p-4 shadow-2xl backdrop-blur"

    >

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">



            <div>

                <p class="text-sm font-medium text-slate-300">

                    {{ __('protection.protection_workflow') }}

                </p>



                <p class="mt-1 text-xs text-slate-600">

                    {{ __('protection.changes_recorded_timeline') }}

                </p>

            </div>



            <div class="flex items-center gap-3">



                <a

                    href="{{ route('cases.show', $case) }}"

                    class="rounded-xl border border-slate-700

                           bg-[#111A2E] px-5 py-3

                           text-sm text-slate-300"

                >

                    {{ __('protection.cancel') }}

                </a>



                <button

                    type="submit"

                    class="rounded-xl bg-gradient-to-r

                           from-blue-600 to-purple-600

                           px-6 py-3

                           text-sm font-semibold text-white"

                >

                    {{ __('protection.save_workflow') }}

                </button>



            </div>

        </div>

    </div>



</form>



@endsection