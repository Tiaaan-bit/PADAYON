<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padayon Massage Center - Create Account</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-[#D6BB9E] flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-8">


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
            <h1 class="text-2xl font-bold text-[#849753]">Padayon Massage Center</h1>
            <h1 class="text-2xl font-bold text-gray-800">Create Account</h1>
            <p class="text-sm text-gray-500 mt-1">Join us today — it's free</p>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div
                class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Name & Phone --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name"
                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Full Name
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        placeholder="Juan dela Cruz" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 outline-none transition
                            focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white
                            @error('name') border-red-400 bg-red-50 @else @enderror">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone"
                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                        placeholder="09XX XXX XXXX" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 outline-none transition
                            focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white
                            @error('phone') bg-red-50 @else border-gray-200 @enderror">
                    @error('phone')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                    Email Address
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    placeholder="you@example.com" autocomplete="email" required
                    class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 outline-none transition
                        focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white
                        @error('email') border-red-400 bg-red-50 @else @enderror">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password & Confirm --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password"
                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Password
                    </label>
                    <input type="password" id="password" name="password" placeholder="Min. 8 characters"
                        autocomplete="new-password" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 outline-none transition
                            focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white
                            @error('password') border-red-400 bg-red-50 @else @enderror">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation"
                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Confirm Password
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        placeholder="Repeat password" autocomplete="new-password" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-gray-50 outline-none transition
                            focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white">
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg text-sm transition-all">
                Create Account
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account?
            <a href="{{ route('login') }}" class="text-[#849753] font-semibold hover:underline">Sign in</a>
        </p>

    </div>
</body>

</html>
