@extends('layouts.user')

@section('title', 'Padayon Massage Center - Book Appointment')

@section('content')

    @php
        $servicesData = $services
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'duration_minutes' => $service->duration_minutes,
                    'price' => $service->price,
                ];
            })
            ->values();

        $therapistsData = $therapists
            ->map(function ($therapist) {
                return [
                    'id' => $therapist->id,
                    'name' => $therapist->name,
                ];
            })
            ->values();

        $addOnsData = $addOns
            ->map(function ($addOn) {
                return [
                    'id' => $addOn->id,
                    'name' => $addOn->name,
                    'price' => $addOn->price,
                    'duration_minutes' => $addOn->duration_minutes,
                ];
            })
            ->values();
    @endphp

    <div class="px-4 sm:px-6 lg:px-8 py-6">

        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Book an Appointment
            </h1>

            <p class="text-gray-600 mt-1">
                Choose your preferred service, therapist, schedule, and payment method.
            </p>
        </div>


        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-100 border border-green-300 text-green-800">
                {{ session('success') }}
            </div>
        @endif


        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-100 border border-red-300 text-red-800">
                <p class="font-bold mb-2">
                    Please correct the following:
                </p>

                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- Appointment Wizard --}}
        <div x-data="appointmentWizard(
            @js(old('service_id', '')),
            @js(old('therapist_id', '')),
            @js(old('level', '')),
            @js(old('add_on_id', '')),
            @js(old('has_previous_operations', '')),
            @js(old('body_problem', '')),
            @js(old('appointment_date', '')),
            @js(old('appointment_time', '')),
            @js(old('payment_method', '')),
            @js(old('payment_type', '')),
            @js($servicesData),
            @js($therapistsData),
            @js($addOnsData),
            @js(route('user.appointments.availableSlots'))
        )" x-init="init()" x-cloak class="bg-white rounded-3xl shadow-lg overflow-hidden">

            {{-- ========================================= --}}
            {{-- STEP INDICATOR --}}
            {{-- ========================================= --}}

            <div class="px-6 pt-6">

                <div class="flex items-center justify-between">

                    {{-- Step 1 --}}
                    <div class="flex items-center gap-2">

                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold"
                            :class="step >= 1 ?
                                'bg-[#8B5E3C] text-white' :
                                'bg-gray-200 text-gray-500'">
                            1
                        </div>

                        <span class="hidden sm:block font-semibold"
                            :class="step >= 1 ?
                                'text-[#8B5E3C]' :
                                'text-gray-400'">
                            Service
                        </span>

                    </div>


                    <div class="flex-1 h-1 mx-3 bg-gray-200">
                        <div class="h-full bg-[#8B5E3C] transition-all duration-300"
                            :style="`width: ${step >= 2 ? '100%' : '0%'}`"></div>
                    </div>


                    {{-- Step 2 --}}
                    <div class="flex items-center gap-2">

                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold"
                            :class="step >= 2 ?
                                'bg-[#8B5E3C] text-white' :
                                'bg-gray-200 text-gray-500'">
                            2
                        </div>

                        <span class="hidden sm:block font-semibold"
                            :class="step >= 2 ?
                                'text-[#8B5E3C]' :
                                'text-gray-400'">
                            Therapist
                        </span>

                    </div>


                    <div class="flex-1 h-1 mx-3 bg-gray-200">
                        <div class="h-full bg-[#8B5E3C] transition-all duration-300"
                            :style="`width: ${step >= 3 ? '100%' : '0%'}`"></div>
                    </div>


                    {{-- Step 3 --}}
                    <div class="flex items-center gap-2">

                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold"
                            :class="step >= 3 ?
                                'bg-[#8B5E3C] text-white' :
                                'bg-gray-200 text-gray-500'">
                            3
                        </div>

                        <span class="hidden sm:block font-semibold"
                            :class="step >= 3 ?
                                'text-[#8B5E3C]' :
                                'text-gray-400'">
                            Schedule
                        </span>

                    </div>


                    <div class="flex-1 h-1 mx-3 bg-gray-200">
                        <div class="h-full bg-[#8B5E3C] transition-all duration-300"
                            :style="`width: ${step >= 4 ? '100%' : '0%'}`"></div>
                    </div>


                    {{-- Step 4 --}}
                    <div class="flex items-center gap-2">

                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold"
                            :class="step >= 4 ?
                                'bg-[#8B5E3C] text-white' :
                                'bg-gray-200 text-gray-500'">
                            4
                        </div>

                        <span class="hidden sm:block font-semibold"
                            :class="step >= 4 ?
                                'text-[#8B5E3C]' :
                                'text-gray-400'">
                            Payment
                        </span>

                    </div>

                </div>

            </div>


            {{-- ========================================= --}}
            {{-- FORM --}}
            {{-- ========================================= --}}

            <form action="{{ route('user.appointments.store') }}" method="POST" class="p-6">

                @csrf


                {{-- Hidden Inputs --}}
                <input type="hidden" name="service_id" x-model="service_id">
                <input type="hidden" name="therapist_id" x-model="therapist_id">
                <input type="hidden" name="level" x-model="level">
                <input type="hidden" name="add_on_id" x-model="add_on_id">

                <input type="hidden" name="has_previous_operations" x-model="has_previous_operations">

                <input type="hidden" name="body_problem" x-model="body_problem">

                <input type="hidden" name="appointment_date" x-model="appointment_date">

                <input type="hidden" name="appointment_time" x-model="appointment_time">

                <input type="hidden" name="payment_method" x-model="payment_method">

                <input type="hidden" name="payment_type" x-model="payment_type">


                {{-- ========================================= --}}
                {{-- STEP 1: SERVICE --}}
                {{-- ========================================= --}}

                <div x-show="step === 1">

                    <div class="mb-8">

                        <h2 class="text-2xl font-bold text-gray-800">
                            Choose Your Service
                        </h2>

                        <p class="text-gray-600 mt-1">
                            Select the massage service you want.
                        </p>

                    </div>


                    {{-- Massage Type Filters --}}
                    <div class="mb-8">

                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            Massage Type
                        </h3>

                        <div class="flex flex-wrap gap-3">

                            {{-- Bed Massage --}}
                            <button type="button" @click="serviceFilter = 'Bed Massage'"
                                :class="serviceFilter === 'Bed Massage'
                                    ?
                                    'bg-[#8B5E3C] text-white shadow-md' :
                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-5 py-2.5 rounded-full font-semibold transition">
                                Bed Massage
                            </button>


                            {{-- Sitting Massage --}}
                            <button type="button" @click="serviceFilter = 'Sitting Massage'"
                                :class="serviceFilter === 'Sitting Massage'
                                    ?
                                    'bg-[#8B5E3C] text-white shadow-md' :
                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-5 py-2.5 rounded-full font-semibold transition">
                                Sitting Massage
                            </button>


                            {{-- Reflexology --}}
                            <button type="button" @click="serviceFilter = 'Reflexology'"
                                :class="serviceFilter === 'Reflexology'
                                    ?
                                    'bg-[#8B5E3C] text-white shadow-md' :
                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-5 py-2.5 rounded-full font-semibold transition">
                                Reflexology
                            </button>


                            {{-- Normal Massage --}}
                            <button type="button" @click="serviceFilter = 'Normal Massage'"
                                :class="serviceFilter === 'Normal Massage'
                                    ?
                                    'bg-[#8B5E3C] text-white shadow-md' :
                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-5 py-2.5 rounded-full font-semibold transition">
                                Normal Massage
                            </button>

                        </div>

                    </div>


                    {{-- Services --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        @forelse($services as $service)
                            <label
                                x-show="
                                serviceFilter === 'all' ||
                                '{{ strtolower($service->name) }}'.includes(
                                    serviceFilter.toLowerCase()
                                )
                            "
                                x-transition class="block cursor-pointer">

                                <input type="radio" name="service_selector" value="{{ $service->id }}"
                                    x-model="service_id" class="sr-only">


                                <div class="h-full border-2 rounded-2xl p-5 transition duration-200"
                                    :class="service_id == '{{ $service->id }}' ?
                                        'border-[#8B5E3C] bg-[#F8F1EA] shadow-md' :
                                        'border-gray-200 hover:border-[#B48A68] hover:shadow-sm'">

                                    {{-- Service Name --}}
                                    <div class="flex items-start justify-between gap-3">

                                        <div>

                                            <h3 class="text-xl font-bold text-gray-800">
                                                {{ $service->name }}
                                            </h3>

                                            @if ($service->description)
                                                <p class="text-gray-600 text-sm mt-2">
                                                    {{ $service->description }}
                                                </p>
                                            @endif

                                        </div>


                                        {{-- Selected Check --}}
                                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center shrink-0"
                                            :class="service_id == '{{ $service->id }}' ?
                                                'border-[#8B5E3C] bg-[#8B5E3C] text-white' :
                                                'border-gray-300'">
                                            <svg x-show="service_id == '{{ $service->id }}'"
                                                xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.42 0l-3.25-3.25a1 1 0 111.42-1.42l2.54 2.54 6.54-6.54a1 1 0 011.42 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>

                                    </div>


                                    {{-- Service Details --}}
                                    <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-200">

                                        <div class="flex items-center gap-2 text-sm text-gray-600">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>

                                            {{ $service->duration_minutes }} minutes

                                        </div>


                                        <div class="text-xl font-bold text-[#8B5E3C]">
                                            ₱{{ number_format($service->price, 2) }}
                                        </div>

                                    </div>

                                </div>

                            </label>

                        @empty

                            <div class="md:col-span-2 text-center py-12">

                                <div class="text-gray-400 mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                <p class="text-gray-600">
                                    No services are currently available.
                                </p>

                            </div>
                        @endforelse

                    </div>


                    {{-- Massage Level --}}
                    <div class="mt-8">

                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            Massage Level
                        </h3>


                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                            {{-- Gentle --}}
                            <label class="cursor-pointer">

                                <input type="radio" value="gentle" x-model="level" class="sr-only">

                                <div class="border-2 rounded-xl p-4 text-center"
                                    :class="level === 'gentle'
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200 hover:border-[#B48A68]'">
                                    <p class="font-bold">
                                        Gentle
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Light pressure
                                    </p>
                                </div>

                            </label>


                            {{-- Mild --}}
                            <label class="cursor-pointer">

                                <input type="radio" value="mild" x-model="level" class="sr-only">

                                <div class="border-2 rounded-xl p-4 text-center"
                                    :class="level === 'mild'
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200 hover:border-[#B48A68]'">
                                    <p class="font-bold">
                                        Mild
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Medium pressure
                                    </p>
                                </div>

                            </label>


                            {{-- Hard --}}
                            <label class="cursor-pointer">

                                <input type="radio" value="hard" x-model="level" class="sr-only">

                                <div class="border-2 rounded-xl p-4 text-center"
                                    :class="level === 'hard'
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200 hover:border-[#B48A68]'">
                                    <p class="font-bold">
                                        Hard
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Firm pressure
                                    </p>
                                </div>

                            </label>

                        </div>

                    </div>


                    {{-- Add-ons --}}
                    <div class="mt-8">

                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            Add-ons
                        </h3>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- None --}}
                            <label class="cursor-pointer">

                                <input type="radio" value="" x-model="add_on_id" class="sr-only">

                                <div class="border-2 rounded-xl p-4"
                                    :class="!add_on_id
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200 hover:border-[#B48A68]'">

                                    <div class="flex justify-between items-center">

                                        <div>
                                            <p class="font-bold">
                                                No Add-on
                                            </p>

                                            <p class="text-sm text-gray-500">
                                                No additional service
                                            </p>
                                        </div>

                                        <span class="font-bold text-[#8B5E3C]">
                                            Free
                                        </span>

                                    </div>

                                </div>

                            </label>


                            @foreach ($addOns as $addOn)
                                <label class="cursor-pointer">

                                    <input type="radio" value="{{ $addOn->id }}" x-model="add_on_id"
                                        class="sr-only">

                                    <div class="border-2 rounded-xl p-4"
                                        :class="add_on_id == '{{ $addOn->id }}' ?
                                            'border-[#8B5E3C] bg-[#F8F1EA]' :
                                            'border-gray-200 hover:border-[#B48A68]'">

                                        <div class="flex justify-between items-center">

                                            <div>

                                                <p class="font-bold">
                                                    {{ $addOn->name }}
                                                </p>

                                                <p class="text-sm text-gray-500">
                                                    +{{ $addOn->duration_minutes }} minutes
                                                </p>

                                            </div>

                                            <span class="font-bold text-[#8B5E3C]">
                                                +₱{{ number_format($addOn->price, 2) }}
                                            </span>

                                        </div>

                                    </div>

                                </label>
                            @endforeach

                        </div>

                    </div>


                    {{-- Previous Operations --}}
                    <div class="mt-8">

                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            Have you had any previous operations?
                        </h3>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <label class="cursor-pointer">

                                <input type="radio" value="yes" x-model="has_previous_operations" class="sr-only">

                                <div class="border-2 rounded-xl p-4"
                                    :class="has_previous_operations === 'yes'
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200'">
                                    <p class="font-bold">
                                        Yes
                                    </p>
                                </div>

                            </label>


                            <label class="cursor-pointer">

                                <input type="radio" value="no" x-model="has_previous_operations" class="sr-only">

                                <div class="border-2 rounded-xl p-4"
                                    :class="has_previous_operations === 'no'
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200'">
                                    <p class="font-bold">
                                        No
                                    </p>
                                </div>

                            </label>

                        </div>

                    </div>


                    {{-- Body Problem --}}
                    <div class="mt-8">

                        <label class="block text-lg font-bold text-gray-800 mb-3">
                            Body Problem
                            <span class="text-sm font-normal text-gray-500">
                                (Optional)
                            </span>
                        </label>

                        <textarea x-model="body_problem" rows="4" maxlength="1000"
                            placeholder="Tell us if you have any body pain or specific area you want the therapist to focus on. You may also enter none."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#8B5E3C] focus:border-[#8B5E3C] outline-none resize-none"></textarea>

                    </div>

                </div>


               {{-- ========================================= --}}
{{-- STEP 2: THERAPIST --}}
{{-- ========================================= --}}

<div x-show="step === 2" x-transition>

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">
            Choose Your Therapist
        </h2>

        <p class="text-gray-600 mt-1">
            Select your preferred available therapist.
        </p>
    </div>

    {{-- 4 THERAPISTS PER ROW --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        @forelse($therapists as $therapist)

            <label class="cursor-pointer">

                <input
                    type="radio"
                    name="therapist_selector"
                    value="{{ $therapist->id }}"
                    x-model="therapist_id"
                    class="sr-only"
                >

                <div
                    class="border-2 rounded-2xl p-4 transition h-full"
                    :class="therapist_id == '{{ $therapist->id }}'
                        ? 'border-[#8B5E3C] bg-[#F8F1EA] shadow-md'
                        : 'border-gray-200 hover:border-[#B48A68]'"
                >

                    {{-- THERAPIST IMAGE --}}
                    <div class="flex justify-center mb-4">

                        @if($therapist->image)
                            <img
                                src="{{ asset('storage/' . $therapist->image) }}"
                                alt="{{ $therapist->name }}"
                                class="w-28 h-28 rounded-full object-cover border-4 border-[#D6BB9E]"
                            >
                        @else
                            <div
                                class="w-28 h-28 rounded-full bg-[#D6BB9E]
                                       flex items-center justify-center
                                       text-[#8B5E3C] font-bold text-3xl"
                            >
                                {{ strtoupper(substr($therapist->name, 0, 1)) }}
                            </div>
                        @endif

                    </div>

                    {{-- THERAPIST INFORMATION --}}
                    <div class="text-center">

                        <h3 class="font-bold text-lg text-gray-800">
                            {{ $therapist->name }}
                        </h3>

                        <p class="text-sm text-green-600 mt-1">
                            ● Available
                        </p>

                    </div>

                </div>

            </label>

        @empty

            <div class="sm:col-span-2 lg:col-span-4 text-center py-12">

                <p class="text-gray-500">
                    No therapists are currently available.
                </p>

            </div>

        @endforelse

    </div>

</div>


                {{-- ========================================= --}}
                {{-- STEP 3: SCHEDULE --}}
                {{-- ========================================= --}}

                <div x-show="step === 3" x-transition>

                    <div class="mb-8">

                        <h2 class="text-2xl font-bold text-gray-800">
                            Choose Your Schedule
                        </h2>

                        <p class="text-gray-600 mt-1">
                            Select your preferred date and available time.
                        </p>

                    </div>


                    {{-- Date --}}
                    <div class="mb-8">

                        <label class="block font-bold text-gray-800 mb-3">
                            Appointment Date
                        </label>

                        <input type="date" x-model="appointment_date" :min="today" @change="loadSlots()"
                            class="w-full md:w-auto border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#8B5E3C] focus:border-[#8B5E3C] outline-none">

                    </div>


                    {{-- Loading --}}
                    <div x-show="loadingSlots" class="text-center py-8">

                        <div class="inline-flex items-center gap-3 text-gray-600">

                            <svg class="animate-spin w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>

                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>

                            Loading available time slots...

                        </div>

                    </div>


                    {{-- Time Slots --}}
                    <div x-show="!loadingSlots && appointment_date" class="mt-6">

                        <h3 class="font-bold text-gray-800 mb-4">
                            Available Time Slots
                        </h3>


                        <div x-show="availableSlots.length > 0"
                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">

                            <template x-for="slot in availableSlots" :key="slot.start">

                                <button type="button"
                                    @click="slot.status === 'available'
                                    ? appointment_time = slot.start
                                    : null"
                                    :disabled="slot.status !== 'available'"
                                    class="rounded-xl border-2 px-4 py-3 font-semibold transition"
                                    :class="slot.status === 'booked' ?
                                        'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' :
                                        appointment_time === slot.start ?
                                        'bg-[#8B5E3C] text-white border-[#8B5E3C]' :
                                        'bg-white text-gray-700 border-gray-200 hover:border-[#8B5E3C]'">

                                    <span x-text="slot.label"></span>

                                </button>

                            </template>

                        </div>


                        {{-- No slots --}}
                        <div x-show="availableSlots.length === 0"
                            class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">

                            <p class="text-gray-500">
                                No available time slots for this date.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- STEP 4: PAYMENT --}}
                {{-- ========================================= --}}

                <div x-show="step === 4" x-transition>

                    <div class="mb-8">

                        <h2 class="text-2xl font-bold text-gray-800">
                            Payment
                        </h2>

                        <p class="text-gray-600 mt-1">
                            Choose how you want to pay for your appointment.
                        </p>

                    </div>


                    {{-- Appointment Summary --}}
                    <div class="bg-[#F8F1EA] rounded-2xl p-6 mb-8">

                        <h3 class="text-lg font-bold text-gray-800 mb-5">
                            Appointment Summary
                        </h3>


                        <div class="space-y-4">

                            <div class="flex justify-between gap-4">

                                <span class="text-gray-600">
                                    Service
                                </span>

                                <span class="font-semibold text-right" x-text="selectedServiceName"></span>

                            </div>


                            <div class="flex justify-between gap-4">

                                <span class="text-gray-600">
                                    Therapist
                                </span>

                                <span class="font-semibold text-right" x-text="selectedTherapistName"></span>

                            </div>


                            <div class="flex justify-between gap-4">

                                <span class="text-gray-600">
                                    Massage Level
                                </span>

                                <span class="font-semibold capitalize" x-text="level"></span>

                            </div>


                            <div class="flex justify-between gap-4">

                                <span class="text-gray-600">
                                    Add-on
                                </span>

                                <span class="font-semibold text-right" x-text="selectedAddOnName"></span>

                            </div>


                            <div class="flex justify-between gap-4">

                                <span class="text-gray-600">
                                    Date
                                </span>

                                <span class="font-semibold" x-text="appointment_date"></span>

                            </div>


                            <div class="flex justify-between gap-4">

                                <span class="text-gray-600">
                                    Time
                                </span>

                                <span class="font-semibold" x-text="selectedSlotLabel"></span>

                            </div>


                            <div class="flex justify-between gap-4">

                                <span class="text-gray-600">
                                    Total Duration
                                </span>

                                <span class="font-semibold">
                                    <span x-text="totalDuration"></span>
                                    minutes
                                </span>

                            </div>


                            <div class="border-t border-[#D6BB9E] pt-4 flex justify-between">

                                <span class="font-bold text-lg">
                                    Total Amount
                                </span>

                                <span class="font-bold text-xl text-[#8B5E3C]">
                                    ₱<span x-text="totalAmount.toFixed(2)"></span>
                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- Payment Method --}}
                    <div class="mb-8">

                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            Payment Method
                        </h3>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Branch --}}
                            <label class="cursor-pointer">

                                <input type="radio" value="branch" x-model="payment_method" class="sr-only">

                                <div class="border-2 rounded-2xl p-5"
                                    :class="payment_method === 'branch'
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200 hover:border-[#B48A68]'">

                                    <h4 class="font-bold text-lg">
                                        Pay at Branch
                                    </h4>

                                    <p class="text-gray-500 text-sm mt-1">
                                        Pay when you visit our branch.
                                    </p>

                                </div>

                            </label>


                            {{-- GCash --}}
                            <label class="cursor-pointer">

                                <input type="radio" value="gcash" x-model="payment_method" class="sr-only">

                                <div class="border-2 rounded-2xl p-5"
                                    :class="payment_method === 'gcash'
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200 hover:border-[#B48A68]'">

                                    <h4 class="font-bold text-lg">
                                        GCash
                                    </h4>

                                    <p class="text-gray-500 text-sm mt-1">
                                        Pay securely using GCash.
                                    </p>

                                </div>

                            </label>

                        </div>

                    </div>


                    {{-- GCash Payment Type --}}
                    <div x-show="payment_method === 'gcash'" x-transition class="mb-8">

                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            GCash Payment Type
                        </h3>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Full --}}
                            <label class="cursor-pointer">

                                <input type="radio" value="full" x-model="payment_type" class="sr-only">

                                <div class="border-2 rounded-xl p-5"
                                    :class="payment_type === 'full'
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200 hover:border-[#B48A68]'">

                                    <div class="flex justify-between">

                                        <div>

                                            <h4 class="font-bold">
                                                Full Payment
                                            </h4>

                                            <p class="text-sm text-gray-500 mt-1">
                                                Pay the full amount now.
                                            </p>

                                        </div>

                                        <span class="font-bold text-[#8B5E3C]">
                                            ₱<span x-text="totalAmount.toFixed(2)"></span>
                                        </span>

                                    </div>

                                </div>

                            </label>


                            {{-- Downpayment --}}
                            <label class="cursor-pointer">

                                <input type="radio" value="downpayment" x-model="payment_type" class="sr-only">

                                <div class="border-2 rounded-xl p-5"
                                    :class="payment_type === 'downpayment'
                                        ?
                                        'border-[#8B5E3C] bg-[#F8F1EA]' :
                                        'border-gray-200 hover:border-[#B48A68]'">

                                    <div class="flex justify-between">

                                        <div>

                                            <h4 class="font-bold">
                                                Downpayment
                                            </h4>

                                            <p class="text-sm text-gray-500 mt-1">
                                                Pay 50% now.
                                            </p>

                                        </div>

                                        <span class="font-bold text-[#8B5E3C]">
                                            ₱<span x-text="(totalAmount / 2).toFixed(2)"></span>
                                        </span>

                                    </div>

                                </div>

                            </label>

                        </div>

                    </div>


                    {{-- Final Notice --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">

                        <p class="text-sm text-yellow-800">

                            <strong>Important:</strong>
                            Your appointment will be submitted as
                            <strong>pending</strong> and will require confirmation.

                        </p>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- NAVIGATION --}}
                {{-- ========================================= --}}

                <div class="mt-10 pt-6 border-t border-gray-200 flex items-center justify-between">

                    {{-- Back --}}
                    <button type="button" @click="prevStep()" x-show="step > 1"
                        class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                        ← Back
                    </button>


                    <div x-show="step === 1"></div>


                    {{-- Next --}}
                    <button type="button" @click="nextStep()" x-show="step < 4" :disabled="!canGoNext()"
                        :class="canGoNext() ?
                            'bg-[#8B5E3C] hover:bg-[#70492F] text-white' :
                            'bg-gray-300 text-gray-500 cursor-not-allowed'"
                        class="px-7 py-3 rounded-xl font-semibold transition">
                        Continue →
                    </button>


                    {{-- Submit --}}
                    <button type="submit" x-show="step === 4" :disabled="!canGoNext()"
                        :class="canGoNext() ?
                            'bg-[#8B5E3C] hover:bg-[#70492F] text-white' :
                            'bg-gray-300 text-gray-500 cursor-not-allowed'"
                        class="px-7 py-3 rounded-xl font-semibold transition">
                        Confirm Appointment
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
