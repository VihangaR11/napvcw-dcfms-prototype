@extends('layouts.app')



@section('title', __('cases_create.title'))

@section('page-title', __('cases_create.page_title'))



@section(

    'page-description',

    __('cases_create.page_description')

)



@section('content')



<form

    method="POST"

    action="{{ route('cases.store') }}"

    class="space-y-6"

>

    @csrf



    @if ($errors->any())

        <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-4">

            <p class="font-medium text-red-300">

                {{ __('cases_create.validation_heading') }}

            </p>



            <ul class="mt-2 list-disc pl-5 text-sm text-red-300">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif





    {{-- Intake Information --}}

    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <h2 class="text-lg font-semibold">

            {{ __('cases_create.complaint_intake') }}

        </h2>



        <p class="mt-1 text-sm text-slate-500">

            {{ __('cases_create.complaint_intake_desc') }}

        </p>





        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.received_date') }} *

                </label>



                <input

                    type="date"

                    name="received_date"

                    value="{{ old('received_date', now()->format('Y-m-d')) }}"

                    required

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.complaint_source') }} *

                </label>



                <select

                    name="complaint_source"

                    required

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

                    <option value="">{{ __('cases_create.select_source') }}</option>



                    @foreach([

                        'Direct Complaint',

                        'Police Station',

                        'NAPVCW Police Protection Division',

                        'Court Order',

                        'Commission Order',

                        'Other Institution'

                    ] as $source)



                        <option

                            value="{{ match($source) {
                                'Direct Complaint' => __('cases_create.sources.direct_complaint'),
                                'Police Station' => __('cases_create.sources.police_station'),
                                'NAPVCW Police Protection Division' => __('cases_create.sources.napvcw_police_protection_division'),
                                'Court Order' => __('cases_create.sources.court_order'),
                                'Commission Order' => __('cases_create.sources.commission_order'),
                                'Other Institution' => __('cases_create.sources.other_institution'),
                                default => $source,
                            } }}"

                            @selected(old('complaint_source') === $source)

                        >

                            {{ match($source) {
                                'Direct Complaint' => __('cases_create.sources.direct_complaint'),
                                'Police Station' => __('cases_create.sources.police_station'),
                                'NAPVCW Police Protection Division' => __('cases_create.sources.napvcw_police_protection_division'),
                                'Court Order' => __('cases_create.sources.court_order'),
                                'Commission Order' => __('cases_create.sources.commission_order'),
                                'Other Institution' => __('cases_create.sources.other_institution'),
                                default => $source,
                            } }}

                        </option>



                    @endforeach

                </select>

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.complaint_mode') }}

                </label>



                <select

                    name="complaint_mode"

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

                    <option value="">{{ __('cases_create.select_mode') }}</option>



                    @foreach([

                        'By Hand',

                        'Post',

                        'Courier',

                        'Email',

                        'Fax',

                        'Telephone / Hotline',

                        'Official Referral'

                    ] as $mode)



                        <option

                            value="{{ match($mode) {
                                'By Hand' => __('cases_create.modes.by_hand'),
                                'Post' => __('cases_create.modes.post'),
                                'Courier' => __('cases_create.modes.courier'),
                                'Email' => __('cases_create.modes.email'),
                                'Fax' => __('cases_create.modes.fax'),
                                'Telephone / Hotline' => __('cases_create.modes.telephone_hotline'),
                                'Official Referral' => __('cases_create.modes.official_referral'),
                                default => $mode,
                            } }}"

                            @selected(old('complaint_mode') === $mode)

                        >

                            {{ match($mode) {
                                'By Hand' => __('cases_create.modes.by_hand'),
                                'Post' => __('cases_create.modes.post'),
                                'Courier' => __('cases_create.modes.courier'),
                                'Email' => __('cases_create.modes.email'),
                                'Fax' => __('cases_create.modes.fax'),
                                'Telephone / Hotline' => __('cases_create.modes.telephone_hotline'),
                                'Official Referral' => __('cases_create.modes.official_referral'),
                                default => $mode,
                            } }}

                        </option>



                    @endforeach



                </select>

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.urgency') }} *

                </label>



                <select

                    name="urgency"

                    required

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

                    <option value="Normal" @selected(old('urgency', 'Normal') === 'Normal')>{{ __('cases_create.urgencies.normal') }}</option>

                    <option value="Urgent" @selected(old('urgency') === 'Urgent')>{{ __('cases_create.urgencies.urgent') }}</option>

                    <option value="Critical" @selected(old('urgency') === 'Critical')>{{ __('cases_create.urgencies.critical') }}</option>

                </select>

            </div>



        </div>



    </div>





    {{-- Party Information --}}

    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <h2 class="text-lg font-semibold">

            {{ __('cases_create.party_information') }}

        </h2>





        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.complainant_name') }}

                </label>



                <input

                    type="text"

                    name="complainant_name"

                    value="{{ old('complainant_name') }}"

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.person_type') }}

                </label>



                <select

                    name="victim_witness_type"

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

                    <option value="">{{ __('cases_create.select_type') }}</option>

                    <option value="Victim" @selected(old('victim_witness_type') === 'Victim')>{{ __('cases_create.person_types.victim') }}</option>

                    <option value="Witness" @selected(old('victim_witness_type') === 'Witness')>{{ __('cases_create.person_types.witness') }}</option>

                    <option value="Representative" @selected(old('victim_witness_type') === 'Representative')>{{ __('cases_create.person_types.representative') }}</option>

                    <option value="Other" @selected(old('victim_witness_type') === 'Other')>{{ __('cases_create.person_types.other') }}</option>

                </select>

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.contact_number') }}

                </label>



                <input

                    type="text"

                    name="contact_number"

                    value="{{ old('contact_number') }}"

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.email') }}

                </label>



                <input

                    type="email"

                    name="email"

                    value="{{ old('email') }}"

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

            </div>



        </div>



    </div>





    {{-- {{ __('cases_create.complaint_classification') }} --}}

    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">



        <h2 class="text-lg font-semibold">

            {{ __('cases_create.complaint_classification') }}

        </h2>





        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">



            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.complaint_category') }} *

                </label>



                <select

                    name="complaint_category"

                    required

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

                    <option value="">{{ __('cases_create.select_category') }}</option>



                    <option value="Rights / Entitlement Violation" @selected(old('complaint_category') === 'Rights / Entitlement Violation')>{{ __('cases_create.categories.rights_entitlement_violation') }}</option>



                    <option value="Protection Request" @selected(old('complaint_category') === 'Protection Request')>{{ __('cases_create.categories.protection_request') }}</option>



                    <option value="Assistance Request" @selected(old('complaint_category') === 'Assistance Request')>{{ __('cases_create.categories.assistance_request') }}</option>



                    <option value="Offence Information" @selected(old('complaint_category') === 'Offence Information')>{{ __('cases_create.categories.offence_information') }}</option>



                    <option value="Court / Commission Order" @selected(old('complaint_category') === 'Court / Commission Order')>{{ __('cases_create.categories.court_commission_order') }}</option>



                    <option value="Police Request" @selected(old('complaint_category') === 'Police Request')>{{ __('cases_create.categories.police_request') }}</option>



                    <option value="Multi-Division Case" @selected(old('complaint_category') === 'Multi-Division Case')>{{ __('cases_create.categories.multi_division_case') }}</option>



                    <option value="Other" @selected(old('complaint_category') === 'Other')>{{ __('cases_create.categories.other') }}</option>



                </select>

            </div>





            <div>

                <label class="block mb-2 text-sm text-slate-300">

                    {{ __('cases_create.initial_division') }}

                </label>



                <select

                    name="primary_division"

                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                           px-4 py-3 text-white focus:border-blue-500 outline-none"

                >

                    <option value="">{{ __('cases_create.not_yet_routed') }}</option>

                    <option value="Law and Law Enforcement" @selected(old('primary_division') === 'Law and Law Enforcement')>{{ __('cases_create.divisions.law_enforcement') }}</option>

                    <option value="Protection Services" @selected(old('primary_division') === 'Protection Services')>{{ __('cases_create.divisions.protection_services') }}</option>

                    <option value="Police Protection" @selected(old('primary_division') === 'Police Protection')>{{ __('cases_create.divisions.police_protection') }}</option>

                    <option value="Assistance Services" @selected(old('primary_division') === 'Assistance Services')>{{ __('cases_create.divisions.assistance_services') }}</option>

                </select>

            </div>



        </div>





        <div class="mt-5">



            <label class="block mb-2 text-sm text-slate-300">

                {{ __('cases_create.complaint_summary') }} *

            </label>



            <textarea

                name="complaint_summary"

                rows="6"

                required

                placeholder="{{ __('cases_create.summary_placeholder') }}"

                class="w-full rounded-xl border border-slate-700 bg-[#0D172A]

                       px-4 py-3 text-white placeholder-slate-600

                       focus:border-blue-500 outline-none"

            >{{ old('complaint_summary') }}</textarea>



        </div>



    </div>





    {{-- Submit --}}

    <div class="flex justify-end gap-3">



        <a

            href="{{ route('dashboard') }}"

            class="rounded-xl border border-slate-700

                   px-5 py-3 text-sm text-slate-300"

        >

            {{ __('cases_create.cancel') }}

        </a>



        <button

            type="submit"

            class="rounded-xl bg-gradient-to-r

                   from-blue-600 to-purple-600

                   px-6 py-3 text-sm font-semibold text-white"

        >

            {{ __('cases_create.register_case') }}

        </button>



    </div>



</form>



@endsection