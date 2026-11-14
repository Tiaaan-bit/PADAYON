@extends('layouts.auth')

@section('title', 'Padayon Massage Center - Reset Password')

@section('content')

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8">

        {{-- Logo --}}
        <div class="text-center mb-8">

            <img src="{{ !empty($websiteSettings?->logo_path)
                ? asset('storage/' . $websiteSettings->logo_path)
                : asset('build/assets/images/logo.png') }}"
                alt="{{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}"
                class="h-40 w-auto mx-auto object-contain mb-4">

            <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
            <p class="text-sm text-gray-500 mt-1">Choose a strong new password</p>
        </div>

        {{-- Flash / Errors --}}
        @if ($errors->any())
            <div
                class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf

            {{-- Hidden fields --}}
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ old('email', $email) }}">

            {{-- Email --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                    Email Address
                </label>

                <div
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-400 bg-gray-100 cursor-not-allowed">
                    {{ old('email', $email) }}
                </div>

                @error('email')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div class="relative">
                <input type="password" id="password" name="password" placeholder="New Password" autocomplete="new-password"
                    required autofocus
                    class="w-full px-4 py-2.5 pr-12 rounded-lg border text-sm text-gray-800 outline-none transition
            focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white
            @error('password') border-red-400 bg-red-50 @else @enderror">

                <button type="button" onclick="togglePasswordVisibility('password', 'passwordEye')"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-[#849753] transition"
                    aria-label="Show password">
                    <svg id="passwordEye" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>

            </div>

            {{-- Password Requirements --}}
            <div id="passwordRequirements" class="hidden mt-3 space-y-2">

                <p class="text-xs font-semibold text-gray-600">
                    Password must contain:
                </p>

                <div class="grid grid-cols-2 gap-x-4 gap-y-1.5">

                    <p id="lengthRequirement" class="hidden text-xs text-gray-500">
                        • At least 8 characters
                    </p>

                    <p id="numberRequirement" class="hidden text-xs text-gray-500">
                        • At least one number
                    </p>

                    <p id="specialRequirement" class="hidden text-xs text-gray-500">
                        • At least one symbol
                    </p>

                    <p id="uppercaseRequirement" class="hidden text-xs text-gray-500">
                        • At least one uppercase letter
                    </p>

                </div>
            </div>


            {{-- Confirm Password --}}
            <div class="relative">
                <input type="password" id="password_confirmation" name="password_confirmation"
                    placeholder="Repeat new password" autocomplete="new-password" required
                    class="w-full px-4 py-2.5 pr-12 rounded-lg border border-gray-200 text-sm text-gray-800 bg-gray-50 outline-none transition
            focus:ring-2 focus:ring-[#849753] focus:border-[#849753] focus:bg-white">

                <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'confirmPasswordEye')"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-[#849753] transition"
                    aria-label="Show confirm password">
                    <svg id="confirmPasswordEye" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>

            </div>


            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg text-sm transition-all">
                Reset Password
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            <a href="{{ route('login') }}" class="text-[#849753] font-semibold hover:underline">
                Back to Sign In
            </a>
        </p>

    </div>

    {{-- Password Indicator Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const passwordInput = document.getElementById('password');
            const passwordRequirements = document.getElementById('passwordRequirements');

            if (!passwordInput) return;

            const lengthRequirement = document.getElementById('lengthRequirement');
            const numberRequirement = document.getElementById('numberRequirement');
            const specialRequirement = document.getElementById('specialRequirement');
            const uppercaseRequirement = document.getElementById('uppercaseRequirement');

            function updatePasswordRequirements() {

                const password = passwordInput.value;

                const requirements = [{
                        element: lengthRequirement,
                        passed: password.length >= 8
                    },
                    {
                        element: numberRequirement,
                        passed: /[0-9]/.test(password)
                    },
                    {
                        element: specialRequirement,
                        passed: /[^A-Za-z0-9]/.test(password)
                    },
                    {
                        element: uppercaseRequirement,
                        passed: /[A-Z]/.test(password)
                    }
                ];

                requirements.forEach(requirement => {

                    requirement.element.classList.toggle(
                        'hidden',
                        requirement.passed
                    );

                });

                const hasUnmetRequirement = requirements.some(
                    requirement => !requirement.passed
                );

                passwordRequirements.classList.toggle(
                    'hidden',
                    !password || !hasUnmetRequirement
                );
            }

            passwordInput.addEventListener('input', updatePasswordRequirements);

        });

        function togglePasswordVisibility(inputId, eyeId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeId);

            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            eyeIcon.innerHTML = isPassword ?
                `<path stroke-linecap="round" stroke-linejoin="round"
            d="M3 3l18 18M10.58 10.59a2 2 0 002.83 2.83M9.88 5.09A10.94 10.94 0 0112 4.88c6.25 0 9.75 7.12 9.75 7.12a16.5 16.5 0 01-3.12 3.88M6.61 6.61C3.79 8.37 2.25 12 2.25 12s3.5 7.12 9.75 7.12c1.18 0 2.26-.22 3.23-.6" />` :
                `<path stroke-linecap="round" stroke-linejoin="round"
            d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z" />
           <circle cx="12" cy="12" r="3" />`;
        }
    </script>

@endsection
