@extends('layouts.app')

@section('title', 'Assign Officer')

@section('page-title', 'Assign Responsible Officer')

@section(
    'page-description',
    'Assign an authorized officer to manage this division workflow.'
)

@section('content')

<div class="max-w-4xl">

    {{-- Case Header --}}
    <div
        class="rounded-2xl
               border border-slate-800
               bg-[#111A2E]
               p-6 mb-6"
    >

        <div
            class="flex flex-col gap-4
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>

                <p
                    class="text-[10px]
                           uppercase
                           tracking-[0.18em]
                           text-slate-500"
                >
                    Case
                </p>

                <h2
                    class="mt-1
                           text-2xl
                           font-semibold
                           text-blue-300"
                >
                    {{ $case->case_number }}
                </h2>

            </div>


            <div class="flex flex-wrap gap-2">

                <span
                    class="rounded-full
                           border border-blue-500/20
                           bg-blue-500/10
                           px-3 py-1
                           text-xs text-blue-300"
                >
                    {{ $assignment->division }}
                </span>

                <span
                    class="rounded-full
                           border border-slate-700
                           bg-[#0D172A]
                           px-3 py-1
                           text-xs text-slate-400"
                >
                    {{ $assignment->status }}
                </span>

            </div>

        </div>


        <div
            class="mt-5
                   rounded-xl
                   border border-slate-800
                   bg-[#0D172A]
                   p-4"
        >

            <p
                class="text-[10px]
                       uppercase
                       tracking-wider
                       text-slate-500"
            >
                Complaint Summary
            </p>

            <p
                class="mt-2
                       text-sm
                       leading-relaxed
                       text-slate-300"
            >
                {{ $case->complaint_summary }}
            </p>

        </div>

    </div>


    {{-- Assignment Form --}}
    <form
        method="POST"
        action="{{ route(
            'cases.assignments.assign',
            [$case, $assignment]
        ) }}"
    >

        @csrf


        <div
            class="rounded-2xl
                   border border-slate-800
                   bg-[#111A2E]
                   p-6"
        >

            <div class="border-b border-slate-800 pb-4">

                <h3 class="text-lg font-semibold">
                    Officer Assignment
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Only active officers belonging to
                    {{ $assignment->division }}
                    are listed below.
                </p>

            </div>


            <div class="mt-6">

                <label
                    for="assigned_user_id"
                    class="block mb-2
                           text-sm
                           font-medium
                           text-slate-300"
                >
                    Responsible Officer *
                </label>


                <select
                    id="assigned_user_id"
                    name="assigned_user_id"
                    required

                    class="w-full
                           rounded-xl
                           border border-slate-700
                           bg-[#0D172A]
                           px-4 py-3
                           text-white
                           outline-none
                           focus:border-blue-500"
                >

                    <option value="">
                        Select an officer
                    </option>

                    @foreach($officers as $officer)

                        <option
                            value="{{ $officer->id }}"
                            @selected(
                                old(
                                    'assigned_user_id',
                                    $assignment->assigned_user_id
                                ) == $officer->id
                            )
                        >
                            {{ $officer->name }}
                            —
                            {{ $officer->employee_number }}
                            —
                            {{ ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $officer->role
                                )
                            ) }}
                        </option>

                    @endforeach

                </select>


                @if($officers->isEmpty())

                    <p class="mt-3 text-sm text-yellow-400">
                        No active eligible officers are currently
                        available for this division.
                    </p>

                @endif

            </div>


            <div class="mt-6">

                <label
                    for="assignment_note"
                    class="block mb-2
                           text-sm
                           font-medium
                           text-slate-300"
                >
                    Assignment Instructions / Note
                </label>


                <textarea
                    id="assignment_note"
                    name="assignment_note"
                    rows="4"

                    placeholder="Optional instruction to the responsible officer..."

                    class="w-full
                           rounded-xl
                           border border-slate-700
                           bg-[#0D172A]
                           px-4 py-3
                           text-white
                           placeholder-slate-600
                           outline-none
                           focus:border-blue-500"
                >{{ old('assignment_note') }}</textarea>

            </div>


            <div
                class="mt-7
                       flex justify-end
                       gap-3"
            >

                <a
                    href="{{ route('cases.show', $case) }}"

                    class="rounded-xl
                           border border-slate-700
                           px-5 py-3
                           text-sm
                           text-slate-300
                           transition
                           hover:bg-[#152238]"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    @disabled($officers->isEmpty())

                    class="rounded-xl
                           bg-gradient-to-r
                           from-blue-600
                           to-purple-600
                           px-6 py-3
                           text-sm
                           font-semibold
                           text-white
                           transition
                           hover:opacity-90
                           disabled:cursor-not-allowed
                           disabled:opacity-40"
                >
                    Assign Officer
                </button>

            </div>

        </div>

    </form>

</div>

@endsection