@extends('layouts.admin')

@section('content')

<div
    x-data="quickBook()"
    x-init="init()"
    class="min-h-screen bg-[#F4EDDB] px-4 py-6 sm:px-6 lg:px-8"
>
    <div class="mx-auto max-w-7xl">

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <div class="mb-2 inline-flex items-center rounded-full bg-[#849753]/10 px-3 py-1">
                        <span class="text-xs font-bold uppercase tracking-wide text-[#849753]">
                            Walk-in Booking
                        </span>
                    </div>

                    <h1 class="text-2xl font-bold text-[#2F2420] sm:text-3xl">
                        Quick Book
                    </h1>

                    <p class="mt-1 text-sm text-gray-600">
                        Create a walk-in appointment for a customer.
                    </p>
                </div>

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-[#2F2420] shadow-sm transition hover:bg-gray-50"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>

                    Back to Dashboard
                </a>

            </div>
        </div>

        {{-- =========================================================
            SUCCESS
        ========================================================== --}}
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 shadow-sm">
                <div class="flex items-start gap-3">

                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-green-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-green-700">
                            Success
                        </p>

                        <p class="mt-1 text-sm text-green-700">
                            {{ session('success') }}
                        </p>
                    </div>

                </div>
            </div>
        @endif

        {{-- =========================================================
            SESSION ERROR
        ========================================================== --}}
        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 shadow-sm">
                <div class="flex items-start gap-3">

                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-red-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-red-700">
                            Unable to create appointment
                        </p>

                        <p class="mt-1 text-sm text-red-600">
                            {{ session('error') }}
                        </p>
                    </div>

                </div>
            </div>
        @endif

        {{-- =========================================================
            VALIDATION ERRORS
        ========================================================== --}}
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 shadow-sm">
                <div class="flex items-start gap-3">

                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-red-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v4m0 4h.01M10.29 3.86l-7.82 14a2 2 0 001.74 2.64h15.58a2 2 0 001.74-2.64l-7.82-14a2 2 0 00-3.42 0z"
                            />
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-red-700">
                            Please fix the following:
                        </p>

                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        @endif

        {{-- =========================================================
            FORM
        ========================================================== --}}
        <form
            method="POST"
            action="{{ route('admin.quick-book.store') }}"
            @submit="submitting = true"
        >
            @csrf

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                {{-- =================================================
                    LEFT / MAIN FORM
                ================================================== --}}
                <div class="space-y-6 xl:col-span-2">

                    {{-- =================================================
                        CUSTOMER INFORMATION
                    ================================================== --}}
                    <div class="rounded-2xl bg-white p-6 shadow-sm">

                        <div class="mb-6 flex items-start gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#849753]/10">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-[#849753]"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-[#2F2420]">
                                    Customer Information
                                </h2>

                                <p class="mt-1 text-sm text-gray-500">
                                    Enter the walk-in customer's information.
                                </p>
                            </div>

                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            {{-- Customer Name --}}
                            <div class="md:col-span-2">

                                <label
                                    for="customer_name"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Customer Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="customer_name"
                                    name="customer_name"
                                    value="{{ old('customer_name') }}"
                                    required
                                    maxlength="255"
                                    placeholder="Enter customer name"
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                                >

                                @error('customer_name')
                                    <p class="mt-1 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Phone --}}
                            <div>

                                <label
                                    for="phone"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Phone Number
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    required
                                    maxlength="20"
                                    placeholder="09XXXXXXXXX"
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                                >

                                @error('phone')
                                    <p class="mt-1 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Email --}}
                            <div>

                                <label
                                    for="email"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Email
                                    <span class="text-xs font-normal text-gray-400">
                                        (optional)
                                    </span>
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    maxlength="255"
                                    placeholder="customer@example.com"
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                                >

                                @error('email')
                                    <p class="mt-1 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror

                                <p class="mt-1 text-xs text-gray-400">
                                    If provided, a registration invitation will be emailed.
                                </p>

                            </div>

                        </div>
                    </div>

                    {{-- =================================================
                        APPOINTMENT DETAILS
                    ================================================== --}}
                    <div class="rounded-2xl bg-white p-6 shadow-sm">

                        <div class="mb-6 flex items-start gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#849753]/10">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-[#849753]"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-[#2F2420]">
                                    Appointment Details
                                </h2>

                                <p class="mt-1 text-sm text-gray-500">
                                    Select the service, massage level, add-on, and therapist.
                                </p>
                            </div>

                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            {{-- Service --}}
                            <div>

                                <label
                                    for="service_id"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Service
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="service_id"
                                    name="service_id"
                                    x-model="serviceId"
                                    @change="serviceChanged()"
                                    required
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                                >
                                    <option value="">
                                        Select a service
                                    </option>

                                    @foreach ($services as $service)
                                        <option
                                            value="{{ $service->id }}"
                                            data-price="{{ $service->price }}"
                                            data-duration="{{ $service->duration_minutes }}"
                                        >
                                            {{ $service->name }}

                                            @if (!empty($service->type))
                                                — {{ $service->type }}
                                            @endif

                                            ({{ $service->duration_minutes }} min —
                                            ₱{{ number_format((float) $service->price, 2) }})
                                        </option>
                                    @endforeach
                                </select>

                                @error('service_id')
                                    <p class="mt-1 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Level --}}
                            <div>

                                <label
                                    for="level"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Massage Level
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="level"
                                    name="level"
                                    required
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                                >
                                    <option value="">
                                        Select level
                                    </option>

                                    <option
                                        value="gentle"
                                        {{ old('level') === 'gentle' ? 'selected' : '' }}
                                    >
                                        Gentle
                                    </option>

                                    <option
                                        value="mild"
                                        {{ old('level') === 'mild' ? 'selected' : '' }}
                                    >
                                        Mild
                                    </option>

                                    <option
                                        value="hard"
                                        {{ old('level') === 'hard' ? 'selected' : '' }}
                                    >
                                        Hard
                                    </option>
                                </select>

                                @error('level')
                                    <p class="mt-1 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Add-on --}}
                            <div>

                                <label
                                    for="add_on_id"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Add-on
                                </label>

                                <select
                                    id="add_on_id"
                                    name="add_on_id"
                                    x-model="addOnId"
                                    @change="addOnChanged()"
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                                >
                                    <option value="">
                                        No add-on
                                    </option>

                                    @foreach ($addOns as $addOn)
                                        <option
                                            value="{{ $addOn->id }}"
                                            data-price="{{ $addOn->price }}"
                                            data-duration="{{ $addOn->duration_minutes }}"
                                        >
                                            {{ $addOn->name }}

                                            @if ($addOn->duration_minutes !== null)
                                                ({{ $addOn->duration_minutes }} min —
                                            @endif

                                            ₱{{ number_format((float) $addOn->price, 2) }}

                                            @if ($addOn->duration_minutes !== null)
                                                )
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                @error('add_on_id')
                                    <p class="mt-1 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Previous Operations --}}
                            <div>

                                <label
                                    for="has_previous_operations"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Previous Operations?
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="has_previous_operations"
                                    name="has_previous_operations"
                                    required
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                                >
                                    <option value="">
                                        Select an option
                                    </option>

                                    <option
                                        value="yes"
                                        {{ old('has_previous_operations') === 'yes' ? 'selected' : '' }}
                                    >
                                        Yes
                                    </option>

                                    <option
                                        value="no"
                                        {{ old('has_previous_operations') === 'no' ? 'selected' : '' }}
                                    >
                                        No
                                    </option>
                                </select>

                                @error('has_previous_operations')
                                    <p class="mt-1 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Therapist --}}
                            <div class="md:col-span-2">

                                <label
                                    for="therapist_id"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Therapist
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="therapist_id"
                                    name="therapist_id"
                                    x-model="therapistId"
                                    @change="loadSlots()"
                                    required
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                                >
                                    <option value="">
                                        Select a therapist
                                    </option>

                                    @foreach ($therapists as $therapist)
                                        <option value="{{ $therapist->id }}">
                                            {{ $therapist->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('therapist_id')
                                    <p class="mt-1 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Body Problem --}}
                            <div class="md:col-span-2">

                                <label
                                    for="body_problem"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Body Problem / Notes
                                    <span class="text-xs font-normal text-gray-400">
                                        (optional)
                                    </span>
                                </label>

                                <textarea
                                    id="body_problem"
                                    name="body_problem"
                                    rows="4"
                                    maxlength="2000"
                                    placeholder="Enter any body problem, pain area, or important notes..."
                                    class="w-full resize-none rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                                >{{ old('body_problem') }}</textarea>

                                @error('body_problem')
                                    <p class="mt-1 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>
                    </div>

                    {{-- =================================================
                        SCHEDULE
                    ================================================== --}}
                    <div class="rounded-2xl bg-white p-6 shadow-sm">

                        <div class="mb-6 flex items-start gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#849753]/10">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-[#849753]"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-[#2F2420]">
                                    Schedule
                                </h2>

                                <p class="mt-1 text-sm text-gray-500">
                                    Select the appointment date and available time.
                                </p>
                            </div>

                        </div>

                        {{-- Date --}}
                        <div class="mb-6">

                            <label
                                for="appointment_date"
                                class="mb-2 block text-sm font-semibold text-gray-700"
                            >
                                Appointment Date
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                id="appointment_date"
                                name="appointment_date"
                                x-model="appointmentDate"
                                @change="loadSlots()"
                                min="{{ now()->timezone('Asia/Manila')->format('Y-m-d') }}"
                                required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#849753] focus:ring-2 focus:ring-[#849753]/20"
                            >

                            @error('appointment_date')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        {{-- Duration information --}}
                        <div
                            x-show="totalDuration > 0"
                            x-cloak
                            class="mb-6 rounded-xl border border-[#849753]/20 bg-[#849753]/5 px-4 py-4"
                        >
                            <div class="flex items-center justify-between gap-4">

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-[#849753]">
                                        Appointment Duration
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-[#2F2420]">
                                        <span x-text="totalDuration"></span> minutes
                                    </p>
                                </div>

                                <div class="text-right">

                                    <p class="text-xs text-gray-400">
                                        Total
                                    </p>

                                    <p class="text-base font-bold text-[#849753]">
                                        ₱<span x-text="totalAmount.toFixed(2)"></span>
                                    </p>

                                </div>

                            </div>
                        </div>

                        {{-- Loading --}}
                        <div
                            x-show="loadingSlots"
                            x-cloak
                            class="rounded-xl border border-[#849753]/20 bg-[#849753]/5 px-4 py-4"
                        >
                            <div class="flex items-center gap-3">

                                <div class="h-5 w-5 animate-spin rounded-full border-2 border-[#849753]/30 border-t-[#849753]"></div>

                                <p class="text-sm text-gray-600">
                                    Checking therapist availability...
                                </p>

                            </div>
                        </div>

                        {{-- Error --}}
                        <div
                            x-show="slotError"
                            x-cloak
                            class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3"
                        >
                            <p
                                class="text-sm text-red-600"
                                x-text="slotError"
                            ></p>
                        </div>

                        {{-- No slots --}}
                        <div
                            x-show="!loadingSlots && slotsLoaded && availableSlots.length === 0"
                            x-cloak
                            class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-6 text-center"
                        >

                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>

                            <p class="mt-3 text-sm font-semibold text-gray-700">
                                No available times
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                This therapist has no available appointment times
                                for the selected date.
                            </p>

                        </div>

                        {{-- Slots --}}
                        <div
                            x-show="!loadingSlots && slotsLoaded"
                            x-cloak
                        >

                            {{-- Early morning --}}
                            <div class="mb-7">

                                <div class="mb-3 flex items-center justify-between">

                                    <div>
                                        <h3 class="text-sm font-bold text-[#2F2420]">
                                            12:00 AM – 1:00 AM
                                        </h3>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Early morning schedule
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-[#849753]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-[#849753]">
                                        Early
                                    </span>

                                </div>

                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">

                                    <template
                                        x-for="slot in earlySlots"
                                        :key="slot.start"
                                    >

                                        <button
                                            type="button"
                                            @click="selectSlot(slot)"
                                            :disabled="slot.status !== 'available'"
                                            :class="{
                                                'border-[#849753] bg-[#849753] text-white shadow-sm':
                                                    selectedTime === slot.time &&
                                                    slot.status === 'available',

                                                'border-gray-200 bg-white text-gray-700 hover:border-[#849753] hover:bg-[#849753]/5':
                                                    selectedTime !== slot.time &&
                                                    slot.status === 'available',

                                                'cursor-not-allowed border-red-200 bg-red-50 text-red-400':
                                                    slot.status === 'booked'
                                            }"
                                            class="rounded-xl border px-3 py-3 text-center text-sm font-semibold transition"
                                        >

                                            <span x-text="slot.label"></span>

                                            <span
                                                x-show="slot.status === 'booked'"
                                                class="mt-1 block text-[10px] font-medium"
                                            >
                                                Booked
                                            </span>

                                        </button>

                                    </template>

                                </div>
                            </div>

                            {{-- Main schedule --}}
                            <div>

                                <div class="mb-3 flex items-center justify-between">

                                    <div>
                                        <h3 class="text-sm font-bold text-[#2F2420]">
                                            1:00 PM – 12:00 AM
                                        </h3>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Main operating schedule
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-[#849753]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-[#849753]">
                                        Main
                                    </span>

                                </div>

                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">

                                    <template
                                        x-for="slot in mainSlots"
                                        :key="slot.start"
                                    >

                                        <button
                                            type="button"
                                            @click="selectSlot(slot)"
                                            :disabled="slot.status !== 'available'"
                                            :class="{
                                                'border-[#849753] bg-[#849753] text-white shadow-sm':
                                                    selectedTime === slot.time &&
                                                    slot.status === 'available',

                                                'border-gray-200 bg-white text-gray-700 hover:border-[#849753] hover:bg-[#849753]/5':
                                                    selectedTime !== slot.time &&
                                                    slot.status === 'available',

                                                'cursor-not-allowed border-red-200 bg-red-50 text-red-400':
                                                    slot.status === 'booked'
                                            }"
                                            class="rounded-xl border px-3 py-3 text-center text-sm font-semibold transition"
                                        >

                                            <span x-text="slot.label"></span>

                                            <span
                                                x-show="slot.status === 'booked'"
                                                class="mt-1 block text-[10px] font-medium"
                                            >
                                                Booked
                                            </span>

                                        </button>

                                    </template>

                                </div>
                            </div>
                        </div>

                        {{-- Hidden time --}}
                        <input
                            type="hidden"
                            name="appointment_time"
                            x-model="selectedTime"
                        >

                        {{-- Selected appointment --}}
                        <div
                            x-show="selectedTime"
                            x-cloak
                            class="mt-6 rounded-xl border border-[#849753]/30 bg-[#849753]/10 px-4 py-4"
                        >
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#849753]">
                                Selected Appointment
                            </p>

                            <p
                                class="mt-1 text-sm font-bold text-[#2F2420]"
                                x-text="selectedLabel"
                            ></p>
                        </div>

                    </div>

                    {{-- =================================================
                        PAYMENT
                    ================================================== --}}
                    <div class="rounded-2xl bg-white p-6 shadow-sm">

                        <div class="mb-6 flex items-start gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#849753]/10">
                                <span class="text-lg font-bold text-[#849753]">
                                    ₱
                                </span>
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-[#2F2420]">
                                    Payment
                                </h2>

                                <p class="mt-1 text-sm text-gray-500">
                                    Quick Book appointments use Pay at Counter.
                                </p>
                            </div>

                        </div>

                        {{-- Payment method --}}
                        <div class="rounded-xl border border-[#849753]/20 bg-[#849753]/5 px-4 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#849753]/15">
                                    <span class="text-lg font-bold text-[#849753]">
                                        ₱
                                    </span>
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-[#2F2420]">
                                        Pay at Counter
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Payment is handled at the branch.
                                    </p>
                                </div>

                            </div>

                        </div>

                        {{-- =================================================
                            PAYMENT STATUS
                            PAID ONLY
                        ================================================== --}}
                        <div class="mt-5">

                            <label class="mb-3 block text-sm font-semibold text-gray-700">
                                Payment Status
                            </label>

                            <div class="rounded-xl border border-[#849753]/30 bg-[#849753]/10 px-4 py-4">

                                <div class="flex items-start gap-3">

                                    <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#849753]">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-3.5 w-3.5 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="3"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                    </div>

                                    <div>

                                        <p class="text-sm font-bold text-[#2F2420]">
                                            Paid
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            Customer has already paid at the counter.
                                        </p>

                                    </div>

                                </div>

                            </div>

                            {{-- Always submit paid --}}
                            <input
                                type="hidden"
                                name="payment_status"
                                value="paid"
                            >

                        </div>

                    </div>

                </div>

                {{-- =================================================
                    RIGHT / SUMMARY
                ================================================== --}}
                <div class="xl:col-span-1">

                    <div class="sticky top-6 rounded-2xl bg-white p-6 shadow-sm">

                        <div class="flex items-center justify-between">

                            <h2 class="text-lg font-bold text-[#2F2420]">
                                Appointment Summary
                            </h2>

                            <span class="rounded-full bg-[#849753]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-[#849753]">
                                Quick Book
                            </span>

                        </div>

                        <div class="mt-6 space-y-4">

                            {{-- Customer --}}
                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Customer
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-gray-800"
                                    x-text="customerName || 'Not provided'"
                                ></p>

                            </div>

                            {{-- Service --}}
                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Service
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-gray-800"
                                    x-text="serviceName || 'Not selected'"
                                ></p>

                            </div>

                            {{-- Add-on --}}
                            <div x-show="addOnId">

                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Add-on
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-gray-800"
                                    x-text="addOnName"
                                ></p>

                            </div>

                            {{-- Therapist --}}
                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Therapist
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-gray-800"
                                    x-text="therapistName || 'Not selected'"
                                ></p>

                            </div>

                            {{-- Date --}}
                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Date
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-gray-800"
                                    x-text="formattedDate || 'Not selected'"
                                ></p>

                            </div>

                            {{-- Time --}}
                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Time
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-gray-800"
                                    x-text="selectedLabel || 'Not selected'"
                                ></p>

                            </div>

                            {{-- Duration --}}
                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Duration
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-gray-800"
                                    x-text="totalDuration + ' minutes'"
                                ></p>

                            </div>

                        </div>

                        <div class="my-6 border-t border-gray-100"></div>

                        {{-- Pricing --}}
                        <div class="space-y-3">

                            <div class="flex items-center justify-between text-sm">

                                <span class="text-gray-500">
                                    Service
                                </span>

                                <span class="font-medium text-gray-800">
                                    ₱<span x-text="servicePrice.toFixed(2)"></span>
                                </span>

                            </div>

                            <div
                                x-show="addOnPrice > 0"
                                class="flex items-center justify-between text-sm"
                            >

                                <span class="text-gray-500">
                                    Add-on
                                </span>

                                <span class="font-medium text-gray-800">
                                    ₱<span x-text="addOnPrice.toFixed(2)"></span>
                                </span>

                            </div>

                            <div class="flex items-center justify-between border-t border-gray-100 pt-3">

                                <span class="text-base font-bold text-[#2F2420]">
                                    Total
                                </span>

                                <span class="text-xl font-bold text-[#849753]">
                                    ₱<span x-text="totalAmount.toFixed(2)"></span>
                                </span>

                            </div>

                        </div>

                        {{-- Payment summary --}}
                        <div class="mt-5 rounded-xl bg-gray-50 px-4 py-3">

                            <div class="flex items-center justify-between">

                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Payment
                                </span>

                                <span
                                    class="text-xs font-bold uppercase text-green-600"
                                    x-text="paymentStatus"
                                ></span>

                            </div>

                        </div>

                        {{-- Submit --}}
                        <button
                            type="submit"
                            :disabled="
                                submitting ||
                                !serviceId ||
                                !therapistId ||
                                !appointmentDate ||
                                !selectedTime
                            "
                            class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-[#849753] px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#748645] disabled:cursor-not-allowed disabled:opacity-50"
                        >

                            <template x-if="!submitting">

                                <span class="flex items-center gap-2">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 4v16m8-8H4"
                                        />
                                    </svg>

                                    Create Walk-in Appointment

                                </span>

                            </template>

                            <template x-if="submitting">

                                <span class="flex items-center gap-2">

                                    <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>

                                    Creating Appointment...

                                </span>

                            </template>

                        </button>

                        <p class="mt-3 text-center text-xs leading-5 text-gray-400">
                            The appointment will be recorded as a walk-in
                            appointment and assigned to the selected therapist.
                        </p>

                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

{{-- ============================================================
     ALPINE COMPONENT
============================================================= --}}
<script>
    function quickBook() {
        return {
            serviceId: @js(old('service_id', '')),
            addOnId: @js(old('add_on_id', '')),
            therapistId: @js(old('therapist_id', '')),

            appointmentDate: @js(
                old(
                    'appointment_date',
                    now()
                        ->timezone('Asia/Manila')
                        ->format('Y-m-d')
                )
            ),

            selectedTime: @js(
                old('appointment_time', '')
            ),

            /*
            |--------------------------------------------------------------------------
            | Quick Book is PAID ONLY
            |--------------------------------------------------------------------------
            */
            paymentStatus: @js(
                old('payment_status', 'paid')
            ),

            customerName: @js(
                old('customer_name', '')
            ),

            slots: [],
            loadingSlots: false,
            slotsLoaded: false,
            slotError: '',
            submitting: false,

            servicePrice: 0,
            serviceDuration: 0,
            serviceName: '',

            addOnPrice: 0,
            addOnDuration: 0,
            addOnName: '',

            therapistName: '',

            totalAmount: 0,
            totalDuration: 0,

            init() {
                this.updateCustomerName();

                const customerInput =
                    document.getElementById(
                        'customer_name'
                    );

                if (customerInput) {
                    customerInput.addEventListener(
                        'input',
                        () => {
                            this.customerName =
                                customerInput.value;
                        }
                    );
                }

                this.updateService();
                this.updateAddOn();
                this.updateTherapist();
                this.updateTotals();

                if (
                    this.serviceId &&
                    this.therapistId &&
                    this.appointmentDate
                ) {
                    this.loadSlots();
                }
            },

            serviceChanged() {
                this.selectedTime = '';

                this.updateService();
                this.updateTotals();
                this.loadSlots();
            },

            addOnChanged() {
                this.selectedTime = '';

                this.updateAddOn();
                this.updateTotals();
                this.loadSlots();
            },

            updateCustomerName() {
                const input =
                    document.getElementById(
                        'customer_name'
                    );

                this.customerName =
                    input?.value || '';
            },

            updateService() {
                const select =
                    document.getElementById(
                        'service_id'
                    );

                if (!select) {
                    return;
                }

                const option =
                    select.options[
                        select.selectedIndex
                    ];

                if (
                    !option ||
                    !option.value
                ) {
                    this.servicePrice = 0;
                    this.serviceDuration = 0;
                    this.serviceName = '';

                    this.updateTotals();

                    return;
                }

                this.serviceId =
                    option.value;

                this.servicePrice =
                    parseFloat(
                        option.dataset.price || 0
                    );

                /*
                |--------------------------------------------------------------------------
                | IMPORTANT:
                | duration comes from data-duration which now
                | comes from duration_minutes.
                |--------------------------------------------------------------------------
                */
                this.serviceDuration =
                    parseInt(
                        option.dataset.duration || 0,
                        10
                    );

                this.serviceName =
                    option.textContent.trim();

                this.updateTotals();
            },

            updateAddOn() {
                const select =
                    document.getElementById(
                        'add_on_id'
                    );

                if (!select) {
                    return;
                }

                const option =
                    select.options[
                        select.selectedIndex
                    ];

                if (
                    !option ||
                    !option.value
                ) {
                    this.addOnPrice = 0;
                    this.addOnDuration = 0;
                    this.addOnName = '';

                    this.updateTotals();

                    return;
                }

                this.addOnId =
                    option.value;

                this.addOnPrice =
                    parseFloat(
                        option.dataset.price || 0
                    );

                this.addOnDuration =
                    parseInt(
                        option.dataset.duration || 0,
                        10
                    );

                this.addOnName =
                    option.textContent.trim();

                this.updateTotals();
            },

            updateTherapist() {
                const select =
                    document.getElementById(
                        'therapist_id'
                    );

                if (!select) {
                    return;
                }

                const option =
                    select.options[
                        select.selectedIndex
                    ];

                if (
                    !option ||
                    !option.value
                ) {
                    this.therapistName = '';
                    return;
                }

                this.therapistId =
                    option.value;

                this.therapistName =
                    option.textContent.trim();
            },

            updateTotals() {
                this.totalAmount =
                    this.servicePrice +
                    this.addOnPrice;

                this.totalDuration =
                    this.serviceDuration +
                    this.addOnDuration;
            },

            async loadSlots() {
                this.updateService();
                this.updateAddOn();
                this.updateTherapist();

                this.selectedTime = '';
                this.slots = [];
                this.slotsLoaded = false;
                this.slotError = '';

                if (
                    !this.serviceId ||
                    !this.therapistId ||
                    !this.appointmentDate
                ) {
                    return;
                }

                this.loadingSlots = true;

                try {
                    const params =
                        new URLSearchParams({
                            therapist_id:
                                this.therapistId,

                            service_id:
                                this.serviceId,

                            appointment_date:
                                this.appointmentDate,
                        });

                    if (this.addOnId) {
                        params.set(
                            'add_on_id',
                            this.addOnId
                        );
                    }

                    const response =
                        await fetch(
                            `{{ route('admin.quick-book.available-slots') }}?${params.toString()}`,
                            {
                                headers: {
                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },
                            }
                        );

                    const data =
                        await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message ||
                            'Unable to load available times.'
                        );
                    }

                    this.slots =
                        Array.isArray(data.slots)
                            ? data.slots
                            : [];

                    this.slotsLoaded = true;

                } catch (error) {
                    console.error(error);

                    this.slotError =
                        error.message ||
                        'Unable to load available times.';

                    this.slotsLoaded = true;

                } finally {
                    this.loadingSlots = false;
                }
            },

            selectSlot(slot) {
                if (
                    !slot ||
                    slot.status !== 'available'
                ) {
                    return;
                }

                this.selectedTime =
                    slot.time;
            },

            /*
            |--------------------------------------------------------------------------
            | 12:00 AM - 1:00 AM
            |--------------------------------------------------------------------------
            */
            get earlySlots() {
                return this.slots.filter(
                    slot => {
                        const hour =
                            parseInt(
                                slot.time
                                    .split(':')[0],
                                10
                            );

                        return hour >= 0 &&
                            hour < 1;
                    }
                );
            },

            /*
            |--------------------------------------------------------------------------
            | 1:00 PM - 12:00 AM
            |--------------------------------------------------------------------------
            */
            get mainSlots() {
                return this.slots.filter(
                    slot => {
                        const hour =
                            parseInt(
                                slot.time
                                    .split(':')[0],
                                10
                            );

                        return hour >= 13;
                    }
                );
            },

            get availableSlots() {
                return this.slots;
            },

            get selectedLabel() {
                if (!this.selectedTime) {
                    return '';
                }

                const slot =
                    this.slots.find(
                        item =>
                            item.time ===
                            this.selectedTime
                    );

                return slot
                    ? slot.label
                    : this.selectedTime;
            },

            get formattedDate() {
                if (!this.appointmentDate) {
                    return '';
                }

                const date =
                    new Date(
                        this.appointmentDate +
                        'T00:00:00'
                    );

                return date.toLocaleDateString(
                    'en-PH',
                    {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                    }
                );
            },
        };
    }
</script>

@endsection