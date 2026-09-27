@extends('layouts.app')

@section('title', 'Register Case')
@section('page-title', 'Register New Case')

@section(
    'page-description',
    'Create the initial complaint record before case routing and division processing.'
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
                Please correct the following:
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
            Complaint Intake
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Record how and when the complaint was received.
        </p>


        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label class="block mb-2 text-sm text-slate-300">
                    Received Date *
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
                    Complaint Source *
                </label>

                <select
                    name="complaint_source"
                    required
                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]
                           px-4 py-3 text-white focus:border-blue-500 outline-none"
                >
                    <option value="">Select source</option>

                    @foreach([
                        'Direct Complaint',
                        'Police Station',
                        'NAPVCW Police Protection Division',
                        'Court Order',
                        'Commission Order',
                        'Other Institution'
                    ] as $source)

                        <option
                            value="{{ $source }}"
                            @selected(old('complaint_source') === $source)
                        >
                            {{ $source }}
                        </option>

                    @endforeach
                </select>
            </div>


            <div>
                <label class="block mb-2 text-sm text-slate-300">
                    Complaint Mode
                </label>

                <select
                    name="complaint_mode"
                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]
                           px-4 py-3 text-white focus:border-blue-500 outline-none"
                >
                    <option value="">Select mode</option>

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
                            value="{{ $mode }}"
                            @selected(old('complaint_mode') === $mode)
                        >
                            {{ $mode }}
                        </option>

                    @endforeach

                </select>
            </div>


            <div>
                <label class="block mb-2 text-sm text-slate-300">
                    Urgency *
                </label>

                <select
                    name="urgency"
                    required
                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]
                           px-4 py-3 text-white focus:border-blue-500 outline-none"
                >
                    <option value="Normal">Normal</option>
                    <option value="Urgent">Urgent</option>
                    <option value="Critical">Critical</option>
                </select>
            </div>

        </div>

    </div>


    {{-- Party Information --}}
    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">

        <h2 class="text-lg font-semibold">
            Complainant / Party Information
        </h2>


        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label class="block mb-2 text-sm text-slate-300">
                    Complainant Name
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
                    Person Type
                </label>

                <select
                    name="victim_witness_type"
                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]
                           px-4 py-3 text-white focus:border-blue-500 outline-none"
                >
                    <option value="">Select type</option>
                    <option value="Victim">Victim</option>
                    <option value="Witness">Witness</option>
                    <option value="Representative">Representative</option>
                    <option value="Other">Other</option>
                </select>
            </div>


            <div>
                <label class="block mb-2 text-sm text-slate-300">
                    Contact Number
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
                    Email
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


    {{-- Complaint Classification --}}
    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">

        <h2 class="text-lg font-semibold">
            Complaint Classification
        </h2>


        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <label class="block mb-2 text-sm text-slate-300">
                    Complaint Category *
                </label>

                <select
                    name="complaint_category"
                    required
                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]
                           px-4 py-3 text-white focus:border-blue-500 outline-none"
                >
                    <option value="">Select category</option>

                    <option value="Rights / Entitlement Violation">
                        Rights / Entitlement Violation
                    </option>

                    <option value="Protection Request">
                        Protection Request
                    </option>

                    <option value="Assistance Request">
                        Assistance Request
                    </option>

                    <option value="Offence Information">
                        Offence Information
                    </option>

                    <option value="Court / Commission Order">
                        Court / Commission Order
                    </option>

                    <option value="Police Request">
                        Police Request
                    </option>

                    <option value="Multi-Division Case">
                        Multi-Division Case
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>
            </div>


            <div>
                <label class="block mb-2 text-sm text-slate-300">
                    Initial Division
                </label>

                <select
                    name="primary_division"
                    class="w-full rounded-xl border border-slate-700 bg-[#0D172A]
                           px-4 py-3 text-white focus:border-blue-500 outline-none"
                >
                    <option value="">Not yet routed</option>
                    <option value="Law and Law Enforcement">
                        Law and Law Enforcement
                    </option>
                    <option value="Protection Services">
                        Protection Services
                    </option>
                    <option value="Police Protection">
                        Police Protection
                    </option>
                    <option value="Assistance Services">
                        Assistance Services
                    </option>
                </select>
            </div>

        </div>


        <div class="mt-5">

            <label class="block mb-2 text-sm text-slate-300">
                Complaint Summary *
            </label>

            <textarea
                name="complaint_summary"
                rows="6"
                required
                placeholder="Enter a concise factual summary of the complaint..."
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
            Cancel
        </a>

        <button
            type="submit"
            class="rounded-xl bg-gradient-to-r
                   from-blue-600 to-purple-600
                   px-6 py-3 text-sm font-semibold text-white"
        >
            Register Case
        </button>

    </div>

</form>

@endsection