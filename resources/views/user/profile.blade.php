@extends('layouts.user')

@section('title', 'Padayon Massage Center - Profile')

@section('content')

    <div class="min-h-screen p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
        <div class="max-w-4xl mx-auto space-y-6">


            {{-- Header --}}
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    Profile Settings
                </h1>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT SIDE --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Profile Information --}}
                    <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">

                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            Profile Information
                        </h2>

                        <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4">
                            @csrf
                            @method('PUT')


                            {{-- Full Name --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Name
                                </label>

                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]">

                                @error('name')
                                    <p class="text-sm text-red-500 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email
                                </label>

                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]">

                                @error('email')
                                    <p class="text-sm text-red-500 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Phone --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Phone
                                </label>

                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                    class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]">

                                @error('phone')
                                    <p class="text-sm text-red-500 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>


                            {{-- Save --}}
                            <button type="submit"
                                class="rounded-xl bg-[#849753] px-5 py-2.5 text-white font-medium hover:bg-[#6F4E37] transition">
                                Save Changes
                            </button>

                        </form>

                    </section>


                    {{-- Security --}}

                    <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5" x-data="{
                        showCurrentPassword: false,
                        showNewPassword: false,
                        showConfirmPassword: false
                    }">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            Security
                        </h2>

                        <form method="POST" action="{{ route('user.profile.password') }}" class="space-y-4">
                            @csrf
                            @method('PUT')

                            {{-- Current Password --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Current Password
                                </label>

                                <div class="relative">
                                    <input :type="showCurrentPassword ? 'text' : 'password'" name="current_password"
                                        autocomplete="current-password"
                                        class="w-full rounded-xl border-gray-300 pr-12 focus:border-[#849753] focus:ring-[#849753]">

                                    <button type="button" @click="showCurrentPassword = !showCurrentPassword"
                                        :aria-label="showCurrentPassword ? 'Hide current password' : 'Show current password'"
                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-[#6F4E37]">
                                        <svg x-show="!showCurrentPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>

                                        <svg x-show="showCurrentPassword" x-cloak xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                                            class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 3l18 18M10.58 10.59a2 2 0 0 0 2.83 2.83M9.88 5.09A10.7 10.7 0 0 1 12 4.88c6.25 0 9.75 7.12 9.75 7.12a15.8 15.8 0 0 1-3.14 3.98M6.61 6.61C3.8 8.37 2.25 12 2.25 12s3.5 7.12 9.75 7.12c1.2 0 2.3-.22 3.3-.6" />
                                        </svg>
                                    </button>
                                </div>

                                @error('current_password')
                                    <p class="text-sm text-red-500 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- New Password --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    New Password
                                </label>

                                <div class="relative">
                                    <input :type="showNewPassword ? 'text' : 'password'" name="password"
                                        autocomplete="new-password"
                                        class="w-full rounded-xl border-gray-300 pr-12 focus:border-[#849753] focus:ring-[#849753]">

                                    <button type="button" @click="showNewPassword = !showNewPassword"
                                        :aria-label="showNewPassword ? 'Hide new password' : 'Show new password'"
                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-[#6F4E37]">
                                        <svg x-show="!showNewPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>

                                        <svg x-show="showNewPassword" x-cloak xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                                            class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 3l18 18M10.58 10.59a2 2 0 0 0 2.83 2.83M9.88 5.09A10.7 10.7 0 0 1 12 4.88c6.25 0 9.75 7.12 9.75 7.12a15.8 15.8 0 0 1-3.14 3.98M6.61 6.61C3.8 8.37 2.25 12 2.25 12s3.5 7.12 9.75 7.12c1.2 0 2.3-.22 3.3-.6" />
                                        </svg>
                                    </button>
                                </div>

                                @error('password')
                                    <p class="text-sm text-red-500 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Confirm New Password --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirm New Password
                                </label>

                                <div class="relative">
                                    <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation"
                                        autocomplete="new-password"
                                        class="w-full rounded-xl border-gray-300 pr-12 focus:border-[#849753] focus:ring-[#849753]">

                                    <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                        :aria-label="showConfirmPassword ? 'Hide confirm password' : 'Show confirm password'"
                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-[#6F4E37]">
                                        <svg x-show="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                                            class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>

                                        <svg x-show="showConfirmPassword" x-cloak xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                                            class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 3l18 18M10.58 10.59a2 2 0 0 0 2.83 2.83M9.88 5.09A10.7 10.7 0 0 1 12 4.88c6.25 0 9.75 7.12 9.75 7.12a15.8 15.8 0 0 1-3.14 3.98M6.61 6.61C3.8 8.37 2.25 12 2.25 12s3.5 7.12 9.75 7.12c1.2 0 2.3-.22 3.3-.6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Change Password --}}
                            <button type="submit"
                                class="rounded-xl bg-red-600 px-5 py-2.5 text-white font-medium hover:bg-red-700 transition">
                                Change Password
                            </button>
                        </form>
                    </section>

                </div>


                {{-- RIGHT SIDE --}}
                <aside class="space-y-6">

                    {{-- Account Information --}}
                    <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">

                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            Account Information
                        </h2>

                        <div class="space-y-3 text-sm text-gray-600">

                            {{-- Member Since --}}
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-gray-500">
                                    Member Since
                                </span>

                                <span class="font-medium text-gray-800">
                                    {{ $user->created_at->format('F d, Y') }}
                                </span>
                            </div>


                            {{-- Email Status --}}
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-gray-500">
                                    Email Status
                                </span>

                                @if ($user->email_verified_at)
                                    <span class="font-medium text-green-600">
                                        Verified
                                    </span>
                                @else
                                    <span class="font-medium text-yellow-600">
                                        Not Verified
                                    </span>
                                @endif
                            </div>


                            {{-- Account Status --}}
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-gray-500">
                                    Account Status
                                </span>

                                <span class="font-medium text-gray-800 capitalize">
                                    {{ $user->status ?? 'Active' }}
                                </span>
                            </div>

                        </div>

                    </section>

                </aside>

            </div>

        </div>

    </div>

@endsection
