<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Padayon Massage Center - Create Account
    </title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    @vite('resources/css/app.css')

</head>


<body class="min-h-screen bg-[#D6BB9E] flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-8">


        {{-- Back --}}
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

            <img src="{{ asset('build/assets/images/logo.png') }}"
                alt="Padayon Massage Center - Blind Massage Specialists"
                class="h-40 w-auto mx-auto object-contain mb-4">

            <h1 class="text-lg font-bold text-gray-800">

                {{ $walkInCustomer ? 'Complete Your Account' : 'Create Account' }}

            </h1>

            <p class="text-sm text-gray-500 mt-1">

                {{ $walkInCustomer ? 'Your appointment information is already registered.' : "Join us today — it's free" }}

            </p>

        </div>


        {{-- Success --}}
        @if (session('success'))
            <div
                class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">

                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" />
                </svg>

                {{ session('success') }}

            </div>
        @endif


        {{-- Error --}}
        @if (session('error'))
            <div
                class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">

                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>

                {{ session('error') }}

            </div>
        @endif


        {{-- Walk-in notice --}}
        @if ($walkInCustomer)
            <div class="mb-6 rounded-xl border border-[#849753]/30 bg-[#849753]/10 px-4 py-4">

                <div class="flex items-start gap-3">

                    <div class="mt-0.5 shrink-0">

                        <svg class="w-5 h-5 text-[#849753]" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-[#2F2420]">

                            Welcome, {{ $walkInCustomer->name }}!

                        </p>

                        <p class="mt-1 text-xs leading-5 text-gray-600">

                            Your walk-in appointment has already been
                            recorded. Create your account below to connect
                            your appointment history to your Padayon account.

                        </p>

                    </div>

                </div>

            </div>
        @endif


        {{-- Registration form --}}
        <form method="POST" action="{{ route('register') }}" class="space-y-5">

            @csrf


            {{-- Name and Phone --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                {{-- Name --}}
                <div>

                    <label for="name"
                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Full Name
                    </label>

                    <input type="text" id="name" name="name"
                        value="{{ old('name', $walkInCustomer?->name ?? '') }}"
                        @if ($walkInCustomer) readonly @endif placeholder="Juan dela Cruz" required
                        autocomplete="name"
                        class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 outline-none transition
                        focus:ring-2 focus:ring-[#849753]
                        focus:border-[#849753]
                        focus:bg-white
                        @error('name')
                            border-red-400 bg-red-50
                        @else
                        @enderror">

                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Phone --}}
                <div>

                    <label for="phone"
                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Phone Number
                    </label>

                    <input type="tel" id="phone" name="phone"
                        value="{{ old('phone', $walkInCustomer?->phone ?? '') }}"
                        @if ($walkInCustomer) readonly @endif placeholder="09XX XXX XXXX" required
                        autocomplete="tel"
                        class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 outline-none transition
                        focus:ring-2 focus:ring-[#849753]
                        focus:border-[#849753]
                        focus:bg-white
                        @error('phone')
                            border-red-400 bg-red-50
                        @else
                        @enderror">

                    @error('phone')
                        <p class="mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            {{-- Email --}}
            <div>

                <label for="email" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                    Email Address
                </label>

                <input type="email" id="email" name="email"
                    value="{{ old('email', $walkInCustomer?->email ?? '') }}"
                    @if ($walkInCustomer) readonly @endif placeholder="you@example.com" autocomplete="email"
                    required
                    class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 outline-none transition
                    focus:ring-2 focus:ring-[#849753]
                    focus:border-[#849753]
                    focus:bg-white
                    @error('email')
                        border-red-400 bg-red-50
                    @else
                    @enderror">

                @error('email')
                    <p class="mt-1.5 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Password --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>

                    <label for="password"
                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Password
                    </label>

                    <input type="password" id="password" name="password" placeholder="Min. 8 characters"
                        autocomplete="new-password" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 outline-none transition
                        focus:ring-2 focus:ring-[#849753]
                        focus:border-[#849753]
                        focus:bg-white
                        @error('password') bg-red-50
                        @enderror">

                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Confirm Password --}}
                <div>

                    <label for="password_confirmation"
                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Confirm Password
                    </label>

                    <input type="password" id="password_confirmation" name="password_confirmation"
                        placeholder="Repeat password" autocomplete="new-password" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-gray-50 outline-none transition
                        focus:ring-2 focus:ring-[#849753]
                        focus:border-[#849753]
                        focus:bg-white">

                </div>

            </div>


            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg text-sm transition-all">

                {{ $walkInCustomer ? 'Complete My Account' : 'Create Account' }}

            </button>

        </form>


        {{-- Login --}}
        <p class="text-center text-sm text-gray-500 mt-6">

            Already have an account?

            <a href="{{ route('login') }}" class="text-[#849753] font-semibold hover:underline">
                Sign in
            </a>

        </p>

    </div>

</body>

</html>
