@extends('layouts.auth')

@section('title', 'Padayon Massage Center - Register')

@section('content')

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

            <img src="{{ !empty($websiteSettings?->logo_path)
                ? asset('storage/' . $websiteSettings->logo_path)
                : asset('build/assets/images/logo.png') }}"
                alt="{{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}"
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



            {{-- Password and Confirm Password --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                {{-- Password --}}
                <div>
                    <label for="password"
                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600">
                        Password
                    </label>

                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Min. 8 characters"
                            autocomplete="new-password" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 pr-12 text-sm text-gray-800 outline-none transition
                focus:border-[#849753] focus:bg-white focus:ring-2 focus:ring-[#849753]
                @error('password') bg-red-50 @enderror">

                        <button type="button"
                            onclick="togglePasswordVisibility('password', 'passwordEye', 'passwordEyeOff')"
                            aria-label="Show password" aria-controls="password"
                            class="absolute inset-y-0 right-0 flex items-center justify-center px-3 text-gray-500 transition hover:text-[#849753] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#849753]">
                            {{-- Eye icon --}}
                            <svg id="passwordEye" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            {{-- Eye-off icon --}}
                            <svg id="passwordEyeOff" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A10.7 10.7 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16.6 16.6 0 0 1-3.2 4.1M6.2 6.2C3.6 8 2.25 12 2.25 12s3.5 7 9.75 7c1.2 0 2.3-.25 3.3-.65" />
                            </svg>
                        </button>
                    </div>

                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation"
                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600">
                        Confirm Password
                    </label>

                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Repeat password" autocomplete="new-password" required
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 pr-12 text-sm text-gray-800 outline-none transition
                focus:border-[#849753] focus:bg-white focus:ring-2 focus:ring-[#849753]">

                        <button type="button"
                            onclick="togglePasswordVisibility('password_confirmation', 'confirmPasswordEye', 'confirmPasswordEyeOff')"
                            aria-label="Show confirm password" aria-controls="password_confirmation"
                            class="absolute inset-y-0 right-0 flex items-center justify-center px-3 text-gray-500 transition hover:text-[#849753] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#849753]">
                            {{-- Eye icon --}}
                            <svg id="confirmPasswordEye" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            {{-- Eye-off icon --}}
                            <svg id="confirmPasswordEyeOff" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A10.7 10.7 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16.6 16.6 0 0 1-3.2 4.1M6.2 6.2C3.6 8 2.25 12 2.25 12s3.5 7 9.75 7c1.2 0 2.3-.25 3.3-.65" />
                            </svg>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Password visibility toggle --}}
            <script>
                function togglePasswordVisibility(inputId, eyeId, eyeOffId) {
                    const input = document.getElementById(inputId);
                    const eye = document.getElementById(eyeId);
                    const eyeOff = document.getElementById(eyeOffId);
                    const button = input.parentElement.querySelector('button');

                    const isPassword = input.type === 'password';

                    input.type = isPassword ? 'text' : 'password';

                    eye.classList.toggle('hidden', isPassword);
                    eyeOff.classList.toggle('hidden', !isPassword);

                    button.setAttribute(
                        'aria-label',
                        isPassword ?
                        'Hide ' + (inputId === 'password' ? 'password' : 'confirm password') :
                        'Show ' + (inputId === 'password' ? 'password' : 'confirm password')
                    );
                }
            </script>



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

@endsection
