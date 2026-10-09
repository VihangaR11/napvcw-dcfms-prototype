<!DOCTYPE html>



<html lang="{{ app()->getLocale() }}">



<head>



    <meta charset="UTF-8">







    <meta



        name="viewport"



        content="width=device-width, initial-scale=1.0"



    >







    <meta



        name="description"



        content="NAPVCW {{ __('common.system_name') }}"



    >







    <title>@yield('title', 'DCFMS') | NAPVCW</title>







    {{-- Theme initializer: prevents theme flashing before Vite/JS loads --}}



    <script>



        (function () {



            try {



                const savedTheme = localStorage.getItem('dcfms-theme');







                const theme =



                    savedTheme === 'light' || savedTheme === 'dark'



                        ? savedTheme



                        : (



                            window.matchMedia('(prefers-color-scheme: light)').matches



                                ? 'light'



                                : 'dark'



                        );







                document.documentElement.setAttribute(



                    'data-theme',



                    theme



                );



            } catch (error) {



                document.documentElement.setAttribute(



                    'data-theme',



                    'dark'



                );



            }



        })();



    </script>







    @vite([



        'resources/css/app.css',



        'resources/js/app.js'



    ])



</head>







<body



    class="min-h-screen



           bg-[#070B1D]



           text-white



           antialiased"



>







@php



    $user = auth()->user();







    $role = $user?->role ?? '';



    $roleLabel = __(

        'common.roles.' . $role

    );



    if ($roleLabel === 'common.roles.' . $role) {

        $roleLabel = ucwords(

            str_replace(

                '_',

                ' ',

                $role

            )

        );

    }



    $divisionLabel = match($user?->division) {

        'Board Secretariat' => __('common.divisions.board_secretariat'),

        'Law and Law Enforcement' => __('common.divisions.law_enforcement'),

        'Protection Services' => __('common.divisions.protection_services'),

        'Police Protection' => __('common.divisions.police_protection'),

        'Assistance Services' => __('common.divisions.assistance_services'),

        default => $user?->division,

    };







    /*



    |--------------------------------------------------------------------------



    | Role / Permission Flags



    |--------------------------------------------------------------------------



    |



    | These flags control navigation visibility only.



    | Controller / policy authorization must remain the final security layer.



    |



    */







    $isSystemAdmin =



        $role === 'system_admin';







    $isBoardSecretary =



        $role === 'board_secretary';







    $isDirectorGeneral =



        $role === 'director_general';







    $isChairman =



        $role === 'chairman';







    $isPolicyDirector =



        $role === 'policy_director';







    $isLegal = in_array(



        $role,



        [



            'legal_director',



            'legal_officer',



            'investigation_officer',



            'director_general',



        ],



        true



    );







    $isProtection = in_array(



        $role,



        [



            'protection_director',



            'protection_officer',



            'director_general',



        ],



        true



    );







    $isPoliceProtection = in_array(



        $role,



        [



            'police_protection_director',



            'police_protection_officer',



            'director_general',



        ],



        true



    );







    $isAssistance = in_array(



        $role,



        [



            'legal_director',



            'legal_officer',



            'protection_director',



            'protection_officer',



            'director_general',



        ],



        true



    );







    $canViewCaseRegistry = in_array(



        $role,



        [



            'chairman',



            'director_general',



            'board_secretary',



            'legal_director',



            'legal_officer',



            'investigation_officer',



            'protection_director',



            'protection_officer',



            'police_protection_director',



            'police_protection_officer',



            'policy_director',



        ],



        true



    );







    $canRegisterCases = in_array(



        $role,



        [



            'board_secretary',



            'director_general',



        ],



        true



    );







    $canManageUsers =



        $role === 'system_admin';







    /*



    |--------------------------------------------------------------------------



    | System Administrator - Pending Account Count



    |--------------------------------------------------------------------------



    */







    $pendingAccountCount = 0;







    if ($isSystemAdmin) {



        $pendingAccountCount = \App\Models\User::query()



            ->where('account_status', 'pending')



            ->count();



    }



@endphp





<div class="min-h-screen">







    {{-- =========================================================



         MOBILE TOP BAR



    ========================================================== --}}



    <div



        class="sticky top-0 z-50



               border-b border-slate-800



               bg-[#0B1226]/95



               backdrop-blur



               lg:hidden"



    >



        <div class="flex items-center justify-between px-4 py-3">







            <div class="flex items-center gap-3">



                <img



                    src="{{ asset('images/napvcw-logo.png') }}"



                    alt="{{ __('layout.napvcw_logo') }}"



                    class="h-10 w-10 rounded-full bg-white p-1 object-contain"



                >







                <div>



                    <p class="text-xs font-semibold text-white">



                        NAPVCW DCFMS



                    </p>







                    <p class="text-[9px] uppercase tracking-wider text-slate-500">



                        {{ __('common.government_service') }}



                    </p>



                </div>



            </div>







            <div class="flex items-center gap-2">







                {{-- Language Switcher --}}

                <div

                    class="flex items-center rounded-lg

                           border border-slate-700

                           bg-[#111A2E] p-1"

                    aria-label="{{ __('common.language') }}"

                >

                    <a

                        href="{{ route('language.switch', 'si') }}"

                        class="rounded-md px-2 py-1.5

                               text-[10px] font-semibold transition

                               {{ app()->getLocale() === 'si'

                                    ? 'bg-white text-slate-900'

                                    : 'text-slate-400 hover:text-white' }}"

                    >

                        සිං

                    </a>



                    <a

                        href="{{ route('language.switch', 'en') }}"

                        class="rounded-md px-2 py-1.5

                               text-[10px] font-semibold transition

                               {{ app()->getLocale() === 'en'

                                    ? 'bg-white text-slate-900'

                                    : 'text-slate-400 hover:text-white' }}"

                    >

                        EN

                    </a>

                </div>



                <button



                    type="button"



                    data-theme-toggle



                    aria-label="{{ __('common.switch_theme') }}"



                    class="flex h-9 w-9 items-center justify-center



                           rounded-lg border border-slate-700



                           bg-[#111A2E] text-slate-300



                           transition hover:border-blue-500/30"



                >



                    <svg



                        data-theme-sun



                        xmlns="http://www.w3.org/2000/svg"



                        viewBox="0 0 24 24"



                        fill="none"



                        stroke="currentColor"



                        stroke-width="1.8"



                        class="h-4 w-4 text-yellow-400"



                    >



                        <circle cx="12" cy="12" r="4"></circle>



                        <path d="M12 2v2 M12 20v2 M4.93 4.93l1.41 1.41 M17.66 17.66l1.41 1.41 M2 12h2 M20 12h2 M4.93 19.07l1.41-1.41 M17.66 6.34l1.41-1.41"></path>



                    </svg>







                    <svg



                        data-theme-moon



                        xmlns="http://www.w3.org/2000/svg"



                        viewBox="0 0 24 24"



                        fill="none"



                        stroke="currentColor"



                        stroke-width="1.8"



                        class="hidden h-4 w-4 text-blue-500"



                    >



                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>



                    </svg>



                </button>







                <button



                    id="mobileMenuButton"



                    type="button"



                    class="rounded-lg border border-slate-700



                           bg-[#111A2E] px-3 py-2 text-sm



                           text-slate-300 transition



                           hover:border-blue-500/30"



                >



                    {{ __('layout.menu') }}




                    </button>







            </div>



        </div>



    </div>





    {{-- =========================================================



         SIDEBAR



    ========================================================== --}}



    <aside



        id="sidebar"



        class="fixed inset-y-0 left-0 z-40



               flex w-72 -translate-x-full flex-col



               border-r border-slate-800



               bg-[#0E1A2B]



               transition-transform duration-300



               lg:translate-x-0"



    >







        {{-- Brand --}}



        <div class="border-b border-slate-800 p-5">







            <div class="flex items-center gap-3">



                <div



                    class="flex h-12 w-12 items-center justify-center



                           rounded-xl bg-white p-1.5 shadow-lg"



                >



                    <img



                        src="{{ asset('images/gov-logo.png') }}"



                        alt="{{ __('layout.government_of_sri_lanka') }}"



                        class="h-full w-full object-contain"



                    >



                </div>







                <div class="min-w-0">



                    <h1 class="text-xl font-bold tracking-wide text-white">



                        DCFMS



                    </h1>







                    <p class="truncate text-[9px] uppercase tracking-[0.18em] text-blue-300">



                        {{ __('common.government_service') }}



                    </p>



                </div>



            </div>







            <div class="mt-4 rounded-xl border border-slate-800 bg-[#111A2E] px-3 py-3">



                <p class="text-[9px] uppercase tracking-widest text-slate-500">



                    {{ __('common.institution') }}



                </p>







                <p class="mt-1 text-[11px] leading-relaxed text-slate-300">



                    National Authority for the Protection



                    of Victims of Crime and Witnesses



                </p>



            </div>







        </div>





        {{-- Navigation --}}



        <nav class="flex-1 overflow-y-auto px-4 py-5">







            <p class="mb-2 px-3 text-[10px] uppercase tracking-[0.18em] text-slate-500">



                {{ __('common.main') }}



            </p>







            <div class="space-y-1">



                <a



                    href="{{ route('dashboard') }}"



                    class="flex items-center gap-3 rounded-xl px-4 py-3 transition



                           {{ request()->routeIs('dashboard')



                                ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg shadow-blue-950/20'



                                : 'text-slate-300 hover:bg-[#152238] hover:text-white'



                           }}"



                >



                    <span class="w-5 text-center text-sm">▦</span>



                    <span class="text-sm font-medium">{{ __('common.dashboard') }}</span>



                </a>



            </div>





            {{-- {{ __('common.case_management') }} --}}



            @if($canViewCaseRegistry || $canRegisterCases)



                <p class="mb-2 mt-7 px-3 text-[10px] uppercase tracking-[0.18em] text-slate-500">



                    {{ __('common.case_management') }}



                </p>







                <div class="space-y-1">







                    @if($canRegisterCases)



                        <a



                            href="{{ route('cases.create') }}"



                            class="flex items-center gap-3 rounded-xl px-4 py-3 transition



                                   {{ request()->routeIs('cases.create')



                                        ? 'border border-blue-500/20 bg-blue-500/10 text-blue-300'



                                        : 'text-slate-300 hover:bg-[#152238] hover:text-white'



                                   }}"



                        >



                            <span class="w-5 text-center text-lg">＋</span>



                            <span class="text-sm">{{ __('common.register_new_case') }}</span>



                        </a>



                    @endif







                    @if($canViewCaseRegistry)



                        <a



                            href="{{ route('cases.index') }}"



                            class="flex items-center gap-3 rounded-xl px-4 py-3 transition



                                   {{ request()->routeIs('cases.index')



                                        ? 'border border-blue-500/20 bg-blue-500/10 text-blue-300'



                                        : 'text-slate-300 hover:bg-[#152238] hover:text-white'



                                   }}"



                        >



                            <span class="w-5 text-center text-sm">▤</span>







                            <span class="text-sm">



                                @if(in_array($role, [



                                    'legal_officer',



                                    'investigation_officer',



                                    'protection_officer',



                                    'police_protection_officer',



                                ], true))



                                    {{ __('common.my_assigned_cases') }}



                                @else



                                    {{ __('common.case_registry') }}



                                @endif



                            </span>



                        </a>



                    @endif







                </div>



            @endif





            {{-- {{ __('common.division_workspaces') }} --}}



            @if($isLegal || $isProtection || $isPoliceProtection || $isAssistance)



                <p class="mb-2 mt-7 px-3 text-[10px] uppercase tracking-[0.18em] text-slate-500">



                    {{ __('common.division_workspaces') }}



                </p>







                <div class="space-y-1">







                    @if($isLegal)



                        <a



                            href="{{ route('cases.index', ['division' => 'Law and Law Enforcement']) }}"



                            class="flex items-center gap-3 rounded-xl px-4 py-3



                                   text-slate-300 transition



                                   hover:bg-[#152238] hover:text-white"



                        >



                            <span class="w-5 text-center">⚖</span>



                            <span class="text-sm">{{ __('common.divisions.law_enforcement') }}</span>



                        </a>



                    @endif







                    @if($isProtection)



                        <a



                            href="{{ route('cases.index', ['division' => 'Protection Services']) }}"



                            class="flex items-center gap-3 rounded-xl px-4 py-3



                                   text-slate-300 transition



                                   hover:bg-[#152238] hover:text-white"



                        >



                            <span class="w-5 text-center">◈</span>



                            <span class="text-sm">{{ __('common.divisions.protection_services') }}</span>



                        </a>



                    @endif







                    @if($isPoliceProtection)



                        <a



                            href="{{ route('cases.index', ['division' => 'Police Protection']) }}"



                            class="flex items-center gap-3 rounded-xl px-4 py-3



                                   text-slate-300 transition



                                   hover:bg-[#152238] hover:text-white"



                        >



                            <span class="w-5 text-center">⌖</span>



                            <span class="text-sm">{{ __('common.divisions.police_protection') }}</span>



                        </a>



                    @endif







                    @if($isAssistance)



                        <a



                            href="{{ route('cases.index', ['division' => 'Assistance Services']) }}"



                            class="flex items-center gap-3 rounded-xl px-4 py-3



                                   text-slate-300 transition



                                   hover:bg-[#152238] hover:text-white"



                        >



                            <span class="w-5 text-center">✦</span>



                            <span class="text-sm">{{ __('common.divisions.assistance_services') }}</span>



                        </a>



                    @endif







                </div>



            @endif





            {{-- {{ __('common.reporting') }} --}}



            @if($canViewCaseRegistry)



                <p class="mb-2 mt-7 px-3 text-[10px] uppercase tracking-[0.18em] text-slate-500">



                    {{ __('common.reporting') }}



                </p>







                <div class="space-y-1">



                    <a



                        href="{{ route('cases.index') }}"



                        class="flex items-center gap-3 rounded-xl px-4 py-3



                               text-slate-300 transition



                               hover:bg-[#152238] hover:text-white"



                    >



                        <span class="w-5 text-center">▧</span>



                        <span class="text-sm">{{ __('common.case_summary_reports') }}</span>



                    </a>



                </div>



            @endif





            {{-- System {{ __('common.administration') }} --}}



            @if($canManageUsers)







                <p class="mb-2 mt-7 px-3 text-[10px] uppercase tracking-[0.18em] text-slate-500">



                    {{ __('common.administration') }}



                </p>







                <div class="space-y-1">







                    <a



                        href="{{ route('admin.users.index') }}"



                        class="flex items-center gap-3 rounded-xl px-4 py-3 transition



                               {{ request()->routeIs('admin.users.*')



                                    ? 'border border-blue-500/20 bg-blue-500/10 text-blue-300'



                                    : 'text-slate-300 hover:bg-[#152238] hover:text-white'



                               }}"



                    >



                        <span class="w-5 text-center">👤</span>



                        <span class="text-sm">{{ __('common.user_accounts') }}</span>







                        @if($pendingAccountCount > 0)



                            <span



                                class="ml-auto flex h-5 min-w-5 items-center justify-center



                                       rounded-full bg-red-500 px-1.5



                                       text-[9px] font-semibold text-white shadow-sm"



                            >



                                {{ $pendingAccountCount }}



                            </span>



                        @endif



                    </a>







                    @if($pendingAccountCount > 0)



                        <a



                            href="{{ route('admin.users.index', ['status' => 'pending']) }}"



                            class="flex items-center gap-3 rounded-xl



                                   border border-yellow-500/10



                                   bg-yellow-500/[0.04]



                                   px-4 py-3 text-yellow-300



                                   transition hover:bg-yellow-500/10"



                        >



                            <span class="w-5 text-center">◷</span>



                            <span class="text-sm">{{ __('common.pending_approvals') }}</span>







                            <span



                                class="ml-auto rounded-full border border-yellow-500/20



                                       bg-yellow-500/10 px-2 py-0.5



                                       text-[9px] font-semibold text-yellow-300"



                            >



                                {{ $pendingAccountCount }}



                            </span>



                        </a>



                    @endif







                </div>







            @endif









            @if(in_array($role, ['system_admin', 'director_general'], true))

                <div class="mt-7">

                    <p class="mb-2 px-3 text-[10px] uppercase tracking-[0.18em] text-slate-500">

                        {{ __('layout.audit') }}

                    </p>



                    <a

                        href="{{ route('admin.audit-logs.index') }}"

                        class="flex items-center gap-3 rounded-xl

                               px-4 py-3 text-slate-300

                               transition hover:bg-[#152238] hover:text-white"

                    >

                        <span class="w-5 text-center">≡</span>

                        <span class="text-sm">{{ __('common.audit_logs') }}</span>

                    </a>

                </div>

            @endif



        </nav>





        {{-- User Panel --}}



        <div class="border-t border-slate-800 p-4">







            <div class="rounded-2xl border border-slate-800 bg-[#111A2E] p-4">







                <div class="flex items-center gap-3">







                    <div class="h-11 w-11 shrink-0 rounded-full bg-white p-1">



                        <img



                            src="{{ asset('images/napvcw-logo.png') }}"



                            alt="{{ __('layout.napvcw_logo') }}"



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



                            {{ __('layout.epf') }}: {{ $user->employee_number }}



                        </p>



                    </div>







                </div>





                @if(!empty($user->division))



                    <div class="mt-3 rounded-lg border border-slate-800 bg-[#0C1528] px-3 py-2">



                        <p class="text-[9px] uppercase tracking-wider text-slate-600">



                            {{ __('layout.division') }}



                        </p>







                        <p class="mt-1 text-[11px] text-slate-400">



                            {{ $divisionLabel }}



                        </p>



                    </div>



                @endif





                {{-- Language Switcher --}}

                <div class="mt-3">

                    <p class="mb-2 text-[9px] uppercase tracking-wider text-slate-600">

                        {{ __('common.language') }}

                    </p>



                    <div

                        class="grid grid-cols-2 gap-1

                               rounded-lg border border-slate-700

                               bg-[#0D172A] p-1"

                    >

                        <a

                            href="{{ route('language.switch', 'si') }}"

                            class="rounded-md px-3 py-2

                                   text-center text-xs font-semibold transition

                                   {{ app()->getLocale() === 'si'

                                        ? 'bg-white text-slate-900'

                                        : 'text-slate-400 hover:bg-[#152238] hover:text-white' }}"

                        >

                            සිංහල

                        </a>



                        <a

                            href="{{ route('language.switch', 'en') }}"

                            class="rounded-md px-3 py-2

                                   text-center text-xs font-semibold transition

                                   {{ app()->getLocale() === 'en'

                                        ? 'bg-white text-slate-900'

                                        : 'text-slate-400 hover:bg-[#152238] hover:text-white' }}"

                        >

                            {{ __('layout.english') }}


                            </a>

                    </div>

                </div>



                <button



                    type="button"



                    data-theme-toggle



                    class="mt-3 flex w-full items-center justify-center gap-2



                           rounded-lg border border-slate-700



                           bg-[#0D172A] px-3 py-2



                           text-xs text-slate-300



                           transition hover:border-blue-500/30"



                >



                    <svg



                        data-theme-sun



                        xmlns="http://www.w3.org/2000/svg"



                        viewBox="0 0 24 24"



                        fill="none"



                        stroke="currentColor"



                        stroke-width="1.8"



                        class="h-4 w-4 text-yellow-400"



                    >



                        <circle cx="12" cy="12" r="4"></circle>



                        <path d="M12 2v2 M12 20v2 M4.93 4.93l1.41 1.41 M17.66 17.66l1.41 1.41 M2 12h2 M20 12h2 M4.93 19.07l1.41-1.41 M17.66 6.34l1.41-1.41"></path>



                    </svg>







                    <svg



                        data-theme-moon



                        xmlns="http://www.w3.org/2000/svg"



                        viewBox="0 0 24 24"



                        fill="none"



                        stroke="currentColor"



                        stroke-width="1.8"



                        class="hidden h-4 w-4 text-blue-500"



                    >



                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>



                    </svg>







                    <span data-theme-label>



                        {{ __('common.light_mode') }}



                    </span>



                </button>





                <form



                    method="POST"



                    action="{{ route('logout') }}"



                    class="mt-3"



                >



                    @csrf







                    <button



                        type="submit"



                        class="w-full rounded-lg



                               border border-red-500/20



                               bg-red-500/10 px-3 py-2



                               text-xs font-medium text-red-300



                               transition hover:bg-red-500/20"



                    >



                        {{ __('layout.sign_out') }}




                        </button>



                </form>







            </div>



        </div>







    </aside>





    {{-- Mobile Overlay --}}



    <div



        id="sidebarOverlay"



        class="fixed inset-0 z-30 hidden bg-black/60 lg:hidden"



    ></div>





    {{-- =========================================================



         MAIN APPLICATION



    ========================================================== --}}



    <main class="min-h-screen lg:ml-72">







        <header



            class="sticky top-0 z-20



                   border-b border-slate-800



                   bg-[#0B1226]/95



                   backdrop-blur"



        >



            <div class="flex items-center justify-between gap-4 px-5 py-4 lg:px-7">







                <div class="flex min-w-0 items-center gap-4">







                    <div



                        class="hidden h-12 w-12 shrink-0 items-center justify-center



                               rounded-full bg-white p-1 sm:flex"



                    >



                        <img



                            src="{{ asset('images/napvcw-logo.png') }}"



                            alt="{{ __('layout.napvcw_logo') }}"



                            class="h-full w-full object-contain"



                        >



                    </div>







                    <div class="min-w-0">



                        <p class="truncate text-[9px] uppercase tracking-[0.18em] text-slate-500">



                            {{ __('layout.authority_name') }}



                        </p>







                        <h2 class="truncate text-base font-semibold text-white sm:text-xl">



                            {{ __('common.system_name') }}



                        </h2>



                    </div>







                </div>





                <div class="hidden items-center gap-3 md:flex">







                    {{-- Language Switcher --}}

                    <div

                        class="flex items-center gap-1

                               rounded-xl border border-slate-700

                               bg-[#111A2E] p-1"

                        aria-label="{{ __('common.language') }}"

                    >

                        <a

                            href="{{ route('language.switch', 'si') }}"

                            class="rounded-lg px-3 py-2

                                   text-xs font-semibold transition

                                   {{ app()->getLocale() === 'si'

                                        ? 'bg-white text-slate-900'

                                        : 'text-slate-400 hover:text-white' }}"

                        >

                            සිංහල

                        </a>



                        <a

                            href="{{ route('language.switch', 'en') }}"

                            class="rounded-lg px-3 py-2

                                   text-xs font-semibold transition

                                   {{ app()->getLocale() === 'en'

                                        ? 'bg-white text-slate-900'

                                        : 'text-slate-400 hover:text-white' }}"

                        >

                            {{ __('layout.english') }}


                            </a>

                    </div>



                    <button



                        type="button"



                        data-theme-toggle



                        class="inline-flex items-center gap-2



                               rounded-xl border border-slate-700



                               bg-[#111A2E] px-3 py-2



                               text-xs text-slate-300



                               transition hover:border-blue-500/30"



                    >



                        <svg



                            data-theme-sun



                            xmlns="http://www.w3.org/2000/svg"



                            viewBox="0 0 24 24"



                            fill="none"



                            stroke="currentColor"



                            stroke-width="1.8"



                            class="h-4 w-4 text-yellow-400"



                        >



                            <circle cx="12" cy="12" r="4"></circle>



                            <path d="M12 2v2 M12 20v2 M4.93 4.93l1.41 1.41 M17.66 17.66l1.41 1.41 M2 12h2 M20 12h2 M4.93 19.07l1.41-1.41 M17.66 6.34l1.41-1.41"></path>



                        </svg>







                        <svg



                            data-theme-moon



                            xmlns="http://www.w3.org/2000/svg"



                            viewBox="0 0 24 24"



                            fill="none"



                            stroke="currentColor"



                            stroke-width="1.8"



                            class="hidden h-4 w-4 text-blue-500"



                        >



                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>



                        </svg>







                        <span



                            data-theme-label



                            class="hidden xl:inline"



                        >



                            {{ __('common.light_mode') }}



                        </span>



                    </button>





                    @if(!empty($user->division))



                        <span



                            class="inline-flex items-center rounded-full



                                   border border-blue-500/20



                                   bg-blue-500/10 px-3 py-1.5



                                   text-xs text-blue-300"



                        >



                            {{ $divisionLabel }}



                        </span>



                    @endif





                    @if($user->is_active)



                        <span



                            class="inline-flex items-center gap-2 rounded-full



                                   border border-green-500/20



                                   bg-green-500/10 px-3 py-1.5



                                   text-xs text-green-300"



                        >



                            <span class="h-2 w-2 rounded-full bg-green-400"></span>



                            {{ __('layout.active') }}




                            </span>



                    @endif







                </div>







            </div>



        </header>





        <section class="px-5 py-6 lg:px-7 lg:py-7">







            {{-- Page Header --}}



            <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">







                <div>



                    <div class="flex items-center gap-2">



                        <span class="text-[10px] uppercase tracking-[0.18em] text-blue-400">



                            NAPVCW DCFMS



                        </span>







                        <span class="text-slate-700">/</span>







                        <span class="text-[10px] text-slate-500">



                            {{ $roleLabel }}



                        </span>



                    </div>







                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-white lg:text-3xl">



                        @yield('page-title', __('common.dashboard'))



                    </h1>







                    @hasSection('page-description')



                        <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-400">



                            @yield('page-description')



                        </p>



                    @endif



                </div>





                @hasSection('page-actions')



                    <div class="flex flex-wrap items-center gap-3">



                        @yield('page-actions')



                    </div>



                @endif







            </div>





            {{-- Flash Success --}}



            @if(session('success'))



                <div



                    class="mb-5 rounded-xl



                           border border-green-500/20



                           bg-green-500/10 p-4



                           text-sm text-green-300"



                >



                    <div class="flex items-start gap-3">



                        <span class="mt-0.5 text-green-400">✓</span>



                        <span>{{ session('success') }}</span>



                    </div>



                </div>



            @endif





            {{-- Flash Error --}}



            @if(session('error'))



                <div



                    class="mb-5 rounded-xl



                           border border-red-500/20



                           bg-red-500/10 p-4



                           text-sm text-red-300"



                >



                    {{ session('error') }}



                </div>



            @endif





            {{-- Validation Errors --}}



            @if($errors->any())



                <div



                    class="mb-5 rounded-xl



                           border border-red-500/20



                           bg-red-500/10 p-4"



                >



                    <p class="text-sm font-medium text-red-300">



                        {{ __('common.review_following') }}



                    </p>







                    <ul class="mt-2 list-disc pl-5 text-sm text-red-300">



                        @foreach($errors->all() as $error)



                            <li>{{ $error }}</li>



                        @endforeach



                    </ul>



                </div>



            @endif





            {{-- Actual Page Content --}}



            @yield('content')







        </section>





        <footer class="border-t border-slate-800 px-5 py-5 lg:px-7">







            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">







                <div>



                    <p class="text-[10px] text-slate-600">



                        National Authority for the Protection



                        of Victims of Crime and Witnesses



                    </p>







                    <p class="mt-1 text-[9px] text-slate-700">



                        {{ __('common.system_name') }}



                    </p>



                </div>







                <div class="text-left md:text-right">



                    <p class="text-[8px] uppercase tracking-[0.15em] text-slate-700">



                        {{ __('common.developed_by') }}



                    </p>







                    <p class="mt-1 text-[10px] font-medium text-blue-400">



                        Vihanga Rathnayake



                    </p>







                    <p class="mt-0.5 text-[9px] text-slate-700">



                        {{ __('layout.developer_affiliation') }}



                    </p>



                </div>







            </div>







        </footer>







    </main>







</div>





{{-- =========================================================



     MOBILE SIDEBAR SCRIPT



\\========================================================= --}}



<script>



    const mobileMenuButton =



        document.getElementById('mobileMenuButton');







    const sidebar =



        document.getElementById('sidebar');







    const sidebarOverlay =



        document.getElementById('sidebarOverlay');





    function openSidebar() {



        if (!sidebar) {



            return;



        }







        sidebar.classList.remove('-translate-x-full');



        sidebarOverlay?.classList.remove('hidden');



        document.body.classList.add('overflow-hidden');



    }





    function closeSidebar() {



        if (!sidebar) {



            return;



        }







        sidebar.classList.add('-translate-x-full');



        sidebarOverlay?.classList.add('hidden');



        document.body.classList.remove('overflow-hidden');



    }





    if (mobileMenuButton) {



        mobileMenuButton.addEventListener(



            'click',



            function () {



                if (



                    sidebar?.classList.contains('-translate-x-full')



                ) {



                    openSidebar();



                } else {



                    closeSidebar();



                }



            }



        );



    }





    if (sidebarOverlay) {



        sidebarOverlay.addEventListener(



            'click',



            closeSidebar



        );



    }





    sidebar



        ?.querySelectorAll('a')



        .forEach(function (link) {



            link.addEventListener(



                'click',



                function () {



                    if (window.innerWidth < 1024) {



                        closeSidebar();



                    }



                }



            );



        });





    window.addEventListener(



        'resize',



        function () {



            if (window.innerWidth >= 1024) {



                sidebarOverlay



                    ?.classList



                    .add('hidden');







                document.body



                    .classList



                    .remove('overflow-hidden');



            }



        }



    );

</script>







</body>



</html>
