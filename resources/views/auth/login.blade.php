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
        content="{{ __('auth.meta_description') }}"
    >



    <title>
        {{ __('auth.page_title') }}
    </title>





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





<div class="min-h-screen lg:grid lg:grid-cols-2">





    {{-- =====================================================

         LEFT SIDE

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

               p-12 xl:p-16"

    >



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

                   flex w-full flex-col"

        >



            <div class="flex items-center gap-4">



                <div

                    class="flex h-16 w-16

                           items-center justify-center

                           rounded-2xl

                           bg-white

                           p-2"

                >



                    <img

                        src="{{ asset('images/napvcw-logo.png') }}"

                        alt="NAPVCW"

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

                        {{ __('auth.ministry_name') }}

                    </p>



                    <p

                        class="mt-1 max-w-sm

                               text-sm

                               font-medium

                               leading-relaxed

                               text-slate-300"

                    >

                        {{ __('auth.authority_name') }}

                    </p>



                </div>



            </div>





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

                    {{ __('auth.authorized_employee_access') }}

                </div>





                <h1

                    class="mt-7

                           text-4xl xl:text-5xl

                           font-semibold

                           leading-tight"

                >

                    {{ __('auth.digital_case_flow') }}



                    <span

                        class="block

                               bg-gradient-to-r

                               from-blue-400

                               to-purple-400

                               bg-clip-text

                               text-transparent"

                    >

                        {{ __('auth.management_system') }}

                    </span>



                </h1>





                <p

                    class="mt-6

                           max-w-lg

                           text-base

                           leading-7

                           text-slate-400"

                >

                    {{ __('auth.hero_description') }}

                </p>





                <div

                    class="mt-9

                           grid grid-cols-2

                           gap-3"

                >



                    <div

                        class="rounded-xl

                               border border-slate-700

                               bg-[#0B1226]/60

                               p-4"

                    >

                        <p class="text-sm font-medium">

                            {{ __('auth.secure_access') }}

                        </p>



                        <p

                            class="mt-1

                                   text-xs

                                   text-slate-500"

                        >

                            {{ __('auth.employee_credential_authentication') }}

                        </p>

                    </div>





                    <div

                        class="rounded-xl

                               border border-slate-700

                               bg-[#0B1226]/60

                               p-4"

                    >

                        <p class="text-sm font-medium">

                            {{ __('auth.role_based') }}

                        </p>



                        <p

                            class="mt-1

                                   text-xs

                                   text-slate-500"

                        >

                            {{ __('auth.access_by_responsibility') }}

                        </p>

                    </div>



                </div>



            </div>





            <p class="text-[10px] text-slate-600">

                {{ __('auth.authority_name') }}

            </p>



        </div>



    </section>





    {{-- =====================================================

         LOGIN

    ====================================================== --}}

    <main

        class="relative flex min-h-screen

               items-center justify-center

               bg-[#070B1D]

               px-5 py-10

               sm:px-8"

    >

        {{-- Language Switcher --}}
        <div
            class="absolute right-5 top-5
                   flex items-center gap-1
                   rounded-xl border border-slate-700
                   bg-[#111A2E] p-1
                   sm:right-8 sm:top-8"
            aria-label="{{ __('auth.language') }}"
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
                English
            </a>
        </div>




        <div class="w-full max-w-md">





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

                        {{ __('auth.government_service') }}

                    </p>



                </div>



            </div>





            <div

                class="rounded-3xl

                       border border-slate-800

                       bg-[#111A2E]

                       p-6 sm:p-8

                       shadow-2xl"

            >



                <div>



                    <p

                        class="text-[10px]

                               font-semibold

                               uppercase

                               tracking-[0.18em]

                               text-blue-400"

                    >

                        {{ __('auth.employee_access') }}

                    </p>





                    <h2

                        class="mt-2

                               text-3xl

                               font-semibold"

                    >{{ __('auth.sign_in') }}</h2>





                    <p

                        class="mt-2

                               text-sm

                               text-slate-500"

                    >

                        {{ __('auth.login_instruction') }}

                    </p>



                </div>





                @if(session('success'))



                    <div

                        class="mt-6

                               rounded-xl

                               border border-green-500/20

                               bg-green-500/10

                               p-4

                               text-sm

                               text-green-300"

                    >

                        {{ session('success') }}

                    </div>



                @endif





                @if($errors->any())



                    <div

                        class="mt-6

                               rounded-xl

                               border border-red-500/20

                               bg-red-500/10

                               p-4

                               text-sm

                               text-red-300"

                    >

                        {{ $errors->first() }}

                    </div>



                @endif





                <form

                    method="POST"

                    action="{{ route('login.attempt') }}"

                    class="mt-7 space-y-5"

                >



                    @csrf





                    {{-- EPF --}}

                    <div>



                        <label

                            for="employee_number"

                            class="mb-2 block

                                   text-sm

                                   font-medium

                                   text-slate-300"

                        >

                            {{ __('auth.employee_number') }}

                        </label>



                        <input

                            id="employee_number"

                            name="employee_number"

                            type="text"

                            value="{{ old('employee_number') }}"

                            required

                            autofocus

                            autocomplete="username"

                            placeholder="{{ __('auth.employee_number_placeholder') }}"



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

                                   focus:border-blue-500

                                   focus:ring-2

                                   focus:ring-blue-500/20"

                        >



                    </div>





                    {{-- {{ __('auth.password') }} --}}

                    <div>



                        <div

                            class="mb-2

                                   flex items-center

                                   justify-between"

                        >



                            <label

                                for="password"

                                class="text-sm

                                       font-medium

                                       text-slate-300"

                            >

                                {{ __('auth.password') }}

                            </label>



                        </div>





                        <div class="relative">



                            <input

                                id="password"

                                name="password"

                                type="password"

                                required

                                autocomplete="current-password"

                                placeholder="{{ __('auth.password_placeholder') }}"



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

                                       focus:border-blue-500

                                       focus:ring-2

                                       focus:ring-blue-500/20"

                            >





                            <button

                                id="passwordToggle"

                                type="button"



                                class="absolute

                                       right-4

                                       top-1/2

                                       -translate-y-1/2

                                       text-xs

                                       font-medium

                                       text-blue-400"

                            >
                                {{ __('auth.show') }}
                            </button>



                        </div>



                    </div>





                    {{-- Remember --}}

                    <label

                        class="flex items-center gap-3"

                    >



                        <input

                            type="checkbox"

                            name="remember"



                            class="rounded

                                   border-slate-600

                                   bg-[#0D172A]

                                   text-blue-600

                                   focus:ring-blue-500"

                        >



                        <span

                            class="text-sm

                                   text-slate-400"

                        >

                            {{ __('auth.remember_me') }}

                        </span>



                    </label>





                    {{-- Sign In --}}

                    <button

                        type="submit"



                        class="flex w-full

                               items-center

                               justify-center

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

                    >{{ __('auth.sign_in') }}<span>

                            →

                        </span>



                    </button>



                </form>



                {{-- =====================================================

     SYSTEM ADMIN DEMO ACCESS

\===================================================== --}}

        <div

                class="mt-5

                 rounded-2xl

                border border-purple-500/20

                bg-purple-500/[0.06]

                p-4"

        >



    <div class="flex items-start justify-between gap-4">



        <div>

            <p

                class="text-sm

                       font-semibold

                       text-purple-300"

            >

                {{ __('auth.system_administration') }}

            </p>



            <p

                class="mt-1

                       text-xs

                       leading-relaxed

                       text-slate-500"

            >

                {{ __('auth.admin_demo_description') }}

            </p>

        </div>



        <span

            class="rounded-full

                   border border-purple-500/20

                   bg-purple-500/10

                   px-2.5 py-1

                   text-[9px]

                   uppercase

                   tracking-wider

                   text-purple-300"

        >

            Admin

        </span>



    </div>





    <button

        type="button"

        id="adminLoginButton"



        class="mt-4

               flex w-full

               items-center justify-center

               gap-2

               rounded-xl

               border border-purple-500/20

               bg-purple-500/10

               px-4 py-3

               text-sm

               font-medium

               text-purple-300

               transition

               hover:bg-purple-500/20"

    >

        {{ __('auth.login_as_admin') }}



        <span>

            →

        </span>

    </button>



</div>



                {{-- Signup --}}

                <div

                    class="mt-7

                           border-t border-slate-800

                           pt-6

                           text-center"

                >



                    <p class="text-sm text-slate-500">



                        {{ __('auth.no_employee_account') }}



                        <a

                            href="{{ route('register') }}"

                            class="ml-1

                                   font-medium

                                   text-blue-400

                                   hover:text-blue-300"

                        >

                            {{ __('auth.create_account') }}

                        </a>



                    </p>



                </div>



            </div>



        </div>



    </main>



</div>





<script>



    const password =

        document.getElementById(

            'password'

        );



    const passwordToggle =

        document.getElementById(

            'passwordToggle'

        );





    passwordToggle?.addEventListener(

        'click',

        function () {



            const hidden =

                password.type ===

                'password';



            password.type =

                hidden

                    ? 'text'

                    : 'password';



            passwordToggle.textContent = hidden
                    ? @json(__('auth.hide'))
                    : @json(__('auth.show'));



        }

    );

    const adminLoginButton =

    document.getElementById(

        'adminLoginButton'

    );



adminLoginButton?.addEventListener(

    'click',

    function () {



        const employeeNumber =

            document.getElementById(

                'employee_number'

            );



        const password =

            document.getElementById(

                'password'

            );



        if (

            employeeNumber &&

            password

        ) {



            employeeNumber.value =

                'SYS001';



            password.value =

                'password123';



            employeeNumber.focus();



        }

    }

    );



</script>



</body>

</html>