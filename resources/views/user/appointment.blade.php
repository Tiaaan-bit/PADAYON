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
                    'image' => $therapist->image,
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

        /*
    |--------------------------------------------------------------------------
    | IMPORTANT:
    | Manila date comes from Laravel/server.
    |--------------------------------------------------------------------------
    */

        $manilaToday = now('Asia/Manila')->format('Y-m-d');
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
        @js(route('user.appointments.availableSlots')),
        @js(route('user.appointments.store')),
        @js($manilaToday)
    )" x-init="init()" x-cloak class="px-4 sm:px-6 lg:px-8 py-6">

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- ============================================================
        LEFT
        ============================================================ --}}

            <div class="xl:col-span-2">

                <div class="bg-white rounded-3xl shadow-lg overflow-hidden">

                    {{-- HEADER --}}

                    <div class="p-6 border-b border-gray-100">

                        <h1 class="text-2xl font-bold text-gray-800">
                            Book an Appointment
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Complete the steps below to schedule your massage.
                        </p>

                    </div>


                    {{-- STEP INDICATOR --}}

                    <div class="px-6 py-5 border-b border-gray-100">

                        <div class="grid grid-cols-4 items-center">

                            <template x-for="number in [1,2,3,4]" :key="number">

                                <div class="flex justify-center">

                                    <button type="button" @click="goToStep(number)"
                                        class="flex items-center justify-center
                                           w-9 h-9 rounded-full
                                           text-sm font-semibold
                                           transition"
                                        :class="step === number ?
                                            'bg-[#849753] text-white' :
                                            step > number ?
                                            'bg-[#849753]/20 text-[#849753]' :
                                            'bg-gray-100 text-gray-400'"
                                        x-text="number"></button>

                                </div>

                            </template>

                        </div>


                        <div class="grid grid-cols-4 mt-3 text-xs text-gray-500">

                            <span class="text-center">
                                Service
                            </span>

                            <span class="text-center">
                                Therapist
                            </span>

                            <span class="text-center">
                                Schedule
                            </span>

                            <span class="text-center">
                                Payment
                            </span>

                        </div>

                    </div>


                    {{-- FORM --}}

                    <form method="POST" :action="storeUrl" class="p-6" @submit="beforeSubmit($event)">

                        @csrf

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


                        {{-- VALIDATION ERRORS --}}

                        @if ($errors->any())

                            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5">

                                <h3 class="font-semibold text-red-800">
                                    Please fix the following:
                                </h3>

                                <ul class="mt-2 space-y-1 text-sm text-red-700">

                                    @foreach ($errors->all() as $error)
                                        <li>
                                            • {{ $error }}
                                        </li>
                                    @endforeach

                                </ul>

                            </div>

                        @endif


                        {{-- ========================================================
                    STEP 1
                    ========================================================= --}}

                        <div x-show="step === 1">

                            <div class="mb-6">

                                <h2 class="text-xl font-bold text-gray-800">
                                    Choose your service
                                </h2>

                            </div>


                            {{-- SERVICE FILTER --}}

                            <div class="flex flex-wrap gap-2 mb-6">

                                <template x-for="filter in serviceFilters" :key="filter">

                                    <button type="button" @click="serviceFilter = filter"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition"
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

                                        <input type="radio" :value="service.id" x-model="service_id"
                                            class="peer sr-only">

                                        <div
                                            class="h-full border-2 border-gray-200
                                               rounded-2xl p-5 transition
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

                                    <template x-for="item in levels" :key="item.value">

                                        <label class="cursor-pointer">

                                            <input type="radio" :value="item.value" x-model="level"
                                                class="peer sr-only">

                                            <div
                                                class="text-center border-2 border-gray-200
                                                   rounded-xl py-3
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

                                    <label class="cursor-pointer block">

                                        <input type="radio" value="" x-model="add_on_id" class="peer sr-only">

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


                                    <template x-for="addOn in addOns" :key="addOn.id">

                                        <label class="cursor-pointer block">

                                            <input type="radio" :value="addOn.id" x-model="add_on_id"
                                                class="peer sr-only">

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

                                    <template x-for="item in previousOperationOptions" :key="item.value">

                                        <label class="cursor-pointer">

                                            <input type="radio" :value="item.value"
                                                x-model="has_previous_operations" class="peer sr-only">

                                            <div
                                                class="text-center border-2 border-gray-200
                                                   rounded-xl p-3
                                                   peer-checked:border-[#849753]
                                                   peer-checked:bg-[#849753]/5">

                                                <span x-text="item.label"></span>

                                            </div>

                                        </label>

                                    </template>

                                </div>

                            </div>


                            {{-- BODY PROBLEM --}}

                            <div class="mt-8">

                                <label class="block font-semibold text-gray-800 mb-2">
                                    Body Problem / Concern
                                </label>

                                <textarea x-model="body_problem" rows="4"
                                    class="w-full rounded-xl border-gray-300
                                       focus:border-[#849753]
                                       focus:ring-[#849753]"
                                    placeholder="Tell us about any pain, discomfort or body concern..."></textarea>

                            </div>

                        </div>


                        {{-- ========================================================
                    STEP 2
                    ========================================================= --}}

                        <div x-show="step === 2">

                            <div class="mb-6">

                                <h2 class="text-xl font-bold text-gray-800">
                                    Choose your therapist
                                </h2>

                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <template x-for="therapist in therapists" :key="therapist.id">

                                    <label class="cursor-pointer">

                                        <input type="radio" :value="therapist.id" x-model="therapist_id"
                                            class="peer sr-only">

                                        <div
                                            class="border-2 border-gray-200
                                               rounded-2xl overflow-hidden
                                               bg-white
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5
                                               transition
                                               hover:border-[#849753]/50
                                               hover:shadow-md">

                                            <div class="relative h-60 bg-gray-100">

                                                <template x-if="therapist.image">

                                                    <img :src="'/storage/' + therapist.image" :alt="therapist.name"
                                                        loading="lazy" decoding="async"
                                                        class="w-full h-full object-cover">

                                                </template>

                                                <template x-if="!therapist.image">

                                                    <div class="w-full h-full flex items-center justify-center">

                                                        <div
                                                            class="w-20 h-20 rounded-full
                                                               bg-[#849753]/10
                                                               flex items-center justify-center">

                                                            <span class="text-2xl font-bold text-[#849753]"
                                                                x-text="therapist.name?.charAt(0) || '?'"></span>

                                                        </div>

                                                    </div>

                                                </template>

                                            </div>


                                            <div class="p-4 text-center">

                                                <h3 class="font-semibold text-gray-800" x-text="therapist.name"></h3>

                                                <p class="text-xs text-green-600 mt-1">
                                                    Available
                                                </p>

                                            </div>

                                        </div>

                                    </label>

                                </template>

                            </div>

                        </div>


                        {{-- ========================================================
                    STEP 3
                    ========================================================= --}}

                        <div x-show="step === 3">

                            <div class="mb-6">

                                <h2 class="text-xl font-bold text-gray-800">
                                    Choose your schedule
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Available times are calculated using the selected
                                    service duration and therapist's current bookings.
                                </p>

                            </div>


                            {{-- DATE --}}

                            <div class="mb-6">

                                <label class="block font-semibold text-gray-800 mb-2">
                                    Appointment Date
                                </label>

                                <input type="date" x-model="appointment_date" :min="today"
                                    class="w-full rounded-xl border-gray-300
                                       focus:border-[#849753]
                                       focus:ring-[#849753]">

                            </div>


                            {{-- DURATION --}}

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


                            {{-- ERROR --}}

                            <div x-show="availabilityError"
                                class="mb-5 rounded-2xl border border-red-200
                                   bg-red-50 p-4">

                                <p class="font-semibold text-red-800" x-text="availabilityError"></p>

                                <button type="button" @click="loadSlots()"
                                    class="mt-2 text-sm font-semibold text-red-700 underline">
                                    Try again
                                </button>

                            </div>


                            {{-- LEGEND --}}

                            <div x-show="!loadingSlots && appointment_date && availableSlots.length"
                                class="mb-5 p-4 rounded-2xl
                                   bg-gray-50 border border-gray-200">

                                <p class="text-xs font-bold text-gray-500 uppercase mb-3">
                                    Schedule Status
                                </p>

                                <div class="flex flex-wrap gap-4">

                                    <div class="flex items-center gap-2">

                                        <span class="w-3 h-3 rounded-full bg-green-500"></span>

                                        <span class="text-sm text-gray-600">
                                            Available
                                        </span>

                                    </div>


                                    <div class="flex items-center gap-2">

                                        <span class="w-3 h-3 rounded-full bg-red-500"></span>

                                        <span class="text-sm text-gray-600">
                                            Booked
                                        </span>

                                    </div>


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


                            {{-- SLOT LIST --}}

                            <div x-show="!loadingSlots && availableSlots.length" class="space-y-3">

                                <template x-for="slot in availableSlots" :key="slot.start + '-' + slot.status">

                                    <div>

                                        {{-- AVAILABLE --}}

                                        <template x-if="slot.status === 'available'">

                                            <button type="button" @click="selectSlot(slot)"
                                                class="w-full text-left
                                                   border-2 rounded-2xl p-4
                                                   transition duration-200"
                                                :class="appointment_time === slot.start ?
                                                    'border-[#849753] bg-[#849753]/10 ring-2 ring-[#849753]/20' :
                                                    'border-green-200 bg-green-50 hover:border-[#849753]'">

                                                <div class="flex items-center justify-between gap-4">

                                                    <div class="flex items-center gap-4">

                                                        <div
                                                            class="w-11 h-11 rounded-full
                                                               bg-green-100
                                                               text-green-600
                                                               flex items-center
                                                               justify-center
                                                               text-lg shrink-0">
                                                            ✓
                                                        </div>

                                                        <div>
                                                            <p class="font-bold text-gray-800" x-text="slot.label">
                                                            </p>

                                                            <p class="text-xs text-gray-500 mt-1"
                                                                x-text="slot.date_label">
                                                            </p>

                                                            <p class="text-sm text-green-600 mt-1">
                                                                Full service duration is available
                                                            </p>
                                                        </div>

                                                    </div>


                                                    <span
                                                        class="px-3 py-1 rounded-full
                                                           bg-green-100
                                                           text-green-700
                                                           text-xs font-bold">
                                                        AVAILABLE
                                                    </span>

                                                </div>

                                            </button>

                                        </template>


                                        {{-- BOOKED --}}

                                        <template x-if="slot.status === 'booked'">

                                            <div
                                                class="w-full border-2 border-red-200
                                                   rounded-2xl p-4 bg-red-50">

                                                <div class="flex items-center justify-between gap-4">

                                                    <div class="flex items-center gap-4">

                                                        <div
                                                            class="w-11 h-11 rounded-full
                                                               bg-red-100
                                                               text-red-600
                                                               flex items-center
                                                               justify-center
                                                               text-lg shrink-0">
                                                            ✕
                                                        </div>

                                                        <div>
                                                            <p class="font-bold text-gray-800" x-text="slot.label">
                                                            </p>

                                                            <p class="text-xs text-gray-500 mt-1"
                                                                x-text="slot.date_label">
                                                            </p>

                                                            <p class="text-sm text-red-600 mt-1">
                                                                ⚠️ This time is already booked.
                                                            </p>
                                                        </div>

                                                    </div>


                                                    <span
                                                        class="px-3 py-1 rounded-full
                                                           bg-red-100
                                                           text-red-700
                                                           text-xs font-bold">
                                                        BOOKED
                                                    </span>

                                                </div>

                                            </div>

                                        </template>


                                        {{-- ADJUST SERVICE --}}

                                        <template x-if="slot.status === 'adjust_service'">

                                            <div
                                                class="w-full border-2 border-amber-300
                                                   rounded-2xl bg-amber-50 overflow-hidden">

                                                <div class="p-4">

                                                    <div class="flex items-start justify-between gap-4">

                                                        <div class="flex items-start gap-4">

                                                            <div
                                                                class="w-11 h-11 rounded-full
                                                                   bg-amber-100
                                                                   text-amber-700
                                                                   flex items-center
                                                                   justify-center
                                                                   text-lg shrink-0">
                                                                ⚠️
                                                            </div>

                                                            <div>
                                                                <p class="font-bold text-gray-800" x-text="slot.label">
                                                                </p>

                                                                <p class="text-xs text-gray-500 mt-1"
                                                                    x-text="slot.date_label">
                                                                </p>

                                                                <p class="text-sm font-semibold text-amber-700 mt-1">
                                                                    Adjust your service time
                                                                </p>

                                                                <p class="text-sm text-gray-600 mt-1"
                                                                    x-text="slot.message">
                                                                </p>
                                                            </div>

                                                        </div>


                                                        <span
                                                            class="px-3 py-1 rounded-full
                                                               bg-amber-100
                                                               text-amber-700
                                                               text-xs font-bold">
                                                            ADJUST
                                                        </span>

                                                    </div>


                                                    <div class="grid grid-cols-2 gap-3 mt-4">

                                                        <div class="bg-white rounded-xl p-3 border border-amber-100">

                                                            <p class="text-xs text-gray-500">
                                                                Available time
                                                            </p>

                                                            <p class="font-bold text-gray-800 mt-1">

                                                                <span x-text="slot.available_minutes"></span>

                                                                min

                                                            </p>

                                                        </div>


                                                        <div class="bg-white rounded-xl p-3 border border-amber-100">

                                                            <p class="text-xs text-gray-500">
                                                                Required time
                                                            </p>

                                                            <p class="font-bold text-gray-800 mt-1">

                                                                <span x-text="slot.required_minutes"></span>

                                                                min

                                                            </p>

                                                        </div>

                                                    </div>

                                                </div>


                                                {{-- RECOMMENDATIONS --}}

                                                <template
                                                    x-if="
                                                    slot.recommendations &&
                                                    slot.recommendations.length
                                                ">

                                                    <div
                                                        class="border-t border-amber-200
                                                           bg-amber-100/50 p-4">

                                                        <p
                                                            class="text-sm font-bold
                                                               text-amber-900 mb-3">
                                                            💡 Try a shorter service:
                                                        </p>


                                                        <div class="space-y-2">

                                                            <template x-for="recommendation in slot.recommendations"
                                                                :key="recommendation.id">

                                                                <button type="button"
                                                                    @click="
                                                                    selectRecommendedService(
                                                                        recommendation.id,
                                                                        slot.start
                                                                    )
                                                                "
                                                                    class="w-full text-left
                                                                       bg-white rounded-xl
                                                                       border border-amber-200
                                                                       p-3 hover:border-[#849753]
                                                                       transition">

                                                                    <div class="flex justify-between gap-3">

                                                                        <div>

                                                                            <p class="font-semibold text-gray-800"
                                                                                x-text="recommendation.name"></p>

                                                                            <p class="text-xs text-gray-500 mt-1">

                                                                                <span
                                                                                    x-text="recommendation.duration_minutes"></span>

                                                                                min service

                                                                                +

                                                                                <span
                                                                                    x-text="selectedAddOn?.duration_minutes || 0"></span>

                                                                                min add-on

                                                                                =

                                                                                <span class="font-semibold"
                                                                                    x-text="recommendation.total_duration"></span>

                                                                                min total

                                                                            </p>

                                                                        </div>


                                                                        <div class="text-right shrink-0">

                                                                            <p class="font-bold text-[#849753]">

                                                                                ₱<span
                                                                                    x-text="formatMoney(recommendation.total_price)"></span>

                                                                            </p>

                                                                            <p
                                                                                class="text-xs text-[#849753]
                                                                                   font-semibold mt-1">
                                                                                Choose
                                                                            </p>

                                                                        </div>

                                                                    </div>

                                                                </button>

                                                            </template>

                                                        </div>

                                                    </div>

                                                </template>


                                                <template
                                                    x-if="
                                                    !slot.recommendations ||
                                                    !slot.recommendations.length
                                                ">

                                                    <div
                                                        class="border-t border-amber-200
                                                           p-4 text-sm text-amber-800">
                                                        No shorter service is available
                                                        for this time. Please choose another
                                                        schedule.
                                                    </div>

                                                </template>

                                            </div>

                                        </template>

                                    </div>

                                </template>

                            </div>


                            {{-- NO SLOTS --}}

                            <div x-show="
                                !loadingSlots &&
                                appointment_date &&
                                !availabilityError &&
                                availableSlots.length === 0
                            "
                                class="py-10 text-center bg-gray-50 rounded-2xl">

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

                            <div x-show="!appointment_date" class="py-10 text-center bg-gray-50 rounded-2xl">

                                <div class="text-3xl mb-2">
                                    📅
                                </div>

                                <p class="text-gray-500">
                                    Please select an appointment date first.
                                </p>

                            </div>

                        </div>


                        {{-- ========================================================
                    STEP 4
                    ========================================================= --}}

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

                                    <input type="radio" value="branch" x-model="payment_method" class="peer sr-only">

                                    <div
                                        class="border-2 border-gray-200 rounded-2xl p-5
                                           peer-checked:border-[#849753]
                                           peer-checked:bg-[#849753]/5">

                                        <h3 class="font-semibold">
                                            Pay at Branch
                                        </h3>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Pay directly at the massage center.
                                        </p>

                                        <p class="text-sm font-semibold text-[#849753] mt-3">
                                            Payment due at branch
                                        </p>

                                    </div>

                                </label>


                                <label class="cursor-pointer">

                                    <input type="radio" value="gcash" x-model="payment_method" class="peer sr-only">

                                    <div
                                        class="border-2 border-gray-200 rounded-2xl p-5
                                           peer-checked:border-[#849753]
                                           peer-checked:bg-[#849753]/5">

                                        <h3 class="font-semibold">
                                            GCash
                                        </h3>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Pay securely through PayMongo.
                                        </p>

                                    </div>

                                </label>

                            </div>


                            {{-- GCash TYPE --}}

                            <div x-show="payment_method === 'gcash'" class="mt-6">

                                <h3 class="font-semibold text-gray-800 mb-3">
                                    Payment Type
                                </h3>

                                <div class="grid grid-cols-2 gap-3">

                                    <label class="cursor-pointer">

                                        <input type="radio" value="full" x-model="payment_type"
                                            class="peer sr-only">

                                        <div
                                            class="border-2 border-gray-200 rounded-xl p-4
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5">

                                            <p class="font-semibold">
                                                Full Payment
                                            </p>

                                            <p class="text-sm text-gray-500 mt-1">

                                                ₱<span x-text="formatMoney(totalAmount)"></span>

                                            </p>

                                        </div>

                                    </label>


                                    <label class="cursor-pointer">

                                        <input type="radio" value="downpayment" x-model="payment_type"
                                            class="peer sr-only">

                                        <div
                                            class="border-2 border-gray-200 rounded-xl p-4
                                               peer-checked:border-[#849753]
                                               peer-checked:bg-[#849753]/5">

                                            <p class="font-semibold">
                                                50% Downpayment
                                            </p>

                                            <p class="text-sm text-gray-500 mt-1">

                                                ₱<span x-text="formatMoney(downpaymentAmount)"></span>

                                            </p>

                                        </div>

                                    </label>

                                </div>

                            </div>


                            {{-- PAYMENT INFORMATION --}}

                            <div x-show="payment_method"
                                class="mt-6 p-5 rounded-2xl
                                   bg-gray-50 border border-gray-200">

                                <div class="space-y-3">

                                    <div class="flex justify-between">

                                        <span class="text-gray-500">
                                            Appointment total
                                        </span>

                                        <span class="font-semibold">

                                            ₱<span x-text="formatMoney(totalAmount)"></span>

                                        </span>

                                    </div>


                                    <div x-show="payment_method === 'gcash'" class="flex justify-between">

                                        <span class="text-gray-500">
                                            Pay now
                                        </span>

                                        <span class="font-bold text-[#849753]">

                                            ₱<span x-text="formatMoney(paymentAmount)"></span>

                                        </span>

                                    </div>


                                    <div x-show="
                                        payment_method === 'gcash' &&
                                        payment_type === 'downpayment'
                                    "
                                        class="flex justify-between">

                                        <span class="text-gray-500">
                                            Remaining balance
                                        </span>

                                        <span class="font-semibold">

                                            ₱<span x-text="formatMoney(remainingBalance)"></span>

                                        </span>

                                    </div>


                                    <div x-show="payment_method === 'branch'" class="text-sm text-gray-600">

                                        No online payment is required.
                                        Your appointment will be submitted as
                                        <strong>pending</strong> until confirmed
                                        by the branch.

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- NAVIGATION --}}

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


                            <button type="submit" x-show="step === 4" :disabled="!canGoNext() || submitting"
                                class="px-6 py-3 rounded-xl
                                   bg-[#849753] text-white
                                   font-semibold
                                   disabled:opacity-50
                                   disabled:cursor-not-allowed
                                   hover:bg-[#6f8144]">

                                <span x-show="!submitting">
                                    Confirm Appointment
                                </span>

                                <span x-show="submitting">
                                    Processing...
                                </span>

                            </button>

                        </div>

                    </form>

                </div>

            </div>


            {{-- ============================================================
        RIGHT SIDE SUMMARY
        ============================================================ --}}

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

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Service
                            </p>

                            <div class="flex justify-between gap-3 mt-1">

                                <div>

                                    <p class="font-semibold text-gray-800" x-text="selectedServiceName || 'Not selected'">
                                    </p>

                                    <p x-show="selectedService" class="text-xs text-gray-500 mt-1">

                                        <span x-text="selectedService?.duration_minutes || 0"></span>

                                        minutes

                                    </p>

                                </div>

                                <button type="button" @click="goToStep(1)" class="text-xs text-[#849753] font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- ADD-ON --}}

                        <div>

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Add-on
                            </p>

                            <div class="flex justify-between gap-3 mt-1">

                                <div>

                                    <p class="font-semibold text-gray-800" x-text="selectedAddOnName"></p>

                                    <p x-show="selectedAddOn" class="text-xs text-gray-500 mt-1">

                                        +

                                        <span x-text="selectedAddOn?.duration_minutes || 0"></span>

                                        minutes

                                    </p>

                                </div>

                                <button type="button" @click="goToStep(1)" class="text-xs text-[#849753] font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- THERAPIST --}}

                        <div>

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Therapist
                            </p>

                            <div class="flex justify-between gap-3 mt-1">

                                <p class="font-semibold text-gray-800" x-text="selectedTherapistName || 'Not selected'">
                                </p>

                                <button type="button" @click="goToStep(2)" class="text-xs text-[#849753] font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- SCHEDULE --}}

                        <div>

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Schedule
                            </p>

                            <div class="flex justify-between gap-3 mt-1">

                                <div>

                                    <p class="font-semibold text-gray-800" x-text="appointment_date || 'Not selected'">
                                    </p>

                                    <p x-show="selectedSlotLabel" class="text-xs text-gray-500 mt-1"
                                        x-text="selectedSlotLabel"></p>

                                </div>

                                <button type="button" @click="goToStep(3)" class="text-xs text-[#849753] font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- PAYMENT --}}

                        <div>

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Payment
                            </p>

                            <div class="flex justify-between gap-3 mt-1">

                                <div>

                                    <p class="font-semibold text-gray-800" x-text="paymentMethodLabel"></p>

                                    <p x-show="payment_type" class="text-xs text-gray-500 mt-1"
                                        x-text="paymentTypeLabel"></p>

                                </div>

                                <button type="button" @click="goToStep(4)" class="text-xs text-[#849753] font-semibold">
                                    Change
                                </button>

                            </div>

                        </div>


                        <div class="border-t border-gray-100"></div>


                        {{-- DURATION --}}

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


                        {{-- PAY NOW --}}

                        <div x-show="payment_method === 'gcash'"
                            class="p-4 rounded-xl bg-[#849753]/10
                               border border-[#849753]/20">

                            <div class="flex justify-between">

                                <span class="text-sm text-gray-600">
                                    Pay now
                                </span>

                                <span class="font-bold text-[#849753]">

                                    ₱<span x-text="formatMoney(paymentAmount)"></span>

                                </span>

                            </div>

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
            availableSlotsUrl = '',
            storeUrl = '',
            manilaToday = ''
        ) {

            return {

                /*
                |--------------------------------------------------------------------------
                | Wizard
                |--------------------------------------------------------------------------
                */

                step: 1,

                submitting: false,

                /*
                |--------------------------------------------------------------------------
                | Data
                |--------------------------------------------------------------------------
                */

                services,
                therapists,
                addOns,

                availableSlots: [],

                loadingSlots: false,

                availabilityError: '',

                availableSlotsUrl,

                storeUrl,

                today: manilaToday,

                /*
                |--------------------------------------------------------------------------
                | Form
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | Service Filters
                |--------------------------------------------------------------------------
                */

                serviceFilter: services.length ?
                    services[0].name : '',

                get serviceFilters() {

                    return [
                        ...new Set(
                            this.services
                            .map(service => service.name)
                            .filter(Boolean)
                        )
                    ];
                },

                /*
                |--------------------------------------------------------------------------
                | Options
                |--------------------------------------------------------------------------
                */

                levels: [{
                        value: 'gentle',
                        label: 'Gentle'
                    },
                    {
                        value: 'mild',
                        label: 'Mild'
                    },
                    {
                        value: 'hard',
                        label: 'Hard'
                    }
                ],

                previousOperationOptions: [{
                        value: 'yes',
                        label: 'Yes'
                    },
                    {
                        value: 'no',
                        label: 'No'
                    }
                ],

                /*
                |--------------------------------------------------------------------------
                | Initialization
                |--------------------------------------------------------------------------
                */

                init() {

                    /*
                    |--------------------------------------------------------------------------
                    | GCash / Branch payment watcher
                    |--------------------------------------------------------------------------
                    */

                    this.$watch(
                        'payment_method',
                        (value) => {

                            if (value === 'branch') {
                                this.payment_type = '';
                            }

                            if (
                                value === 'gcash' &&
                                !this.payment_type
                            ) {
                                this.payment_type = 'full';
                            }
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Service
                    |--------------------------------------------------------------------------
                    |
                    | We reload availability when service changes.
                    |
                    | Do NOT manually clear appointment_time here.
                    | loadSlots() will preserve it if still valid.
                    |--------------------------------------------------------------------------
                    */

                    this.$watch(
                        'service_id',
                        async () => {

                            if (
                                this.step === 3 &&
                                this.therapist_id &&
                                this.appointment_date
                            ) {
                                await this.loadSlots();
                            } else {
                                this.appointment_time = '';
                            }
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Add-on
                    |--------------------------------------------------------------------------
                    */

                    this.$watch(
                        'add_on_id',
                        async () => {

                            if (
                                this.step === 3 &&
                                this.service_id &&
                                this.therapist_id &&
                                this.appointment_date
                            ) {
                                await this.loadSlots();
                            } else {
                                this.appointment_time = '';
                            }
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Therapist
                    |--------------------------------------------------------------------------
                    */

                    this.$watch(
                        'therapist_id',
                        async () => {

                            if (
                                this.step === 3 &&
                                this.service_id &&
                                this.appointment_date
                            ) {
                                await this.loadSlots();
                            } else {
                                this.appointment_time = '';
                            }
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Date
                    |--------------------------------------------------------------------------
                    */

                    this.$watch(
                        'appointment_date',
                        async () => {

                            /*
                            |--------------------------------------------------------------------------
                            | Changing date should always clear the previous
                            | selected time.
                            |--------------------------------------------------------------------------
                            */

                            this.appointment_time = '';

                            if (
                                this.step === 3 &&
                                this.service_id &&
                                this.therapist_id &&
                                this.appointment_date
                            ) {
                                await this.loadSlots();
                            }
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Existing old form data
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

                /*
                |--------------------------------------------------------------------------
                | Filtered Services
                |--------------------------------------------------------------------------
                */

                get filteredServices() {

                    if (!this.serviceFilter) {
                        return this.services;
                    }

                    return this.services.filter(
                        service =>
                        String(service.name || '')
                        .toLowerCase()
                        .includes(
                            String(this.serviceFilter)
                            .toLowerCase()
                        )
                    );
                },

                /*
                |--------------------------------------------------------------------------
                | Selected Service
                |--------------------------------------------------------------------------
                */

                get selectedService() {

                    return this.services.find(
                        service =>
                        String(service.id) ===
                        String(this.service_id)
                    ) || null;
                },

                get selectedServiceName() {

                    return this.selectedService ?
                        this.selectedService.name :
                        '';
                },

                /*
                |--------------------------------------------------------------------------
                | Selected Therapist
                |--------------------------------------------------------------------------
                */

                get selectedTherapist() {

                    return this.therapists.find(
                        therapist =>
                        String(therapist.id) ===
                        String(this.therapist_id)
                    ) || null;
                },

                get selectedTherapistName() {

                    return this.selectedTherapist ?
                        this.selectedTherapist.name :
                        '';
                },

                /*
                |--------------------------------------------------------------------------
                | Selected Add-on
                |--------------------------------------------------------------------------
                */

                get selectedAddOn() {

                    return this.addOns.find(
                        addOn =>
                        String(addOn.id) ===
                        String(this.add_on_id)
                    ) || null;
                },

                get selectedAddOnName() {

                    return this.selectedAddOn ?
                        this.selectedAddOn.name :
                        'None';
                },

                /*
                |--------------------------------------------------------------------------
                | Total Duration
                |--------------------------------------------------------------------------
                */

                get totalDuration() {

                    const serviceDuration =
                        Number(
                            this.selectedService
                            ?.duration_minutes || 0
                        );

                    const addOnDuration =
                        Number(
                            this.selectedAddOn
                            ?.duration_minutes || 0
                        );

                    return serviceDuration +
                        addOnDuration;
                },

                /*
                |--------------------------------------------------------------------------
                | Total Amount
                |--------------------------------------------------------------------------
                */

                get totalAmount() {

                    const servicePrice =
                        Number(
                            this.selectedService
                            ?.price || 0
                        );

                    const addOnPrice =
                        Number(
                            this.selectedAddOn
                            ?.price || 0
                        );

                    return servicePrice +
                        addOnPrice;
                },

                /*
                |--------------------------------------------------------------------------
                | Downpayment
                |--------------------------------------------------------------------------
                */

                get downpaymentAmount() {

                    return Math.round(
                        this.totalAmount * 0.5 * 100
                    ) / 100;
                },

                /*
                |--------------------------------------------------------------------------
                | Payment Amount
                |--------------------------------------------------------------------------
                */

                get paymentAmount() {

                    if (this.payment_method === 'branch') {
                        return 0;
                    }

                    if (
                        this.payment_type === 'downpayment'
                    ) {
                        return this.downpaymentAmount;
                    }

                    return this.totalAmount;
                },

                /*
                |--------------------------------------------------------------------------
                | Remaining Balance
                |--------------------------------------------------------------------------
                */

                get remainingBalance() {

                    return Math.max(
                        0,
                        this.totalAmount -
                        this.paymentAmount
                    );
                },

                /*
                |--------------------------------------------------------------------------
                | Selected Slot
                |--------------------------------------------------------------------------
                */

                get selectedSlot() {

                    return this.availableSlots.find(
                        slot =>
                        String(slot.start) ===
                        String(this.appointment_time) &&
                        slot.status === 'available'
                    ) || null;
                },

                get selectedSlotLabel() {

                    return this.selectedSlot ?
                        this.selectedSlot.label :
                        '';
                },

                /*
                |--------------------------------------------------------------------------
                | Selected Slot Date
                |--------------------------------------------------------------------------
                */

                get selectedSlotDateLabel() {

                    return this.selectedSlot ?
                        this.selectedSlot.date_label :
                        '';
                },

                /*
                |--------------------------------------------------------------------------
                | Payment Labels
                |--------------------------------------------------------------------------
                */

                get paymentMethodLabel() {

                    if (
                        this.payment_method === 'gcash'
                    ) {
                        return 'GCash';
                    }

                    if (
                        this.payment_method === 'branch'
                    ) {
                        return 'Pay at Branch';
                    }

                    return 'Not selected';
                },

                get paymentTypeLabel() {

                    if (
                        this.payment_type === 'full'
                    ) {
                        return 'Full Payment';
                    }

                    if (
                        this.payment_type === 'downpayment'
                    ) {
                        return '50% Downpayment';
                    }

                    return '';
                },

                /*
                |--------------------------------------------------------------------------
                | Money
                |--------------------------------------------------------------------------
                */

                formatMoney(value) {

                    return Number(value || 0)
                        .toLocaleString(
                            'en-PH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }
                        );
                },

                /*
                |--------------------------------------------------------------------------
                | Can Continue
                |--------------------------------------------------------------------------
                */

                canGoNext() {

                    /*
                    |--------------------------------------------------------------------------
                    | Step 1
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
                    | Step 2
                    |--------------------------------------------------------------------------
                    */

                    if (this.step === 2) {

                        return Boolean(
                            this.therapist_id
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Step 3
                    |--------------------------------------------------------------------------
                    */

                    if (this.step === 3) {

                        return Boolean(
                            this.appointment_date &&
                            this.appointment_time &&
                            this.selectedSlot
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Step 4
                    |--------------------------------------------------------------------------
                    */

                    if (this.step === 4) {

                        if (
                            this.payment_method === 'gcash'
                        ) {

                            return Boolean(
                                this.payment_method &&
                                this.payment_type
                            );
                        }

                        return Boolean(
                            this.payment_method
                        );
                    }

                    return false;
                },

                /*
                |--------------------------------------------------------------------------
                | Next
                |--------------------------------------------------------------------------
                */

                async nextStep() {

                    if (!this.canGoNext()) {
                        return;
                    }

                    if (this.step < 4) {
                        this.step++;
                    }

                    if (
                        this.step === 3 &&
                        this.service_id &&
                        this.therapist_id &&
                        this.appointment_date
                    ) {

                        await this.loadSlots();
                    }
                },

                /*
                |--------------------------------------------------------------------------
                | Previous
                |--------------------------------------------------------------------------
                */

                prevStep() {

                    if (this.step > 1) {
                        this.step--;
                    }
                },

                /*
                |--------------------------------------------------------------------------
                | Go To Step
                |--------------------------------------------------------------------------
                */

                async goToStep(targetStep) {

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

                            if (this.appointment_date) {
                                await this.loadSlots();
                            }
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

                /*
                |--------------------------------------------------------------------------
                | Select Slot
                |--------------------------------------------------------------------------
                */

                selectSlot(slot) {

                    if (!slot) {
                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Only genuinely available slots can be selected.
                    |--------------------------------------------------------------------------
                    */

                    if (
                        slot.status !== 'available'
                    ) {
                        return;
                    }

                    this.appointment_time =
                        slot.start;
                },

                /*
                |--------------------------------------------------------------------------
                | Select Recommended Shorter Service
                |--------------------------------------------------------------------------
                */

                async selectRecommendedService(
                    serviceId,
                    slotStart
                ) {

                    this.service_id =
                        serviceId;

                    await this.$nextTick();

                    await this.loadSlots(
                        slotStart
                    );
                },

                /*
                |--------------------------------------------------------------------------
                | Load Availability
                |--------------------------------------------------------------------------
                |
                | preserveTime:
                |
                | When service/add-on/therapist changes, we try to keep the
                | same selected time if that time is still available.
                |
                |--------------------------------------------------------------------------
                */

                async loadSlots(
                    preserveTime = null
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Remember current selection
                    |--------------------------------------------------------------------------
                    */

                    const previousTime =
                        preserveTime ||
                        this.appointment_time ||
                        '';

                    this.availableSlots = [];

                    this.availabilityError = '';

                    /*
                    |--------------------------------------------------------------------------
                    | Do NOT immediately clear appointment_time.
                    |
                    | We first check whether the previous time remains available.
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !this.service_id ||
                        !this.therapist_id ||
                        !this.appointment_date
                    ) {

                        this.appointment_time = '';

                        this.loadingSlots = false;

                        return;
                    }

                    this.loadingSlots = true;

                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | Request Parameters
                        |--------------------------------------------------------------------------
                        */

                        const params =
                            new URLSearchParams({
                                therapist_id: this.therapist_id,

                                service_id: this.service_id,

                                add_on_id: this.add_on_id || '',

                                date: this.appointment_date
                            });

                        /*
                        |--------------------------------------------------------------------------
                        | Fetch Availability
                        |--------------------------------------------------------------------------
                        */

                        const response =
                            await fetch(
                                `${this.availableSlotsUrl}?${params.toString()}`, {
                                    method: 'GET',

                                    headers: {
                                        'Accept': 'application/json',

                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                }
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | Parse Response
                        |--------------------------------------------------------------------------
                        */

                        let data = null;

                        try {

                            data =
                                await response.json();

                        } catch (jsonError) {

                            throw new Error(
                                'The server returned an invalid response.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Server Error
                        |--------------------------------------------------------------------------
                        */

                        if (!response.ok) {

                            throw new Error(
                                data?.message ||
                                'Unable to load therapist availability.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Normalize Slots
                        |--------------------------------------------------------------------------
                        */

                        this.availableSlots =
                            Array.isArray(data) ?
                            data.map(
                                slot => ({

                                    ...slot,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Status
                                    |--------------------------------------------------------------------------
                                    */

                                    status: slot.status ||
                                        'booked',

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Date Label
                                    |--------------------------------------------------------------------------
                                    */

                                    date_label: slot.date_label ||
                                        this.appointment_date,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Available Minutes
                                    |--------------------------------------------------------------------------
                                    */

                                    available_minutes: Number(
                                        slot.available_minutes ||
                                        0
                                    ),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Required Minutes
                                    |--------------------------------------------------------------------------
                                    */

                                    required_minutes: Number(
                                        slot.required_minutes ||
                                        this.totalDuration
                                    ),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Recommendations
                                    |--------------------------------------------------------------------------
                                    */

                                    recommendations: Array.isArray(
                                            slot.recommendations
                                        ) ?
                                        slot.recommendations : []
                                })
                            ) : [];

                        /*
                        |--------------------------------------------------------------------------
                        | Restore Previously Selected Time
                        |--------------------------------------------------------------------------
                        |
                        | Only restore it if:
                        |
                        | 1. It still exists.
                        | 2. It is still available.
                        |--------------------------------------------------------------------------
                        */

                        const matchingSlot =
                            this.availableSlots.find(
                                slot =>
                                String(slot.start) ===
                                String(previousTime) &&
                                slot.status === 'available'
                            );

                        if (matchingSlot) {

                            this.appointment_time =
                                matchingSlot.start;

                        } else {

                            this.appointment_time = '';
                        }

                    } catch (error) {

                        console.error(
                            'Availability error:',
                            error
                        );

                        this.availableSlots = [];

                        this.appointment_time = '';

                        this.availabilityError =
                            error?.message ||
                            'Unable to load availability. Please try again.';

                    } finally {

                        this.loadingSlots = false;
                    }
                },

                /*
                |--------------------------------------------------------------------------
                | Submit
                |--------------------------------------------------------------------------
                */

                beforeSubmit(event) {

                    if (this.submitting) {

                        event.preventDefault();

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Final browser-side validation
                    |--------------------------------------------------------------------------
                    |
                    | Server remains authoritative.
                    |--------------------------------------------------------------------------
                    */

                    if (!this.service_id) {

                        event.preventDefault();

                        this.step = 1;

                        return;
                    }

                    if (!this.therapist_id) {

                        event.preventDefault();

                        this.step = 2;

                        return;
                    }

                    if (
                        !this.appointment_date ||
                        !this.selectedSlot
                    ) {

                        event.preventDefault();

                        this.step = 3;

                        return;
                    }

                    if (!this.payment_method) {

                        event.preventDefault();

                        this.step = 4;

                        return;
                    }

                    if (
                        this.payment_method === 'gcash' &&
                        !this.payment_type
                    ) {

                        event.preventDefault();

                        this.step = 4;

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Prevent Duplicate Submission
                    |--------------------------------------------------------------------------
                    */

                    this.submitting = true;
                }
            };
        }
    </script>
@endpush
