<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Therapist Dashboard - Padayon Massage Center</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .calendar-day {
            min-height: 25px;
        }

        @media (max-width: 640px) {
            .calendar-day {
                min-height: 38px;
            }
        }
    </style>
</head>

<body class="bg-[#D6BB9E] font-sans overflow-x-hidden">

    {{-- Therapist Sidebar --}}

    <main class="min-h-screen lg:pl-64 pb-20 lg:pb-6">

        <div class="px-4 sm:px-6 lg:px-8 py-5">

            {{-- =========================================================
                HEADER
            ========================================================== --}}
            <div class="flex items-center justify-between mb-5">

                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
                        Welcome back, {{ auth()->guard('therapist')->user()->name }}.
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ auth()->guard('therapist')->user()->specialty }}
                    </p>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-xs font-semibold px-4 py-2 rounded-lg bg-[#849753] text-white hover:bg-[#6F4E37] transition">
                        Logout
                    </button>
                </form>

            </div>

            {{-- =========================================================
                MAIN DASHBOARD
            ========================================================== --}}
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_320px] gap-5">

                {{-- =====================================================
                    LEFT SIDE - APPOINTMENTS
                ====================================================== --}}
                <section class="min-w-0 space-y-5">

                    {{-- =================================================
                        ALL APPOINTMENTS TODAY
                    ================================================== --}}
                    <section class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-4 py-4 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-semibold text-gray-900">
                                    Today's Appointments
                                </h2>
                                <span class="text-xs font-semibold text-[#849753] bg-[#849753]/10 px-2 py-0.5 rounded-full">
                                    {{ $todayAppointments->count() }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4">
                            @forelse($todayAppointments as $appointment)
                                <div class="rounded-lg bg-gray-50 border border-gray-100 px-4 py-3 mb-3 last:mb-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-xs font-bold text-gray-900">
                                                    {{ $appointment->user->name ?? 'Client' }}
                                                </span>
                                                @if ($appointment->status === 'confirm')
                                                    <span class="text-[9px] text-green-700 bg-green-50 px-2 py-0.5 rounded-full font-semibold">
                                                        Confirmed
                                                    </span>
                                                @elseif($appointment->status === 'pending')
                                                    <span class="text-[9px] text-yellow-700 bg-yellow-50 px-2 py-0.5 rounded-full font-semibold">
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-xs font-semibold text-gray-800">
                                                {{ $appointment->service->name ?? 'Service' }}
                                            </p>

                                            @if ($appointment->service->description)
                                                <p class="text-[10px] text-gray-500 mt-0.5">
                                                    {{ $appointment->service->description }}
                                                </p>
                                            @endif

                                            @if ($appointment->add_on)
                                                <p class="text-[10px] text-[#849753] mt-1">
                                                    + {{ $appointment->add_on->name }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="text-right shrink-0">
                                            <p class="text-xs font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                            </p>
                                            <p class="text-[10px] text-gray-500">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-500">
                                        No appointments scheduled for today.
                                    </p>
                                </div>
                            @endforelse
                        </div>

                    </section>

                    {{-- =================================================
                        UPCOMING APPOINTMENTS
                    ================================================== --}}
                    <section class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-4 py-4 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-semibold text-gray-900">
                                    Upcoming Appointments
                                </h2>
                                <span class="text-xs font-semibold text-[#849753] bg-[#849753]/10 px-2 py-0.5 rounded-full">
                                    {{ $upcomingAppointments->count() }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4">
                            @forelse($upcomingAppointments as $appointment)
                                <div class="rounded-lg bg-gray-50 border border-gray-100 px-4 py-3 mb-3 last:mb-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-xs font-bold text-gray-900">
                                                    {{ $appointment->user->name ?? 'Client' }}
                                                </span>
                                                @if ($appointment->status === 'confirm')
                                                    <span class="text-[9px] text-green-700 bg-green-50 px-2 py-0.5 rounded-full font-semibold">
                                                        Confirmed
                                                    </span>
                                                @elseif($appointment->status === 'pending')
                                                    <span class="text-[9px] text-yellow-700 bg-yellow-50 px-2 py-0.5 rounded-full font-semibold">
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-xs font-semibold text-gray-800">
                                                {{ $appointment->service->name ?? 'Service' }}
                                            </p>

                                            @if ($appointment->service->description)
                                                <p class="text-[10px] text-gray-500 mt-0.5">
                                                    {{ $appointment->service->description }}
                                                </p>
                                            @endif

                                            @if ($appointment->add_on)
                                                <p class="text-[10px] text-[#849753] mt-1">
                                                    + {{ $appointment->add_on->name }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="text-right shrink-0">
                                            <p class="text-xs font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d') }}
                                            </p>
                                            <p class="text-[10px] text-gray-500">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-500">
                                        No upcoming appointments.
                                    </p>
                                </div>
                            @endforelse
                        </div>

                    </section>

                </section>

                {{-- =====================================================
                    RIGHT SIDEBAR - CALENDAR & INFO
                ====================================================== --}}
                <aside class="space-y-4">

                    {{-- =================================================
                        CALENDAR
                    ================================================== --}}
                    <section class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden"
                        x-data="dashboardCalendar(@js($calendarEvents))">

                        {{-- Calendar Header --}}
                        <div class="px-4 py-3">

                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-semibold text-gray-900">
                                    Calendar
                                </h2>
                            </div>

                            {{-- Month navigation --}}
                            <div class="flex items-center justify-between mt-2">

                                <button type="button" @click="previousMonth()"
                                    class="w-7 h-7 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                                    aria-label="Previous month">
                                    ‹
                                </button>

                                <p class="text-sm font-semibold text-gray-700" x-text="monthName">
                                </p>

                                <button type="button" @click="nextMonth()"
                                    class="w-7 h-7 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                                    aria-label="Next month">
                                    ›
                                </button>

                            </div>

                        </div>

                        {{-- Calendar --}}
                        <div class="px-3 pb-2">

                            {{-- Weekdays --}}
                            <div class="grid grid-cols-7 mb-1">

                                <template x-for="day in weekdays" :key="day">

                                    <div class="text-center text-[10px] font-semibold text-gray-400 uppercase"
                                        x-text="day.charAt(0)">
                                    </div>

                                </template>

                            </div>

                            {{-- Dates --}}
                            <div class="grid grid-cols-7 border-t border-l border-gray-100">

                                <template x-for="(day, index) in calendarDays" :key="index">

                                    <div class="calendar-day relative border-r border-b border-gray-100 p-1 overflow-hidden"
                                        :class="{
                                            'bg-gray-50': !day.currentMonth,
                                            'bg-[#F4EDDB]': day.isToday
                                        }">

                                        {{-- Date --}}
                                        <div class="flex justify-center">

                                            <span
                                                class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[9px]"
                                                :class="{
                                                    'text-gray-400': !day.currentMonth,
                                                    'text-gray-700': day.currentMonth && !day.isToday,
                                                    'bg-[#849753] text-white font-bold': day.isToday
                                                }"
                                                x-text="day.number">
                                            </span>

                                        </div>

                                        {{-- Events --}}
                                        <div class="mt-0.5 space-y-0.5">

                                            <template x-for="event in day.events"
                                                :key="event.date + event.time + event.title">

                                                <div class="rounded-md bg-[#849753]/15 px-1 py-0.5 text-[7px] text-[#526333] leading-tight"
                                                    :title="event.title + ' at ' + event.time">

                                                    <p class="font-semibold truncate" x-text="event.title">
                                                    </p>

                                                    <p class="truncate" x-text="event.time">
                                                    </p>

                                                </div>

                                            </template>

                                        </div>

                                    </div>

                                </template>

                            </div>

                            {{-- Calendar Footer --}}
                            <div class="flex items-center justify-between mt-1">

                                <button type="button" @click="goToToday()"
                                    class="text-[10px] text-[#849753] hover:text-[#6F4E37] transition">
                                    Today
                                </button>

                            </div>

                        </div>

                    </section>

                    {{-- =================================================
                        QUICK STATS
                    ================================================== --}}
                    <section class="rounded-2xl bg-white border border-gray-200 shadow-sm">

                        <div class="px-4 py-4">

                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-semibold text-gray-900">
                                    Quick Stats
                                </h2>
                            </div>

                            <div class="mt-4 space-y-3">

                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600">Today's Appointments</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $todayAppointments->count() }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600">Upcoming</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $upcomingAppointments->count() }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600">Total Clients</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $totalClients }}</span>
                                </div>

                            </div>

                        </div>

                    </section>

                </aside>

            </div>

        </div>

    </main>

    {{-- ================================================================
        CALENDAR JAVASCRIPT
    ================================================================= --}}
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

                    return this.currentDate.toLocaleDateString('en-US', {
                        month: 'short',
                        year: 'numeric'
                    });

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

                    // Previous month's days
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

                    // Current month's days
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

                    // Next month's days
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

            };

        }
    </script>

</body>

</html>