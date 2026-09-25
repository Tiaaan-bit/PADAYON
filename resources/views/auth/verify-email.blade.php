<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padayon Massage Center - Verify Email</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-[#F4EDDB] flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 mx-auto">

        {{-- Icon --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
                <svg class="w-9 h-9 text-[#849753]" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
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

        {{-- Errors --}}
        @if ($errors->any())
            <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                {{ $errors->first() }}
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

        {{-- Verify code form --}}
        <form method="POST" action="{{ route('verification.verify-code') }}" class="space-y-4">
            @csrf

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Verification Code
                </label>
                <input type="text" name="code" id="code" maxlength="6" inputmode="numeric"
                    autocomplete="one-time-code" placeholder="Enter 6-digit code" value="{{ old('code') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]">
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg text-sm transition-all">
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
</body>

</html>
