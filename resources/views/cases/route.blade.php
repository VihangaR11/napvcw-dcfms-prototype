@extends('layouts.app')

@section('title', 'Route Case')
@section('page-title', 'Route Case')

@section(
    'page-description',
    'Assign this master case to one or more operational divisions for parallel processing.'
)

@section('content')

<div class="max-w-4xl">

    <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6 mb-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <p class="text-xs uppercase tracking-wider text-slate-500">
                    Case Number
                </p>

                <h2 class="mt-1 text-2xl font-semibold text-blue-300">
                    {{ $case->case_number }}
                </h2>
            </div>

            <div class="flex flex-wrap gap-2">

                <span class="rounded-full border border-blue-500/20 bg-blue-500/10 px-3 py-1 text-xs text-blue-300">
                    {{ $case->current_status }}
                </span>

                <span class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-300">
                    {{ $case->complaint_category }}
                </span>

            </div>

        </div>

        <div class="mt-5 rounded-xl border border-slate-800 bg-[#0D172A] p-4">

            <p class="text-xs uppercase tracking-wider text-slate-500">
                Complaint Summary
            </p>

            <p class="mt-2 text-sm leading-relaxed text-slate-300">
                {{ $case->complaint_summary }}
            </p>

        </div>

    </div>


    <form method="POST" action="{{ route('cases.route', $case) }}">
        @csrf

        <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-6">

            <h3 class="text-lg font-semibold">
                Select Divisions
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Multiple divisions may be selected when the same case requires simultaneous handling.
            </p>


            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                @foreach($divisions as $division)

                    @php
                        $alreadyAssigned = in_array($division, $existingAssignments);
                    @endphp

                    <label
                        class="relative flex cursor-pointer items-start gap-4
                               rounded-2xl border
                               {{ $alreadyAssigned
                                    ? 'border-green-500/30 bg-green-500/[0.06]'
                                    : 'border-slate-800 bg-[#0D172A] hover:border-blue-500/30'
                               }}
                               p-5 transition"
                    >

                        <input
                            type="checkbox"
                            name="divisions[]"
                            value="{{ $division }}"
                            class="mt-1 rounded border-slate-600 bg-[#111A2E] text-blue-600 focus:ring-blue-500"
                            {{ $alreadyAssigned ? 'disabled' : '' }}
                        >

                        <div class="flex-1">

                            <div class="flex items-center justify-between gap-3">

                                <p class="font-medium text-white">
                                    {{ $division }}
                                </p>

                                @if($alreadyAssigned)

                                    <span class="rounded-full border border-green-500/20 bg-green-500/10 px-2 py-1 text-[10px] uppercase text-green-300">
                                        Assigned
                                    </span>

                                @endif

                            </div>


                            @switch($division)

                                @case('Law and Law Enforcement')
                                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                                        Rights, entitlement, legal, inquiry, offence, court and commission related matters.
                                    </p>
                                    @break

                                @case('Protection Services')
                                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                                        Threats, influence, protection needs, counselling and protection-related follow-up.
                                    </p>
                                    @break

                                @case('Police Protection')
                                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                                        Threat assessment and police protection coordination.
                                    </p>
                                    @break

                                @case('Assistance Services')
                                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                                        Victim and witness assistance, referral and support-related cases.
                                    </p>
                                    @break

                            @endswitch

                        </div>

                    </label>

                @endforeach

            </div>


            <div class="mt-6">

                <label class="block mb-2 text-sm text-slate-300">
                    Routing Note
                </label>

                <textarea
                    name="routing_note"
                    rows="4"
                    placeholder="Optional instruction or reason for routing..."
                    class="w-full rounded-xl border border-slate-700
                           bg-[#0D172A]
                           px-4 py-3 text-white
                           placeholder-slate-600
                           outline-none
                           focus:border-blue-500"
                >{{ old('routing_note') }}</textarea>

            </div>


            <div class="mt-6 flex justify-end gap-3">

                <a
                    href="{{ route('cases.show', $case) }}"
                    class="rounded-xl border border-slate-700 px-5 py-3 text-sm text-slate-300 hover:bg-[#152238]"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-gradient-to-r from-blue-600 to-purple-600
                           px-6 py-3 text-sm font-semibold text-white"
                >
                    Route Case
                </button>

            </div>

        </div>

    </form>

</div>

@endsection