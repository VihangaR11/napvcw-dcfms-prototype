@extends('layouts.app')

@section('title', 'All Cases')
@section('page-title', 'Case Registry')
@section('page-description', 'Search and review registered DCFMS cases.')

@section('content')

<div class="rounded-2xl border border-slate-800 bg-[#111A2E]">

    <div class="border-b border-slate-800 p-5">

        <form
            method="GET"
            action="{{ route('cases.index') }}"
            class="flex flex-col lg:flex-row gap-3"
        >

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search case number, complainant or summary..."
                class="flex-1 rounded-xl border border-slate-700 bg-[#0D172A]
                       px-4 py-2.5 text-sm text-white outline-none"
            >

            <select
                name="status"
                class="rounded-xl border border-slate-700 bg-[#0D172A]
                       px-4 py-2.5 text-sm text-white"
            >
                <option value="">All Statuses</option>
                <option value="Registered">Registered</option>
                <option value="Under Processing">Under Processing</option>
                <option value="Closed">Closed</option>
            </select>

            <button
                class="rounded-xl bg-blue-600 px-5 py-2.5
                       text-sm font-medium"
            >
                Search
            </button>

        </form>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-left">

            <thead class="border-b border-slate-800 bg-[#0D172A]">
                <tr class="text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-4">Case Number</th>
                    <th class="px-5 py-4">Category</th>
                    <th class="px-5 py-4">Division</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Urgency</th>
                    <th class="px-5 py-4">Received</th>
                    <th class="px-5 py-4"></th>
                </tr>
            </thead>

            <tbody>

                @forelse($cases as $case)

                    <tr class="border-b border-slate-800/70 hover:bg-[#152238]">

                        <td class="px-5 py-4 text-sm font-medium text-blue-300">
                            {{ $case->case_number }}
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-300">
                            {{ $case->complaint_category }}
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-400">
                            {{ $case->primary_division ?? 'Not Routed' }}
                        </td>

                        <td class="px-5 py-4">
                            <span class="rounded-full bg-blue-500/10
                                         border border-blue-500/20
                                         px-3 py-1 text-xs text-blue-300">
                                {{ $case->current_status }}
                            </span>
                        </td>

                        <td class="px-5 py-4 text-sm">
                            {{ $case->urgency }}
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-400">
                            {{ $case->received_date->format('d M Y') }}
                        </td>

                        <td class="px-5 py-4 text-right">

                            <a
                                href="{{ route('cases.show', $case) }}"
                                class="text-sm text-blue-400 hover:text-blue-300"
                            >
                                View →
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="7"
                            class="px-5 py-12 text-center text-slate-500"
                        >
                            No cases have been registered yet.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($cases->hasPages())
        <div class="p-5 border-t border-slate-800">
            {{ $cases->links() }}
        </div>
    @endif

</div>

@endsection