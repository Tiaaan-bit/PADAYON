@extends('layouts.admin')

@section('title', 'Padayon Massage Center -Dashboard')

@push('styles')
    <style>
        .calendar-day {
            min-height: 60px;
        }

        @media (max-width: 640px) {
            .calendar-day {
                min-height: 50px;
            }
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 9999px;
        }

        /*
                            |--------------------------------------------------------------------------
                            | Calendar Hover Popup
                            |--------------------------------------------------------------------------
                            */

        .calendar-popup {
            visibility: hidden;
            opacity: 0;
            transform: translateY(4px);
            transition: all 0.15s ease;
            pointer-events: none;
        }

        .calendar-day:hover .calendar-popup {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        /*
                            |--------------------------------------------------------------------------
                            | Calendar Highlight
                            |--------------------------------------------------------------------------
                            */

        .calendar-highlight {
            opacity: 0;
            transition: opacity 0.15s ease;
            pointer-events: none;
        }

        .calendar-day:hover .calendar-highlight {
            opacity: 1;
        }
    </style>
@endpush


@section('content')
    <div x-data="dashboardCalendar(@js($calendarEvents))">
        <div class="px-4 sm:px-6 lg:px-8 py-5">

            {{-- =========================================================
                MAIN DASHBOARD
            ========================================================== --}}
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_320px] gap-5">


                {{-- =====================================================
                    LEFT SIDE
                ====================================================== --}}
                <section class="min-w-0">


                    {{-- =================================================
                        WELCOME IMAGE
                    ================================================== --}}
                    <div
                        class="relative w-full overflow-hidden rounded-2xl bg-white
                        border border-gray-200 shadow-sm
                        h-55 sm:h-75 md:h-100 lg:min-h-125 xl:min-h-125">

                        <img src="{{ asset('images/Welcome.webp') }}" alt="Welcome"
                            class="absolute inset-0 w-full h-full object-fill object-center">

                    </div>


                    {{-- =================================================
                        THERAPISTS
                    ================================================== --}}
                    <section
                        class="mt-5 rounded-2xl bg-white border border-gray-200
                        shadow-sm overflow-hidden">

                        <div class="px-4 py-4">

                            <div class="flex items-center gap-2">

                                <h2 class="text-sm font-semibold text-gray-900">
                                    Duty for today
                                </h2>

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                Currently Available Therapist
                            </p>

                        </div>


                        <div class="px-4 pb-4">

                            <div class="grid grid-cols-4 gap-3">

                                @forelse($therapists as $therapist)
                                    <div
                                        class="rounded-xl border border-gray-200
                                        bg-white shadow-sm overflow-hidden
                                        hover:shadow-md transition">

                                        {{-- Therapist image --}}
                                        <div class="relative h-60 bg-gray-100">

                                            @if ($therapist->image)
                                                <img src="{{ asset('storage/' . $therapist->image) }}"
                                                    alt="{{ $therapist->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div
                                                    class="w-full h-full flex
                                                    items-center justify-center">

                                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                        stroke-width="1.5" viewBox="0 0 24 24">
                                                        <path
                                                            d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />
                                                    </svg>

                                                </div>
                                            @endif

                                        </div>


                                        {{-- Card content --}}
                                        <div class="p-2">

                                            <h3
                                                class="text-xs font-semibold
                                                text-gray-800 truncate">
                                                {{ $therapist->name }}
                                            </h3>

                                            <p
                                                class="text-[10px] text-[#849753]
                                                font-medium truncate">
                                                {{ $therapist->specialty }}
                                            </p>

                                        </div>

                                    </div>

                                @empty

                                    <div class="col-span-4 text-center py-6">

                                        <p class="text-xs text-gray-500">
                                            No therapists available today.
                                        </p>

                                    </div>
                                @endforelse

                            </div>

                        </div>

                    </section>


                    {{-- =================================================
                        TODAY'S APPOINTMENTS TABLE
                    ================================================== --}}
                    <section
                        class="mt-5 rounded-2xl bg-white border border-gray-200
                        shadow-sm overflow-hidden">

                        {{-- Header --}}
                        <div
                            class="px-5 py-4 border-b border-gray-100
                            flex items-center justify-between">

                            <div>

                                <h2 class="text-sm font-semibold text-gray-900">
                                    Today's Appointments
                                </h2>

                                <p class="text-xs text-gray-500 mt-1">
                                    Appointment schedule for today
                                </p>

                            </div>

                            <span class="text-xs text-gray-400">
                                {{ $appointments->count() }} appointments
                            </span>

                        </div>


                        @if ($appointments->isEmpty())

                            {{-- Empty State --}}
                            <div class="py-16 text-center">

                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>

                                <p class="text-sm text-gray-400">
                                    No appointments scheduled for today.
                                </p>

                            </div>
                        @else
                            <div class="overflow-x-auto scrollbar-thin">

                                <table class="w-full text-sm">

                                    <thead>

                                        <tr class="bg-gray-50 text-left">

                                            {{-- TIME --}}
                                            <th
                                                class="px-5 py-3 text-[10px]
                                                font-semibold text-gray-400
                                                uppercase tracking-wide
                                                whitespace-nowrap">
                                                Time
                                            </th>

                                            {{-- CLIENT --}}
                                            <th
                                                class="px-5 py-3 text-[10px]
                                                font-semibold text-gray-400
                                                uppercase tracking-wide
                                                whitespace-nowrap">
                                                Client
                                            </th>

                                            {{-- SERVICE --}}
                                            <th
                                                class="px-5 py-3 text-[10px]
                                                font-semibold text-gray-400
                                                uppercase tracking-wide
                                                whitespace-nowrap">
                                                Service
                                            </th>

                                            {{-- THERAPIST --}}
                                            <th
                                                class="px-5 py-3 text-[10px]
                                                font-semibold text-gray-400
                                                uppercase tracking-wide
                                                whitespace-nowrap">
                                                Therapist
                                            </th>

                                            {{-- PAYMENT --}}
                                            <th
                                                class="px-5 py-3 text-[10px]
                                                font-semibold text-gray-400
                                                uppercase tracking-wide
                                                whitespace-nowrap">
                                                Payment
                                            </th>

                                            {{-- STATUS --}}
                                            <th
                                                class="px-5 py-3 text-[10px]
                                                font-semibold text-gray-400
                                                uppercase tracking-wide
                                                whitespace-nowrap">
                                                Status
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody class="divide-y divide-gray-100">

                                        @foreach ($appointments as $appointment)
                                            <tr class="hover:bg-gray-50 transition">

                                                {{-- TIME --}}
                                                <td class="px-5 py-4 whitespace-nowrap">

                                                    <p
                                                        class="font-semibold
                                                        text-gray-800 text-xs">
                                                        {{ $appointment->appointment_time
                                                            ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A')
                                                            : 'N/A' }}-
                                                        {{ \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A') }}

                                                    </p>



                                                </td>


                                                {{-- CLIENT --}}
                                                <td class="px-5 py-4">

                                                    <div class="flex items-center gap-2">

                                                        <div
                                                            class="w-8 h-8 rounded-full
                                                            bg-[#849753]
                                                            flex items-center
                                                            justify-center text-white
                                                            font-bold text-xs shrink-0">

                                                            {{ strtoupper(substr($appointment->user->name ?? 'N', 0, 1)) }}

                                                        </div>


                                                        <div class="min-w-0">

                                                            <p
                                                                class="font-semibold
                                                                text-gray-800
                                                                text-xs truncate">
                                                                {{ $appointment->user->name ?? 'N/A' }}
                                                            </p>

                                                            <p
                                                                class="text-[10px]
                                                                text-gray-400
                                                                truncate">
                                                                {{ $appointment->user->email ?? '' }}
                                                            </p>

                                                        </div>

                                                    </div>

                                                </td>


                                                {{-- SERVICE --}}
                                                <td class="px-5 py-4">

                                                    <p
                                                        class="font-semibold
                                                        text-gray-800 text-xs">
                                                        {{ $appointment->service->name ?? 'N/A' }}
                                                    </p>

                                                    @if ($appointment->addOn)
                                                        <p
                                                            class="text-[10px]
                                                            text-gray-500 mt-1">
                                                            Add-on:
                                                            {{ $appointment->addOn->name }}
                                                        </p>
                                                    @endif

                                                    <p
                                                        class="text-[10px]
                                                        text-gray-400 capitalize">
                                                        Level:
                                                        {{ $appointment->level ?? 'N/A' }}
                                                    </p>

                                                </td>


                                                {{-- THERAPIST --}}
                                                <td class="px-5 py-4">

                                                    <p
                                                        class="text-xs font-medium
                                                        text-gray-700">
                                                        {{ $appointment->therapist->name ?? 'N/A' }}
                                                    </p>

                                                </td>


                                                {{-- PAYMENT --}}
                                                <td class="px-5 py-4 whitespace-nowrap">

                                                    <p
                                                        class="text-xs font-semibold
                                                        text-gray-800">
                                                        ₱{{ number_format($appointment->amount_paid ?? 0, 2) }}
                                                    </p>

                                                    @if ($appointment->payment_method === 'branch')
                                                        <span
                                                            class="text-[9px]
                                                            text-gray-500">
                                                            Pay at Counter
                                                        </span>
                                                    @elseif ($appointment->payment_method === 'gcash')
                                                        <span
                                                            class="text-[9px]
                                                            text-blue-600">
                                                            GCash
                                                        </span>
                                                    @else
                                                        <span
                                                            class="text-[9px]
                                                            text-gray-500">
                                                            {{ ucfirst($appointment->payment_method ?? 'N/A') }}
                                                        </span>
                                                    @endif

                                                </td>


                                                {{-- STATUS --}}
                                                <td class="px-5 py-4">

                                                    @if ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::CONFIRMED)
                                                        <span
                                                            class="inline-flex items-center
                                                            gap-1.5 px-2.5 py-1
                                                            bg-green-50
                                                            border border-green-200
                                                            text-green-700
                                                            text-[10px] font-semibold
                                                            rounded-full whitespace-nowrap">

                                                            <span
                                                                class="w-1.5 h-1.5
                                                                rounded-full
                                                                bg-green-500"></span>

                                                            Confirmed

                                                        </span>
                                                    @elseif ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::PENDING)
                                                        <span
                                                            class="inline-flex items-center
                                                            gap-1.5 px-2.5 py-1
                                                            bg-yellow-50
                                                            border border-yellow-200
                                                            text-yellow-700
                                                            text-[10px] font-semibold
                                                            rounded-full whitespace-nowrap">

                                                            <span
                                                                class="w-1.5 h-1.5
                                                                rounded-full
                                                                bg-yellow-400"></span>

                                                            Pending

                                                        </span>
                                                    @elseif ($appointment->status === 'rejected')
                                                        <span
                                                            class="inline-flex items-center
                                                            gap-1.5 px-2.5 py-1
                                                            bg-red-50
                                                            border border-red-200
                                                            text-red-700
                                                            text-[10px] font-semibold
                                                            rounded-full whitespace-nowrap">

                                                            <span
                                                                class="w-1.5 h-1.5
                                                                rounded-full
                                                                bg-red-500"></span>

                                                            Rejected

                                                        </span>
                                                    @elseif ($appointment->status === 'cancelled')
                                                        <span
                                                            class="inline-flex items-center
                                                            px-2.5 py-1
                                                            bg-gray-100
                                                            border border-gray-200
                                                            text-gray-500
                                                            text-[10px] font-semibold
                                                            rounded-full whitespace-nowrap">
                                                            Cancelled
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center
                                                            px-2.5 py-1
                                                            bg-gray-100
                                                            border border-gray-200
                                                            text-gray-500
                                                            text-[10px] font-semibold
                                                            rounded-full whitespace-nowrap">
                                                            {{ ucfirst($appointment->status?->value ?? 'N/A') }} </span>
                                                    @endif

                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        @endif

                    </section>

                </section>


                {{-- =====================================================
                    RIGHT SIDEBAR
                ====================================================== --}}
                <aside class="space-y-4">


                    {{-- =================================================
                        CALENDAR
                    ================================================== --}}
                    <section
                        class="rounded-2xl bg-white border border-gray-200
                        shadow-sm overflow-visible">

                        {{-- Calendar Header --}}
                        <div class="px-4 py-3">

                            <h2 class="text-sm font-semibold text-gray-900">
                                Appointment Calendar
                            </h2>




                            {{-- Month Navigation --}}
                            <div class="flex items-center justify-between mt-3">

                                <button type="button" @click="previousMonth()"
                                    class="w-7 h-7 rounded-md
                                    text-gray-400
                                    hover:text-gray-700
                                    hover:bg-gray-100 transition">
                                    ‹
                                </button>


                                <p class="text-sm font-semibold text-gray-700" x-text="monthName"></p>


                                <button type="button" @click="nextMonth()"
                                    class="w-7 h-7 rounded-md
                                    text-gray-400
                                    hover:text-gray-700
                                    hover:bg-gray-100 transition">
                                    ›
                                </button>

                            </div>

                        </div>


                        {{-- Calendar --}}
                        <div class="px-3 pb-3">


                            {{-- Weekdays --}}
                            <div class="grid grid-cols-7 mb-1">

                                <template x-for="day in weekdays" :key="day">

                                    <div class="text-center text-[9px]
                                        font-semibold text-gray-400
                                        uppercase"
                                        x-text="day.charAt(0)"></div>

                                </template>

                            </div>


                            {{-- Dates --}}
                            <div
                                class="grid grid-cols-7
                                border-t border-l border-gray-100">

                                <template x-for="(day, index) in calendarDays" :key="index">

                                    <div class="calendar-day relative
                                        border-r border-b border-gray-100
                                        p-1 group"
                                        :class="{
                                            'bg-gray-50': !day.currentMonth,
                                            'bg-[#F4EDDB]': day.isToday,
                                            'cursor-pointer': day.events.length > 0
                                        }">


                                        {{-- =================================================
                                            DATE NUMBER
                                        ================================================== --}}
                                        <div
                                            class="flex justify-center
                                            relative z-10">

                                            <span
                                                class="inline-flex items-center
                                                justify-center w-5 h-5
                                                rounded-full text-[9px]"
                                                :class="{
                                                    'text-gray-400':
                                                        !day.currentMonth,
                                                
                                                    'text-gray-700': day.currentMonth &&
                                                        !day.isToday,
                                                
                                                    'bg-[#849753] text-white font-bold': day.isToday
                                                }"
                                                x-text="day.number"></span>

                                        </div>


                                        {{-- =================================================
                                            APPOINTMENT INDICATOR
                                        ================================================== --}}
                                        <div x-show="day.events.length > 0"
                                            class="flex justify-center
                                            mt-1 relative z-10">

                                            <span
                                                class="w-1.5 h-1.5
                                                rounded-full bg-[#849753]"></span>

                                        </div>


                                        {{-- =================================================
                                            HOVER HIGHLIGHT
                                        ================================================== --}}
                                        <div x-show="day.events.length > 0"
                                            class="calendar-highlight
                                            absolute inset-0
                                            rounded-md border-2
                                            border-[#849753]
                                            bg-[#849753]/10">
                                        </div>


                                        {{-- =================================================
                                            APPOINTMENT HOVER POPUP
                                        ================================================== --}}
                                        <div x-show="day.events.length > 0"
                                            class="calendar-popup
                                            absolute z-100
                                            right-full top-0 mr-2
                                            w-64 max-w-62.5
                                        bg-white border border-gray-200 rounded-xl shadow-xl p-3">


                                            {{-- Popup Header --}}
                                            <div
                                                class="flex items-center
                                                justify-between gap-2
                                                mb-2 pb-2
                                                border-b border-gray-100">

                                                <div>

                                                    <p class="text-xs
                                                        font-semibold
                                                        text-gray-900"
                                                        x-text="formatDate(day.date)"></p>

                                                    <p class="text-[9px]
                                                        text-gray-400 mt-0.5"
                                                        x-text="
                                                            day.events.length +
                                                            ' appointment' +
                                                            (day.events.length > 1 ? 's' : '')
                                                        ">
                                                    </p>

                                                </div>


                                                {{-- Appointment count --}}
                                                <span
                                                    class="shrink-0
                                                    inline-flex items-center
                                                    justify-center
                                                    min-w-5 h-5 px-1
                                                    rounded-full
                                                    bg-[#849753]/10
                                                    text-[#526333]
                                                    text-[9px]
                                                    font-semibold"
                                                    x-text="day.events.length"></span>

                                            </div>


                                            {{-- =================================================
                                                ALL APPOINTMENTS
                                            ================================================== --}}
                                            <div
                                                class="space-y-2
                                                max-h-64
                                                overflow-y-auto
                                                scrollbar-thin">

                                                <template x-for="event in day.events" :key="event.id">

                                                    <div
                                                        class="rounded-lg
                                                        bg-gray-50
                                                        border border-gray-100
                                                        px-2.5 py-2">


                                                        {{-- Time + Status --}}
                                                        <div
                                                            class="flex items-center
                                                            justify-between
                                                            gap-2">

                                                            <p class="text-[10px]
                                                                font-semibold
                                                                text-[#849753]"
                                                                x-text="event.time"></p>


                                                            <span
                                                                class="text-[8px]
                                                                px-1.5 py-0.5
                                                                rounded-full
                                                                bg-[#849753]/10
                                                                text-[#526333]"
                                                                x-text="event.status"></span>

                                                        </div>


                                                        {{-- Service --}}
                                                        <p class="text-[10px]
                                                            font-semibold
                                                            text-gray-800
                                                            mt-1 truncate"
                                                            x-text="event.title"></p>


                                                        {{-- Client --}}
                                                        <p class="text-[9px]
                                                            text-gray-500
                                                            truncate"
                                                            x-text="event.user"></p>


                                                        {{-- Therapist --}}

                                                        <p class="text-[9px] text-gray-400 truncate"
                                                            x-text="'Therapist: ' + (event.therapist || 'N/A')"></p>

                                                    </div>

                                                </template>

                                            </div>

                                        </div>

                                    </div>

                                </template>

                            </div>


                            {{-- =================================================
                                CALENDAR FOOTER
                            ================================================== --}}
                            <div class="flex items-center
                                justify-between mt-2">

                                <button type="button" @click="goToToday()"
                                    class="text-[10px]
                                    text-[#849753]
                                    hover:text-[#6F4E37]
                                    transition">
                                    Today
                                </button>


                                <span class="text-[9px] text-gray-400">

                                    <span x-text="events.length"></span>

                                    appointments

                                </span>

                            </div>

                        </div>

                    </section>


                    {{-- =================================================
                        ANNOUNCEMENTS
                    ================================================== --}}
                    <section class="rounded-2xl bg-white border border-gray-200
                        shadow-sm">

                        <div class="px-4 py-4">

                            <h2 class="text-sm font-semibold text-gray-900">
                                Announcements
                            </h2>


                            <div class="mt-4">

                                @forelse($posts as $post)
                                    <article
                                        class="py-2 border-b
                                        border-gray-100 last:border-0">

                                        <h3
                                            class="text-xs font-medium
                                            text-gray-900">
                                            {{ $post->title }}
                                        </h3>


                                        @if ($post->content)
                                            <p
                                                class="text-[11px]
                                                text-gray-600 mt-1
                                                line-clamp-2">
                                                {{ $post->content }}
                                            </p>
                                        @endif


                                        @if ($post->published_at)
                                            <p
                                                class="text-[9px]
                                                text-gray-400 mt-1">
                                                {{ $post->published_at->format('M d, Y') }}
                                            </p>
                                        @endif

                                    </article>

                                @empty

                                    <p class="text-xs text-gray-500">
                                        None
                                    </p>
                                @endforelse

                            </div>

                        </div>

                    </section>


                    {{-- =================================================
                        UPCOMING APPOINTMENTS
                    ================================================== --}}
                    <section class="rounded-2xl bg-white border border-gray-200
                        shadow-sm">

                        <div class="px-4 py-4">


                            <div class="flex items-center
                                justify-between">

                                <div>

                                    <h2
                                        class="text-sm font-semibold
                                        text-gray-900">
                                        Upcoming
                                    </h2>

                                    <p class="text-[10px]
                                        text-gray-500 mt-1">
                                        Next appointments
                                    </p>

                                </div>


                                <span class="text-[9px] text-gray-400">
                                    {{ $upcomingAppointments->count() }}
                                </span>

                            </div>


                            <div class="mt-3 space-y-2">

                                @forelse($upcomingAppointments->take(5)
                                                as $appointment)
                                    <div
                                        class="rounded-lg bg-gray-50
                                        border border-gray-100
                                        px-3 py-2">

                                        <div
                                            class="flex items-start
                                            justify-between gap-2">


                                            <div class="min-w-0">


                                                {{-- Service --}}
                                                <p
                                                    class="text-xs
                                                    font-semibold
                                                    text-gray-900
                                                    truncate">
                                                    {{ $appointment->service->name ?? 'Service' }}
                                                </p>


                                                {{-- Client --}}
                                                <p
                                                    class="text-[10px]
                                                    text-gray-500
                                                    truncate mt-0.5">
                                                    {{ $appointment->user->name ?? 'N/A' }}
                                                </p>


                                                {{-- Therapist --}}
                                                <p
                                                    class="text-[10px]
                                                    text-gray-400
                                                    truncate">
                                                    Therapist:
                                                    {{ $appointment->therapist->name ?? 'N/A' }}
                                                </p>


                                                {{-- Date --}}
                                                <p
                                                    class="text-[10px]
                                                    text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('D, M d, Y') }}
                                                </p>

                                            </div>


                                            {{-- Status --}}
                                            @if ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::CONFIRMED)
                                                <span
                                                    class="shrink-0
                                                    text-[9px]
                                                    text-green-700">
                                                    Confirmed
                                                </span>
                                            @elseif ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::PENDING)
                                                <span
                                                    class="shrink-0
                                                    text-[9px]
                                                    text-yellow-700">
                                                    Pending
                                                </span>
                                            @elseif ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::REJECTED)
                                                <span
                                                    class="shrink-0
                                                    text-[9px]
                                                    text-red-700">
                                                    Rejected
                                                </span>
                                            @elseif ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::CANCELLED)
                                                <span
                                                    class="shrink-0
                                                    text-[9px]
                                                    text-gray-600">
                                                    Cancelled
                                                </span>
                                            @else
                                                <span
                                                    class="shrink-0
                                                    text-[9px]
                                                    text-gray-500">
                                                    {{ ucfirst($appointment->status?->value ?? 'N/A') }} </span>
                                            @endif

                                        </div>


                                        {{-- Time --}}
                                        <p
                                            class="text-[10px]
                                            text-gray-500 mt-2">

                                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}

                                            @if ($appointment->appointment_end_time)
                                                -

                                                {{ \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A') }}
                                            @endif

                                        </p>

                                    </div>

                                @empty

                                    <div
                                        class="rounded-lg bg-gray-50
                                        border border-gray-100
                                        px-3 py-3">

                                        <p class="text-[10px]
                                            text-gray-500">
                                            No upcoming appointments.
                                        </p>

                                    </div>
                                @endforelse

                            </div>

                        </div>

                    </section>

                </aside>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script>
        function dashboardCalendar(events) {

            return {


                events: events || [],

                currentDate: new Date(),

                weekdays: [
                    'Sun',
                    'Mon',
                    'Tue',
                    'Wed',
                    'Thu',
                    'Fri',
                    'Sat'
                ],




                get monthName() {

                    return this.currentDate.toLocaleDateString(
                        'en-US', {
                            month: 'long',
                            year: 'numeric'
                        }
                    );

                },


                get calendarDays() {

                    const year =
                        this.currentDate.getFullYear();

                    const month =
                        this.currentDate.getMonth();


                    const firstDay =
                        new Date(
                            year,
                            month,
                            1
                        );


                    const lastDay =
                        new Date(
                            year,
                            month + 1,
                            0
                        );


                    const previousMonthLastDay =
                        new Date(
                            year,
                            month,
                            0
                        ).getDate();


                    const days = [];

                    for (
                        let i = firstDay.getDay() - 1; i >= 0; i--
                    ) {

                        const date =
                            new Date(
                                year,
                                month - 1,
                                previousMonthLastDay - i
                            );


                        days.push(
                            this.createDay(
                                date,
                                false
                            )
                        );

                    }

                    for (
                        let day = 1; day <= lastDay.getDate(); day++
                    ) {

                        const date =
                            new Date(
                                year,
                                month,
                                day
                            );


                        days.push(
                            this.createDay(
                                date,
                                true
                            )
                        );

                    }


                    let nextDay = 1;


                    while (days.length < 42) {

                        const date =
                            new Date(
                                year,
                                month + 1,
                                nextDay
                            );


                        days.push(
                            this.createDay(
                                date,
                                false
                            )
                        );


                        nextDay++;

                    }


                    return days;

                },

                createDay(date, currentMonth) {

                    const dateString =
                        date.getFullYear() +
                        '-' +
                        String(
                            date.getMonth() + 1
                        ).padStart(2, '0') +
                        '-' +
                        String(
                            date.getDate()
                        ).padStart(2, '0');


                    const today =
                        new Date();


                    const todayString =
                        today.getFullYear() +
                        '-' +
                        String(
                            today.getMonth() + 1
                        ).padStart(2, '0') +
                        '-' +
                        String(
                            today.getDate()
                        ).padStart(2, '0');
                    return {
                        number: date.getDate(),
                        date: dateString,
                        currentMonth: currentMonth,
                        isToday: dateString === todayString,
                        events: this.events.filter(
                            event =>
                            event.date === dateString
                        )
                    };
                },

                formatDate(dateString) {

                    const date =
                        new Date(
                            dateString + 'T00:00:00'
                        );


                    return date.toLocaleDateString(
                        'en-US', {
                            weekday: 'short',
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        }
                    );

                },

                previousMonth() {

                    this.currentDate =
                        new Date(
                            this.currentDate.getFullYear(),
                            this.currentDate.getMonth() - 1,
                            1
                        );
                },

                nextMonth() {

                    this.currentDate =
                        new Date(
                            this.currentDate.getFullYear(),
                            this.currentDate.getMonth() + 1,
                            1
                        );

                },

                goToToday() {

                    this.currentDate =
                        new Date();
                }
            }
        }
    </script>
@endpush
