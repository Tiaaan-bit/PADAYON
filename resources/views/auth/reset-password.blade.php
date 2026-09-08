<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padayon Massage Center - Reset Password</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#F4EDDB] flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-100 rounded-2xl mb-3">
                <svg class="w-8 h-8 text-[#849753]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
            <p class="text-sm text-gray-500 mt-1">Choose a strong new password</p>
        </div>

        {{-- Flash / Errors --}}
        @if($errors->any())
            <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf

            {{-- Hidden fields --}}
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ old('email', $email) }}">

            {{-- Email (read-only display) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                    Email Address
                </label>
                <div class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-400 bg-gray-100 cursor-not-allowed">
                    {{ old('email', $email) }}
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                    New Password
                </label>
                <input type="password" id="password" name="password"
                    placeholder="Min. 8 characters"
                    autocomplete="new-password"
                    required autofocus
                    class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 outline-none transition
                        focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white
                        @error('password') border-red-400 bg-red-50 @else @enderror">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                    Confirm New Password
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    placeholder="Repeat new password"
                    autocomplete="new-password"
                    required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-gray-50 outline-none transition
                        focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white">
            </div>

            {{-- Hint --}}
            <p class="text-xs text-gray-400 leading-relaxed">
                Use at least 8 characters with a mix of letters, numbers, and symbols for a strong password.
            </p>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg text-sm transition-all">
                Reset Password
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            <a href="{{ route('login') }}" class="text-[#849753] font-semibold hover:underline">Back to Sign In</a>
        </p>

    </div>
</body>
</html>