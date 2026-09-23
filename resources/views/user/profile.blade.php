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


    {{-- Success Message --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif


    {{-- Error Message --}}
    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            {{ session('error') }}
        </div>
    @endif


    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            <p class="font-medium mb-1">Please check the following:</p>

            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT SIDE --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Profile Information --}}
            <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">

                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Profile Information
                </h2>

                <form
                    method="POST"
                    action="{{ route('user.profile.update') }}"
                    class="space-y-4"
                >
                    @csrf
                    @method('PUT')


                    {{-- Full Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]"
                        >

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

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]"
                        >

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

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]"
                        >

                        @error('phone')
                            <p class="text-sm text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Save --}}
                    <button
                        type="submit"
                        class="rounded-xl bg-[#849753] px-5 py-2.5 text-white font-medium hover:bg-[#6F4E37] transition"
                    >
                        Save Changes
                    </button>

                </form>

            </section>


            {{-- Security --}}
            <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">

                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Security
                </h2>

                <form
                    method="POST"
                    action="{{ route('user.profile.password') }}"
                    class="space-y-4"
                >
                    @csrf
                    @method('PUT')


                    {{-- Current Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Current Password
                        </label>

                        <input
                            type="password"
                            name="current_password"
                            class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]"
                        >

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

                        <input
                            type="password"
                            name="password"
                            class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]"
                        >

                        @error('password')
                            <p class="text-sm text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]"
                        >
                    </div>


                    {{-- Change Password --}}
                    <button
                        type="submit"
                        class="rounded-xl bg-red-600 px-5 py-2.5 text-white font-medium hover:bg-red-700 transition"
                    >
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
