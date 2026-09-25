<!DOCTYPE html>

<html lang="en">

<head>


    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Padayon Massage Center - Sign In</title>

    @vite('resources/css/app.css')
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">


</head>

<body class="min-h-screen bg-[#D6BB9E] flex items-center justify-center p-4">


    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8">

        <a href="{{ route('home.showHomePage') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-[#849753] hover:underline mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>

            Back
        </a>


        {{-- Logo --}}

        <div class="text-center mb-8">

            <img src="{{ asset('images/logo.webp') }}"
                alt="Padayon Massage Center - Blind Massage Specialists"
                class="h-40 w-auto mx-auto object-contain mb-4">

            <h1 class="text-lg font-bold text-gray-800">
                Welcome back
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Sign in to your account
            </p>

        </div>


        {{-- Flash Messages --}}

        @if ($errors->has('email'))
            <div
                class="mb-5 px-4 py-3
                bg-red-50
                border border-red-200
                text-red-700
                text-sm
                rounded-lg
                flex items-center gap-2">

                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path
                        d="M12 9v4m0 4h.01M10.29 3.86l-7.43 12.85A2 2 0 004.59 20h14.82a2 2 0 001.73-3.29L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>

                {{ $errors->first('email') }}

            </div>
        @endif


        @if (session('success'))
            <div
                class="mb-5 px-4 py-3
                bg-green-50
                border border-green-200
                text-green-700
                text-sm
                rounded-lg
                flex items-center gap-2">

                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" />
                </svg>

                {{ session('success') }}

            </div>
        @endif


        @if (session('error'))
            <div
                class="mb-5 px-4 py-3
                bg-red-50
                border border-red-200
                text-red-700
                text-sm
                rounded-lg
                flex items-center gap-2">

                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>

                {{ session('error') }}

            </div>
        @endif


        @if (session('info'))
            <div
                class="mb-5 px-4 py-3
                bg-blue-50
                border border-blue-200
                text-blue-700
                text-sm
                rounded-lg
                flex items-center gap-2">

                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />

                    <path d="M12 8v4m0 4h.01" />
                </svg>

                {{ session('info') }}

            </div>
        @endif


        {{-- Login Form --}}

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">

            @csrf


            {{-- Email --}}

            <div>

                <label for="email"
                    class="block
                    text-xs
                    font-semibold
                    text-gray-600
                    uppercase
                    tracking-wide
                    mb-1.5">
                    Email Address
                </label>

                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    placeholder="you@example.com" autocomplete="email" required autofocus
                    class="w-full
                    px-4 py-2.5
                    rounded-lg
                    border
                    text-sm
                    text-gray-800
                    outline-none
                    transition
                    focus:ring-2
                    focus:ring-[#849753]
                    focus:border-[#849753]
                    focus:bg-white
                    @error('email')
                        border-red-400
                        bg-red-50
                    @else
                    @enderror">

            </div>


            {{-- Password --}}

            <div class="relative">

                <label for="password"
                    class="block
                    text-xs
                    font-semibold
                    text-gray-600
                    uppercase
                    tracking-wide
                    mb-1.5">
                    Password
                </label>

                <input type="password" id="password" name="password" placeholder="••••••••"
                    autocomplete="current-password" required
                    class="w-full
                    px-4 py-2.5
                    pr-12
                    rounded-lg
                    border
                    border-gray-300
                    text-sm
                    text-gray-800
                    outline-none
                    transition
                    focus:ring-2
                    focus:ring-[#849753]
                    focus:border-[#849753]
                    focus:bg-white">


                {{-- Password Toggle --}}

                <button type="button" id="togglePassword"
                    class="hidden
                    absolute
                    right-3
                    top-8
                    items-center
                    text-gray-500
                    hover:text-gray-700"
                    aria-label="Show password">

                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6-1c-1.5-4-5-7-9-7s-7.5 3-9 7c1.5 4 5 7 9 7s7.5-3 9-7z" />
                    </svg>

                </button>

            </div>


            {{-- Forgot Password --}}

            <div class="text-center mb-8">

                <a href="{{ route('password.request') }}"
                    class="text-sm
                    text-[#849753]
                    font-medium
                    hover:underline">
                    Forgot password?
                </a>

            </div>


            {{-- Submit --}}

            <button type="submit"
                class="w-full
                py-3
                bg-[#849753]
                hover:bg-[#6F4E37]
                active:scale-[.99]
                text-white
                font-semibold
                rounded-lg
                text-sm
                transition-all">
                Sign In
            </button>

        </form>


        {{-- Register --}}

        <p class="text-center text-sm text-gray-500 mt-6">

            Don't have an account?

            <a href="{{ route('register') }}"
                class="text-[#849753]
                font-semibold
                hover:underline">
                Create one
            </a>

        </p>

    </div>


    {{-- ================================================================ --}}
    {{-- RATE LIMIT MODAL --}}
    {{-- ================================================================ --}}

    @if (session('login_rate_limited'))
        <div id="rateLimitModal"
            class="fixed inset-0
            z-50
            flex items-center
            justify-center
            bg-black/50
            backdrop-blur-sm
            px-4">

            <div
                class="relative
                w-full
                max-w-sm
                bg-white
                rounded-2xl
                shadow-2xl
                p-7
                text-center">


                {{-- X Close Button --}}

                <button type="button" id="rateLimitX"
                    class="absolute
                    top-4
                    right-4
                    flex
                    h-8
                    w-8
                    items-center
                    justify-center
                    rounded-full
                    text-gray-400
                    hover:bg-gray-100
                    hover:text-gray-700
                    transition"
                    aria-label="Close">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>

                </button>


                {{-- Warning Icon --}}

                <div
                    class="mx-auto
                    mb-5
                    flex
                    h-16
                    w-16
                    items-center
                    justify-center
                    rounded-full
                    bg-red-100">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.29 3.86l-7.43 12.85A2 2 0 004.59 20h14.82a1.99 1.99 0 001.73-2.99L13.71 3.86a1.99 1.99 0 00-3.42 0z" />
                    </svg>

                </div>


                <h2 class="text-xl font-bold text-gray-800">
                    Too Many Login Attempts
                </h2>

                <p class="text-sm text-gray-500 mt-2">
                    You have reached the maximum number of login attempts.
                    Please wait before trying again.
                </p>


                {{-- Timer --}}

                <div class="mt-6">

                    <p
                        class="text-xs
                        uppercase
                        tracking-wider
                        font-semibold
                        text-gray-400">
                        Try again in
                    </p>

                    <div id="rateLimitTimer"
                        class="mt-2
                        text-5xl
                        font-bold
                        text-[#849753]
                        tabular-nums">
                        10:00
                    </div>

                </div>


                {{-- Progress Bar --}}

                <div
                    class="mt-6
                    h-2
                    w-full
                    overflow-hidden
                    rounded-full
                    bg-gray-200">

                    <div id="rateLimitProgress"
                        class="h-full
                        bg-[#849753]
                        transition-all
                        duration-1000"
                        style="width: 100%"></div>

                </div>


                {{-- Try Again Button --}}

                <button type="button" id="rateLimitClose" disabled
                    class="mt-6
                    w-full
                    rounded-lg
                    bg-gray-300
                    py-3
                    text-sm
                    font-semibold
                    text-gray-500
                    cursor-not-allowed">
                    Please wait...
                </button>

            </div>

        </div>
    @endif


    {{-- ================================================================ --}}
    {{-- PASSWORD TOGGLE SCRIPT --}}
    {{-- ================================================================ --}}

    <script>
        const password = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        if (password && togglePassword && eyeIcon) {

            // Show / hide eye icon while typing

            password.addEventListener('input', function() {

                if (password.value.length > 0) {

                    togglePassword.classList.remove('hidden');
                    togglePassword.classList.add('flex');

                } else {

                    togglePassword.classList.add('hidden');
                    togglePassword.classList.remove('flex');

                    // Reset to hidden password when cleared

                    password.type = 'password';

                    togglePassword.setAttribute(
                        'aria-label',
                        'Show password'
                    );

                    eyeIcon.innerHTML = `
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0
                        3 3 0 016 0zm6-1
                        c-1.5-4-5-7-9-7
                        s-7.5 3-9 7
                        c1.5 4 5 7 9 7
                        s7.5-3 9-7z"
                    />
                `;
                }

            });


            // Toggle password visibility

            togglePassword.addEventListener('click', function() {

                if (password.type === 'password') {

                    password.type = 'text';

                    togglePassword.setAttribute(
                        'aria-label',
                        'Hide password'
                    );

                    eyeIcon.innerHTML = `
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13.875 18.825
                        A10.05 10.05 0 0112 19
                        c-4 0-7.5-3-9-7
                        a13.16 13.16 0 013.1-4.36
                        M9.88 9.88
                        A3 3 0 0114.12 14.12
                        M6.1 6.1L3 3
                        m18 18L3 3"
                    />
                `;

                } else {

                    password.type = 'password';

                    togglePassword.setAttribute(
                        'aria-label',
                        'Show password'
                    );

                    eyeIcon.innerHTML = `
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0
                        3 3 0 016 0zm6-1
                        c-1.5-4-5-7-9-7
                        s-7.5 3-9 7
                        c1.5 4 5 7 9 7
                        s7.5-3 9-7z"
                    />
                `;
                }

            });

        }
    </script>


    {{-- ================================================================ --}}
    {{-- RATE LIMIT SCRIPT --}}
    {{-- ================================================================ --}}

    @if (session('login_rate_limited'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const modal =
                    document.getElementById('rateLimitModal');

                const timer =
                    document.getElementById('rateLimitTimer');

                const progress =
                    document.getElementById('rateLimitProgress');

                const closeButton =
                    document.getElementById('rateLimitClose');

                const xButton =
                    document.getElementById('rateLimitX');

                const retryAt =
                    {{ session('retry_after') }};

                const totalSeconds = 600;

                let countdown = null;

                let retryButtonHandlerAdded = false;


                // --------------------------------------------------------
                // Disable login button while rate limited
                // --------------------------------------------------------

                const loginButton =
                    document.querySelector(
                        'button[type="submit"]'
                    );

                if (loginButton) {

                    loginButton.disabled = true;

                    loginButton.classList.add(
                        'opacity-50',
                        'cursor-not-allowed'
                    );

                }


                // --------------------------------------------------------
                // Close modal using X
                // --------------------------------------------------------

                xButton.addEventListener('click', function() {

                    modal.remove();

                    if (countdown) {
                        clearInterval(countdown);
                        countdown = null;
                    }

                });


                // --------------------------------------------------------
                // Enable retry button
                // --------------------------------------------------------

                function enableRetry() {

                    timer.textContent = '00:00';

                    progress.style.width = '0%';

                    closeButton.disabled = false;

                    closeButton.textContent = 'Try Again';


                    closeButton.classList.remove(
                        'bg-gray-300',
                        'text-gray-500',
                        'cursor-not-allowed'
                    );


                    closeButton.classList.add(
                        'bg-[#849753]',
                        'text-white',
                        'hover:bg-[#6F4E37]'
                    );


                    if (!retryButtonHandlerAdded) {

                        retryButtonHandlerAdded = true;

                        closeButton.addEventListener(
                            'click',
                            function() {

                                modal.remove();

                                if (loginButton) {

                                    loginButton.disabled = false;

                                    loginButton.classList.remove(
                                        'opacity-50',
                                        'cursor-not-allowed'
                                    );

                                }

                            }
                        );

                    }

                }


                // --------------------------------------------------------
                // Update countdown
                // --------------------------------------------------------

                function updateTimer() {

                    const now =
                        Math.floor(Date.now() / 1000);

                    let remaining =
                        retryAt - now;


                    if (remaining <= 0) {

                        enableRetry();

                        if (countdown) {

                            clearInterval(countdown);

                            countdown = null;

                        }

                        return;

                    }


                    const minutes =
                        Math.floor(remaining / 60);

                    const seconds =
                        remaining % 60;


                    timer.textContent =
                        String(minutes).padStart(2, '0') +
                        ':' +
                        String(seconds).padStart(2, '0');


                    const percentage =
                        Math.max(
                            0,
                            Math.min(
                                100,
                                (remaining / totalSeconds) * 100
                            )
                        );


                    progress.style.width =
                        percentage + '%';

                }


                // --------------------------------------------------------
                // Start timer
                // --------------------------------------------------------

                updateTimer();

                countdown =
                    setInterval(
                        updateTimer,
                        1000
                    );

            });
        </script>
    @endif


</body>

</html>
