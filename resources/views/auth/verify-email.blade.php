@extends('layouts.auth')

@section('title', 'Padayon Massage Center - Verify Email')

@section('content')

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 mx-auto">

        {{-- Icon --}}
        <div class="text-center mb-8">

            <img src="{{ !empty($websiteSettings?->logo_path)
                ? asset('storage/' . $websiteSettings->logo_path)
                : asset('build/assets/images/logo.png') }}"
                alt="{{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}"
                class="h-40 w-auto mx-auto object-contain mb-4">

            <h1 class="text-2xl font-bold text-gray-800">Verify Your Email</h1>
            <p class="text-sm text-gray-500 mt-1">One more step before you get started</p>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div
                class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Info text --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6 text-center">
            <p class="text-sm text-gray-600 leading-relaxed">
                We sent a verification code to
                <span class="font-semibold text-[#849753]">{{ auth()->user()->email }}</span>.
                Enter the 6-digit code below to activate your account.
            </p>
        </div>

        {{-- Status badge --}}
        <div class="flex items-center justify-center mb-6">
            <span
                class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm font-medium rounded-full">
                <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                Account status: <strong>Pending</strong>
            </span>
        </div>


        {{-- Verify Code Form --}}
        <form method="POST" action="{{ route('verification.verify-code') }}" class="space-y-6" x-data="{
            digits: ['', '', '', '', '', ''],
        
            updateDigit(index, event) {
                const input = event.target;
                const value = input.value.replace(/\D/g, '').slice(-1);
        
                this.digits[index] = value;
                input.value = value;
        
                // Automatically focus the next box
                if (value && index < 5) {
                    const inputs = input.closest('[data-otp]').querySelectorAll('input');
                    inputs[index + 1]?.focus();
                }
            },
        
            handleBackspace(index, event) {
                if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
                    const inputs = event.target.closest('[data-otp]').querySelectorAll('input');
                    inputs[index - 1]?.focus();
                }
            },
        
            handlePaste(event) {
                event.preventDefault();
        
                const pasted = event.clipboardData.getData('text')
                    .replace(/\D/g, '')
                    .slice(0, 6);
        
                this.digits = ['', '', '', '', '', ''];
        
                [...pasted].forEach((digit, index) => {
                    this.digits[index] = digit;
                });
        
                const inputs = event.target.closest('[data-otp]').querySelectorAll('input');
        
                inputs.forEach((input, index) => {
                    input.value = this.digits[index];
                });
        
                this.$nextTick(() => {
                    const nextEmpty = this.digits.findIndex(digit => !digit);
                    inputs[nextEmpty === -1 ? 5 : nextEmpty]?.focus();
                });
            }
        }"
            x-init="$nextTick(() => $el.querySelector('[data-otp] input')?.focus())">

            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3 text-center">
                    Enter your 6-digit verification code
                </label>

                {{-- Six OTP Boxes --}}
                <div data-otp class="flex justify-center gap-2 sm:gap-3">

                    <template x-for="(digit, index) in digits" :key="index">
                        <input type="text" inputmode="numeric" maxlength="1" autocomplete="off" x-model="digits[index]"
                            @input="updateDigit(index, $event)" @keydown="handleBackspace(index, $event)"
                            @paste="handlePaste($event)"
                            class="w-11 h-12 sm:w-12 sm:h-14 text-center text-xl font-bold text-gray-800 border border-gray-300 rounded-lg outline-none transition focus:ring-2 focus:ring-[#849753] focus:border-[#849753]">
                    </template>

                </div>

                {{-- Combined Code --}}
                <input type="hidden" name="code" :value="digits.join('')">

                @error('code')
                    <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
                @enderror
            </div>

            {{-- Verify Button --}}
            <button type="submit" :disabled="digits.join('').length !== 6"
                class="w-full py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                Verify Code
            </button>

        </form>


        {{-- Resend button --}}
        <form method="POST" action="{{ route('verification.resend-code') }}" class="mt-4">
            @csrf
            <button type="submit"
                class="w-full py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg text-sm transition-all">
                Resend Verification Code
            </button>
        </form>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 underline transition">
                Sign out and use a different account
            </button>
        </form>

    </div>
@endsection
