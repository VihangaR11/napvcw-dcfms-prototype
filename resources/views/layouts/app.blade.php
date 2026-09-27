<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="NAPVCW Digital Case Flow Management System"
    >

    <title>
        @yield('title', 'DCFMS') | NAPVCW
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#070B1D] text-white antialiased">

@php
    $user = auth()->user();

    $roleLabel = ucwords(str_replace('_', ' ', $user->role));

    $isManagement = in_array($user->role, [
        'system_admin',
        'director_general',
        'board_secretary',
    ]);

    $isLegal = in_array($user->role, [
        'legal_director',
        'legal_officer',
        'investigation_officer',
        'director_general',
        'system_admin',
    ]);

    $isProtection = in_array($user->role, [
        'protection_director',
        'protection_officer',
        'director_general',
        'system_admin',
    ]);

    $isPoliceProtection = in_array($user->role, [
        'police_protection_officer',
        'director_general',
        'system_admin',
    ]);

    $isAssistance = in_array($user->role, [
        'assistance_officer',
        'director_general',
        'system_admin',
    ]);
@endphp


<div class="min-h-screen">

    {{-- =========================================================
         MOBILE TOP BAR
    ========================================================== --}}
    <div
        class="lg:hidden sticky top-0 z-50
               border-b border-slate-800
               bg-[#0B1226]/95 backdrop-blur"
    >
        <div class="flex items-center justify-between px-4 py-3">

            <div class="flex items-center gap-3">

                <img
                    src="{{ asset('images/napvcw-logo.png') }}"
                    alt="NAPVCW Logo"
                    class="h-10 w-10 rounded-full bg-white p-1 object-contain"
                >

                <div>
                    <p class="text-xs font-semibold text-white">
                        NAPVCW DCFMS
                    </p>

                    <p class="text-[9px] uppercase tracking-wider text-slate-500">
                        Government Service
                    </p>
                </div>

            </div>

            <button
                id="mobileMenuButton"
                type="button"
                class="rounded-lg border border-slate-700
                       bg-[#111A2E] px-3 py-2
                       text-sm text-slate-300"
            >
                Menu
            </button>

        </div>
    </div>


    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}
    <aside
        id="sidebar"
        class="fixed inset-y-0 left-0 z-40
               w-72
               -translate-x-full lg:translate-x-0
               transition-transform duration-300
               border-r border-slate-800
               bg-[#0E1A2B]
               flex flex-col"
    >

        {{-- Brand Header --}}
        <div class="border-b border-slate-800 p-5">

            <div class="flex items-center gap-3">

                <div
                    class="h-12 w-12 rounded-xl
                           bg-white p-1.5
                           shadow-lg
                           flex items-center justify-center"
                >
                    <img
                        src="{{ asset('images/gov-logo.png') }}"
                        alt="Government of Sri Lanka Logo"
                        class="h-full w-full object-contain"
                    >
                </div>

                <div class="min-w-0">

                    <h1 class="text-xl font-bold tracking-wide text-white">
                        DCFMS
                    </h1>

                    <p
                        class="truncate
                               text-[9px]
                               uppercase
                               tracking-[0.18em]
                               text-blue-300"
                    >
                        Government Service
                    </p>

                </div>

            </div>


            <div
                class="mt-4 rounded-xl
                       border border-slate-800
                       bg-[#111A2E]
                       px-3 py-3"
            >

                <p class="text-[9px] uppercase tracking-widest text-slate-500">
                    Institution
                </p>

                <p class="mt-1 text-[11px] leading-relaxed text-slate-300">
                    National Authority for the Protection of Victims of Crime and Witnesses
                </p>

            </div>

        </div>


        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-4 py-5">

            {{-- =========================
                 MAIN
            ========================== --}}
            <p
                class="px-3 mb-2
                       text-[10px]
                       uppercase
                       tracking-[0.18em]
                       text-slate-500"
            >
                Main
            </p>


            <div class="space-y-1">

                <a
                    href="{{ route('dashboard') }}"
                    class="
                        flex items-center gap-3
                        rounded-xl px-4 py-3
                        transition
                        {{ request()->routeIs('dashboard')
                            ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg shadow-blue-950/20'
                            : 'text-slate-300 hover:bg-[#152238] hover:text-white'
                        }}
                    "
                >
                    <span class="w-5 text-center text-sm">▦</span>

                    <span class="text-sm font-medium">
                        Dashboard
                    </span>
                </a>

            </div>


            {{-- =========================
                 CASE MANAGEMENT
            ========================== --}}
            <p
                class="px-3 mt-7 mb-2
                       text-[10px]
                       uppercase
                       tracking-[0.18em]
                       text-slate-500"
            >
                Case Management
            </p>


            <div class="space-y-1">

                @if($isManagement)

                    <a
                        href="{{ route('cases.create') }}"
                        class="
                            flex items-center gap-3
                            rounded-xl px-4 py-3
                            transition
                            {{ request()->routeIs('cases.create')
                                ? 'bg-blue-500/10 border border-blue-500/20 text-blue-300'
                                : 'text-slate-300 hover:bg-[#152238] hover:text-white'
                            }}
                        "
                    >
                        <span class="w-5 text-center text-lg">＋</span>

                        <span class="text-sm">
                            Register New Case
                        </span>
                    </a>


                    <a
                        href="{{ route('cases.index') }}"
                        class="
                            flex items-center gap-3
                            rounded-xl px-4 py-3
                            transition
                            {{ request()->routeIs('cases.index')
                                ? 'bg-blue-500/10 border border-blue-500/20 text-blue-300'
                                : 'text-slate-300 hover:bg-[#152238] hover:text-white'
                            }}
                        "
                    >
                        <span class="w-5 text-center text-sm">▤</span>

                        <span class="text-sm">
                            All Cases
                        </span>
                    </a>

                @else

                    <a
                        href="{{ route('cases.index') }}"
                        class="
                            flex items-center gap-3
                            rounded-xl px-4 py-3
                            transition
                            {{ request()->routeIs('cases.index')
                                ? 'bg-blue-500/10 border border-blue-500/20 text-blue-300'
                                : 'text-slate-300 hover:bg-[#152238] hover:text-white'
                            }}
                        "
                    >
                        <span class="w-5 text-center text-sm">◎</span>

                        <span class="text-sm">
                            My Assigned Cases
                        </span>
                    </a>

                @endif

            </div>


            {{-- =========================
                 DIVISION WORKSPACES
            ========================== --}}
            <p
                class="px-3 mt-7 mb-2
                       text-[10px]
                       uppercase
                       tracking-[0.18em]
                       text-slate-500"
            >
                Division Workspaces
            </p>


            <div class="space-y-1">

                @if($isLegal)

                    <a
                        href="#"
                        class="
                            flex items-center gap-3
                            rounded-xl px-4 py-3
                            text-slate-300
                            transition
                            hover:bg-[#152238]
                            hover:text-white
                        "
                    >
                        <span class="w-5 text-center">⚖</span>

                        <span class="text-sm">
                            Law & Law Enforcement
                        </span>
                    </a>

                @endif


                @if($isProtection)

                    <a
                        href="#"
                        class="
                            flex items-center gap-3
                            rounded-xl px-4 py-3
                            text-slate-300
                            transition
                            hover:bg-[#152238]
                            hover:text-white
                        "
                    >
                        <span class="w-5 text-center">◈</span>

                        <span class="text-sm">
                            Protection Services
                        </span>
                    </a>

                @endif


                @if($isPoliceProtection)

                    <a
                        href="#"
                        class="
                            flex items-center gap-3
                            rounded-xl px-4 py-3
                            text-slate-300
                            transition
                            hover:bg-[#152238]
                            hover:text-white
                        "
                    >
                        <span class="w-5 text-center">⌖</span>

                        <span class="text-sm">
                            Police Protection
                        </span>
                    </a>

                @endif


                @if($isAssistance)

                    <a
                        href="#"
                        class="
                            flex items-center gap-3
                            rounded-xl px-4 py-3
                            text-slate-300
                            transition
                            hover:bg-[#152238]
                            hover:text-white
                        "
                    >
                        <span class="w-5 text-center">✦</span>

                        <span class="text-sm">
                            Assistance Services
                        </span>
                    </a>

                @endif

            </div>


            {{-- =========================
                 REPORTING
            ========================== --}}
            <p
                class="px-3 mt-7 mb-2
                       text-[10px]
                       uppercase
                       tracking-[0.18em]
                       text-slate-500"
            >
                Reporting
            </p>


            <div class="space-y-1">

                <a
                    href="#"
                    class="
                        flex items-center gap-3
                        rounded-xl px-4 py-3
                        text-slate-300
                        transition
                        hover:bg-[#152238]
                        hover:text-white
                    "
                >
                    <span class="w-5 text-center">▧</span>

                    <span class="text-sm">
                        Case Summary Reports
                    </span>
                </a>

            </div>


            {{-- =========================
                 ADMINISTRATION
            ========================== --}}
            @if($user->role === 'system_admin')

                <p
                    class="px-3 mt-7 mb-2
                           text-[10px]
                           uppercase
                           tracking-[0.18em]
                           text-slate-500"
                >
                    Administration
                </p>

                <div class="space-y-1">

                    <a
                        href="#"
                        class="
                            flex items-center gap-3
                            rounded-xl px-4 py-3
                            text-slate-300
                            transition
                            hover:bg-[#152238]
                            hover:text-white
                        "
                    >
                        <span class="w-5 text-center">👤</span>

                        <span class="text-sm">
                            User Accounts
                        </span>
                    </a>

                    <a
                        href="#"
                        class="
                            flex items-center gap-3
                            rounded-xl px-4 py-3
                            text-slate-300
                            transition
                            hover:bg-[#152238]
                            hover:text-white
                        "
                    >
                        <span class="w-5 text-center">⚙</span>

                        <span class="text-sm">
                            System Settings
                        </span>
                    </a>

                </div>

            @endif

        </nav>


        {{-- Logged In User --}}
        <div class="border-t border-slate-800 p-4">

            <div
                class="rounded-2xl
                       border border-slate-800
                       bg-[#111A2E]
                       p-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="h-11 w-11 shrink-0
                               rounded-full
                               bg-white p-1"
                    >
                        <img
                            src="{{ asset('images/napvcw-logo.png') }}"
                            alt="NAPVCW"
                            class="h-full w-full object-contain"
                        >
                    </div>


                    <div class="min-w-0 flex-1">

                        <p class="truncate text-sm font-semibold text-white">
                            {{ $user->name }}
                        </p>

                        <p class="truncate text-[11px] text-slate-500">
                            {{ $roleLabel }}
                        </p>

                        <p class="truncate text-[10px] text-blue-400">
                            {{ $user->employee_number }}
                        </p>

                    </div>

                </div>


                <div
                    class="mt-3 rounded-lg
                           border border-slate-800
                           bg-[#0C1528]
                           px-3 py-2"
                >
                    <p class="text-[9px] uppercase tracking-wider text-slate-600">
                        Division
                    </p>

                    <p class="mt-1 text-[11px] text-slate-400">
                        {{ $user->division }}
                    </p>
                </div>


                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="mt-3"
                >
                    @csrf

                    <button
                        type="submit"
                        class="
                            w-full rounded-lg
                            border border-red-500/20
                            bg-red-500/10
                            px-3 py-2
                            text-xs font-medium text-red-300
                            transition
                            hover:bg-red-500/20
                        "
                    >
                        Sign Out
                    </button>

                </form>

            </div>

        </div>

    </aside>


    {{-- Mobile Overlay --}}
    <div
        id="sidebarOverlay"
        class="fixed inset-0 z-30 hidden
               bg-black/60 lg:hidden"
    >
    </div>


    {{-- =========================================================
         MAIN CONTENT
    ========================================================== --}}
    <main class="min-h-screen lg:ml-72">

        {{-- Top Header --}}
        <header
            class="sticky top-0 z-20
                   border-b border-slate-800
                   bg-[#0B1226]/95
                   backdrop-blur"
        >

            <div
                class="flex items-center justify-between
                       gap-4
                       px-5 lg:px-7
                       py-4"
            >

                <div class="flex items-center gap-4 min-w-0">

                    <div
                        class="hidden sm:flex
                               h-12 w-12 shrink-0
                               rounded-full
                               bg-white p-1
                               items-center justify-center"
                    >
                        <img
                            src="{{ asset('images/napvcw-logo.png') }}"
                            alt="NAPVCW Logo"
                            class="h-full w-full object-contain"
                        >
                    </div>


                    <div class="min-w-0">

                        <p
                            class="truncate
                                   text-[9px]
                                   uppercase
                                   tracking-[0.18em]
                                   text-slate-500"
                        >
                            National Authority for the Protection of Victims of Crime and Witnesses
                        </p>

                        <h2
                            class="truncate
                                   text-base sm:text-xl
                                   font-semibold
                                   text-white"
                        >
                            Digital Case Flow Management System
                        </h2>

                    </div>

                </div>


                <div class="hidden md:flex items-center gap-3">

                    <span
                        class="inline-flex items-center
                               rounded-full
                               border border-blue-500/20
                               bg-blue-500/10
                               px-3 py-1.5
                               text-xs text-blue-300"
                    >
                        {{ $user->division }}
                    </span>

                    @if($user->is_active)

                        <span
                            class="inline-flex items-center gap-2
                                   rounded-full
                                   border border-green-500/20
                                   bg-green-500/10
                                   px-3 py-1.5
                                   text-xs text-green-300"
                        >
                            <span class="h-2 w-2 rounded-full bg-green-400"></span>
                            Active
                        </span>

                    @endif

                </div>

            </div>

        </header>


        {{-- =========================================================
             PAGE CONTENT
        ========================================================== --}}
        <section class="px-5 lg:px-7 py-6 lg:py-7">

            {{-- Page Header --}}
            <div
                class="mb-6
                       flex flex-col gap-4
                       lg:flex-row
                       lg:items-end
                       lg:justify-between"
            >

                <div>

                    <div class="flex items-center gap-2">

                        <span
                            class="text-[10px]
                                   uppercase
                                   tracking-[0.18em]
                                   text-blue-400"
                        >
                            NAPVCW DCFMS
                        </span>

                        <span class="text-slate-700">/</span>

                        <span class="text-[10px] text-slate-500">
                            {{ $roleLabel }}
                        </span>

                    </div>


                    <h1
                        class="mt-2
                               text-2xl lg:text-3xl
                               font-semibold
                               tracking-tight
                               text-white"
                    >
                        @yield('page-title', 'Dashboard')
                    </h1>


                    @hasSection('page-description')

                        <p
                            class="mt-2
                                   max-w-3xl
                                   text-sm
                                   leading-relaxed
                                   text-slate-400"
                        >
                            @yield('page-description')
                        </p>

                    @endif

                </div>


                {{-- Optional Page Actions --}}
                @hasSection('page-actions')

                    <div class="flex items-center gap-3">
                        @yield('page-actions')
                    </div>

                @endif

            </div>


            {{-- Flash Success --}}
            @if(session('success'))

                <div
                    class="mb-5
                           rounded-xl
                           border border-green-500/20
                           bg-green-500/10
                           p-4
                           text-sm
                           text-green-300"
                >
                    <div class="flex items-start gap-3">

                        <span class="mt-0.5 text-green-400">
                            ✓
                        </span>

                        <span>
                            {{ session('success') }}
                        </span>

                    </div>
                </div>

            @endif


            {{-- Flash Error --}}
            @if(session('error'))

                <div
                    class="mb-5
                           rounded-xl
                           border border-red-500/20
                           bg-red-500/10
                           p-4
                           text-sm
                           text-red-300"
                >
                    {{ session('error') }}
                </div>

            @endif


            {{-- Validation Errors --}}
            @if($errors->any())

                <div
                    class="mb-5
                           rounded-xl
                           border border-red-500/20
                           bg-red-500/10
                           p-4"
                >
                    <p class="text-sm font-medium text-red-300">
                        Please review the following:
                    </p>

                    <ul
                        class="mt-2
                               list-disc
                               pl-5
                               text-sm
                               text-red-300"
                    >
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            @endif


            {{-- Actual Page --}}
            @yield('content')

        </section>


        {{-- =========================================================
             FOOTER
        ========================================================== --}}
        <footer
            class="border-t border-slate-800
                   px-5 lg:px-7
                   py-5"
        >

            <div
                class="flex flex-col gap-2
                       md:flex-row
                       md:items-center
                       md:justify-between"
            >

                <p class="text-[10px] text-slate-600">
                    National Authority for the Protection of Victims of Crime and Witnesses
                </p>

                <p class="text-[10px] text-slate-700">
                    Digital Case Flow Management System · Prototype Environment
                </p>

            </div>

        </footer>

    </main>

</div>


{{-- =========================================================
     MOBILE SIDEBAR SCRIPT
========================================================= --}}
<script>
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }

    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function () {

            if (sidebar.classList.contains('-translate-x-full')) {
                openSidebar();
            } else {
                closeSidebar();
            }

        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
</script>

</body>
</html>