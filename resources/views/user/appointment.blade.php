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
                    'duration_minutes' => (int) $service->duration_minutes,
                    'price' => (float) $service->price,
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
                    'price' => (float) $addOn->price,
                    'duration_minutes' => (int) $addOn->duration_minutes,
                ];
            })
            ->values();

    @endphp


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
    )" x-init="init()" x-cloak class="px-4 sm:px-6 lg:px-8 py-6">

        {{-- ============================================================
        MAIN LAYOUT
    ============================================================ --}}

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- ========================================================
            LEFT SIDE
        ========================================================= --}}

            <div class="xl:col-span-2">

                <div class="bg-white rounded-3xl shadow-lg overflow-hidden">

                    {{-- =================================================
                    HEADER
                ================================================== --}}

                    <div class="p-6 border-b border-gray-100">

                        <h1 class="text-2xl font-bold text-gray-800">
                            Book an Appointment
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Choose your service, therapist, schedule and payment.
                        </p>

                    </div>


                    {{-- =================================================
                    STEP INDICATOR
                ================================================== --}}

                    <div class="px-6 py-5 border-b border-gray-100">

                        <div class="flex items-center justify-between">

                            <template x-for="number in [1,2,3,4]" :key="number">

                                <div class="flex items-center flex-1">

                                    <button type="button" @click="goToStep(number)"
                                        class="flex items-center justify-center
                                           w-9 h-9 rounded-full text-sm
                                           font-semibold transition"
                                        :class="step === number ?
                                            'bg-[#849753] text-white' :
                                            step > number ?
                                            'bg-[#849753]/20 text-[#849753]' :
                                            'bg-gray-100 text-gray-400'">
                                        <span x-text="number"></span>
                                    </button>

                                    <div x-show="number < 4" class="h-1 flex-1 mx-2 rounded"
                                        :class="step > number ?
                                            'bg-[#849753]' :
                                            'bg-gray-100'">
                                    </div>

                                </div>

                            </template>

                        </div>


                        <div class="grid grid-cols-4 mt-2 text-xs text-gray-500">

                            <span>Service</span>

                            <span class="text-center">
                                Therapist
                            </span>

                            <span class="text-center">
                                Schedule
                            </span>

                            <span class="text-right">
                                Payment
                            </span>

                        </div>

                    </div>

                    {{-- Success Modal --}}
                    @if (session('success'))
                        <div x-data="{
                            show: true
                        }" x-init="setTimeout(() => {
                            show = false;
                        }, 5000);" x-show="show"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="fixed inset-0 z-9999 flex items-center justify-center bg-black/40 px-4"
                            style="display: none;">

                            {{-- Modal --}}
                            <div x-show="show" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                                @click.outside="show = false"
                                class="w-full max-w-md rounded-2xl bg-white p-8 text-center shadow-2xl">

                                {{-- Success Icon --}}
                                <div
                                    class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
                                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>

                                {{-- Title --}}
                                <h2 class="text-2xl font-bold text-gray-800">
                                    Booking Successful!
                                </h2>

                                {{-- Message --}}
                                <p class="mt-3 text-gray-600">
                                    {{ session('success') }}
                                </p>

                                {{-- Close Button --}}
                                <button type="button" @click="show = false"
                                    class="mt-6 w-full rounded-xl bg-[#849753] px-5 py-3 font-semibold text-white transition hover:bg-[#6f8245]">
                                    Okay
                                </button>

                                {{-- Progress bar --}}
                                <div class="mt-4 h-1 w-full overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-[#849753]"
                                        style="animation: successProgress 5s linear forwards;"></div>
                                </div>

                            </div>
                        </div>

                        <style>
                            @keyframes successProgress {
                                from {
                                    width: 100%;
                                }

                                to {
                                    width: 0%;
                                }
                            }
                        </style>
                    @endif
                    {{-- =================================================
                    FORM
                ================================================== --}}

                    <form method="POST" action="{{ route('user.appointments.store') }}" class="p-6">

                        @csrf

                        <input type="hidden" name="appointment_time" x-model="appointment_time">


                        @if ($errors->any())
                            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                            <span class="text-red-600 font-bold">!</span>
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="font-semibold text-red-800">
                                            Please fix the following:
                                        </h3>

                                        <ul class="mt-2 space-y-1 text-sm text-red-700">
                                            @foreach ($errors->all() as $error)
                                                <li>• {{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif




                        {{-- =================================================
                        STEP 1
                    ================================================== --}}

                        <div x-show="step === 1">

                            <div class="mb-6">

                                <h2 class="text-xl font-bold text-gray-800">
                                    Choose your service
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Select the massage service and additional options.
                                </p>

                            </div>


                            {{-- SERVICE FILTER --}}

                            <div class="flex flex-wrap gap-2 mb-6">

                                <template x-for="filter in serviceFilters" :key="filter">

                                    <button type="button" @click="serviceFilter = filter"
                                        class="px-4 py-2 rounded-full
                                           text-sm font-medium transition"
                                        :class="serviceFilter === filter ?
                                            'bg-[#849753] text-white' :
                                            'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                        x-text="filter"></button>

                                </template>

                            </div>


                            {{-- SERVICES --}}

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <template x-for="service in filteredServices" :key="service.id">

                                    <label class="relative cursor-pointer">

                                        <input type="radio" name="service_id" :value="service.id" x-model="service_id"
                                            class="peer sr-only">

                                        <div
                                            class="h-full border-2
                                               border-gray-200 rounded-2xl
                                               p-5 transition
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5
                                               hover:border-[#849753]/50">

                                            <div class="flex justify-between gap-4">

                                                <div>

                                                    <h3 class="font-semibold text-gray-800" x-text="service.name"></h3>

                                                    <p class="text-sm text-gray-500 mt-1"
                                                        x-text="service.description || ''"></p>

                                                </div>

                                                <div class="text-right shrink-0">

                                                    <p class="font-bold text-[#849753]">
                                                        ₱<span x-text="formatMoney(service.price)"></span>
                                                    </p>

                                                    <p class="text-xs text-gray-500 mt-1">

                                                        <span x-text="service.duration_minutes"></span>

                                                        min

                                                    </p>

                                                </div>

                                            </div>

                                        </div>

                                    </label>

                                </template>

                            </div>


                            {{-- LEVEL --}}

                            <div class="mt-8">

                                <h3 class="font-semibold text-gray-800 mb-3">
                                    Massage Level
                                </h3>

                                <div class="grid grid-cols-3 gap-3">

                                    <template
                                        x-for="item in [
                                        {value:'gentle',label:'Gentle'},
                                        {value:'mild',label:'Mild'},
                                        {value:'hard',label:'Hard'}
                                    ]"
                                        :key="item.value">

                                        <label class="cursor-pointer">

                                            <input type="radio" name="level" :value="item.value" x-model="level"
                                                class="peer sr-only">

                                            <div
                                                class="text-center border-2
                                                   border-gray-200 rounded-xl
                                                   py-3
                                                   peer-checked:border-[#849753]
                                                   peer-checked:bg-[#849753]/5">

                                                <span class="text-sm font-medium" x-text="item.label"></span>

                                            </div>

                                        </label>

                                    </template>

                                </div>

                            </div>


                            {{-- ADD-ONS --}}

                            <div class="mt-8">

                                <h3 class="font-semibold text-gray-800 mb-3">
                                    Add-on
                                </h3>

                                <div class="space-y-3">

                                    {{-- NO ADD-ON --}}

                                    <label class="cursor-pointer block">

                                        <input type="radio" name="add_on_id" value="" x-model="add_on_id"
                                            class="peer sr-only">

                                        <div
                                            class="border-2 border-gray-200
                                               rounded-xl p-4
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5">

                                            <div class="flex justify-between">

                                                <span class="font-medium">
                                                    No Add-on
                                                </span>

                                                <span class="text-gray-500">
                                                    Free
                                                </span>

                                            </div>

                                        </div>

                                    </label>


                                    {{-- ADD-ONS --}}

                                    <template x-for="addOn in addOns" :key="addOn.id">

                                        <label class="cursor-pointer block">

                                            <input type="radio" name="add_on_id" :value="addOn.id"
                                                x-model="add_on_id" class="peer sr-only">

                                            <div
                                                class="border-2 border-gray-200
                                                   rounded-xl p-4
                                                   peer-checked:border-[#849753]
                                                   peer-checked:bg-[#849753]/5">

                                                <div class="flex justify-between">

                                                    <div>

                                                        <span class="font-medium" x-text="addOn.name"></span>

                                                        <span class="text-xs text-gray-500 ml-2">
                                                            +

                                                            <span x-text="addOn.duration_minutes"></span>

                                                            min
                                                        </span>

                                                    </div>

                                                    <span class="font-semibold text-[#849753]">
                                                        +₱<span x-text="formatMoney(addOn.price)"></span>
                                                    </span>

                                                </div>

                                            </div>

                                        </label>

                                    </template>

                                </div>

                            </div>


                            {{-- PREVIOUS OPERATIONS --}}

                            <div class="mt-8">

                                <h3 class="font-semibold text-gray-800 mb-3">
                                    Have you had previous operations?
                                </h3>

                                <div class="grid grid-cols-2 gap-3">

                                    <label class="cursor-pointer">

                                        <input type="radio" name="has_previous_operations" value="yes"
                                            x-model="has_previous_operations" class="peer sr-only">

                                        <div
                                            class="text-center border-2
                                               border-gray-200 rounded-xl p-3
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5">
                                            Yes
                                        </div>

                                    </label>


                                    <label class="cursor-pointer">

                                        <input type="radio" name="has_previous_operations" value="no"
                                            x-model="has_previous_operations" class="peer sr-only">

                                        <div
                                            class="text-center border-2
                                               border-gray-200 rounded-xl p-3
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5">
                                            No
                                        </div>

                                    </label>

                                </div>

                            </div>


                            {{-- BODY PROBLEM --}}

                            <div class="mt-8">

                                <label class="block font-semibold text-gray-800 mb-2">
                                    Body Problem / Concern
                                </label>

                                <textarea name="body_problem" x-model="body_problem" rows="4"
                                    class="w-full rounded-xl border-gray-300
                                       focus:border-[#849753]
                                       focus:ring-[#849753]"
                                    placeholder="Tell us about any pain, discomfort or body concern..."></textarea>

                            </div>

                        </div>


                        {{-- =================================================
                        STEP 2 - THERAPIST
                    ================================================== --}}

                        <div x-show="step === 2">

                            <div class="mb-6">

                                <h2 class="text-xl font-bold text-gray-800">
                                    Choose your therapist
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Select your preferred available therapist.
                                </p>

                            </div>


                            <div
                                class="grid grid-cols-1 sm:grid-cols-2
                                   lg:grid-cols-2 gap-4">

                                <template x-for="therapist in therapists" :key="therapist.id">

                                    <label class="cursor-pointer">

                                        <input type="radio" name="therapist_id" :value="therapist.id"
                                            x-model="therapist_id" class="peer sr-only">

                                        <div
                                            class="border-2 border-gray-200
                                               rounded-2xl p-5 text-center
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5
                                               transition">

                                            <div
                                                class="w-16 h-16 mx-auto rounded-full
                                                   bg-[#849753]/10
                                                   flex items-center
                                                   justify-center mb-3">

                                                <span class="text-xl font-bold text-[#849753]"
                                                    x-text="therapist.name.charAt(0)"></span>

                                            </div>

                                            <h3 class="font-semibold text-gray-800" x-text="therapist.name"></h3>

                                            <p class="text-xs text-green-600 mt-1">
                                                Available
                                            </p>

                                        </div>

                                    </label>

                                </template>

                            </div>

                        </div>


                        {{-- =================================================
                        STEP 3 - SCHEDULE
                    ================================================== --}}

                        <div x-show="step === 3">

                            <div class="mb-6">

                                <h2 class="text-xl font-bold text-gray-800">
                                    Choose your schedule
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Select a date and time based on the therapist's
                                    current bookings.
                                </p>

                            </div>


                            {{-- DATE --}}

                            <div class="mb-6">

                                <label class="block font-semibold text-gray-800 mb-2">
                                    Appointment Date
                                </label>

                                <input type="date" name="appointment_date" x-model="appointment_date"
                                    :min="today"
                                    class="w-full rounded-xl border-gray-300
                                       focus:border-[#849753]
                                       focus:ring-[#849753]">

                            </div>


                            {{-- DURATION INFORMATION --}}

                            <div x-show="selectedService"
                                class="mb-6 p-4 rounded-2xl
                                   bg-[#849753]/10
                                   border border-[#849753]/20">

                                <div class="flex justify-between items-center gap-4">

                                    <div>

                                        <p class="text-sm text-gray-500">
                                            Required appointment duration
                                        </p>

                                        <p class="text-lg font-bold text-gray-800">

                                            <span x-text="totalDuration"></span>

                                            minutes

                                        </p>

                                    </div>


                                    <div class="text-right">

                                        <p class="text-sm text-gray-500">
                                            Service + Add-on
                                        </p>

                                        <p class="font-semibold text-[#849753]">

                                            <span x-text="selectedService?.duration_minutes || 0"></span>

                                            +

                                            <span x-text="selectedAddOn?.duration_minutes || 0"></span>

                                            min

                                        </p>

                                    </div>

                                </div>

                            </div>


                            {{-- =================================================
                            LEGEND
                        ================================================== --}}

                            <div x-show="!loadingSlots && appointment_date"
                                class="mb-5 p-4 rounded-2xl
                                   bg-gray-50 border border-gray-200">

                                <p class="text-xs font-bold text-gray-500 uppercase mb-3">
                                    Schedule Status
                                </p>

                                <div class="flex flex-wrap gap-4">

                                    {{-- AVAILABLE --}}

                                    <div class="flex items-center gap-2">

                                        <span class="w-3 h-3 rounded-full bg-green-500"></span>

                                        <span class="text-sm text-gray-600">
                                            Available
                                        </span>

                                    </div>


                                    {{-- BOOKED --}}

                                    <div class="flex items-center gap-2">

                                        <span class="w-3 h-3 rounded-full bg-red-500"></span>

                                        <span class="text-sm text-gray-600">
                                            Booked
                                        </span>

                                    </div>


                                    {{-- ADJUST SERVICE --}}

                                    <div class="flex items-center gap-2">

                                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>

                                        <span class="text-sm text-gray-600">
                                            Adjust service
                                        </span>

                                    </div>

                                </div>

                            </div>


                            {{-- LOADING --}}

                            <div x-show="loadingSlots" class="py-10 text-center">

                                <div
                                    class="animate-spin w-8 h-8
                                       border-4 border-gray-200
                                       border-t-[#849753]
                                       rounded-full mx-auto">
                                </div>

                                <p class="text-sm text-gray-500 mt-3">
                                    Checking therapist availability...
                                </p>

                            </div>


                            {{-- =================================================
                            SLOT LIST
                        ================================================== --}}

                            <div x-show="
                                !loadingSlots &&
                                appointment_date &&
                                availableSlots.length
                            "
                                class="space-y-3">

                                <template x-for="slot in availableSlots" :key="slot.start">

                                    <div>


                                        {{-- =================================================
                                        AVAILABLE
                                    ================================================== --}}

                                        <template x-if="slot.status === 'available'">

                                            <button type="button" @click="selectSlot(slot)"
                                                class="w-full text-left
                                                   border-2 rounded-2xl
                                                   p-4 transition duration-200"
                                                :class="appointment_time === slot.start ?
                                                    'border-[#849753] bg-[#849753]/10 ring-2 ring-[#849753]/20' :
                                                    'border-green-200 bg-green-50 hover:border-[#849753] hover:bg-[#849753]/5'">

                                                <div
                                                    class="flex items-center
                                                       justify-between gap-4">

                                                    <div
                                                        class="flex items-center
                                                           gap-4">

                                                        {{-- GREEN ICON --}}

                                                        <div
                                                            class="w-11 h-11
                                                               rounded-full
                                                               bg-green-100
                                                               text-green-600
                                                               flex items-center
                                                               justify-center
                                                               text-lg
                                                               shrink-0">
                                                            ✓
                                                        </div>


                                                        <div>

                                                            <p class="font-bold
                                                                   text-gray-800"
                                                                x-text="slot.label"></p>

                                                            <p
                                                                class="text-sm
                                                                   text-green-600
                                                                   mt-1">
                                                                Full service duration
                                                                is available
                                                            </p>

                                                        </div>

                                                    </div>


                                                    {{-- STATUS --}}

                                                    <span
                                                        class="px-3 py-1 rounded-full
                                                           bg-green-100
                                                           text-green-700
                                                           text-xs font-bold
                                                           whitespace-nowrap">
                                                        AVAILABLE
                                                    </span>

                                                </div>

                                            </button>

                                        </template>



                                        {{-- =================================================
                                        BOOKED
                                    ================================================== --}}

                                        <template x-if="slot.status === 'booked'">

                                            <div
                                                class="w-full border-2
                                                   border-red-200
                                                   rounded-2xl p-4
                                                   bg-red-50">

                                                <div
                                                    class="flex items-center
                                                       justify-between gap-4">

                                                    <div
                                                        class="flex items-center
                                                           gap-4">

                                                        {{-- RED ICON --}}

                                                        <div
                                                            class="w-11 h-11
                                                               rounded-full
                                                               bg-red-100
                                                               text-red-600
                                                               flex items-center
                                                               justify-center
                                                               text-lg
                                                               shrink-0">
                                                            ✕
                                                        </div>


                                                        <div>

                                                            <p class="font-bold
                                                                   text-gray-800"
                                                                x-text="slot.label"></p>

                                                            <p
                                                                class="text-sm
                                                                   text-red-600
                                                                   mt-1">
                                                                ⚠️ This time is
                                                                already booked.
                                                            </p>

                                                        </div>

                                                    </div>


                                                    {{-- BOOKED LABEL --}}

                                                    <span
                                                        class="px-3 py-1 rounded-full
                                                           bg-red-100
                                                           text-red-700
                                                           text-xs font-bold
                                                           whitespace-nowrap">
                                                        BOOKED
                                                    </span>

                                                </div>

                                            </div>

                                        </template>



                                        {{-- =================================================
                                        ADJUST SERVICE
                                    ================================================== --}}

                                        <template x-if="slot.status === 'adjust_service'">

                                            <div
                                                class="w-full border-2
                                                   border-amber-300
                                                   rounded-2xl
                                                   bg-amber-50
                                                   overflow-hidden">

                                                {{-- WARNING HEADER --}}

                                                <div class="p-4">

                                                    <div
                                                        class="flex items-start
                                                           justify-between
                                                           gap-4">

                                                        <div
                                                            class="flex items-start
                                                               gap-4">

                                                            {{-- WARNING ICON --}}

                                                            <div
                                                                class="w-11 h-11
                                                                   rounded-full
                                                                   bg-amber-100
                                                                   text-amber-700
                                                                   flex items-center
                                                                   justify-center
                                                                   text-lg
                                                                   shrink-0">
                                                                ⚠️
                                                            </div>


                                                            <div>

                                                                <p class="font-bold
                                                                       text-gray-800"
                                                                    x-text="slot.label"></p>

                                                                <p
                                                                    class="text-sm
                                                                       font-semibold
                                                                       text-amber-700
                                                                       mt-1">
                                                                    Adjust your
                                                                    service time
                                                                </p>

                                                                <p class="text-sm
                                                                       text-gray-600
                                                                       mt-1"
                                                                    x-text="slot.message"></p>

                                                            </div>

                                                        </div>


                                                        {{-- STATUS --}}

                                                        <span
                                                            class="px-3 py-1
                                                               rounded-full
                                                               bg-amber-100
                                                               text-amber-700
                                                               text-xs font-bold
                                                               whitespace-nowrap">
                                                            ADJUST SERVICE
                                                        </span>

                                                    </div>


                                                    {{-- AVAILABLE VS REQUIRED --}}

                                                    <div
                                                        class="grid grid-cols-2
                                                           gap-3 mt-4">

                                                        <div
                                                            class="bg-white
                                                               rounded-xl p-3
                                                               border
                                                               border-amber-100">

                                                            <p
                                                                class="text-xs
                                                                   text-gray-500">
                                                                Available time
                                                            </p>

                                                            <p
                                                                class="font-bold
                                                                   text-gray-800
                                                                   mt-1">

                                                                <span
                                                                    x-text="
                                                                    slot.available_minutes
                                                                "></span>

                                                                minutes

                                                            </p>

                                                        </div>


                                                        <div
                                                            class="bg-white
                                                               rounded-xl p-3
                                                               border
                                                               border-amber-100">

                                                            <p
                                                                class="text-xs
                                                                   text-gray-500">
                                                                Required time
                                                            </p>

                                                            <p
                                                                class="font-bold
                                                                   text-gray-800
                                                                   mt-1">

                                                                <span
                                                                    x-text="
                                                                    slot.required_minutes
                                                                "></span>

                                                                minutes

                                                            </p>

                                                        </div>

                                                    </div>

                                                </div>


                                                {{-- =================================================
                                                RECOMMENDATIONS
                                            ================================================== --}}

                                                <template
                                                    x-if="
                                                    slot.recommendations &&
                                                    slot.recommendations.length > 0
                                                ">

                                                    <div
                                                        class="border-t
                                                           border-amber-200
                                                           bg-amber-100/50
                                                           p-4">

                                                        <p
                                                            class="text-sm
                                                               font-bold
                                                               text-amber-900
                                                               mb-3">
                                                            💡 Try a shorter
                                                            service:
                                                        </p>


                                                        <div class="space-y-2">

                                                            <template
                                                                x-for="
                                                                recommendation
                                                                in slot.recommendations
                                                            "
                                                                :key="recommendation.id">

                                                                <button type="button"
                                                                    @click="
                                                                    selectRecommendedService(
                                                                        recommendation.id,
                                                                        slot.start
                                                                    )
                                                                "
                                                                    class="w-full
                                                                       text-left
                                                                       bg-white
                                                                       rounded-xl
                                                                       border
                                                                       border-amber-200
                                                                       p-3
                                                                       hover:border-[#849753]
                                                                       hover:bg-[#849753]/5
                                                                       transition">

                                                                    <div
                                                                        class="flex
                                                                           items-center
                                                                           justify-between
                                                                           gap-3">

                                                                        <div>

                                                                            <p class="font-semibold
                                                                                   text-gray-800"
                                                                                x-text="
                                                                                recommendation.name
                                                                            ">
                                                                            </p>

                                                                            <p
                                                                                class="text-xs
                                                                                   text-gray-500
                                                                                   mt-1">

                                                                                <span
                                                                                    x-text="
                                                                                    recommendation.duration_minutes
                                                                                "></span>

                                                                                min service

                                                                                +

                                                                                <span
                                                                                    x-text="
                                                                                    selectedAddOn?.duration_minutes || 0
                                                                                "></span>

                                                                                min add-on

                                                                                =

                                                                                <span class="font-semibold"
                                                                                    x-text="
                                                                                    recommendation.total_duration
                                                                                "></span>

                                                                                min total

                                                                            </p>

                                                                        </div>


                                                                        <div
                                                                            class="text-right
                                                                               shrink-0">

                                                                            <p
                                                                                class="font-bold
                                                                                   text-[#849753]">
                                                                                ₱<span
                                                                                    x-text="
                                                                                    formatMoney(
                                                                                        recommendation.total_price
                                                                                    )
                                                                                "></span>
                                                                            </p>

                                                                            <p
                                                                                class="text-xs
                                                                                   text-[#849753]
                                                                                   font-semibold
                                                                                   mt-1">
                                                                                Choose
                                                                            </p>

                                                                        </div>

                                                                    </div>

                                                                </button>

                                                            </template>

                                                        </div>

                                                    </div>

                                                </template>


                                                {{-- NO RECOMMENDATION --}}

                                                <template
                                                    x-if="
                                                    !slot.recommendations ||
                                                    slot.recommendations.length === 0
                                                ">

                                                    <div
                                                        class="border-t
                                                           border-amber-200
                                                           p-4
                                                           text-sm
                                                           text-amber-800">

                                                        No shorter service is
                                                        available for this time.
                                                        Please choose another
                                                        schedule.

                                                    </div>

                                                </template>

                                            </div>

                                        </template>

                                    </div>

                                </template>

                            </div>


                            {{-- =================================================
                            NO SLOTS
                        ================================================== --}}

                            <div x-show="
                                !loadingSlots &&
                                appointment_date &&
                                availableSlots.length === 0
                            "
                                class="py-10 text-center
                                   bg-gray-50 rounded-2xl">

                                <div class="text-3xl mb-2">
                                    😔
                                </div>

                                <p class="font-semibold text-gray-700">
                                    No schedule is available for this date.
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    Please try another date or therapist.
                                </p>

                            </div>


                            {{-- DATE NOT SELECTED --}}

                            <div x-show="!appointment_date"
                                class="py-10 text-center
                                   bg-gray-50 rounded-2xl">

                                <div class="text-3xl mb-2">
                                    📅
                                </div>

                                <p class="text-gray-500">
                                    Please select an appointment date first.
                                </p>

                            </div>

                        </div>


                        {{-- =================================================
                        STEP 4 - PAYMENT
                    ================================================== --}}

                        <div x-show="step === 4">

                            <div class="mb-6">

                                <h2 class="text-xl font-bold text-gray-800">
                                    Payment
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Choose how you would like to pay.
                                </p>

                            </div>


                            {{-- PAYMENT METHOD --}}

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <label class="cursor-pointer">

                                    <input type="radio" name="payment_method" value="branch" x-model="payment_method"
                                        class="peer sr-only">

                                    <div
                                        class="border-2 border-gray-200
                                           rounded-2xl p-5
                                           peer-checked:border-[#849753]
                                           peer-checked:bg-[#849753]/5">

                                        <h3 class="font-semibold">
                                            Pay at Branch
                                        </h3>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Pay directly at the massage center.
                                        </p>

                                    </div>

                                </label>


                                <label class="cursor-pointer">

                                    <input type="radio" name="payment_method" value="gcash" x-model="payment_method"
                                        class="peer sr-only">

                                    <div
                                        class="border-2 border-gray-200
                                           rounded-2xl p-5
                                           peer-checked:border-[#849753]
                                           peer-checked:bg-[#849753]/5">

                                        <h3 class="font-semibold">
                                            GCash
                                        </h3>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Pay securely through GCash.
                                        </p>

                                    </div>

                                </label>

                            </div>


                            {{-- PAYMENT TYPE --}}

                            <div x-show="payment_method === 'gcash'" class="mt-6">

                                <h3 class="font-semibold text-gray-800 mb-3">
                                    Payment Type
                                </h3>

                                <div class="grid grid-cols-2 gap-3">

                                    <label class="cursor-pointer">

                                        <input type="radio" name="payment_type" value="full" x-model="payment_type"
                                            class="peer sr-only">

                                        <div
                                            class="border-2 border-gray-200
                                               rounded-xl p-4
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5">

                                            <p class="font-semibold">
                                                Full Payment
                                            </p>

                                            <p class="text-sm text-gray-500">
                                                ₱<span x-text="formatMoney(totalAmount)"></span>
                                            </p>

                                        </div>

                                    </label>


                                    <label class="cursor-pointer">

                                        <input type="radio" name="payment_type" value="downpayment"
                                            x-model="payment_type" class="peer sr-only">

                                        <div
                                            class="border-2 border-gray-200
                                               rounded-xl p-4
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5">

                                            <p class="font-semibold">
                                                50% Downpayment
                                            </p>

                                            <p class="text-sm text-gray-500">
                                                ₱<span x-text="formatMoney(totalAmount * 0.5)"></span>
                                            </p>

                                        </div>

                                    </label>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        NAVIGATION
                    ================================================== --}}

                        <div
                            class="flex justify-between items-center
                               mt-8 pt-6 border-t border-gray-100">

                            <button type="button" @click="prevStep()" x-show="step > 1"
                                class="px-5 py-3 rounded-xl
                                   border border-gray-300
                                   text-gray-700 hover:bg-gray-50">
                                Back
                            </button>


                            <div x-show="step === 1" class="ml-auto"></div>


                            <button type="button" x-show="step < 4" @click="nextStep()" :disabled="!canGoNext()"
                                class="px-6 py-3 rounded-xl
                                   bg-[#849753] text-white
                                   font-semibold
                                   disabled:opacity-50
                                   disabled:cursor-not-allowed
                                   hover:bg-[#6f8144]">
                                Continue
                            </button>


                            <button type="submit" x-show="step === 4" :disabled="!canGoNext()"
                                class="px-6 py-3 rounded-xl
                                   bg-[#849753] text-white
                                   font-semibold
                                   disabled:opacity-50
                                   disabled:cursor-not-allowed
                                   hover:bg-[#6f8144]">
                                Confirm Appointment
                            </button>

                        </div>

                    </form>

                </div>

            </div>


            {{-- ========================================================
            RIGHT SIDE - BOOKING SUMMARY
        ========================================================= --}}

            <div class="xl:col-span-1">

                <div class="bg-white rounded-3xl shadow-lg
                       sticky top-6 overflow-hidden">

                    <div class="p-6 bg-[#849753] text-white">

                        <h2 class="text-xl font-bold">
                            Booking Summary
                        </h2>

                        <p class="text-sm opacity-90 mt-1">
                            Your appointment details
                        </p>

                    </div>


                    <div class="p-6 space-y-6">


                        {{-- SERVICE --}}

                        <div>

                            <div class="flex justify-between
                                   items-start gap-3">

                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                           uppercase font-semibold">
                                        Service
                                    </p>

                                    <p class="font-semibold text-gray-800 mt-1"
                                        x-text="
                                        selectedServiceName ||
                                        'Not selected'
                                    ">
                                    </p>

                                    <p x-show="selectedService" class="text-xs text-gray-500 mt-1">

                                        <span
                                            x-text="
                                            selectedService?.duration_minutes || 0
                                        "></span>

                                        minutes

                                    </p>

                                </div>


                                <button type="button" @click="goToStep(1)"
                                    class="text-xs text-[#849753]
                                       font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- ADD-ON --}}

                        <div>

                            <div class="flex justify-between
                                   items-start gap-3">

                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                           uppercase font-semibold">
                                        Add-on
                                    </p>

                                    <p class="font-semibold text-gray-800 mt-1" x-text="selectedAddOnName"></p>

                                    <p x-show="selectedAddOn" class="text-xs text-gray-500 mt-1">

                                        +

                                        <span
                                            x-text="
                                            selectedAddOn?.duration_minutes || 0
                                        "></span>

                                        minutes

                                    </p>

                                </div>


                                <button type="button" @click="goToStep(1)"
                                    class="text-xs text-[#849753]
                                       font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- THERAPIST --}}

                        <div>

                            <div class="flex justify-between
                                   items-start gap-3">

                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                           uppercase font-semibold">
                                        Therapist
                                    </p>

                                    <p class="font-semibold text-gray-800 mt-1"
                                        x-text="
                                        selectedTherapistName ||
                                        'Not selected'
                                    ">
                                    </p>

                                </div>


                                <button type="button" @click="goToStep(2)"
                                    class="text-xs text-[#849753]
                                       font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- SCHEDULE --}}

                        <div>

                            <div class="flex justify-between
                                   items-start gap-3">

                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                           uppercase font-semibold">
                                        Schedule
                                    </p>

                                    <p class="font-semibold text-gray-800 mt-1"
                                        x-text="
                                        appointment_date ||
                                        'Not selected'
                                    ">
                                    </p>

                                    <p x-show="selectedSlotLabel" class="text-xs text-gray-500 mt-1"
                                        x-text="selectedSlotLabel"></p>

                                </div>


                                <button type="button" @click="goToStep(3)"
                                    class="text-xs text-[#849753]
                                       font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- PAYMENT --}}

                        <div>

                            <div class="flex justify-between
                                   items-start gap-3">

                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                           uppercase font-semibold">
                                        Payment
                                    </p>

                                    <p class="font-semibold text-gray-800 mt-1" x-text="paymentMethodLabel"></p>

                                    <p x-show="
                                        payment_method === 'gcash' &&
                                        payment_type
                                    "
                                        class="text-xs text-gray-500 mt-1" x-text="paymentTypeLabel"></p>

                                </div>


                                <button type="button" @click="goToStep(4)"
                                    class="text-xs text-[#849753]
                                       font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- TOTAL DURATION --}}

                        <div class="flex justify-between">

                            <span class="text-gray-500">
                                Total Duration
                            </span>

                            <span class="font-bold text-gray-800">

                                <span x-text="totalDuration"></span>

                                min

                            </span>

                        </div>


                        {{-- TOTAL --}}

                        <div class="flex justify-between items-center">

                            <span class="text-gray-500">
                                Total Amount
                            </span>

                            <span class="text-2xl font-bold text-[#849753]">

                                ₱<span x-text="formatMoney(totalAmount)"></span>

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection


@push('scripts')
    <script>
        function appointmentWizard(
            oldServiceId = '',
            oldTherapistId = '',
            oldLevel = '',
            oldAddOnId = '',
            oldPreviousOps = '',
            oldBodyProblem = '',
            oldDate = '',
            oldTime = '',
            oldPaymentMethod = '',
            oldPaymentType = '',
            services = [],
            therapists = [],
            addOns = [],
            availableSlotsUrl = ''
        ) {

            return {

                /* ============================================================
                   WIZARD
                ============================================================ */

                step: 1,

                serviceFilter: 'Bed Massage',

                serviceFilters: [
                    'Bed Massage',
                    'Sitting Massage',
                    'Reflexology',
                    'Normal Massage'
                ],


                /* ============================================================
                   FORM DATA
                ============================================================ */

                service_id: oldServiceId || '',

                therapist_id: oldTherapistId || '',

                level: oldLevel || '',

                add_on_id: oldAddOnId || '',

                has_previous_operations: oldPreviousOps || '',

                body_problem: oldBodyProblem || '',

                appointment_date: oldDate || '',

                appointment_time: oldTime || '',

                payment_method: oldPaymentMethod || '',

                payment_type: oldPaymentType || '',


                /* ============================================================
                   DATA
                ============================================================ */

                services: services,

                therapists: therapists,

                addOns: addOns,

                availableSlots: [],

                loadingSlots: false,

                availableSlotsUrl: availableSlotsUrl,

                today: '',


                /* ============================================================
                   INIT
                ============================================================ */

                init() {

                    this.setToday();


                    this.$watch('payment_method', (value) => {
                        if (value === 'branch') {
                            this.payment_type = '';
                        }

                        if (value === 'gcash' && !this.payment_type) {
                            this.payment_type = 'full';
                        }
                    });


                    /*
                    |--------------------------------------------------------------------------
                    | SERVICE WATCHER
                    |--------------------------------------------------------------------------
                    */

                    this.$watch('service_id', () => {

                        this.appointment_time = '';

                        if (
                            this.step === 3 &&
                            this.therapist_id &&
                            this.appointment_date
                        ) {
                            this.loadSlots();
                        }

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | ADD-ON WATCHER
                    |--------------------------------------------------------------------------
                    */

                    this.$watch('add_on_id', () => {

                        this.appointment_time = '';

                        if (
                            this.step === 3 &&
                            this.therapist_id &&
                            this.appointment_date
                        ) {
                            this.loadSlots();
                        }

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | THERAPIST WATCHER
                    |--------------------------------------------------------------------------
                    */

                    this.$watch('therapist_id', () => {

                        this.appointment_time = '';

                        if (
                            this.step === 3 &&
                            this.service_id &&
                            this.appointment_date
                        ) {
                            this.loadSlots();
                        }

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | DATE WATCHER
                    |--------------------------------------------------------------------------
                    */

                    this.$watch('appointment_date', () => {

                        this.appointment_time = '';

                        if (
                            this.step === 3 &&
                            this.service_id &&
                            this.therapist_id &&
                            this.appointment_date
                        ) {
                            this.loadSlots();
                        }

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | INITIAL LOAD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        this.service_id &&
                        this.therapist_id &&
                        this.appointment_date
                    ) {

                        this.loadSlots();

                    }

                },


                /* ============================================================
                   TODAY
                ============================================================ */

                setToday() {

                    const now = new Date();

                    const year = now.getFullYear();

                    const month =
                        String(now.getMonth() + 1).padStart(2, '0');

                    const day =
                        String(now.getDate()).padStart(2, '0');

                    this.today =
                        `${year}-${month}-${day}`;

                },


                /* ============================================================
                   SERVICE FILTER
                ============================================================ */

                get filteredServices() {

                    if (
                        !this.serviceFilter ||
                        this.serviceFilter === 'All'
                    ) {
                        return this.services;
                    }

                    return this.services.filter(service => {

                        return String(service.name || '')
                            .toLowerCase()
                            .includes(
                                this.serviceFilter.toLowerCase()
                            );

                    });

                },


                /* ============================================================
                   SELECTED SERVICE
                ============================================================ */

                get selectedService() {

                    return this.services.find(service =>

                        String(service.id) ===
                        String(this.service_id)

                    ) || null;

                },


                get selectedServiceName() {

                    return this.selectedService ?
                        this.selectedService.name :
                        '';

                },


                /* ============================================================
                   SELECTED THERAPIST
                ============================================================ */

                get selectedTherapist() {

                    return this.therapists.find(therapist =>

                        String(therapist.id) ===
                        String(this.therapist_id)

                    ) || null;

                },


                get selectedTherapistName() {

                    return this.selectedTherapist ?
                        this.selectedTherapist.name :
                        '';

                },


                /* ============================================================
                   SELECTED ADD-ON
                ============================================================ */

                get selectedAddOn() {

                    return this.addOns.find(addOn =>

                        String(addOn.id) ===
                        String(this.add_on_id)

                    ) || null;

                },


                get selectedAddOnName() {

                    return this.selectedAddOn ?
                        this.selectedAddOn.name :
                        'None';

                },


                /* ============================================================
                   TOTAL DURATION
                ============================================================ */

                get totalDuration() {

                    const serviceDuration =
                        Number(
                            this.selectedService?.duration_minutes || 0
                        );

                    const addOnDuration =
                        Number(
                            this.selectedAddOn?.duration_minutes || 0
                        );

                    return serviceDuration + addOnDuration;

                },


                /* ============================================================
                   TOTAL AMOUNT
                ============================================================ */

                get totalAmount() {

                    const servicePrice =
                        Number(
                            this.selectedService?.price || 0
                        );

                    const addOnPrice =
                        Number(
                            this.selectedAddOn?.price || 0
                        );

                    return servicePrice + addOnPrice;

                },


                /* ============================================================
                   SELECTED SLOT
                ============================================================ */

                get selectedSlot() {

                    return this.availableSlots.find(slot =>

                        String(slot.start) ===
                        String(this.appointment_time)

                        &&
                        slot.status === 'available'

                    ) || null;

                },


                get selectedSlotLabel() {

                    return this.selectedSlot ?
                        this.selectedSlot.label :
                        '';

                },


                /* ============================================================
                   PAYMENT LABELS
                ============================================================ */

                get paymentMethodLabel() {

                    if (this.payment_method === 'gcash') {
                        return 'GCash';
                    }

                    if (this.payment_method === 'branch') {
                        return 'Pay at Branch';
                    }

                    return 'Not selected';

                },


                get paymentTypeLabel() {

                    if (this.payment_type === 'full') {
                        return 'Full Payment';
                    }

                    if (this.payment_type === 'downpayment') {
                        return '50% Downpayment';
                    }

                    return '';

                },


                /* ============================================================
                   MONEY
                ============================================================ */

                formatMoney(value) {

                    return Number(value || 0)
                        .toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                },


                /* ============================================================
                   CAN CONTINUE
                ============================================================ */

                canGoNext() {

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 1
                    |--------------------------------------------------------------------------
                    */

                    if (this.step === 1) {

                        return Boolean(
                            this.service_id &&
                            this.level &&
                            this.has_previous_operations !== ''
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | STEP 2
                    |--------------------------------------------------------------------------
                    */

                    if (this.step === 2) {

                        return Boolean(
                            this.therapist_id
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | STEP 3
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    | The appointment time must correspond to an AVAILABLE slot.
                    |
                    */

                    if (this.step === 3) {

                        return Boolean(

                            this.appointment_date &&

                            this.appointment_time &&

                            this.selectedSlot &&

                            this.selectedSlot.status === 'available'

                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | STEP 4
                    |--------------------------------------------------------------------------
                    */

                    if (this.step === 4) {
                        if (this.payment_method === 'gcash') {
                            return Boolean(this.payment_method && this.payment_type);
                        }

                        return Boolean(this.payment_method);
                    }


                    return false;

                },


                /* ============================================================
                   NEXT STEP
                ============================================================ */

                nextStep() {

                    if (!this.canGoNext()) {
                        return;
                    }

                    if (this.step < 4) {

                        this.step++;

                        if (
                            this.step === 3 &&
                            this.service_id &&
                            this.therapist_id &&
                            this.appointment_date
                        ) {

                            this.loadSlots();

                        }

                    }

                },


                /* ============================================================
                PREVIOUS STEP
                ============================================================ */

                prevStep() {

                    if (this.step > 1) {
                        this.step--;
                    }

                },


                /* ============================================================
                   GO TO STEP
                ============================================================ */

                goToStep(targetStep) {

                    if (targetStep === 1) {

                        this.step = 1;

                        return;

                    }


                    if (targetStep === 2) {

                        if (
                            this.service_id &&
                            this.level &&
                            this.has_previous_operations !== ''
                        ) {

                            this.step = 2;

                        }

                        return;

                    }


                    if (targetStep === 3) {

                        if (
                            this.service_id &&
                            this.level &&
                            this.has_previous_operations !== '' &&
                            this.therapist_id
                        ) {

                            this.step = 3;

                            this.loadSlots();

                        }

                        return;

                    }


                    if (targetStep === 4) {

                        if (
                            this.service_id &&
                            this.level &&
                            this.has_previous_operations !== '' &&
                            this.therapist_id &&
                            this.appointment_date &&
                            this.selectedSlot
                        ) {

                            this.step = 4;

                        }

                    }

                },


                /* ============================================================
                   SELECT AVAILABLE SLOT
                ============================================================ */

                selectSlot(slot) {

                    /*
                    |--------------------------------------------------------------------------
                    | NEVER allow BOOKED or ADJUST SERVICE
                    |--------------------------------------------------------------------------
                    */

                    if (!slot) {
                        return;
                    }

                    if (slot.status !== 'available') {

                        console.warn(
                            'This slot cannot be selected:',
                            slot.status
                        );

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Select only genuinely available slot
                    |--------------------------------------------------------------------------
                    */

                    this.appointment_time =
                        slot.start;

                },


                /* ============================================================
                   SELECT RECOMMENDED SERVICE
                ============================================================ */

                async selectRecommendedService(
                    serviceId,
                    slotStart
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Change service
                    |--------------------------------------------------------------------------
                    */

                    this.service_id = serviceId;

                    this.appointment_time = '';


                    /*
                    |--------------------------------------------------------------------------
                    | Wait for Alpine/reactivity
                    |--------------------------------------------------------------------------
                    */

                    await this.$nextTick();


                    /*
                    |--------------------------------------------------------------------------
                    | Reload availability
                    |--------------------------------------------------------------------------
                    */

                    await this.loadSlots();


                    /*
                    |--------------------------------------------------------------------------
                    | Try to keep the same start time
                    |--------------------------------------------------------------------------
                    */

                    const matchingSlot =
                        this.availableSlots.find(slot =>

                            String(slot.start) ===
                            String(slotStart)

                            &&
                            slot.status === 'available'

                        );


                    if (matchingSlot) {

                        this.appointment_time =
                            matchingSlot.start;

                    }

                },


                /* ============================================================
                   LOAD AVAILABLE SLOTS
                ============================================================ */

                async loadSlots() {

                    this.availableSlots = [];

                    this.appointment_time = '';


                    /*
                    |--------------------------------------------------------------------------
                    | Required fields
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !this.service_id ||
                        !this.therapist_id ||
                        !this.appointment_date
                    ) {

                        return;

                    }


                    this.loadingSlots = true;


                    try {

                        const params =
                            new URLSearchParams({

                                therapist_id: this.therapist_id,

                                service_id: this.service_id,

                                add_on_id: this.add_on_id || '',

                                date: this.appointment_date

                            });


                        const response =
                            await fetch(

                                `${this.availableSlotsUrl}?${params.toString()}`,

                                {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                }

                            );


                        if (!response.ok) {

                            throw new Error(
                                'Failed to load available slots.'
                            );

                        }


                        const data =
                            await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | Normalize response
                        |--------------------------------------------------------------------------
                        */

                        this.availableSlots =
                            Array.isArray(data) ?
                            data.map(slot => ({

                                ...slot,

                                status: slot.status || 'booked',

                                available_minutes: Number(
                                    slot.available_minutes || 0
                                ),

                                required_minutes: Number(
                                    slot.required_minutes ||
                                    this.totalDuration
                                ),

                                recommendations: Array.isArray(
                                        slot.recommendations
                                    ) ?
                                    slot.recommendations : []

                            })) : [];


                    } catch (error) {

                        console.error(
                            'Availability error:',
                            error
                        );

                        this.availableSlots = [];


                    } finally {

                        this.loadingSlots = false;

                    }

                }

            };

        }
    </script>
@endpush
