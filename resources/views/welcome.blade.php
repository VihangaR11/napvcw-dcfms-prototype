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
        NAPVCW Digital Case Flow Management System
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>

        html,
        body {
            margin: 0;
            padding: 0;
            background: #070B1D;
            overflow: hidden;
        }

        .splash-background {
            background:
                radial-gradient(
                    circle at 50% 35%,
                    rgba(37, 99, 235, 0.10),
                    transparent 35%
                ),
                radial-gradient(
                    circle at 65% 65%,
                    rgba(147, 51, 234, 0.08),
                    transparent 35%
                ),
                linear-gradient(
                    145deg,
                    #070B1D,
                    #0B1226,
                    #070B1D
                );
        }

        .logo-ring {
            animation:
                ringRotate 10s linear infinite;
        }

        .logo-glow {
            animation:
                logoPulse 2.6s ease-in-out infinite;
        }

        @keyframes ringRotate {

            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }

        }

        @keyframes logoPulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow:
                    0 0 0 rgba(59, 130, 246, 0);
            }

            50% {
                transform: scale(1.03);
                box-shadow:
                    0 0 45px rgba(59, 130, 246, 0.12);
            }

        }

        .fade-up {
            opacity: 0;
            transform: translateY(10px);

            animation:
                fadeUp 0.8s ease forwards;
        }

        @keyframes fadeUp {

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }

        .progress-inner {
            width: 0%;
            transition:
                width 0.08s linear;
        }

    </style>

</head>


<body>

<div
    class="splash-background
           relative
           flex min-h-screen
           items-center
           justify-center
           overflow-hidden
           text-white"
>

    {{-- =====================================================
         BACKGROUND DECORATION
    ====================================================== --}}

    <div
        class="absolute
               left-1/4 top-1/4
               h-80 w-80
               rounded-full
               bg-blue-600/5
               blur-3xl"
    ></div>

    <div
        class="absolute
               bottom-1/4 right-1/4
               h-80 w-80
               rounded-full
               bg-purple-600/5
               blur-3xl"
    ></div>


    {{-- =====================================================
         MAIN SPLASH CONTENT
    ====================================================== --}}

    <main
        class="relative z-10
               w-full
               max-w-md
               px-6
               text-center"
    >

        {{-- =================================================
             LOGO
        ================================================== --}}

        <div
            class="relative
                   mx-auto
                   flex h-40 w-40
                   items-center
                   justify-center"
        >

            {{-- Rotating outer ring --}}
            <div
                class="logo-ring
                       absolute inset-0
                       rounded-full
                       border
                       border-dashed
                       border-blue-400/50"
            ></div>


            {{-- Inner ring --}}
            <div
                class="absolute inset-3
                       rounded-full
                       border border-purple-400/20"
            ></div>


            {{-- Logo --}}
            <div
                class="logo-glow
                       relative
                       flex h-28 w-28
                       items-center
                       justify-center
                       rounded-full
                       border border-slate-700
                       bg-white
                       p-2"
            >

                <img
                    src="{{ asset('images/napvcw-splash-logo.jpeg') }}"
                    alt="NAPVCW Logo"
                    class="h-full
                           w-full
                           rounded-full
                           object-contain"
                >

            </div>

        </div>


        {{-- =================================================
             INSTITUTION
        ================================================== --}}

        <div
            class="fade-up mt-7"
            style="animation-delay: 0.15s;"
        >

            <p
                class="text-[10px]
                       font-semibold
                       uppercase
                       tracking-[0.24em]
                       text-blue-400"
            >
                Ministry of Justice and National Integration
            </p>


            <p
                class="mt-2
                       text-[11px]
                       uppercase
                       tracking-[0.12em]
                       text-slate-500"
            >
                National Authority for the Protection
                of Victims of Crime and Witnesses
            </p>

        </div>


        {{-- =================================================
             SYSTEM TITLE
        ================================================== --}}

        <div
            class="fade-up mt-6"
            style="animation-delay: 0.3s;"
        >

            <h1
                class="text-3xl
                       font-semibold
                       tracking-tight
                       text-white"
            >
                DCFMS
            </h1>


            <p
                class="mt-2
                       text-sm
                       font-medium
                       text-slate-300"
            >
                Digital Case Flow Management System
            </p>

        </div>


        {{-- =================================================
             PROGRESS
        ================================================== --}}

        <div
            class="fade-up mt-10"
            style="animation-delay: 0.45s;"
        >

            <div
                class="flex items-center
                       justify-between
                       text-[10px]
                       text-slate-500"
            >

                <span id="progressPercent">
                    0%
                </span>

                <span id="progressStatus">
                    Initializing secure environment...
                </span>

            </div>


            <div
                class="mt-3
                       h-[3px]
                       overflow-hidden
                       rounded-full
                       bg-slate-800"
            >

                <div
                    id="progressBar"
                    class="progress-inner
                           h-full
                           rounded-full
                           bg-gradient-to-r
                           from-blue-500
                           via-cyan-400
                           to-purple-500"
                ></div>

            </div>

        </div>


        {{-- =================================================
             SECURITY STATUS
        ================================================== --}}

        <div
            class="fade-up mt-6
                   flex items-center
                   justify-center gap-2"
            style="animation-delay: 0.6s;"
        >

            <span
                class="h-1.5 w-1.5
                       rounded-full
                       bg-green-400"
            ></span>

            <p
                id="systemMessage"
                class="text-[10px]
                       tracking-wide
                       text-slate-600"
            >
                Establishing secure session
            </p>

        </div>

    </main>


    {{-- =====================================================
         DEVELOPER CREDIT
    ====================================================== --}}

    <footer
        class="absolute
               bottom-7
               left-0
               w-full
               px-5
               text-center"
    >

        <div
            class="mx-auto
                   inline-flex
                   flex-col
                   items-center"
        >

            <p
                class="text-[8px]
                       uppercase
                       tracking-[0.20em]
                       text-slate-700"
            >
                Developed by
            </p>


            <p
                class="mt-1
                       text-[11px]
                       font-medium
                       bg-gradient-to-r
                       from-blue-400
                       to-purple-400
                       bg-clip-text
                       text-transparent"
            >
                Vihanga Rathnayake
            </p>


            <p
                class="mt-1
                       text-[9px]
                       text-slate-700"
            >
                Faculty of Computing
                · University of Sri Jayewardenepura
            </p>

        </div>

    </footer>

</div>


{{-- =========================================================
     SPLASH CONTROLLER
========================================================= --}}

<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const progressBar =
                document.getElementById(
                    'progressBar'
                );

            const progressPercent =
                document.getElementById(
                    'progressPercent'
                );

            const progressStatus =
                document.getElementById(
                    'progressStatus'
                );

            const systemMessage =
                document.getElementById(
                    'systemMessage'
                );


            let progress = 0;


            const interval =
                setInterval(
                    function () {

                        /*
                        |--------------------------------------------------------------------------
                        | Progress
                        |--------------------------------------------------------------------------
                        */

                        progress +=
                            Math.floor(
                                Math.random() * 4
                            ) + 1;


                        if (progress > 100) {
                            progress = 100;
                        }


                        progressBar.style.width =
                            progress + '%';

                        progressPercent.textContent =
                            progress + '%';


                        /*
                        |--------------------------------------------------------------------------
                        | Status Messages
                        |--------------------------------------------------------------------------
                        */

                        if (progress < 25) {

                            progressStatus.textContent =
                                'Initializing secure environment...';

                            systemMessage.textContent =
                                'Establishing secure session';

                        }

                        else if (progress < 50) {

                            progressStatus.textContent =
                                'Loading system services...';

                            systemMessage.textContent =
                                'Preparing authentication services';

                        }

                        else if (progress < 75) {

                            progressStatus.textContent =
                                'Preparing access controls...';

                            systemMessage.textContent =
                                'Loading role-based security';

                        }

                        else if (progress < 95) {

                            progressStatus.textContent =
                                'Finalizing system access...';

                            systemMessage.textContent =
                                'Verifying system readiness';

                        }

                        else {

                            progressStatus.textContent =
                                'Ready';

                            systemMessage.textContent =
                                'Secure environment ready';

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Redirect
                        |--------------------------------------------------------------------------
                        */

                        if (progress >= 100) {

                            clearInterval(
                                interval
                            );

                            setTimeout(
                                function () {

                                    window.location.href =
                                        "{{ route('login') }}";

                                },
                                450
                            );

                        }

                    },
                    65
                );

        }
    );

</script>

</body>

</html>