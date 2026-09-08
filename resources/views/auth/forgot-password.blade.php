<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padayon Massage Center - Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#F4EDDB] flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-amber-100 rounded-2xl mb-3">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Forgot Password?</h1>
            <p class="text-sm text-gray-500 mt-1">No worries — we'll send you a reset link</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">
            Enter the email address linked to your account and we'll send you a password reset link.
        </p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                    Email Address
                </label>
                <input type="email" id="email" name="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    autocomplete="email"
                    required autofocus
                    class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 outline-none transition
                        focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white
                        @error('email') border-red-400 bg-red-50 @else @enderror">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg text-sm transition-all">
                Send Reset Link
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Remembered your password?
            <a href="{{ route('login') }}" class="text-[#849753] font-semibold hover:underline">Back to Sign In</a>
        </p>

    </div>
</body>
</html>