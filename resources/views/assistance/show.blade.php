@extends('layouts.app')



@section('title', __('assistance.title'))



@section('page-title', __('assistance.page_title'))



@section(

    'page-description',

    __('assistance.page_description')

)



@section('page-actions')

    <a

        href="{{ route('cases.show', $case) }}"

        class="inline-flex items-center gap-2 rounded-xl

               border border-slate-700 bg-[#111A2E]

               px-5 py-2.5 text-sm text-slate-300

               hover:bg-[#152238]"

    >

        ← {{ __('assistance.master_case') }}

    </a>

@endsection



@section('content')

@php
    $assistanceStatusLabel = match($assistanceDetail->assistance_status) {
        'Assistance Request Received' => __('assistance.statuses.assistance_request_received'),
        '{{ __('assistance.assistance_type') }} Identified' => __('assistance.statuses.assistance_type_identified'),
        'Officer Assigned' => __('assistance.statuses.officer_assigned'),
        'Referral Required' => __('assistance.statuses.referral_required'),
        'Referral Initiated' => __('assistance.statuses.referral_initiated'),
        'Under Follow-up' => __('assistance.statuses.under_follow_up'),
        'Assistance Completed' => __('assistance.statuses.assistance_completed'),
        'Closed' => __('assistance.statuses.closed'),
        default => $assistanceDetail->assistance_status,
    };
@endphp




<form

    method="POST"

    action="{{ route('assistance.update', $case) }}"

    class="space-y-6"

>

    @csrf



    <div

        class="rounded-3xl border border-slate-800

               bg-gradient-to-br from-[#101A35]

               via-[#111A2E] to-[#0B1226]

               p-6"

    >



        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">



            <div>

                <p class="text-xs uppercase tracking-wider text-slate-500">

                    {{ __('assistance.master_case') }}

                </p>



                <h2 class="mt-1 text-3xl font-semibold text-blue-300">

                    {{ $case->case_number }}

                </h2>



                <p class="mt-3 text-sm text-slate-400">

                    {{ $case->complaint_summary }}

                </p>

            </div>



            <span

                class="rounded-full border border-blue-500/20

                       bg-blue-500/10 px-3 py-1

                       text-xs text-blue-300"

            >

                {{ $assistanceStatusLabel }}

            </span>



        </div>



    </div>





    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <h3 class="text-lg font-semibold">

            {{ __('assistance.assignment_classification') }}

        </h3>



        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('assistance.assistance_officer') }}

                </label>





            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('assistance.assistance_status') }} *

                </label>



                <select

                    name="assistance_status"

                    required

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

                    @foreach([

                        'Assistance Request Received',

                        '{{ __('assistance.assistance_type') }} Identified',

                        'Officer Assigned',

                        'Referral Required',

                        'Referral Initiated',

                        'Under Follow-up',

                        'Assistance Completed',

                        'Closed',

                    ] as $status)

                        <option

                            value="{{ match($status) {
                                'Assistance Request Received' => __('assistance.statuses.assistance_request_received'),
                                '{{ __('assistance.assistance_type') }} Identified' => __('assistance.statuses.assistance_type_identified'),
                                'Officer Assigned' => __('assistance.statuses.officer_assigned'),
                                'Referral Required' => __('assistance.statuses.referral_required'),
                                'Referral Initiated' => __('assistance.statuses.referral_initiated'),
                                'Under Follow-up' => __('assistance.statuses.under_follow_up'),
                                'Assistance Completed' => __('assistance.statuses.assistance_completed'),
                                'Closed' => __('assistance.statuses.closed'),
                                default => $status,
                            } }}"

                            @selected(

                                old(

                                    'assistance_status',

                                    $assistanceDetail->assistance_status

                                ) === $status

                            )

                        >

                            {{ match($status) {
                                'Assistance Request Received' => __('assistance.statuses.assistance_request_received'),
                                '{{ __('assistance.assistance_type') }} Identified' => __('assistance.statuses.assistance_type_identified'),
                                'Officer Assigned' => __('assistance.statuses.officer_assigned'),
                                'Referral Required' => __('assistance.statuses.referral_required'),
                                'Referral Initiated' => __('assistance.statuses.referral_initiated'),
                                'Under Follow-up' => __('assistance.statuses.under_follow_up'),
                                'Assistance Completed' => __('assistance.statuses.assistance_completed'),
                                'Closed' => __('assistance.statuses.closed'),
                                default => $status,
                            } }}

                        </option>

                    @endforeach

                </select>

            </div>





            <div class="md:col-span-2">

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('assistance.assistance_type') }}

                </label>



                <select

                    name="assistance_type"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

                    <option value="">{{ __('assistance.select_assistance_type') }}</option>



                    @foreach([

                        'Medical Assistance',

                        'Counselling',

                        'Rehabilitation',

                        'Victim Impact Statement Support',

                        'Court Participation Support',

                        'Compensation Related Support',

                        'Dependent / Next-of-Kin Support',

                        'Remote Testimony Support',

                        'Other Assistance',

                    ] as $type)

                        <option

                            value="{{ match($type) {
                                'Medical Assistance' => __('assistance.types.medical_assistance'),
                                'Counselling' => __('assistance.types.counselling'),
                                'Rehabilitation' => __('assistance.types.rehabilitation'),
                                'Victim Impact Statement Support' => __('assistance.types.victim_impact_statement_support'),
                                'Court Participation Support' => __('assistance.types.court_participation_support'),
                                'Compensation Related Support' => __('assistance.types.compensation_related_support'),
                                'Dependent / Next-of-Kin Support' => __('assistance.types.dependent_next_of_kin_support'),
                                'Remote Testimony Support' => __('assistance.types.remote_testimony_support'),
                                'Other Assistance' => __('assistance.types.other_assistance'),
                                default => $type,
                            } }}"

                            @selected(

                                old(

                                    'assistance_type',

                                    $assistanceDetail->assistance_type

                                ) === $type

                            )

                        >

                            {{ match($type) {
                                'Medical Assistance' => __('assistance.types.medical_assistance'),
                                'Counselling' => __('assistance.types.counselling'),
                                'Rehabilitation' => __('assistance.types.rehabilitation'),
                                'Victim Impact Statement Support' => __('assistance.types.victim_impact_statement_support'),
                                'Court Participation Support' => __('assistance.types.court_participation_support'),
                                'Compensation Related Support' => __('assistance.types.compensation_related_support'),
                                'Dependent / Next-of-Kin Support' => __('assistance.types.dependent_next_of_kin_support'),
                                'Remote Testimony Support' => __('assistance.types.remote_testimony_support'),
                                'Other Assistance' => __('assistance.types.other_assistance'),
                                default => $type,
                            } }}

                        </option>

                    @endforeach

                </select>

            </div>



        </div>



    </div>





    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <h3 class="text-lg font-semibold">

            {{ __('assistance.referral_follow_up') }}

        </h3>



        <div class="mt-5">



            <label

                class="flex items-center gap-3

                       rounded-xl border border-slate-800

                       bg-[#0D172A] p-4"

            >

                <input

                    type="checkbox"

                    name="referral_required"

                    value="1"

                    @checked(

                        old(

                            'referral_required',

                            $assistanceDetail->referral_required

                        )

                    )

                >



                <span class="text-sm text-slate-300">

                    {{ __('assistance.referral_required') }}

                </span>

            </label>



        </div>





        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('assistance.referred_to') }}

                </label>



                <input

                    type="text"

                    name="referred_to"

                    value="{{ old(

                        'referred_to',

                        $assistanceDetail->referred_to

                    ) }}"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('assistance.referral_date') }}

                </label>



                <input

                    type="date"

                    name="referral_date"

                    value="{{ old(

                        'referral_date',

                        optional($assistanceDetail->referral_date)->format('Y-m-d')

                    ) }}"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('assistance.follow_up_date') }}

                </label>



                <input

                    type="date"

                    name="follow_up_date"

                    value="{{ old(

                        'follow_up_date',

                        optional($assistanceDetail->follow_up_date)->format('Y-m-d')

                    ) }}"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('assistance.completed_date') }}

                </label>



                <input

                    type="date"

                    name="completed_date"

                    value="{{ old(

                        'completed_date',

                        optional($assistanceDetail->completed_date)->format('Y-m-d')

                    ) }}"

                    class="w-full rounded-xl border border-slate-700

                           bg-[#0D172A] px-4 py-3 text-white"

                >

            </div>



        </div>





        <div class="mt-5">



            <label class="block mb-2 text-sm text-slate-300">

                {{ __('assistance.current_action') }}

            </label>



            <textarea

                name="current_action"

                rows="4"

                class="w-full rounded-xl border border-slate-700

                       bg-[#0D172A] px-4 py-3 text-white"

            >{{ old(

                'current_action',

                $assistanceDetail->current_action

            ) }}</textarea>



        </div>



    </div>





    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <h3 class="text-lg font-semibold">

            {{ __('assistance.assistance_outcome') }}

        </h3>



        <div class="mt-5">



            <label class="block mb-2 text-sm text-slate-300">

                {{ __('assistance.outcome_result') }}

            </label>



            <textarea

                name="assistance_outcome"

                rows="5"

                class="w-full rounded-xl border border-slate-700

                       bg-[#0D172A] px-4 py-3 text-white"

            >{{ old(

                'assistance_outcome',

                $assistanceDetail->assistance_outcome

            ) }}</textarea>



        </div>





        <div class="mt-5">



            <label class="block mb-2 text-sm text-slate-300">

                {{ __('assistance.remarks') }}

            </label>



            <textarea

                name="remarks"

                rows="4"

                class="w-full rounded-xl border border-slate-700

                       bg-[#0D172A] px-4 py-3 text-white"

            >{{ old(

                'remarks',

                $assistanceDetail->remarks

            ) }}</textarea>



        </div>



    </div>





    <div

        class="sticky bottom-4 z-10 rounded-2xl

               border border-slate-700

               bg-[#0B1226]/95

               p-4 shadow-2xl backdrop-blur"

    >

        <div class="flex justify-end gap-3">



            <a

                href="{{ route('cases.show', $case) }}"

                class="rounded-xl border border-slate-700

                       px-5 py-3 text-sm text-slate-300"

            >

                {{ __('assistance.cancel') }}

            </a>



            <button

                type="submit"

                class="rounded-xl bg-gradient-to-r

                       from-blue-600 to-purple-600

                       px-6 py-3 text-sm

                       font-semibold text-white"

            >

                {{ __('assistance.save_workflow') }}

            </button>



        </div>

    </div>



</form>



@endsection