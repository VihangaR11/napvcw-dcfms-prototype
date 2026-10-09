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
        Create Account | NAPVCW DCFMS
    </title>


    {{-- Prevent theme flash --}}
    <script>
    (function () {
        try {
            const savedTheme =
                localStorage.getItem(
                    'dcfms-theme'
                );

            const theme =
                savedTheme === 'light' ||
                savedTheme === 'dark'
                    ? savedTheme
                    : (
                        window.matchMedia(
                            '(prefers-color-scheme: light)'
                        ).matches
                            ? 'light'
                            : 'dark'
                    );

            document.documentElement
                .setAttribute(
                    'data-theme',
                    theme
                );
        } catch (error) {
            document.documentElement
                .setAttribute(
                    'data-theme',
                    'dark'
                );
        }
    })();
        (function () {

            const savedTheme =
                localStorage.getItem(
                    'dcfms-theme'
                );

            const theme =
                savedTheme === 'light' ||
                savedTheme === 'dark'
                    ? savedTheme
                    : 'dark';

            document.documentElement
                .setAttribute(
                    'data-theme',
                    theme
                );

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


{{-- =========================================================
     PAGE
========================================================= --}}
<div class="min-h-screen lg:grid lg:grid-cols-2">


    {{-- =====================================================
         LEFT BRANDING
    ====================================================== --}}
    <section
        class="relative
               hidden lg:flex
               overflow-hidden
               border-r border-slate-800
               bg-gradient-to-br
               from-[#0E1A2B]
               via-[#111A33]
               to-[#070B1D]
               p-12
               xl:p-16"
    >

        {{-- Decoration --}}
        <div
            class="absolute
                   -top-32 -left-32
                   h-96 w-96
                   rounded-full
                   bg-blue-600/20
                   blur-3xl"
        ></div>

        <div
            class="absolute
                   -bottom-32 -right-32
                   h-96 w-96
                   rounded-full
                   bg-purple-600/20
                   blur-3xl"
        ></div>


        <div
            class="relative z-10
                   flex w-full
                   flex-col"
        >

            {{-- Institution --}}
            <div class="flex items-center gap-4">

                <div
                    class="flex h-16 w-16
                           items-center justify-center
                           rounded-2xl
                           bg-white
                           p-2
                           shadow-xl"
                >

                    <img
                        src="{{ asset('images/napvcw-logo.png') }}"
                        alt="NAPVCW Logo"
                        class="h-full w-full object-contain"
                    >

                </div>


                <div>

                    <p
                        class="text-[10px]
                               uppercase
                               tracking-[0.18em]
                               text-blue-300"
                    >
                        Government of Sri Lanka
                    </p>

                    <p
                        class="mt-1
                               max-w-sm
                               text-sm
                               font-medium
                               leading-relaxed
                               text-slate-300"
                    >
                        National Authority for the Protection
                        of Victims of Crime and Witnesses
                    </p>

                </div>

            </div>


            {{-- Main Branding --}}
            <div class="my-auto max-w-xl">

                <div
                    class="inline-flex
                           rounded-full
                           border border-blue-500/20
                           bg-blue-500/10
                           px-3 py-1
                           text-[10px]
                           font-semibold
                           uppercase
                           tracking-[0.16em]
                           text-blue-300"
                >
                    Secure Employee Access
                </div>


                <h1
                    class="mt-7
                           text-4xl xl:text-5xl
                           font-semibold
                           leading-tight"
                >
                    Digital Case Flow

                    <span
                        class="block
                               bg-gradient-to-r
                               from-blue-400
                               to-purple-400
                               bg-clip-text
                               text-transparent"
                    >
                        Management System
                    </span>

                </h1>


                <p
                    class="mt-6
                           max-w-lg
                           text-base
                           leading-7
                           text-slate-400"
                >
                    A centralized digital environment for secure
                    case registration, workflow coordination,
                    case monitoring and institutional reporting.
                </p>


                <div
                    class="mt-9
                           rounded-2xl
                           border border-slate-700
                           bg-[#0B1226]/60
                           p-5"
                >

                    <p
                        class="text-sm
                               font-medium
                               text-slate-200"
                    >
                        Account Registration
                    </p>

                    <p
                        class="mt-2
                               text-sm
                               leading-relaxed
                               text-slate-500"
                    >
                        Register using your official EPF number
                        and designation. Newly registered accounts
                        require authorization before system access.
                    </p>

                </div>

            </div>


            {{-- Footer --}}
            <p
                class="text-[10px]
                       text-slate-600"
            >
                National Authority for the Protection
                of Victims of Crime and Witnesses
            </p>

        </div>

    </section>


    {{-- =====================================================
         REGISTRATION AREA
    ====================================================== --}}
    <main
        class="flex min-h-screen
               items-center justify-center
               bg-[#070B1D]
               px-5
               py-10
               sm:px-8"
    >

        <div class="w-full max-w-xl">


            {{-- Mobile Brand --}}
            <div
                class="mb-8
                       flex items-center gap-3
                       lg:hidden"
            >

                <div
                    class="h-12 w-12
                           rounded-xl
                           bg-white
                           p-1.5"
                >

                    <img
                        src="{{ asset('images/napvcw-logo.png') }}"
                        alt="NAPVCW"
                        class="h-full w-full object-contain"
                    >

                </div>

                <div>

                    <p class="font-semibold">
                        NAPVCW DCFMS
                    </p>

                    <p
                        class="text-[10px]
                               uppercase
                               tracking-wider
                               text-slate-500"
                    >
                        Government Service
                    </p>

                </div>

            </div>


            {{-- Registration Card --}}
            <div
                class="rounded-3xl
                       border border-slate-800
                       bg-[#111A2E]
                       p-6
                       shadow-2xl
                       sm:p-8"
            >

                {{-- Header --}}
                <div>

                    <p
                        class="text-[10px]
                               font-semibold
                               uppercase
                               tracking-[0.18em]
                               text-blue-400"
                    >
                        Employee Registration
                    </p>

                    <h2
                        class="mt-2
                               text-3xl
                               font-semibold"
                    >
                        Create your account
                    </h2>

                    <p
                        class="mt-2
                               text-sm
                               leading-relaxed
                               text-slate-500"
                    >
                        Enter your official employment details
                        to request access to DCFMS.
                    </p>

                </div>


                {{-- Validation Errors --}}
                @if($errors->any())

                    <div
                        class="mt-6
                               rounded-xl
                               border border-red-500/20
                               bg-red-500/10
                               p-4"
                    >

                        <p
                            class="text-sm
                                   font-medium
                                   text-red-300"
                        >
                            Please review the information below.
                        </p>

                        <ul
                            class="mt-2
                                   list-disc
                                   space-y-1
                                   pl-5
                                   text-xs
                                   text-red-300"
                        >

                            @foreach($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                {{-- Registration Form --}}
                <form
                    method="POST"
                    action="{{ route('register.store') }}"
                    class="mt-7 space-y-5"
                >

                    @csrf


                    {{-- Name --}}
                    <div>

                        <label
                            for="name"
                            class="mb-2 block
                                   text-sm
                                   font-medium
                                   text-slate-300"
                        >
                            Full Name
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Enter your full name"

                            class="w-full
                                   rounded-xl
                                   border border-slate-700
                                   bg-[#0D172A]
                                   px-4 py-3.5
                                   text-sm
                                   text-white
                                   placeholder-slate-600
                                   outline-none
                                   transition
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500/20"
                        >

                    </div>


                    {{-- Designation --}}
                    <div>

                        <label
                            for="designation"
                            class="mb-2 block
                                   text-sm
                                   font-medium
                                   text-slate-300"
                        >
                            Designation
                        </label>

                        <select
                            id="designation"
                            name="designation"
                            required

                            class="w-full
                                   rounded-xl
                                   border border-slate-700
                                   bg-[#0D172A]
                                   px-4 py-3.5
                                   text-sm
                                   text-white
                                   outline-none
                                   transition
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500/20"
                        >

                            <option value="">
                                Select your designation
                            </option>
                            <option
    value="Director - Police Protection"
    {{ old('designation') === 'Director - Police Protection' ? 'selected' : '' }}
>
    Director - Police Protection
</option>
<option
    value="Assistant Director - Protection Services"
    {{ old('designation') === 'Assistant Director - Protection Services' ? 'selected' : '' }}
>
    Assistant Director - Protection Services
</option>

                            @foreach($designations as $designation)

                                <option
                                    value="{{ $designation }}"
                                    @selected(
                                        old('designation')
                                        === $designation
                                    )
                                >
                                    {{ $designation }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- EPF --}}
                    <div>

                        <label
                            for="employee_number"
                            class="mb-2 block
                                   text-sm
                                   font-medium
                                   text-slate-300"
                        >
                            EPF Number
                        </label>

                        <input
                            id="employee_number"
                            name="employee_number"
                            type="text"
                            value="{{ old('employee_number') }}"
                            required
                            autocomplete="username"
                            placeholder="Enter your EPF number"

                            class="w-full
                                   rounded-xl
                                   border border-slate-700
                                   bg-[#0D172A]
                                   px-4 py-3.5
                                   text-sm
                                   uppercase
                                   text-white
                                   placeholder-slate-600
                                   outline-none
                                   transition
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500/20"
                        >

                    </div>


                    {{-- Password --}}
                    <div>

                        <label
                            for="password"
                            class="mb-2 block
                                   text-sm
                                   font-medium
                                   text-slate-300"
                        >
                            Password
                        </label>


                        <div class="relative">

                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                minlength="5"
                                autocomplete="new-password"
                                placeholder="Minimum 5 characters"

                                class="w-full
                                       rounded-xl
                                       border border-slate-700
                                       bg-[#0D172A]
                                       px-4 py-3.5
                                       pr-16
                                       text-sm
                                       text-white
                                       placeholder-slate-600
                                       outline-none
                                       transition
                                       focus:border-blue-500
                                       focus:ring-2
                                       focus:ring-blue-500/20"
                            >


                            <button
                                type="button"
                                data-password-toggle="password"

                                class="absolute
                                       right-4 top-1/2
                                       -translate-y-1/2
                                       text-xs
                                       font-medium
                                       text-blue-400
                                       hover:text-blue-300"
                            >
                                Show
                            </button>

                        </div>


                        <div
                            class="mt-2
                                   flex items-center gap-2
                                   text-[11px]
                                   text-slate-500"
                        >
                            <span
                                class="h-1.5 w-1.5
                                       rounded-full
                                       bg-blue-400"
                            ></span>

                            Minimum 5 characters
                        </div>

                    </div>


                    {{-- Confirm Password --}}
                    <div>

                        <label
                            for="password_confirmation"
                            class="mb-2 block
                                   text-sm
                                   font-medium
                                   text-slate-300"
                        >
                            Confirm Password
                        </label>


                        <div class="relative">

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                minlength="5"
                                autocomplete="new-password"
                                placeholder="Re-enter your password"

                                class="w-full
                                       rounded-xl
                                       border border-slate-700
                                       bg-[#0D172A]
                                       px-4 py-3.5
                                       pr-16
                                       text-sm
                                       text-white
                                       placeholder-slate-600
                                       outline-none
                                       transition
                                       focus:border-blue-500
                                       focus:ring-2
                                       focus:ring-blue-500/20"
                            >


                            <button
                                type="button"
                                data-password-toggle="password_confirmation"

                                class="absolute
                                       right-4 top-1/2
                                       -translate-y-1/2
                                       text-xs
                                       font-medium
                                       text-blue-400
                                       hover:text-blue-300"
                            >
                                Show
                            </button>

                        </div>

                    </div>


                    {{-- Information --}}
                    <div
                        class="rounded-xl
                               border border-blue-500/20
                               bg-blue-500/[0.06]
                               p-4"
                    >

                        <p
                            class="text-xs
                                   leading-relaxed
                                   text-slate-400"
                        >
                            Your account will be submitted for authorization
                            after registration. You will be able to access
                            DCFMS once your account has been activated.
                        </p>

                    </div>


                    {{-- Submit --}}
                    <button
                        type="submit"

                        class="flex w-full
                               items-center justify-center
                               gap-3
                               rounded-xl
                               bg-gradient-to-r
                               from-blue-600
                               to-purple-600
                               px-5 py-3.5
                               text-sm
                               font-semibold
                               text-white
                               shadow-lg
                               shadow-blue-950/20
                               transition
                               hover:opacity-90"
                    >
                        Create Account

                        <span>
                            →
                        </span>

                    </button>

                </form>


                {{-- Login --}}
                <div
                    class="mt-7
                           border-t border-slate-800
                           pt-6
                           text-center"
                >

                    <p class="text-sm text-slate-500">

                        Already have an account?

                        <a
                            href="{{ route('login') }}"
                            class="ml-1
                                   font-medium
                                   text-blue-400
                                   hover:text-blue-300"
                        >
                            Sign in
                        </a>

                    </p>

                </div>

            </div>

        </div>

    </main>

</div>


{{-- Password Toggle --}}
<script>

    document
        .querySelectorAll(
            '[data-password-toggle]'
        )
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {

                    const inputId =
                        button.getAttribute(
                            'data-password-toggle'
                        );

                    const input =
                        document.getElementById(
                            inputId
                        );

                    if (!input) {
                        return;
                    }

                    const hidden =
                        input.type === 'password';

                    input.type =
                        hidden
                            ? 'text'
                            : 'password';

                    button.textContent =
                        hidden
                            ? 'Hide'
                            : 'Show';

                }
            );

        });

</script>

</body>
</html>