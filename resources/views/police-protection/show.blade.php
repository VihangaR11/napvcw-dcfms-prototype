@extends('layouts.app')



@section('title', __('police_protection.title'))



@section('page-title', __('police_protection.page_title'))



@section(

    'page-description',

    __('police_protection.page_description')

)



@section('page-actions')

    <a

        href="{{ route('cases.show', $case) }}"

        class="inline-flex items-center gap-2

               rounded-xl border border-slate-700

               bg-[#111A2E]

               px-5 py-2.5

               text-sm text-slate-300

               hover:bg-[#152238]"

    >

        ← {{ __('police_protection.master_case') }}

    </a>

@endsection



@section('content')



@php

    $statusColor = match($detail->assessment_status) {

        'Awaiting Request' => 'border-slate-600 bg-slate-500/10 text-slate-300',

        'Request {{ __('police_protection.received') }}' => 'border-blue-500/20 bg-blue-500/10 text-blue-300',

        'Assessment {{ __('police_protection.started') }}' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-300',

        'Assessment In Progress' => 'border-orange-500/20 bg-orange-500/10 text-orange-300',

        'Assessment {{ __('police_protection.completed') }}' => 'border-green-500/20 bg-green-500/10 text-green-300',

        default => 'border-blue-500/20 bg-blue-500/10 text-blue-300',

    };


    $assessmentStatusLabel = match($detail->assessment_status) {
        'Awaiting Request' => __('police_protection.assessment_statuses.awaiting_request'),
        'Request {{ __('police_protection.received') }}' => __('police_protection.assessment_statuses.request_received'),
        'Assessment {{ __('police_protection.started') }}' => __('police_protection.assessment_statuses.assessment_started'),
        'Assessment In Progress' => __('police_protection.assessment_statuses.assessment_in_progress'),
        'Assessment {{ __('police_protection.completed') }}' => __('police_protection.assessment_statuses.assessment_completed'),
        default => $detail->assessment_status,
    };

    $threatLevelLabel = match($detail->threat_level) {
        'Low' => __('police_protection.threat_levels.low'),
        'Moderate' => __('police_protection.threat_levels.moderate'),
        'High' => __('police_protection.threat_levels.high'),
        'Critical' => __('police_protection.threat_levels.critical'),
        null => __('police_protection.not_determined'),
        default => $detail->threat_level,
    };

    $recommendedThreatStatusLabel = match($case->threat_assessment_status) {
        'Very High' => __('police_protection.recommended_statuses.very_high'),
        'High' => __('police_protection.recommended_statuses.high'),
        'Low' => __('police_protection.recommended_statuses.low'),
        'Very Low' => __('police_protection.recommended_statuses.very_low'),
        null => null,
        default => $case->threat_assessment_status,
    };

@endphp



<form

    method="POST"

    action="{{ route('police-protection.update', $case) }}"

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



        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">



            <div class="max-w-4xl">



                <div class="flex flex-wrap gap-2">



                    <span class="rounded-full border px-3 py-1 text-xs font-medium {{ $statusColor }}">

                        {{ $assessmentStatusLabel }}

                    </span>



                    <span

                        class="rounded-full border border-slate-700

                               bg-[#0C1528] px-3 py-1

                               text-xs text-slate-300"

                    >

                        {{ __('police_protection.division_name') }}

                    </span>



                </div>



                <p class="mt-5 text-xs uppercase tracking-wider text-slate-500">

                    {{ __('police_protection.master_case') }}

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

                            {{ __('police_protection.received') }}

                        </p>



                        <p class="mt-2 text-sm font-medium">

                            {{ optional($detail->request_received_date)->format('d M Y') ?? __('police_protection.not_received') }}

                        </p>

                    </div>



                    <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                        <p class="text-[9px] uppercase tracking-wider text-slate-500">

                            {{ __('police_protection.started') }}

                        </p>



                        <p class="mt-2 text-sm font-medium">

                            {{ optional($detail->assessment_started_date)->format('d M Y') ?? __('police_protection.not_started') }}

                        </p>

                    </div>



                    <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                        <p class="text-[9px] uppercase tracking-wider text-slate-500">

                            {{ __('police_protection.completed') }}

                        </p>



                        <p class="mt-2 text-sm font-medium">

                            {{ optional($detail->assessment_completed_date)->format('d M Y') ?? __('police_protection.pending') }}

                        </p>

                    </div>



                    <div class="rounded-xl border border-slate-800 bg-[#0D172A]/80 p-4">

                        <p class="text-[9px] uppercase tracking-wider text-slate-500">

                            {{ __('police_protection.threat_level') }}

                        </p>



                        <p class="mt-2 text-sm font-medium">

                            {{ $threatLevelLabel }}

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

                {{ __('police_protection.assessment_assignment') }}

            </h3>



            <p class="mt-1 text-sm text-slate-500">

                {{ __('police_protection.assessment_assignment_desc') }}

            </p>

        </div>



        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('police_protection.police_protection_officer') }}

                </label>



                <select

                    name="police_protection_officer_id"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >



                    <option value="">{{ __('police_protection.select_officer') }}</option>



                    @foreach($officers as $officer)

                        <option

                            value="{{ $officer->id }}"

                            @selected(

                                old(

                                    'police_protection_officer_id',

                                    $detail->police_protection_officer_id

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

                    {{ __('police_protection.assessment_status') }} *

                </label>



                <select

                    name="assessment_status"

                    required

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >



                    @foreach([

                        'Awaiting Request',

                        'Request {{ __('police_protection.received') }}',

                        'Assessment {{ __('police_protection.started') }}',

                        'Assessment In Progress',

                        'Assessment {{ __('police_protection.completed') }}',

                    ] as $status)

                        <option

                            value="{{ match($status) {
                                'Awaiting Request' => __('police_protection.assessment_statuses.awaiting_request'),
                                'Request {{ __('police_protection.received') }}' => __('police_protection.assessment_statuses.request_received'),
                                'Assessment {{ __('police_protection.started') }}' => __('police_protection.assessment_statuses.assessment_started'),
                                'Assessment In Progress' => __('police_protection.assessment_statuses.assessment_in_progress'),
                                'Assessment {{ __('police_protection.completed') }}' => __('police_protection.assessment_statuses.assessment_completed'),
                                'Very High' => __('police_protection.recommended_statuses.very_high'),
                                'High' => __('police_protection.recommended_statuses.high'),
                                'Low' => __('police_protection.recommended_statuses.low'),
                                'Very Low' => __('police_protection.recommended_statuses.very_low'),
                                default => $status,
                            } }}"

                            @selected(

                                old(

                                    'assessment_status',

                                    $detail->assessment_status

                                ) === $status

                            )

                        >

                            {{ match($status) {
                                'Awaiting Request' => __('police_protection.assessment_statuses.awaiting_request'),
                                'Request {{ __('police_protection.received') }}' => __('police_protection.assessment_statuses.request_received'),
                                'Assessment {{ __('police_protection.started') }}' => __('police_protection.assessment_statuses.assessment_started'),
                                'Assessment In Progress' => __('police_protection.assessment_statuses.assessment_in_progress'),
                                'Assessment {{ __('police_protection.completed') }}' => __('police_protection.assessment_statuses.assessment_completed'),
                                'Very High' => __('police_protection.recommended_statuses.very_high'),
                                'High' => __('police_protection.recommended_statuses.high'),
                                'Low' => __('police_protection.recommended_statuses.low'),
                                'Very Low' => __('police_protection.recommended_statuses.very_low'),
                                default => $status,
                            } }}

                        </option>

                    @endforeach



                </select>

            </div>



        </div>

    </div>





    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <h3 class="text-lg font-semibold">

            {{ __('police_protection.assessment_timeline') }}

        </h3>



        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('police_protection.request_received_date') }}

                </label>



                <input

                    type="date"

                    name="request_received_date"

                    value="{{ old(

                        'request_received_date',

                        optional($detail->request_received_date)->format('Y-m-d')

                    ) }}"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('police_protection.assessment_started_date') }}

                </label>



                <input

                    type="date"

                    name="assessment_started_date"

                    value="{{ old(

                        'assessment_started_date',

                        optional($detail->assessment_started_date)->format('Y-m-d')

                    ) }}"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('police_protection.assessment_completed_date') }}

                </label>



                <input

                    type="date"

                    name="assessment_completed_date"

                    value="{{ old(

                        'assessment_completed_date',

                        optional($detail->assessment_completed_date)->format('Y-m-d')

                    ) }}"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>



        </div>



    </div>





    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <h3 class="text-lg font-semibold">

            {{ __('police_protection.threat_assessment_result') }}

        </h3>



        <div class="mt-6">



            <label class="block mb-2 text-sm text-slate-300">

                {{ __('police_protection.threat_level') }}

            </label>



            <select

                name="threat_level"

                class="w-full md:w-1/2 rounded-xl border border-slate-700

                       bg-[#0D172A] px-4 py-3 text-white"

            >



                <option value="">{{ __('police_protection.not_determined') }}</option>



                @foreach([

                    'Low',

                    'Moderate',

                    'High',

                    'Critical',

                ] as $level)

                    <option

                        value="{{ match($level) {
                                'Low' => __('police_protection.threat_levels.low'),
                                'Moderate' => __('police_protection.threat_levels.moderate'),
                                'High' => __('police_protection.threat_levels.high'),
                                'Critical' => __('police_protection.threat_levels.critical'),
                                default => $level,
                            } }}"

                        @selected(

                            old(

                                'threat_level',

                                $detail->threat_level

                            ) === $level

                        )

                    >

                        {{ match($level) {
                                'Low' => __('police_protection.threat_levels.low'),
                                'Moderate' => __('police_protection.threat_levels.moderate'),
                                'High' => __('police_protection.threat_levels.high'),
                                'Critical' => __('police_protection.threat_levels.critical'),
                                default => $level,
                            } }}

                    </option>

                @endforeach



            </select>



        </div>





        <div class="mt-5">



            <label class="block mb-2 text-sm text-slate-300">

                {{ __('police_protection.assessment_summary') }}

            </label>



            <textarea

                name="assessment_summary"

                rows="6"

                placeholder="{{ __('police_protection.assessment_summary_placeholder') }}"

                class="w-full rounded-xl border border-slate-700

                       bg-[#0D172A] px-4 py-3 text-white"

            >{{ old(

                'assessment_summary',

                $detail->assessment_summary

            ) }}</textarea>



        </div>



        {{-- =========================================================

     DIRECTOR THREAT ASSESSMENT RECOMMENDATION

\========================================================= --}}



@if(auth()->user()->role === 'police_protection_director')



    <div

        class="mb-6

               rounded-2xl

               border border-red-500/20

               bg-red-500/[0.05]

               p-6"

    >



        <div

            class="flex flex-col

                   gap-5

                   lg:flex-row

                   lg:items-center

                   lg:justify-between"

        >



            <div class="max-w-2xl">



                <div

                    class="flex items-center

                           gap-3"

                >



                    <div

                        class="flex h-10 w-10

                               items-center

                               justify-center

                               rounded-xl

                               border border-red-500/20

                               bg-red-500/10

                               text-red-300"

                    >

                        ⚠

                    </div>



                    <div>



                        <h3

                            class="font-semibold

                                   text-white"

                        >

                            {{ __('police_protection.threat_assessment_recommendation') }}

                        </h3>



                        <p

                            class="mt-1

                                   text-xs

                                   text-slate-500"

                        >

                            {{ __('police_protection.director_police_protection_division') }}

                        </p>



                    </div>



                </div>





                <p

                    class="mt-4

                           text-sm

                           leading-relaxed

                           text-slate-400"

                >

                    {{ __('police_protection.recommendation_intro_1') }}

                    {{ __('police_protection.recommendation_intro_2') }}

                    {{ __('police_protection.recommendation_intro_3') }}

                    {{ __('police_protection.recommendation_intro_4') }}

                </p>



            </div>





            <form

                method="POST"

                action="{{ route(

                    'police-protection.threat-status.update',

                    ['case' => $case->id]

                ) }}"



                class="w-full

                       lg:w-80"

            >



                @csrf





                <label

                    for="threat_assessment_status"

                    class="mb-2

                           block

                           text-xs

                           font-medium

                           uppercase

                           tracking-wider

                           text-slate-400"

                >

                    {{ __('police_protection.recommended_threat_status') }}

                </label>





                <select

                    id="threat_assessment_status"

                    name="threat_assessment_status"

                    required



                    class="w-full

                           rounded-xl

                           border border-slate-700

                           bg-[#0D172A]

                           px-4 py-3

                           text-sm

                           text-white

                           outline-none

                           transition

                           focus:border-red-500

                           focus:ring-2

                           focus:ring-red-500/20"

                >



                    <option value="">

                        {{ __('police_protection.select_threat_status') }}

                    </option>



                    @foreach([

                        'Very High',

                        'High',

                        'Low',

                        'Very Low'

                    ] as $status)



                        <option

                            value="{{ match($status) {
                                'Awaiting Request' => __('police_protection.assessment_statuses.awaiting_request'),
                                'Request {{ __('police_protection.received') }}' => __('police_protection.assessment_statuses.request_received'),
                                'Assessment {{ __('police_protection.started') }}' => __('police_protection.assessment_statuses.assessment_started'),
                                'Assessment In Progress' => __('police_protection.assessment_statuses.assessment_in_progress'),
                                'Assessment {{ __('police_protection.completed') }}' => __('police_protection.assessment_statuses.assessment_completed'),
                                'Very High' => __('police_protection.recommended_statuses.very_high'),
                                'High' => __('police_protection.recommended_statuses.high'),
                                'Low' => __('police_protection.recommended_statuses.low'),
                                'Very Low' => __('police_protection.recommended_statuses.very_low'),
                                default => $status,
                            } }}"

                            @selected(

                                old(

                                    'threat_assessment_status',

                                    $case->threat_assessment_status

                                ) === $status

                            )

                        >

                            {{ match($status) {
                                'Awaiting Request' => __('police_protection.assessment_statuses.awaiting_request'),
                                'Request {{ __('police_protection.received') }}' => __('police_protection.assessment_statuses.request_received'),
                                'Assessment {{ __('police_protection.started') }}' => __('police_protection.assessment_statuses.assessment_started'),
                                'Assessment In Progress' => __('police_protection.assessment_statuses.assessment_in_progress'),
                                'Assessment {{ __('police_protection.completed') }}' => __('police_protection.assessment_statuses.assessment_completed'),
                                'Very High' => __('police_protection.recommended_statuses.very_high'),
                                'High' => __('police_protection.recommended_statuses.high'),
                                'Low' => __('police_protection.recommended_statuses.low'),
                                'Very Low' => __('police_protection.recommended_statuses.very_low'),
                                default => $status,
                            } }}

                        </option>



                    @endforeach



                </select>





                <button

                    type="submit"



                    class="mt-3

                           w-full

                           rounded-xl

                           bg-gradient-to-r

                           from-red-600

                           to-orange-600

                           px-4 py-3

                           text-sm

                           font-semibold

                           text-white

                           transition

                           hover:opacity-90"

                >

                    {{ __('police_protection.save_recommendation') }}

                </button>



            </form>



        </div>





        @if($case->threat_assessment_status)



            <div

                class="mt-5

                       border-t

                       border-red-500/10

                       pt-4"

            >



                <p class="text-xs text-slate-500">



                    {{ __('police_protection.current_recommendation') }}



                    <span class="font-semibold text-red-300">

                        {{ $recommendedThreatStatusLabel }}

                    </span>



                    @if($case->threat_assessment_at)



                        · {{ __('police_protection.updated') }}

                        {{ $case->threat_assessment_at->format('d M Y, h:i A') }}



                    @endif



                </p>



            </div>



        @endif



    </div>



@endif





        <div class="mt-5">



            <label class="block mb-2 text-sm text-slate-300">

                {{ __('police_protection.recommendation') }}

            </label>



            <textarea

                name="recommendation"

                rows="5"

                placeholder="Record {{ __('police_protection.division_name') }} recommendation..."

                class="w-full rounded-xl border border-slate-700

                       bg-[#0D172A] px-4 py-3 text-white"

            >{{ old(

                'recommendation',

                $detail->recommendation

            ) }}</textarea>



        </div>



    </div>





    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <label class="block mb-2 text-sm text-slate-300">

            {{ __('police_protection.remarks') }}

        </label>



        <textarea

            name="remarks"

            rows="4"

            placeholder="{{ __('police_protection.remarks_placeholder') }}"

            class="w-full rounded-xl border border-slate-700

                   bg-[#0D172A] px-4 py-3 text-white"

        >{{ old(

            'remarks',

            $detail->remarks

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

                    {{ __('police_protection.threat_assessment_workflow') }}

                </p>



                <p class="mt-1 text-xs text-slate-600">

                    {{ __('police_protection.results_returned_automatically') }}

                </p>

            </div>



            <div class="flex items-center gap-3">



                <a

                    href="{{ route('cases.show', $case) }}"

                    class="rounded-xl border border-slate-700

                           bg-[#111A2E] px-5 py-3

                           text-sm text-slate-300"

                >

                    {{ __('police_protection.cancel') }}

                </a>



                <button

                    type="submit"

                    class="rounded-xl

                           bg-gradient-to-r from-blue-600 to-purple-600

                           px-6 py-3

                           text-sm font-semibold text-white"

                >

                    {{ __('police_protection.save_threat_assessment') }}

                </button>



            </div>



        </div>



    </div>



</form>



@endsection