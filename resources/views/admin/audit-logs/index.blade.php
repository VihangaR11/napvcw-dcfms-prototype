@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Log Viewer')
@section(
    'page-description',
    'Review security-sensitive and operational system activity recorded by DCFMS.'
)

@section('content')

@php
    $roleLabel = fn (?string $role) =>
        $role
            ? ucwords(str_replace('_', ' ', $role))
            : 'System';

    $formatValue = function ($value) {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return implode(', ', array_map(
                fn ($item) => is_scalar($item) || $item === null
                    ? (string) $item
                    : json_encode($item),
                $value
            ));
        }

        return $value === null || $value === ''
            ? '—'
            : (string) $value;
    };
@endphp


<div class="space-y-6">

    {{-- =========================================================
         FILTERS
    ========================================================== --}}

    <div
        class="rounded-2xl
               border border-slate-800
               bg-[#111A2E]
               p-5"
    >
        <form
            method="GET"
            action="{{ route('admin.audit-logs.index') }}"
            class="grid grid-cols-1 gap-4 xl:grid-cols-12"
        >

            <div class="xl:col-span-4">
                <label class="mb-2 block text-xs font-medium text-slate-400">
                    Search
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Case number, user, action, module..."
                    class="w-full
                           rounded-xl
                           border border-slate-700
                           bg-[#0D172A]
                           px-4 py-2.5
                           text-sm text-white
                           outline-none
                           placeholder:text-slate-600
                           focus:border-blue-500/50"
                >
            </div>


            <div class="xl:col-span-2">
                <label class="mb-2 block text-xs font-medium text-slate-400">
                    Action
                </label>

                <select
                    name="action"
                    class="w-full
                           rounded-xl
                           border border-slate-700
                           bg-[#0D172A]
                           px-3 py-2.5
                           text-sm text-white"
                >
                    <option value="">All Actions</option>

                    @foreach($actions as $action)
                        <option
                            value="{{ $action }}"
                            {{ request('action') === $action ? 'selected' : '' }}
                        >
                            {{ str_replace('_', ' ', $action) }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="xl:col-span-2">
                <label class="mb-2 block text-xs font-medium text-slate-400">
                    Module
                </label>

                <select
                    name="module"
                    class="w-full
                           rounded-xl
                           border border-slate-700
                           bg-[#0D172A]
                           px-3 py-2.5
                           text-sm text-white"
                >
                    <option value="">All Modules</option>

                    @foreach($modules as $module)
                        <option
                            value="{{ $module }}"
                            {{ request('module') === $module ? 'selected' : '' }}
                        >
                            {{ $module }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="xl:col-span-2">
                <label class="mb-2 block text-xs font-medium text-slate-400">
                    User
                </label>

                <select
                    name="user_id"
                    class="w-full
                           rounded-xl
                           border border-slate-700
                           bg-[#0D172A]
                           px-3 py-2.5
                           text-sm text-white"
                >
                    <option value="">All Users</option>

                    @foreach($users as $auditUser)
                        <option
                            value="{{ $auditUser->id }}"
                            {{ (string) request('user_id') === (string) $auditUser->id ? 'selected' : '' }}
                        >
                            {{ $auditUser->name }}
                            @if($auditUser->employee_number)
                                ({{ $auditUser->employee_number }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="xl:col-span-2">
                <label class="mb-2 block text-xs font-medium text-slate-400">
                    From
                </label>

                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="w-full
                           rounded-xl
                           border border-slate-700
                           bg-[#0D172A]
                           px-3 py-2.5
                           text-sm text-white"
                >
            </div>


            <div class="xl:col-span-2">
                <label class="mb-2 block text-xs font-medium text-slate-400">
                    To
                </label>

                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="w-full
                           rounded-xl
                           border border-slate-700
                           bg-[#0D172A]
                           px-3 py-2.5
                           text-sm text-white"
                >
            </div>


            <div class="flex items-end gap-3 xl:col-span-10">

                <button
                    type="submit"
                    class="rounded-xl
                           bg-blue-600
                           px-5 py-2.5
                           text-sm font-semibold text-white
                           transition hover:bg-blue-500"
                >
                    Apply Filters
                </button>

                <a
                    href="{{ route('admin.audit-logs.index') }}"
                    class="rounded-xl
                           border border-slate-700
                           px-5 py-2.5
                           text-sm font-medium text-slate-300
                           transition hover:bg-slate-800"
                >
                    Reset
                </a>

            </div>

        </form>
    </div>


    {{-- =========================================================
         SUMMARY
    ========================================================== --}}

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500">
                Visible Records
            </p>

            <p class="mt-2 text-2xl font-bold text-white">
                {{ $logs->total() }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500">
                Actions
            </p>

            <p class="mt-2 text-2xl font-bold text-white">
                {{ $actions->count() }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500">
                Modules
            </p>

            <p class="mt-2 text-2xl font-bold text-white">
                {{ $modules->count() }}
            </p>
        </div>

    </div>


    {{-- =========================================================
         AUDIT LOG TABLE
    ========================================================== --}}

    <div
        class="overflow-hidden
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]"
    >

        <div class="border-b border-slate-800 px-5 py-4">

            <h3 class="font-semibold text-white">
                Recorded Activity
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Audit records are read-only and are intended for accountability and traceability.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full text-left">

                <thead class="border-b border-slate-800 bg-[#0D172A]">

                    <tr class="text-xs uppercase tracking-wider text-slate-500">

                        <th class="px-5 py-4">Date / Time</th>

                        <th class="px-5 py-4">User</th>

                        <th class="px-5 py-4">Action</th>

                        <th class="px-5 py-4">Module</th>

                        <th class="px-5 py-4">Case</th>

                        <th class="px-5 py-4">Description</th>

                        <th class="px-5 py-4">Changes</th>

                        <th class="px-5 py-4">IP</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($logs as $log)

                        <tr
                            class="border-b border-slate-800/70
                                   align-top
                                   transition
                                   hover:bg-[#152238]"
                        >

                            <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-400">

                                <div class="font-medium text-slate-300">
                                    {{ $log->created_at?->format('d M Y') }}
                                </div>

                                <div class="mt-1">
                                    {{ $log->created_at?->format('h:i:s A') }}
                                </div>

                            </td>


                            <td class="px-5 py-4">

                                @if($log->user)

                                    <div class="text-sm font-medium text-white">
                                        {{ $log->user->name }}
                                    </div>

                                    <div class="mt-1 text-[10px] text-slate-500">
                                        {{ $log->user->employee_number ?? 'No EPF' }}
                                        ·
                                        {{ $roleLabel($log->user->role) }}
                                    </div>

                                @else

                                    <span class="text-xs text-slate-500">
                                        System / Deleted User
                                    </span>

                                @endif

                            </td>


                            <td class="px-5 py-4">

                                <span
                                    class="inline-flex rounded-full
                                           border border-blue-500/20
                                           bg-blue-500/10
                                           px-2.5 py-1
                                           text-[10px] font-semibold
                                           text-blue-300"
                                >
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>

                            </td>


                            <td class="px-5 py-4 text-sm text-slate-300">
                                {{ $log->module ?? '—' }}
                            </td>


                            <td class="px-5 py-4">

                                @if($log->case)

                                    <a
                                        href="{{ route('cases.show', $log->case) }}"
                                        class="text-sm font-medium text-blue-400 hover:text-blue-300"
                                    >
                                        {{ $log->case->case_number }}
                                    </a>

                                @elseif($log->dcfms_case_id)

                                    <span class="text-xs text-slate-500">
                                        Case #{{ $log->dcfms_case_id }}
                                    </span>

                                @else

                                    <span class="text-xs text-slate-600">
                                        —
                                    </span>

                                @endif

                            </td>


                            <td class="max-w-sm px-5 py-4 text-xs leading-relaxed text-slate-400">
                                {{ $log->description ?? '—' }}
                            </td>


                            <td class="min-w-[300px] px-5 py-4">

                                @php
                                    $oldValues = $log->old_values ?? [];
                                    $newValues = $log->new_values ?? [];
                                    $changeKeys = collect([
                                        ...array_keys($oldValues),
                                        ...array_keys($newValues),
                                    ])->unique()->values();
                                @endphp


                                @if($changeKeys->isEmpty())

                                    <span class="text-xs text-slate-600">
                                        No structured changes
                                    </span>

                                @else

                                    <div class="space-y-2">

                                        @foreach($changeKeys as $key)

                                            @php
                                                $oldValue = $oldValues[$key] ?? null;
                                                $newValue = $newValues[$key] ?? null;
                                            @endphp

                                            <div
                                                class="rounded-lg
                                                       border border-slate-800
                                                       bg-[#0D172A]
                                                       p-2.5"
                                            >

                                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                                    {{ str_replace('_', ' ', $key) }}
                                                </p>

                                                <div class="mt-2 grid grid-cols-2 gap-2 text-[11px]">

                                                    <div>
                                                        <span class="text-slate-600">
                                                            Before
                                                        </span>

                                                        <p class="mt-1 break-words text-red-300/80">
                                                            {{ $formatValue($oldValue) }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <span class="text-slate-600">
                                                            After
                                                        </span>

                                                        <p class="mt-1 break-words text-green-300/80">
                                                            {{ $formatValue($newValue) }}
                                                        </p>
                                                    </div>

                                                </div>

                                            </div>

                                        @endforeach

                                    </div>

                                @endif

                            </td>


                            <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">
                                {{ $log->ip_address ?? '—' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-5 py-14 text-center"
                            >

                                <p class="text-sm font-medium text-slate-400">
                                    No audit records found.
                                </p>

                                <p class="mt-1 text-xs text-slate-600">
                                    Try changing or resetting the current filters.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($logs->hasPages())

            <div class="border-t border-slate-800 p-5">
                {{ $logs->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
