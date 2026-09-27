<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | NAPVCW DCFMS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#070B1D] text-white">

    <div class="min-h-screen flex">

        <!-- =========================================================
             LEFT BRANDING SECTION
        ========================================================== -->
        <div
            class="hidden lg:flex lg:w-1/2 relative overflow-hidden
                   bg-gradient-to-br from-[#0E1A2B] via-[#111A33] to-[#070B1D]
                   items-center justify-center">

            <!-- Decorative Background -->
            <div
                class="absolute w-96 h-96 rounded-full
                       bg-blue-600/20 blur-3xl -top-20 -left-20">
            </div>

            <div
                class="absolute w-96 h-96 rounded-full
                       bg-purple-600/20 blur-3xl -bottom-20 -right-20">
            </div>

            <!-- Branding Content -->
            <div class="relative z-10 max-w-2xl px-16">

                <!-- Logos -->
                <div class="flex items-center gap-5 mb-8">

                    <!-- Government Logo -->
                    <div
                        class="flex items-center justify-center
                               h-20 w-20 rounded-2xl
                               border border-slate-700
                               bg-white p-2 shadow-lg">

                        <img
                            src="{{ asset('images/gov-logo.png') }}"
                            alt="Government of Sri Lanka Logo"
                            class="h-full w-full object-contain"
                        >

                    </div>

                    <!-- Divider -->
                    <div class="h-12 w-px bg-slate-700"></div>

                    <!-- NAPVCW Logo -->
                    <div
                        class="flex items-center justify-center
                               h-20 w-20 rounded-full
                               border border-slate-700
                               bg-white p-2 shadow-lg">

                        <img
                            src="{{ asset('images/napvcw-logo.png') }}"
                            alt="NAPVCW Logo"
                            class="h-full w-full object-contain"
                        >

                    </div>

                </div>


                <!-- Government Badge -->
                <div
                    class="inline-flex items-center px-3 py-1 rounded-full
                           border border-blue-500/30
                           bg-blue-500/10
                           text-blue-300
                           text-xs font-semibold
                           uppercase tracking-wider mb-8">

                    Government of Sri Lanka

                </div>


                <!-- Main Title -->
                <p
                    class="text-xs uppercase tracking-[0.20em]
                           text-slate-400 mb-3">

                    National Authority for the Protection of
                    Victims of Crime and Witnesses

                </p>

                <h1 class="text-4xl xl:text-5xl font-semibold leading-tight">

                    Digital Case Flow

                    <span class="block text-blue-400">
                        Management System
                    </span>

                </h1>


                <!-- Description -->
                <p class="mt-6 text-slate-400 leading-relaxed max-w-xl">

                    A secure digital platform for complaint registration,
                    case routing, inter-divisional workflow tracking,
                    case monitoring and summary reporting.

                </p>


                <!-- System Scope -->
                <div class="mt-10 border-l-2 border-blue-500 pl-5">

                    <p class="text-sm text-slate-300 font-medium">
                        Operational Case Management
                    </p>

                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Board Secretariat · Law & Law Enforcement ·
                        Protection Services · Police Protection ·
                        Assistance Services
                    </p>

                </div>


                <!-- Security Note -->
                <div class="mt-10 flex items-center gap-3 text-xs text-slate-500">

                    <span
                        class="flex h-8 w-8 items-center justify-center
                               rounded-lg bg-blue-500/10 text-blue-400">
                        🔒
                    </span>

                    <span>
                        Restricted to authorized NAPVCW personnel only.
                    </span>

                </div>

            </div>

        </div>


        <!-- =========================================================
             LOGIN SECTION
        ========================================================== -->
        <div
            class="w-full lg:w-1/2 flex items-center justify-center
                   px-6 py-10">

            <div class="w-full max-w-md">

                <!-- Mobile Header -->
                <div class="lg:hidden mb-8">

                    <div class="flex items-center gap-3 mb-5">

                        <img
                            src="{{ asset('images/gov-logo.png') }}"
                            alt="Government Logo"
                            class="h-12 w-12 rounded-lg bg-white p-1.5 object-contain"
                        >

                        <img
                            src="{{ asset('images/napvcw-logo.png') }}"
                            alt="NAPVCW Logo"
                            class="h-12 w-12 rounded-full bg-white p-1.5 object-contain"
                        >

                    </div>

                    <p class="text-blue-400 font-semibold">
                        NAPVCW
                    </p>

                    <h1 class="text-2xl font-semibold mt-2">
                        Digital Case Flow Management System
                    </h1>

                </div>


                <!-- Login Card -->
                <div
                    class="rounded-2xl border border-slate-800
                           bg-[#0B1124]
                           p-8 shadow-2xl">

                    <!-- Logos on Card -->
                    <div class="flex items-center justify-center gap-4 mb-5">

                        <div
                            class="h-12 w-12 rounded-full
                                   bg-white p-1.5
                                   flex items-center justify-center">

                            <img
                                src="{{ asset('images/gov-logo.png') }}"
                                alt="Government Logo"
                                class="h-full w-full object-contain"
                            >

                        </div>

                        <div class="w-8 h-px bg-slate-700"></div>

                        <div
                            class="h-12 w-12 rounded-full
                                   bg-white p-1.5
                                   flex items-center justify-center">

                            <img
                                src="{{ asset('images/napvcw-logo.png') }}"
                                alt="NAPVCW Logo"
                                class="h-full w-full object-contain"
                            >

                        </div>

                    </div>


                    <!-- Authorized Badge -->
                    <div class="flex justify-center mb-5">

                        <span
                            class="inline-flex items-center
                                   rounded-full
                                   border border-blue-500/30
                                   bg-blue-500/10
                                   px-3 py-1
                                   text-[10px]
                                   font-semibold
                                   uppercase
                                   tracking-[0.15em]
                                   text-blue-300">

                            Authorized Personnel Only

                        </span>

                    </div>


                    <!-- Login Heading -->
                    <div class="text-center mb-8">

                        <h2 class="text-3xl font-semibold">
                            Employee Sign In
                        </h2>

                        <p class="text-slate-400 mt-3 text-sm">
                            Enter your employee number and assigned password
                            to continue.
                        </p>

                    </div>


                    <!-- Error Message -->
                    @if ($errors->any())

                        <div
                            class="mb-5 rounded-xl
                                   border border-red-500/30
                                   bg-red-500/10
                                   p-4 text-sm text-red-300">

                            {{ $errors->first() }}

                        </div>

                    @endif


                    <!-- Login Form -->
                    <form
                        method="POST"
                        action="{{ route('login.attempt') }}"
                        class="space-y-6">

                        @csrf


                        <!-- Employee Number -->
                        <div>

                            <label
                                for="employee_number"
                                class="block text-sm font-medium
                                       text-slate-300 mb-2">

                                Employee Number / EPF Number

                            </label>

                            <div class="relative">

                                <span
                                    class="absolute inset-y-0 left-0
                                           flex items-center pl-4
                                           text-blue-400 text-xs font-bold">

                                    ID

                                </span>

                                <input
                                    id="employee_number"
                                    name="employee_number"
                                    type="text"
                                    value="{{ old('employee_number') }}"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="Enter employee number"

                                    class="w-full rounded-xl
                                           border border-slate-700
                                           bg-[#111A2E]
                                           pl-12 pr-4 py-3
                                           text-white
                                           placeholder-slate-600
                                           outline-none
                                           transition
                                           focus:border-blue-500
                                           focus:ring-2
                                           focus:ring-blue-500/20"
                                >

                            </div>

                        </div>


                        <!-- Password -->
                        <div>

                            <label
                                for="password"
                                class="block text-sm font-medium
                                       text-slate-300 mb-2">

                                Password

                            </label>

                            <div class="relative">

                                <span
                                    class="absolute inset-y-0 left-0
                                           flex items-center pl-4
                                           text-blue-400 text-xs font-bold">

                                    PW

                                </span>

                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter password"

                                    class="w-full rounded-xl
                                           border border-slate-700
                                           bg-[#111A2E]
                                           pl-12 pr-16 py-3
                                           text-white
                                           placeholder-slate-600
                                           outline-none
                                           transition
                                           focus:border-blue-500
                                           focus:ring-2
                                           focus:ring-blue-500/20"
                                >

                                <!-- Show / Hide Password -->
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-0
                                           flex items-center pr-4
                                           text-xs text-blue-400
                                           hover:text-blue-300">

                                    Show

                                </button>

                            </div>

                        </div>


                        <!-- Remember -->
                        <div class="flex items-center justify-between">

                            <div class="flex items-center">

                                <input
                                    id="remember"
                                    name="remember"
                                    type="checkbox"

                                    class="rounded
                                           border-slate-600
                                           bg-[#111A2E]
                                           text-blue-600
                                           focus:ring-blue-500"
                                >

                                <label
                                    for="remember"
                                    class="ml-2 text-sm text-slate-400">

                                    Remember employee number

                                </label>

                            </div>

                        </div>


                        <!-- Login Button -->
                        <button
                            type="submit"

                            class="w-full rounded-xl
                                   bg-gradient-to-r
                                   from-blue-600
                                   to-purple-600
                                   px-4 py-3
                                   font-semibold text-white
                                   shadow-lg
                                   shadow-blue-900/20
                                   transition
                                   hover:opacity-90
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-500/40">

                            Sign In Securely →

                        </button>

                    </form>


                    <!-- Divider -->
                    <div class="my-7 border-t border-slate-800"></div>


                    <!-- Prototype Account Note -->
                    <div
                        class="rounded-xl
                               border border-slate-800
                               bg-[#0E172A]
                               p-4">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm font-medium text-slate-300">
                                    Prototype Access
                                </p>

                                <p class="text-xs text-slate-500 mt-1">
                                    Use authorized demonstration credentials.
                                </p>
                            </div>

                            <span
                                class="rounded-full
                                       bg-yellow-500/10
                                       border border-yellow-500/20
                                       px-2 py-1
                                       text-[9px]
                                       font-bold
                                       uppercase
                                       text-yellow-400">

                                Prototype

                            </span>

                        </div>

                    </div>


                    <!-- Footer Security Message -->
                    <p class="mt-6 text-center text-[11px] text-slate-600 leading-relaxed">

                        Access is restricted to authorized officers.
                        All activity may be recorded for accountability
                        and system auditing.

                    </p>

                </div>


                <!-- Bottom Footer -->
                <div class="mt-6 text-center">

                    <p class="text-xs text-slate-600">
                        National Authority for the Protection of Victims
                        of Crime and Witnesses
                    </p>

                    <p class="text-[10px] text-slate-700 mt-1">
                        Digital Case Flow Management System — Prototype
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         PASSWORD VISIBILITY SCRIPT
    ========================================================== -->
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', function () {

            const isPassword =
                passwordInput.getAttribute('type') === 'password';

            passwordInput.setAttribute(
                'type',
                isPassword ? 'text' : 'password'
            );

            togglePassword.textContent =
                isPassword ? 'Hide' : 'Show';

        });
    </script>

</body>
</html>