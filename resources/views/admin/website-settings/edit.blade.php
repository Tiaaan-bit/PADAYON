@extends('layouts.admin')

@section('title', 'Website Settings')

@section('content')
    <div class="min-h-screen space-y-6 p-4 sm:p-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Website Settings
            </h1>
        </div>

        <form action="{{ route('admin.website-settings.update') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">

            @csrf
            @method('PUT')

            {{-- WEBSITE IDENTITY --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-gray-800">
                    Website Identity
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Change your website name and logo displayed across the website.
                </p>

                <div class="mt-5 space-y-5">
                    {{-- Website Name --}}
                    <div>
                        <label for="website_name" class="mb-2 block text-sm font-medium text-gray-700">
                            Website Name
                        </label>

                        <input id="website_name" name="website_name" type="text"
                            value="{{ old('website_name', $settings->website_name) }}" required maxlength="255"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20">

                        @error('website_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Website Name --}}
                    <div>
                        <label for="website_name" class="mb-2 block text-sm font-medium text-gray-700">
                            Website SubName
                        </label>

                        <input id="website_subname" name="website_subname" type="text"
                            value="{{ old('website_name', $settings->website_subname) }}" required maxlength="255"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20">

                        @error('website_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Current Logo --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Current Website Logo
                        </label>

                        @if ($settings->logo_path)
                            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Current website logo"
                                class="h-20 w-20 rounded-xl border border-gray-200 bg-white object-contain p-2">
                        @else
                            <img src="{{ asset('build/assets/images/logo.png') }}" alt="Default website logo"
                                class="h-20 w-20 rounded-xl border border-gray-200 bg-white object-contain p-2">

                            <p class="mt-2 text-xs text-gray-500">
                                Using the default logo.
                            </p>
                        @endif
                    </div>

                    {{-- Upload Logo --}}
                    <div>
                        <label for="logo" class="mb-2 block text-sm font-medium text-gray-700">
                            Upload New Logo
                        </label>

                        <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp"
                            class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-[#849753] file:px-4 file:py-2 file:font-medium file:text-white hover:file:bg-[#718344]">

                        <p class="mt-1 text-xs text-gray-500">
                            Accepted formats: JPG, JPEG, PNG, and WebP. Maximum size: 2 MB.
                            Leave empty to keep the current logo.
                        </p>

                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- FACEBOOK PAGE --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-gray-800">
                    Facebook Page
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Set the Facebook page name and link.
                </p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="facebook_name" class="mb-2 block text-sm font-medium text-gray-700">
                            Facebook Display Name
                        </label>

                        <input id="facebook_name" name="facebook_name" type="text"
                            value="{{ old('facebook_name', $settings->facebook_name) }}" required maxlength="255"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20">
                    </div>

                    <div>
                        <label for="facebook_url" class="mb-2 block text-sm font-medium text-gray-700">
                            Facebook URL
                        </label>

                        <input id="facebook_url" name="facebook_url" type="url"
                            value="{{ old('facebook_url', $settings->facebook_url) }}" required maxlength="2048"
                            placeholder="https://www.facebook.com/..."
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20">
                    </div>
                </div>
            </section>

            {{-- LOCATION --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-gray-800">
                    Location
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Set the business address and the map link customers can open.
                </p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="business_address" class="mb-2 block text-sm font-medium text-gray-700">
                            Business Address
                        </label>

                        <textarea id="business_address" name="business_address" rows="3" required maxlength="1000"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20">{{ old('business_address', $settings->business_address) }}</textarea>
                    </div>

                    <div>
                        <label for="location_url" class="mb-2 block text-sm font-medium text-gray-700">
                            Location URL
                        </label>

                        <input id="location_url" name="location_url" type="url"
                            value="{{ old('location_url', $settings->location_url) }}" maxlength="2048"
                            placeholder="https://maps.google.com/..."
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20">

                        <p class="mt-1 text-xs text-gray-500">
                            Paste the Google Maps link for your business.
                        </p>
                    </div>
                </div>
            </section>

            {{-- PHONE --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-gray-800">
                    Contact Phone
                </h2>

                <div class="mt-5">
                    <label for="phone_number" class="mb-2 block text-sm font-medium text-gray-700">
                        Phone Number
                    </label>

                    <input id="phone_number" name="phone_number" type="tel"
                        value="{{ old('phone_number', $settings->phone_number) }}" required maxlength="30"
                        placeholder="09XX XXX XXXX"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20">
                </div>
            </section>

            {{-- WORKING HOURS --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-gray-800">
                    Working Hours
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Set the daily opening and closing times.
                </p>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="opening_time" class="mb-2 block text-sm font-medium text-gray-700">
                            Opening Time
                        </label>

                        <input id="opening_time" name="opening_time" type="time"
                            value="{{ old('opening_time', substr($settings->opening_time, 0, 5)) }}" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20">
                    </div>

                    <div>
                        <label for="closing_time" class="mb-2 block text-sm font-medium text-gray-700">
                            Closing Time
                        </label>

                        <input id="closing_time" name="closing_time" type="time"
                            value="{{ old('closing_time', substr($settings->closing_time, 0, 5)) }}" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-[#849753] focus:outline-none focus:ring-2 focus:ring-[#849753]/20">
                    </div>
                </div>
            </section>

            {{-- SAVE --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="rounded-xl bg-[#849753] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#718344] focus:outline-none focus:ring-2 focus:ring-[#849753] focus:ring-offset-2">
                    Save Website Settings
                </button>
            </div>
        </form>
    </div>
@endsection
