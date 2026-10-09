@extends('layouts.app')



@section('title', __('legal.title'))



@section('page-title', __('legal.page_title'))



@section(

    'page-description',

    __('legal.page_description')

)



@section('page-actions')



    <a

        href="{{ route('cases.show', $case) }}"

        class="inline-flex items-center gap-2

               rounded-xl

               border border-slate-700

               bg-[#111A2E]

               px-5 py-2.5

               text-sm font-medium

               text-slate-300

               transition

               hover:bg-[#152238]

               hover:text-white"

    >

        ← {{ __('legal.master_case') }}

    </a>



@endsection





@section('content')



@php

    $user = auth()->user();



    $statusColor = match($legalDetail->legal_status) {

        '{{ __('legal.received') }} by Legal Division' => 'border-slate-600 bg-slate-500/10 text-slate-300',

        '{{ __('legal.re_number') }} Assigned' => 'border-blue-500/20 bg-blue-500/10 text-blue-300',

        'LO / IO Assigned' => 'border-indigo-500/20 bg-indigo-500/10 text-indigo-300',

        'Inquiry Started' => 'border-cyan-500/20 bg-cyan-500/10 text-cyan-300',

        'Observation Requested' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-300',

        'Awaiting Observation' => 'border-orange-500/20 bg-orange-500/10 text-orange-300',

        'First Reminder Sent' => 'border-amber-500/20 bg-amber-500/10 text-amber-300',

        'Second Reminder Sent' => 'border-red-500/20 bg-red-500/10 text-red-300',

        'Field Visit Required' => 'border-purple-500/20 bg-purple-500/10 text-purple-300',

        'Investigation In Progress' => 'border-violet-500/20 bg-violet-500/10 text-violet-300',

        'IO Report Prepared' => 'border-sky-500/20 bg-sky-500/10 text-sky-300',

        'Legal Review' => 'border-fuchsia-500/20 bg-fuchsia-500/10 text-fuchsia-300',

        '{{ __('legal.case_conference') }} Recommended' => 'border-teal-500/20 bg-teal-500/10 text-teal-300',

        '{{ __('legal.case_conference') }} Scheduled' => 'border-teal-500/20 bg-teal-500/10 text-teal-300',

        '{{ __('legal.case_conference') }} Conducted' => 'border-green-500/20 bg-green-500/10 text-green-300',

        '{{ __('legal.board_submission_required') }}' => 'border-purple-500/20 bg-purple-500/10 text-purple-300',

        'Submitted to Board' => 'border-purple-500/20 bg-purple-500/10 text-purple-300',

        'Awaiting {{ __('legal.board_decision') }}' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-300',

        '{{ __('legal.board_decision') }} Issued' => 'border-green-500/20 bg-green-500/10 text-green-300',

        'Closed' => 'border-green-500/20 bg-green-500/10 text-green-300',

        default => 'border-blue-500/20 bg-blue-500/10 text-blue-300',

    };



    $canManageLegal = in_array($user->role, [

        'legal_director',

        'legal_officer',

        'investigation_officer',

        'director_general',

        'system_admin',

    ]);



    $observationOverdue =

        $legalDetail->observation_due_date &&

        !$legalDetail->first_reminder_date &&

        now()->startOfDay()->gt($legalDetail->observation_due_date);



    $secondReminderSuggested =

        $legalDetail->first_reminder_date &&

        !$legalDetail->second_reminder_date &&

        now()->startOfDay()->gte(

            $legalDetail->first_reminder_date->copy()->addDays(7)

        );


    $legalStatusLabel = match($legalDetail->legal_status) {
        '{{ __('legal.received') }} by Legal Division' => __('legal.statuses.received_by_legal_division'),
        '{{ __('legal.re_number') }} Assigned' => __('legal.statuses.re_number_assigned'),
        'LO / IO Assigned' => __('legal.statuses.lo_io_assigned'),
        'Inquiry Started' => __('legal.statuses.inquiry_started'),
        'Observation Requested' => __('legal.statuses.observation_requested'),
        'Awaiting Observation' => __('legal.statuses.awaiting_observation'),
        'First Reminder Sent' => __('legal.statuses.first_reminder_sent'),
        'Second Reminder Sent' => __('legal.statuses.second_reminder_sent'),
        'Field Visit Required' => __('legal.statuses.field_visit_required'),
        'Investigation In Progress' => __('legal.statuses.investigation_in_progress'),
        'IO Report Prepared' => __('legal.statuses.io_report_prepared'),
        'Legal Review' => __('legal.statuses.legal_review'),
        '{{ __('legal.case_conference') }} Recommended' => __('legal.statuses.case_conference_recommended'),
        '{{ __('legal.case_conference') }} Scheduled' => __('legal.statuses.case_conference_scheduled'),
        '{{ __('legal.case_conference') }} Conducted' => __('legal.statuses.case_conference_conducted'),
        '{{ __('legal.board_submission_required') }}' => __('legal.statuses.board_submission_required'),
        'Submitted to Board' => __('legal.statuses.submitted_to_board'),
        'Awaiting {{ __('legal.board_decision') }}' => __('legal.statuses.awaiting_board_decision'),
        '{{ __('legal.board_decision') }} Issued' => __('legal.statuses.board_decision_issued'),
        'Closed' => __('legal.statuses.closed'),
        default => $legalDetail->legal_status,
    };

@endphp





<form

    method="POST"

    action="{{ route('legal.update', $case) }}"

    class="space-y-6"

>

    @csrf





    {{-- =========================================================

         LEGAL WORKFLOW HERO

    ========================================================== --}}

    <div

        class="relative overflow-hidden

               rounded-3xl

               border border-slate-800

               bg-gradient-to-br

               from-[#101A35]

               via-[#111A2E]

               to-[#0B1226]

               p-6 lg:p-7"

    >



        <div

            class="absolute -top-20 -right-20

                   h-60 w-60

                   rounded-full

                   bg-blue-600/10

                   blur-3xl"

        ></div>



        <div

            class="absolute -bottom-24 left-1/3

                   h-60 w-60

                   rounded-full

                   bg-purple-600/10

                   blur-3xl"

        ></div>



        <div class="relative z-10">



            <div

                class="flex flex-col gap-5

                       lg:flex-row

                       lg:items-start

                       lg:justify-between"

            >



                <div class="max-w-4xl">



                    <div class="flex flex-wrap gap-2">



                        <span

                            class="rounded-full border

                                   px-3 py-1 text-xs font-medium

                                   {{ $statusColor }}"

                        >

                            {{ $legalStatusLabel }}

                        </span>



                        <span

                            class="rounded-full

                                   border border-slate-700

                                   bg-[#0C1528]

                                   px-3 py-1

                                   text-xs text-slate-300"

                        >

                            {{ __('legal.division_name') }}

                        </span>



                        <span

                            class="rounded-full

                                   border border-slate-700

                                   bg-[#0C1528]

                                   px-3 py-1

                                   text-xs text-slate-400"

                        >

                            {{ $case->complaint_category }}

                        </span>



                    </div>





                    <p class="mt-5 text-xs uppercase tracking-[0.16em] text-slate-500">

                        {{ __('legal.master_case') }}

                    </p>



                    <h2 class="mt-1 text-3xl font-semibold text-blue-300">

                        {{ $case->case_number }}

                    </h2>



                    <p class="mt-4 max-w-3xl text-sm leading-relaxed text-slate-400">

                        {{ $case->complaint_summary }}

                    </p>





                    <div

                        class="mt-6

                               grid grid-cols-2

                               md:grid-cols-4

                               gap-4"

                    >



                        <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                            <p class="text-[9px] uppercase tracking-wider text-slate-500">

                                {{ __('legal.received') }}

                            </p>

                            <p class="mt-2 text-sm font-medium">

                                {{ $case->received_date->format('d M Y') }}

                            </p>

                        </div>



                        <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                            <p class="text-[9px] uppercase tracking-wider text-slate-500">

                                {{ __('legal.re_number') }}

                            </p>

                            <p class="mt-2 text-sm font-medium text-blue-300">

                                {{ $legalDetail->re_number ?: __('legal.not_assigned') }}

                            </p>

                        </div>



                        <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                            <p class="text-[9px] uppercase tracking-wider text-slate-500">

                                {{ __('legal.legal_officer') }}

                            </p>

                            <p class="mt-2 text-sm font-medium">

                                {{ $legalDetail->legalOfficer?->name ?? __('legal.not_assigned') }}

                            </p>

                        </div>



                        <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                            <p class="text-[9px] uppercase tracking-wider text-slate-500">

                                {{ __('legal.investigation_officer') }}

                            </p>

                            <p class="mt-2 text-sm font-medium">

                                {{ $legalDetail->investigationOfficer?->name ?? __('legal.not_assigned') }}

                            </p>

                        </div>



                    </div>



                </div>





                <div

                    class="shrink-0

                           rounded-2xl

                           border border-slate-700

                           bg-white/95

                           p-3"

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

         ALERTS / DEADLINES

    ========================================================== --}}

    @if($observationOverdue || $secondReminderSuggested)



        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">



            @if($observationOverdue)



                <div

                    class="rounded-2xl

                           border border-orange-500/20

                           bg-orange-500/[0.07]

                           p-5"

                >

                    <p class="text-sm font-semibold text-orange-300">

                        {{ __('legal.observation_overdue') }}

                    </p>



                    <p class="mt-2 text-xs leading-relaxed text-slate-400">

                        {{ __('legal.observation_overdue_desc') }}

                    </p>

                </div>



            @endif





            @if($secondReminderSuggested)



                <div

                    class="rounded-2xl

                           border border-red-500/20

                           bg-red-500/[0.07]

                           p-5"

                >

                    <p class="text-sm font-semibold text-red-300">

                        {{ __('legal.second_reminder_due') }}

                    </p>



                    <p class="mt-2 text-xs leading-relaxed text-slate-400">

                        {{ __('legal.second_reminder_due_desc') }}

                    </p>

                </div>



            @endif



        </div>



    @endif





    {{-- =========================================================

         LEGAL REGISTRATION & ASSIGNMENT

    ========================================================== --}}

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

                   md:justify-between

                   border-b border-slate-800

                   pb-4"

        >



            <div>

                <h3 class="text-lg font-semibold">

                    {{ __('legal.registration_assignment') }}

                </h3>



                <p class="mt-1 text-sm text-slate-500">

                    {{ __('legal.registration_assignment_desc') }}

                </p>

            </div>



            <span

                class="w-fit rounded-full

                       border border-blue-500/20

                       bg-blue-500/10

                       px-3 py-1

                       text-[10px] uppercase tracking-wider

                       text-blue-300"

            >

                {{ __('legal.legal_intake') }}

            </span>



        </div>





        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label

                    for="re_number"

                    class="block mb-2 text-sm font-medium text-slate-300"

                >

                    {{ __('legal.re_number') }}

                </label>



                <input

                    id="re_number"

                    type="text"

                    name="re_number"

                    value="{{ old('re_number', $legalDetail->re_number) }}"

                    placeholder="{{ __('legal.re_number_placeholder') }}"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white

                           placeholder-slate-600

                           outline-none

                           transition

                           focus:border-blue-500

                           focus:ring-2

                           focus:ring-blue-500/20"

                >

            </div>





            <div>

                <label

                    for="legal_status"

                    class="block mb-2 text-sm font-medium text-slate-300"

                >

                    {{ __('legal.workflow_status') }} *

                </label>



                <select

                    id="legal_status"

                    name="legal_status"

                    required



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white

                           outline-none

                           focus:border-blue-500"

                >



                    @foreach([

                        '{{ __('legal.received') }} by Legal Division',

                        '{{ __('legal.re_number') }} Assigned',

                        'LO / IO Assigned',

                        'Inquiry Started',

                        'Observation Requested',

                        'Awaiting Observation',

                        'First Reminder Sent',

                        'Second Reminder Sent',

                        'Field Visit Required',

                        'Investigation In Progress',

                        'IO Report Prepared',

                        'Legal Review',

                        '{{ __('legal.case_conference') }} Recommended',

                        '{{ __('legal.case_conference') }} Scheduled',

                        '{{ __('legal.case_conference') }} Conducted',

                        '{{ __('legal.board_submission_required') }}',

                        'Submitted to Board',

                        'Awaiting {{ __('legal.board_decision') }}',

                        '{{ __('legal.board_decision') }} Issued',

                        'Closed',

                    ] as $status)



                        <option

                            value="{{ match($status) {
                                '{{ __('legal.received') }} by Legal Division' => __('legal.statuses.received_by_legal_division'),
                                '{{ __('legal.re_number') }} Assigned' => __('legal.statuses.re_number_assigned'),
                                'LO / IO Assigned' => __('legal.statuses.lo_io_assigned'),
                                'Inquiry Started' => __('legal.statuses.inquiry_started'),
                                'Observation Requested' => __('legal.statuses.observation_requested'),
                                'Awaiting Observation' => __('legal.statuses.awaiting_observation'),
                                'First Reminder Sent' => __('legal.statuses.first_reminder_sent'),
                                'Second Reminder Sent' => __('legal.statuses.second_reminder_sent'),
                                'Field Visit Required' => __('legal.statuses.field_visit_required'),
                                'Investigation In Progress' => __('legal.statuses.investigation_in_progress'),
                                'IO Report Prepared' => __('legal.statuses.io_report_prepared'),
                                'Legal Review' => __('legal.statuses.legal_review'),
                                '{{ __('legal.case_conference') }} Recommended' => __('legal.statuses.case_conference_recommended'),
                                '{{ __('legal.case_conference') }} Scheduled' => __('legal.statuses.case_conference_scheduled'),
                                '{{ __('legal.case_conference') }} Conducted' => __('legal.statuses.case_conference_conducted'),
                                '{{ __('legal.board_submission_required') }}' => __('legal.statuses.board_submission_required'),
                                'Submitted to Board' => __('legal.statuses.submitted_to_board'),
                                'Awaiting {{ __('legal.board_decision') }}' => __('legal.statuses.awaiting_board_decision'),
                                '{{ __('legal.board_decision') }} Issued' => __('legal.statuses.board_decision_issued'),
                                'Closed' => __('legal.statuses.closed'),
                                default => $status,
                            } }}"

                            @selected(

                                old(

                                    'legal_status',

                                    $legalDetail->legal_status

                                ) === $status

                            )

                        >

                            {{ match($status) {
                                '{{ __('legal.received') }} by Legal Division' => __('legal.statuses.received_by_legal_division'),
                                '{{ __('legal.re_number') }} Assigned' => __('legal.statuses.re_number_assigned'),
                                'LO / IO Assigned' => __('legal.statuses.lo_io_assigned'),
                                'Inquiry Started' => __('legal.statuses.inquiry_started'),
                                'Observation Requested' => __('legal.statuses.observation_requested'),
                                'Awaiting Observation' => __('legal.statuses.awaiting_observation'),
                                'First Reminder Sent' => __('legal.statuses.first_reminder_sent'),
                                'Second Reminder Sent' => __('legal.statuses.second_reminder_sent'),
                                'Field Visit Required' => __('legal.statuses.field_visit_required'),
                                'Investigation In Progress' => __('legal.statuses.investigation_in_progress'),
                                'IO Report Prepared' => __('legal.statuses.io_report_prepared'),
                                'Legal Review' => __('legal.statuses.legal_review'),
                                '{{ __('legal.case_conference') }} Recommended' => __('legal.statuses.case_conference_recommended'),
                                '{{ __('legal.case_conference') }} Scheduled' => __('legal.statuses.case_conference_scheduled'),
                                '{{ __('legal.case_conference') }} Conducted' => __('legal.statuses.case_conference_conducted'),
                                '{{ __('legal.board_submission_required') }}' => __('legal.statuses.board_submission_required'),
                                'Submitted to Board' => __('legal.statuses.submitted_to_board'),
                                'Awaiting {{ __('legal.board_decision') }}' => __('legal.statuses.awaiting_board_decision'),
                                '{{ __('legal.board_decision') }} Issued' => __('legal.statuses.board_decision_issued'),
                                'Closed' => __('legal.statuses.closed'),
                                default => $status,
                            } }}

                        </option>



                    @endforeach



                </select>

            </div>





            <div>

                <label

                    for="legal_officer_id"

                    class="block mb-2 text-sm font-medium text-slate-300"

                >

                    {{ __('legal.legal_officer') }}

                </label>



                <select

                    id="legal_officer_id"

                    name="legal_officer_id"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white

                           outline-none

                           focus:border-blue-500"

                >



                    <option value="">

                        Select {{ __('legal.legal_officer') }}

                    </option>



                    @foreach($legalOfficers as $officer)



                        <option

                            value="{{ $officer->id }}"

                            @selected(

                                old(

                                    'legal_officer_id',

                                    $legalDetail->legal_officer_id

                                ) == $officer->id

                            )

                        >

                            {{ $officer->name }}

                            — {{ $officer->employee_number }}

                        </option>



                    @endforeach



                </select>

            </div>





            <div>

                <label

                    for="investigation_officer_id"

                    class="block mb-2 text-sm font-medium text-slate-300"

                >

                    {{ __('legal.investigation_officer') }}

                </label>



                <select

                    id="investigation_officer_id"

                    name="investigation_officer_id"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white

                           outline-none

                           focus:border-blue-500"

                >



                    <option value="">

                        Select {{ __('legal.investigation_officer') }}

                    </option>



                    @foreach($investigationOfficers as $officer)



                        <option

                            value="{{ $officer->id }}"

                            @selected(

                                old(

                                    'investigation_officer_id',

                                    $legalDetail->investigation_officer_id

                                ) == $officer->id

                            )

                        >

                            {{ $officer->name }}

                            — {{ $officer->employee_number }}

                        </option>



                    @endforeach



                </select>

            </div>



        </div>



    </div>





    {{-- =========================================================

         INQUIRY & OBSERVATION

    ========================================================== --}}

    <div

        class="rounded-2xl

               border border-slate-800

               bg-[#111A2E]

               p-6"

    >



        <div class="border-b border-slate-800 pb-4">



            <h3 class="text-lg font-semibold">

                {{ __('legal.inquiry_observation_tracking') }}

            </h3>



            <p class="mt-1 text-sm text-slate-500">

                {{ __('legal.inquiry_observation_desc') }}

            </p>



        </div>





        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('legal.inquiry_started_date') }}

                </label>



                <input

                    type="date"

                    name="inquiry_started_date"

                    value="{{ old(

                        'inquiry_started_date',

                        optional($legalDetail->inquiry_started_date)->format('Y-m-d')

                    ) }}"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('legal.observation_requested_date') }}

                </label>



                <input

                    type="date"

                    name="observation_requested_date"

                    value="{{ old(

                        'observation_requested_date',

                        optional($legalDetail->observation_requested_date)->format('Y-m-d')

                    ) }}"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('legal.observation_due_date') }}

                </label>



                <input

                    type="date"

                    name="observation_due_date"

                    value="{{ old(

                        'observation_due_date',

                        optional($legalDetail->observation_due_date)->format('Y-m-d')

                    ) }}"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('legal.first_reminder_date') }}

                </label>



                <input

                    type="date"

                    name="first_reminder_date"

                    value="{{ old(

                        'first_reminder_date',

                        optional($legalDetail->first_reminder_date)->format('Y-m-d')

                    ) }}"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('legal.second_reminder_date') }}

                </label>



                <input

                    type="date"

                    name="second_reminder_date"

                    value="{{ old(

                        'second_reminder_date',

                        optional($legalDetail->second_reminder_date)->format('Y-m-d')

                    ) }}"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('legal.field_visit_date') }}

                </label>



                <input

                    type="date"

                    name="field_visit_date"

                    value="{{ old(

                        'field_visit_date',

                        optional($legalDetail->field_visit_date)->format('Y-m-d')

                    ) }}"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white"

                >

            </div>



        </div>





        <div

            class="mt-5

                   rounded-xl

                   border border-slate-800

                   bg-[#0D172A]

                   p-4"

        >



            <label class="flex items-center gap-3">



                <input

                    type="checkbox"

                    name="field_visit_required"

                    value="1"

                    @checked(

                        old(

                            'field_visit_required',

                            $legalDetail->field_visit_required

                        )

                    )



                    class="rounded

                           border-slate-600

                           bg-[#111A2E]

                           text-blue-600

                           focus:ring-blue-500"

                >



                <div>

                    <p class="text-sm font-medium text-slate-300">

                        {{ __('legal.field_visit_required') }}

                    </p>



                    <p class="mt-1 text-xs text-slate-500">

                        {{ __('legal.field_visit_required_desc') }}

                    </p>

                </div>



            </label>



        </div>



    </div>





    {{-- =========================================================

         INVESTIGATION FINDINGS

    ========================================================== --}}

    <div

        class="rounded-2xl

               border border-slate-800

               bg-[#111A2E]

               p-6"

    >



        <div class="border-b border-slate-800 pb-4">



            <h3 class="text-lg font-semibold">

                {{ __('legal.investigation_findings_recommendation') }}

            </h3>



            <p class="mt-1 text-sm text-slate-500">

                {{ __('legal.investigation_findings_desc') }}

            </p>



        </div>





        <div class="mt-6 space-y-5">



            <div>

                <label

                    for="io_findings"

                    class="block mb-2 text-sm font-medium text-slate-300"

                >

                    {{ __('legal.investigation_officer') }} Findings

                </label>



                <textarea

                    id="io_findings"

                    name="io_findings"

                    rows="6"

                    placeholder="{{ __('legal.io_findings_placeholder') }}"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white

                           placeholder-slate-600

                           outline-none

                           focus:border-blue-500"

                >{{ old(

                    'io_findings',

                    $legalDetail->io_findings

                ) }}</textarea>

            </div>





            <div>

                <label

                    for="legal_recommendation"

                    class="block mb-2 text-sm font-medium text-slate-300"

                >

                    {{ __('legal.legal_recommendation') }}

                </label>



                <textarea

                    id="legal_recommendation"

                    name="legal_recommendation"

                    rows="6"

                    placeholder="{{ __('legal.legal_recommendation_placeholder') }}"



                    class="w-full rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-white

                           placeholder-slate-600

                           outline-none

                           focus:border-blue-500"

                >{{ old(

                    'legal_recommendation',

                    $legalDetail->legal_recommendation

                ) }}</textarea>

            </div>



        </div>



    </div>





    {{-- =========================================================

         CASE CONFERENCE

    ========================================================== --}}

    <div

        class="rounded-2xl

               border border-slate-800

               bg-[#111A2E]

               p-6"

    >



        <div class="border-b border-slate-800 pb-4">



            <h3 class="text-lg font-semibold">

                {{ __('legal.case_conference') }}

            </h3>



            <p class="mt-1 text-sm text-slate-500">

                {{ __('legal.case_conference_desc') }}

            </p>



        </div>





        <div class="mt-6">



            <label

                class="flex items-center gap-3

                       rounded-xl

                       border border-slate-800

                       bg-[#0D172A]

                       p-4"

            >



                <input

                    type="checkbox"

                    name="case_conference_required"

                    value="1"

                    @checked(

                        old(

                            'case_conference_required',

                            $legalDetail->case_conference_required

                        )

                    )



                    class="rounded

                           border-slate-600

                           bg-[#111A2E]

                           text-blue-600"

                >



                <div>

                    <p class="text-sm font-medium text-slate-300">

                        {{ __('legal.case_conference') }} Required

                    </p>



                    <p class="mt-1 text-xs text-slate-500">

                        {{ __('legal.case_conference_required_desc') }}

                    </p>

                </div>



            </label>





            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">



                <div>

                    <label class="block mb-2 text-sm text-slate-300">

                        {{ __('legal.conference_date') }}

                    </label>



                    <input

                        type="date"

                        name="case_conference_date"

                        value="{{ old(

                            'case_conference_date',

                            optional($legalDetail->case_conference_date)->format('Y-m-d')

                        ) }}"



                        class="w-full rounded-xl

                               border border-slate-700

                               bg-[#0D172A]

                               px-4 py-3 text-white"

                    >

                </div>





                <div>

                    <label class="block mb-2 text-sm text-slate-300">

                        {{ __('legal.conference_outcome') }}

                    </label>



                    <select

                        name="case_conference_outcome"



                        class="w-full rounded-xl

                               border border-slate-700

                               bg-[#0D172A]

                               px-4 py-3 text-white"

                    >



                        <option value="">

                            {{ __('legal.not_determined') }}

                        </option>



                        @foreach([

                            'Settled',

                            'Not Settled',

                            'Further Action Required',

                        ] as $outcome)



                            <option

                                value="{{ match($outcome) {
                                    'Settled' => __('legal.outcomes.settled'),
                                    'Not Settled' => __('legal.outcomes.not_settled'),
                                    'Further Action Required' => __('legal.outcomes.further_action_required'),
                                    default => $outcome,
                                } }}"

                                @selected(

                                    old(

                                        'case_conference_outcome',

                                        $legalDetail->case_conference_outcome

                                    ) === $outcome

                                )

                            >

                                {{ match($outcome) {
                                    'Settled' => __('legal.outcomes.settled'),
                                    'Not Settled' => __('legal.outcomes.not_settled'),
                                    'Further Action Required' => __('legal.outcomes.further_action_required'),
                                    default => $outcome,
                                } }}

                            </option>



                        @endforeach



                    </select>

                </div>



            </div>



        </div>



    </div>





    {{-- =========================================================

         BOARD ESCALATION

    ========================================================== --}}

    <div

        class="rounded-2xl

               border border-slate-800

               bg-[#111A2E]

               p-6"

    >



        <div class="border-b border-slate-800 pb-4">



            <h3 class="text-lg font-semibold">

                {{ __('legal.board_escalation_decision') }}

            </h3>



            <p class="mt-1 text-sm text-slate-500">

                {{ __('legal.board_escalation_desc') }}

            </p>



        </div>





        <div class="mt-6">



            <label

                class="flex items-center gap-3

                       rounded-xl

                       border border-slate-800

                       bg-[#0D172A]

                       p-4"

            >



                <input

                    type="checkbox"

                    name="board_submission_required"

                    value="1"

                    @checked(

                        old(

                            'board_submission_required',

                            $legalDetail->board_submission_required

                        )

                    )



                    class="rounded

                           border-slate-600

                           bg-[#111A2E]

                           text-blue-600"

                >



                <div>

                    <p class="text-sm font-medium text-slate-300">

                        {{ __('legal.board_submission_required') }}

                    </p>



                    <p class="mt-1 text-xs text-slate-500">

                        {{ __('legal.board_submission_required_desc') }}

                    </p>

                </div>



            </label>





            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">



                <div>

                    <label class="block mb-2 text-sm text-slate-300">

                        {{ __('legal.board_submission_date') }}

                    </label>



                    <input

                        type="date"

                        name="board_submission_date"

                        value="{{ old(

                            'board_submission_date',

                            optional($legalDetail->board_submission_date)->format('Y-m-d')

                        ) }}"



                        class="w-full rounded-xl

                               border border-slate-700

                               bg-[#0D172A]

                               px-4 py-3 text-white"

                    >

                </div>





                <div>

                    <label class="block mb-2 text-sm text-slate-300">

                        {{ __('legal.board_decision') }}

                    </label>



                    <textarea

                        name="board_decision"

                        rows="4"

                        placeholder="{{ __('legal.board_decision_placeholder') }}"



                        class="w-full rounded-xl

                               border border-slate-700

                               bg-[#0D172A]

                               px-4 py-3

                               text-white

                               placeholder-slate-600"

                    >{{ old(

                        'board_decision',

                        $legalDetail->board_decision

                    ) }}</textarea>

                </div>



            </div>



        </div>



    </div>





    {{-- =========================================================

         WORKFLOW REMARKS

    ========================================================== --}}

    <div

        class="rounded-2xl

               border border-slate-800

               bg-[#111A2E]

               p-6"

    >



        <label

            for="remarks"

            class="block mb-2 text-sm font-medium text-slate-300"

        >

            {{ __('legal.workflow_remarks') }}

        </label>



        <p class="mb-4 text-xs text-slate-500">

            {{ __('legal.workflow_remarks_desc') }}

        </p>



        <textarea

            id="remarks"

            name="remarks"

            rows="4"

            placeholder="{{ __('legal.remarks_placeholder') }}"



            class="w-full rounded-xl

                   border border-slate-700

                   bg-[#0D172A]

                   px-4 py-3

                   text-white

                   placeholder-slate-600

                   outline-none

                   focus:border-blue-500"

        >{{ old(

            'remarks',

            $legalDetail->remarks

        ) }}</textarea>



    </div>





    {{-- =========================================================

         ACTION BAR

    ========================================================== --}}

    <div

        class="sticky bottom-4 z-10

               rounded-2xl

               border border-slate-700

               bg-[#0B1226]/95

               p-4

               shadow-2xl

               backdrop-blur"

    >



        <div

            class="flex flex-col gap-3

                   sm:flex-row

                   sm:items-center

                   sm:justify-between"

        >



            <div>

                <p class="text-sm font-medium text-slate-300">

                    {{ __('legal.legal_workflow') }}

                </p>



                <p class="mt-1 text-xs text-slate-600">

                    {{ __('legal.changes_recorded_timeline') }}

                </p>

            </div>





            <div class="flex items-center gap-3">



                <a

                    href="{{ route('cases.show', $case) }}"



                    class="rounded-xl

                           border border-slate-700

                           bg-[#111A2E]

                           px-5 py-3

                           text-sm text-slate-300

                           transition

                           hover:bg-[#152238]"

                >

                    {{ __('legal.cancel') }}

                </a>





                @if($canManageLegal)



                    <button

                        type="submit"



                        class="rounded-xl

                               bg-gradient-to-r

                               from-blue-600

                               to-purple-600

                               px-6 py-3

                               text-sm font-semibold

                               text-white

                               shadow-lg shadow-blue-950/20

                               transition

                               hover:opacity-90"

                    >

                        Save {{ __('legal.legal_workflow') }}

                    </button>



                @endif



            </div>



        </div>



    </div>



</form>



@endsection