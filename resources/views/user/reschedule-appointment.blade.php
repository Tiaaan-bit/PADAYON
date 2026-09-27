@extends('layouts.user')

@section('title', 'Padayon Massage Center - Reschedule Appointment')

@section('content')

    <div class="p-4 sm:p-6" x-data="rescheduleAppointment()">

        {{-- PAGE HEADER --}}
        <div class="mb-6">

            <h2 class="text-xl font-bold text-gray-800">
                Reschedule Appointment
            </h2>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- APPOINTMENT INFORMATION --}}
            <div class="lg:col-span-1">

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-xs font-semibold text-[#849753] uppercase tracking-wide">
                            Current Appointment
                        </p>

                        <h3 class="text-lg font-bold text-gray-800 mt-1">
                            Appointment #{{ $appointment->id }}
                        </h3>
                    </div>

                    <div class="p-5 space-y-4">

                        {{-- SERVICE --}}
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Service
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $appointment->service?->name ?? 'N/A' }}
                            </p>
                        </div>

                        {{-- THERAPIST --}}
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Therapist
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $appointment->therapist?->name ?? 'N/A' }}
                            </p>
                        </div>

                        {{-- DURATION --}}
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Duration
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ (int) $appointment->service_duration_minutes + (int) $appointment->addons_duration_minutes }}
                                minutes
                            </p>
                        </div>

                        {{-- ADD-ON --}}
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Add-on
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $appointment->addOn?->name ?? 'None' }}
                            </p>
                        </div>

                        {{-- CURRENT DATE --}}
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Current Date
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $appointment->appointment_date?->format('F d, Y') }}
                            </p>
                        </div>

                        {{-- CURRENT TIME --}}
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Current Time
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                -
                                {{ \Carbon\Carbon::parse($appointment->appointment_end_time)->format('g:i A') }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-[#F4EDDB] p-4">

                            <p class="text-sm font-semibold text-[#6F4E37]">
                                What can you change?
                            </p>

                            <p class="text-sm text-gray-600 mt-1">
                                Only your appointment date and time can be changed.
                                Your service, therapist, add-on, duration, price,
                                and payment remain unchanged.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- RESCHEDULE FORM --}}
            <div class="lg:col-span-2">

                <form method="POST" action="{{ route('user.appointments.reschedule.update', $appointment->id) }}"
                    @submit="submitting = true" class="bg-white rounded-2xl border border-gray-200 shadow-sm">

                    @csrf
                    @method('PUT')

                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">
                            Select New Schedule
                        </h3>
                    </div>

                    <div class="p-5 space-y-6">

                        {{-- DATE --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                New Appointment Date
                            </label>

                            <input type="date" name="appointment_date" x-model="selectedDate" @change="loadSlots()"
                                min="{{ now('Asia/Manila')->format('Y-m-d') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]"
                                required>

                            @error('appointment_date')
                                <p class="text-sm text-red-600 mt-2">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- TIME --}}
                        <div>

                            <div class="flex items-center justify-between mb-3">

                                <label class="block text-sm font-medium text-gray-700">
                                    Available Times
                                </label>

                                <span x-show="loading" x-cloak class="text-xs text-gray-400">
                                    Checking availability...
                                </span>

                            </div>


                            {{-- LOADING --}}
                            <div x-show="loading" x-cloak class="rounded-xl border border-gray-200 p-6 text-center">
                                <p class="text-sm text-gray-500">
                                    Checking available schedules...
                                </p>
                            </div>


                            {{-- EMPTY --}}
                            <div x-show="!loading && selectedDate && slots.length === 0" x-cloak
                                class="rounded-xl border border-gray-200 bg-gray-50 p-6 text-center">
                                <p class="text-sm font-medium text-gray-700">
                                    No available times
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    Please choose another date.
                                </p>
                            </div>


                            {{-- SLOTS --}}
                            <div x-show="!loading && slots.length > 0" x-cloak
                                class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">

                                <template x-for="slot in slots" :key="slot.start">

                                    <button type="button" @click="selectedTime = slot.start"
                                        :class="selectedTime === slot.start ?
                                            'border-[#849753] bg-[#849753] text-white' :
                                            'border-gray-200 bg-white text-gray-700 hover:border-[#849753] hover:bg-[#F4EDDB]'"
                                        class="rounded-xl border p-4 text-left transition">

                                        <span class="block font-semibold" x-text="slot.label"></span>

                                        <span class="block text-xs mt-1"
                                            :class="selectedTime === slot.start ?
                                                'text-white/80' :
                                                'text-gray-400'">
                                            Available
                                        </span>

                                    </button>

                                </template>

                            </div>


                            <input type="hidden" name="appointment_time" x-model="selectedTime">

                            @error('appointment_time')
                                <p class="text-sm text-red-600 mt-2">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- NEW SCHEDULE SUMMARY --}}
                        <div x-show="selectedDate && selectedTime" x-cloak
                            class="rounded-xl bg-[#F4EDDB] border border-[#849753]/20 p-4">

                            <p class="text-xs uppercase font-semibold text-[#6F4E37]">
                                New Schedule
                            </p>

                            <p class="text-lg font-bold text-gray-800 mt-1" x-text="formattedSchedule()"></p>

                        </div>


                        {{-- SERVER ERROR --}}
                        @if ($errors->has('appointment'))
                            <div class="rounded-xl bg-red-50 border border-red-200 p-4">
                                <p class="text-sm text-red-700">
                                    {{ $errors->first('appointment') }}
                                </p>
                            </div>
                        @endif

                    </div>


                    {{-- FOOTER --}}
                    <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row gap-3 sm:justify-end">

                        <a href="{{ route('user.my-appointments') }}"
                            class="rounded-xl border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 text-center">
                            Cancel
                        </a>

                        <button type="submit" :disabled="!selectedDate || !selectedTime || loading || submitting"
                            class="rounded-xl bg-[#849753] px-6 py-2.5 text-white text-sm font-medium hover:bg-[#6F4E37] transition disabled:opacity-50 disabled:cursor-not-allowed">

                            <span x-show="!submitting">
                                Confirm Reschedule
                            </span>

                            <span x-show="submitting" x-cloak>
                                Rescheduling...
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <script>
        function rescheduleAppointment() {
            return {
                selectedDate: '',
                selectedTime: '',
                slots: [],
                loading: false,
                submitting: false,

                async loadSlots() {
                    this.selectedTime = '';
                    this.slots = [];

                    if (!this.selectedDate) {
                        return;
                    }

                    this.loading = true;

                    try {
                        const url = new URL(
                            @js(route('user.appointments.reschedule.slots', $appointment->id)),
                            window.location.origin
                        );

                        url.searchParams.set('date', this.selectedDate);

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(
                                data.message ?? 'Unable to load available times.'
                            );
                        }

                        this.slots = data;

                    } catch (error) {
                        console.error(error);

                        this.slots = [];

                        alert(
                            error.message ??
                            'Unable to load available times.'
                        );

                    } finally {
                        this.loading = false;
                    }
                },

                formattedSchedule() {
                    if (!this.selectedDate || !this.selectedTime) {
                        return '';
                    }

                    const date = new Date(
                        this.selectedDate + 'T00:00:00'
                    );

                    const dateLabel = date.toLocaleDateString(
                        'en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric'
                        }
                    );

                    const slot = this.slots.find(
                        item => item.start === this.selectedTime
                    );

                    return slot ?
                        `${dateLabel} • ${slot.label}` :
                        dateLabel;
                }
            };
        }
    </script>

@endsection
