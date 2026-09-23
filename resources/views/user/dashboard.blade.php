@extends('layouts.user')

@section('title', 'Padayon Massage Center - Dashboard')

@section('content')

    <div class="px-4 sm:px-6 lg:px-8 py-5" x-data="dashboardCalendar(@js($calendarEvents))">

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
                    class="relative w-full overflow-hidden rounded-2xl bg-white border border-gray-200 shadow-sm
                    h-55 sm:h-75 md:h-100 lg:min-h-125
                    xl:min-h-[calc(100vh-150px)]">

                    <img src="{{ asset('build/assets/images/Welcome.webp') }}" alt="Welcome" loading="eager"
                        class="absolute inset-0 w-full h-full object-cover object-center">

                </div>


                {{-- =================================================
                THERAPISTS SECTION
            ================================================== --}}

                <section class="mt-5 rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">

                    {{-- Section Header --}}

                    <div class="px-4 py-4">

                        <div class="flex items-center gap-2">

                            <h2 class="text-sm font-semibold text-gray-900">
                                Duty for today
                            </h2>

                        </div>

                        <p class="text-xs text-gray-500 mt-1">
                            We are here to serve you
                        </p>

                    </div>


                    {{-- Therapist Cards --}}

                    <div class="px-4 pb-4">

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">

                            @forelse($therapists as $therapist)
                                <div
                                    class="rounded-xl border border-gray-200 bg-white shadow-sm
                                    overflow-hidden hover:shadow-md transition">

                                    {{-- Therapist Image --}}

                                    <div class="relative h-60 bg-gray-100">

                                        @if ($therapist->image)
                                            <img src="{{ asset('storage/' . $therapist->image) }}"
                                                alt="{{ $therapist->name }}" loading="lazy" decoding="async"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">

                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24">

                                                    <path
                                                        d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />

                                                </svg>

                                            </div>
                                        @endif

                                    </div>


                                    {{-- Therapist Information --}}

                                    <div class="p-2">

                                        <h3 class="text-xs font-semibold text-gray-800 truncate">
                                            {{ $therapist->name }}
                                        </h3>

                                        <p class="text-[10px] text-[#849753] font-medium truncate">
                                            {{ $therapist->specialty }}
                                        </p>

                                    </div>

                                </div>

                            @empty

                                <div class="col-span-full text-center py-6">

                                    <p class="text-xs text-gray-500">
                                        No therapists available today.
                                    </p>

                                </div>
                            @endforelse

                        </div>

                    </div>

                </section>

            </section>



            {{-- =====================================================
            RIGHT SIDEBAR
        ====================================================== --}}

            <aside class="space-y-4">

                {{-- ========================================================= --}}
                {{-- CALENDAR --}}
                {{-- ========================================================= --}}
                <section x-data="dashboardCalendar(@js($calendarEvents))"
                    class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-visible">
                    {{-- Calendar Header --}}
                    <div class="px-4 py-3">

                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-semibold text-gray-900">
                                Calendar
                            </h2>
                        </div>

                        {{-- Month Navigation --}}
                        <div class="flex items-center justify-between mt-2">

                            {{-- Previous Month --}}
                            <button type="button" @click="previousMonth()"
                                class="w-7 h-7 rounded-md text-gray-400
                   hover:text-gray-700 hover:bg-gray-100
                   transition"
                                aria-label="Previous month">
                                ‹
                            </button>

                            {{-- Current Month --}}
                            <p class="text-sm font-semibold text-gray-700" x-text="monthName"></p>

                            {{-- Next Month --}}
                            <button type="button" @click="nextMonth()"
                                class="w-7 h-7 rounded-md text-gray-400
                   hover:text-gray-700 hover:bg-gray-100
                   transition"
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
                                <div class="text-center text-[10px] font-semibold
                       text-gray-400 uppercase"
                                    x-text="day.charAt(0)"></div>
                            </template>

                        </div>


                        {{-- Calendar Dates --}}
                        <div class="grid grid-cols-7 border-t border-l border-gray-100">

                            <template x-for="(day, index) in calendarDays" :key="index">

                                <div class="calendar-day relative border-r border-b
                       border-gray-100 p-1"
                                    :class="{
                                        'bg-gray-50': !day.currentMonth,
                                        'bg-[#F4EDDB]': day.isToday,
                                        'cursor-pointer': day.events.length > 0
                                    }">

                                    {{-- Date Number --}}
                                    <div class="flex justify-center relative z-10">

                                        <span
                                            class="inline-flex items-center justify-center
                               w-5 h-5 rounded-full text-[9px]"
                                            :class="{
                                                'text-gray-400': !day.currentMonth,
                                                'text-gray-700': day.currentMonth && !day.isToday,
                                                'bg-[#849753] text-white font-bold': day.isToday
                                            }"
                                            x-text="day.number"></span>

                                    </div>


                                    {{-- Appointment Indicator --}}
                                    <div x-show="day.events.length > 0" class="flex justify-center mt-1 relative z-10">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#849753]"></span>
                                    </div>


                                    {{-- Hover Highlight --}}
                                    <div x-show="day.events.length > 0"
                                        class="calendar-highlight absolute inset-0
                           rounded-md border-2 border-[#849753]
                           bg-[#849753]/10">
                                    </div>


                                    {{-- Appointment Hover Popup --}}
                                    <div x-show="day.events.length > 0"
                                        class="calendar-popup absolute z-100
                           right-full top-0 mr-2
                           w-64 max-w-62.5
                           bg-white border border-gray-200
                           rounded-xl shadow-xl p-3">

                                        {{-- Popup Header --}}
                                        <div
                                            class="flex items-center justify-between
                               gap-2 mb-2 pb-2
                               border-b border-gray-100">

                                            <div>

                                                <p class="text-xs font-semibold text-gray-900"
                                                    x-text="formatDate(day.date)"></p>

                                                <p class="text-[9px] text-gray-400 mt-0.5"
                                                    x-text="
                                    day.events.length +
                                    ' appointment' +
                                    (day.events.length > 1 ? 's' : '')
                                ">
                                                </p>

                                            </div>

                                            <span
                                                class="shrink-0 inline-flex items-center
                                   justify-center min-w-5 h-5 px-1
                                   rounded-full
                                   bg-[#849753]/10 text-[#526333]
                                   text-[9px] font-semibold"
                                                x-text="day.events.length"></span>

                                        </div>


                                        {{-- Appointments --}}
                                        <div
                                            class="space-y-2 max-h-64
                               overflow-y-auto scrollbar-thin">

                                            <template x-for="event in day.events"
                                                :key="event.id ??
                                                    event.date + event.time + event.title">

                                                <div
                                                    class="rounded-lg bg-gray-50
                                       border border-gray-100
                                       px-2.5 py-2">

                                                    {{-- Time + Status --}}
                                                    <div
                                                        class="flex items-center
                                           justify-between gap-2">

                                                        <p class="text-[10px] font-semibold
                                               text-[#849753]"
                                                            x-text="event.time"></p>

                                                        <span
                                                            class="text-[8px] px-1.5 py-0.5
                                               rounded-full
                                               bg-[#849753]/10
                                               text-[#526333]"
                                                            x-text="event.status"></span>

                                                    </div>


                                                    {{-- Service --}}
                                                    <p class="text-[10px] font-semibold
                                           text-gray-800 mt-1 truncate"
                                                        x-text="event.title"></p>


                                                    {{-- Client --}}
                                                    <p class="text-[9px] text-gray-500
                                           truncate"
                                                        x-text="event.user"></p>


                                                    {{-- Therapist --}}
                                                    <p class="text-[9px] text-gray-400
                                           truncate"
                                                        x-text="
                                        'Therapist: ' +
                                        event.therapist
                                    ">
                                                    </p>

                                                </div>

                                            </template>

                                        </div>

                                    </div>

                                </div>

                            </template>

                        </div>


                        {{-- Calendar Footer --}}
                        <div class="flex items-center justify-between mt-1">

                            <button type="button" @click="goToToday()"
                                class="text-[10px] text-[#849753]
                   hover:text-[#6F4E37] transition">
                                Today
                            </button>

                        </div>

                    </div>

                </section>



                {{-- =================================================
                ANNOUNCEMENTS
            ================================================== --}}

                <section class="rounded-2xl bg-white border border-gray-200 shadow-sm">

                    <div class="px-4 py-4">

                        <div class="flex items-center gap-2">

                            <h2 class="text-sm font-semibold text-gray-900">
                                Announcements
                            </h2>

                        </div>


                        <div class="mt-4">

                            @forelse($posts as $post)
                                <article class="py-2">

                                    <h3 class="text-xs font-medium text-gray-900">
                                        {{ $post->title }}
                                    </h3>

                                    @if ($post->content)
                                        <p class="text-[11px] text-gray-600 mt-1 line-clamp-2">
                                            {{ $post->content }}
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
                TODAY'S APPOINTMENTS
            ================================================== --}}

                <section class="rounded-2xl bg-white border border-gray-200 shadow-sm">

                    <div class="px-4 py-4">

                        <div class="flex items-center justify-between">

                            <div>

                                <h2 class="text-sm font-semibold text-gray-900">
                                    Today's Appointment
                                </h2>

                                <p class="text-[10px] text-gray-500 mt-1">
                                    Your appointments for today
                                </p>

                            </div>

                            <span
                                class="inline-flex items-center justify-center
                                   min-w-6 h-6 px-2 rounded-full
                                   bg-[#F4EDDB] text-[#526333]
                                   text-[10px] font-bold">
                                {{ $todayAppointments->count() }}
                            </span>

                        </div>


                        <div class="mt-3 space-y-2">

                            @forelse($todayAppointments as $appointment)
                                <div
                                    class="rounded-lg bg-gray-50 border border-gray-100
                                       px-3 py-3">

                                    <div class="flex items-start justify-between gap-2">

                                        {{-- Appointment Information --}}

                                        <div class="min-w-0">

                                            {{-- Service --}}

                                            <p
                                                class="text-xs font-semibold
                                                   text-gray-900 truncate">
                                                {{ $appointment->service->name ?? 'Service' }}
                                            </p>


                                            {{-- Therapist --}}

                                            @if ($appointment->therapist)
                                                <p class="text-[10px] text-gray-500 mt-1">
                                                    Therapist:
                                                    {{ $appointment->therapist->name }}
                                                </p>
                                            @endif


                                            {{-- Time --}}

                                            <p class="text-[10px] text-gray-500 mt-1">

                                                {{ $appointment->start_datetime->format('h:i A') }}

                                                @if ($appointment->end_datetime)
                                                    -
                                                    {{ $appointment->end_datetime->format('h:i A') }}
                                                @endif

                                            </p>

                                        </div>


                                        {{-- Status --}}

                                        @if ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::CONFIRMED)
                                            <span
                                                class="shrink-0 rounded-full
                                                   bg-green-100 px-2 py-1
                                                   text-[9px] font-medium
                                                   text-green-700">
                                                Confirmed
                                            </span>
                                        @elseif ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::PENDING)
                                            <span
                                                class="shrink-0 rounded-full
                                                   bg-yellow-100 px-2 py-1
                                                   text-[9px] font-medium
                                                   text-yellow-700">
                                                Pending
                                            </span>
                                        @endif

                                    </div>


                                    {{-- Today Label --}}

                                    <div class="mt-2">

                                        <span class="text-[9px] text-[#849753] font-medium">
                                            Today
                                        </span>

                                    </div>

                                </div>

                            @empty

                                <div
                                    class="rounded-lg bg-gray-50
                                       border border-gray-100 px-3 py-3">

                                    <p class="text-[10px] text-gray-500">
                                        No appointments scheduled for today.
                                    </p>

                                </div>
                            @endforelse

                        </div>

                    </div>

                </section>



                {{-- =================================================
                UPCOMING APPOINTMENTS
            ================================================== --}}

                <section class="rounded-2xl bg-white border border-gray-200 shadow-sm">

                    <div class="px-4 py-4">

                        <div class="flex items-center justify-between">

                            <div>

                                <h2 class="text-sm font-semibold text-gray-900">
                                    Upcoming
                                </h2>

                                <p class="text-[10px] text-gray-500 mt-1">
                                    Your future appointments
                                </p>

                            </div>

                        </div>


                        <div class="mt-3 space-y-2">

                            @forelse($upcomingAppointments as $appointment)
                                <div
                                    class="rounded-lg bg-gray-50 border border-gray-100
                                       px-3 py-3">

                                    <div class="flex items-start justify-between gap-2">

                                        {{-- Appointment Information --}}

                                        <div class="min-w-0">

                                            {{-- Service Name --}}

                                            <p
                                                class="text-xs font-semibold
                                                   text-gray-900 truncate">
                                                {{ $appointment->service->name ?? 'Service' }}
                                            </p>


                                            {{-- Therapist --}}

                                            @if ($appointment->therapist)
                                                <p class="text-[10px] text-gray-500 mt-1">
                                                    Therapist:
                                                    {{ $appointment->therapist->name }}
                                                </p>
                                            @endif


                                            {{-- Date --}}

                                            <p class="text-[10px] text-gray-500 mt-1">
                                                {{ $appointment->start_datetime->format('D, M d, Y') }}
                                            </p>


                                            {{-- Time --}}

                                            <p class="text-[10px] text-gray-500 mt-1">

                                                {{ $appointment->start_datetime->format('h:i A') }}

                                                @if ($appointment->end_datetime)
                                                    -
                                                    {{ $appointment->end_datetime->format('h:i A') }}
                                                @endif

                                            </p>

                                        </div>


                                        {{-- Status --}}

                                        @if ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::CONFIRMED)
                                            <span
                                                class="shrink-0 rounded-full
                                                   bg-green-100 px-2 py-1
                                                   text-[9px] font-medium
                                                   text-green-700">
                                                Confirmed
                                            </span>
                                        @elseif ($appointment->status === \App\Enums\Admin\Appointment\AppointmentStatus::PENDING)
                                            <span
                                                class="shrink-0 rounded-full
                                                   bg-yellow-100 px-2 py-1
                                                   text-[9px] font-medium
                                                   text-yellow-700">
                                                Pending
                                            </span>
                                        @endif

                                    </div>

                                </div>

                            @empty

                                <div
                                    class="rounded-lg bg-gray-50
                                       border border-gray-100 px-3 py-3">

                                    <p class="text-[10px] text-gray-500">
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

@endsection

{{-- PAGE-SPECIFIC CSS --}}

@push('styles')
    <style>
        .calendar-day {
            min-height: 25px;
        }

        @media (max-width: 640px) {
            .calendar-day {
                min-height: 38px;
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
                        | Appointment Hover Popup
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
                        | Hover Highlight
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


{{-- ================================================================
    PAGE-SPECIFIC JAVASCRIPT
================================================================ --}}

@push('scripts')
    <script>
        function dashboardCalendar(events) {

            return {

                /*
                |--------------------------------------------------------------------------
                | Appointment Events
                |--------------------------------------------------------------------------
                */

                events: events || [],


                /*
                |--------------------------------------------------------------------------
                | Current Calendar Date
                |--------------------------------------------------------------------------
                */

                currentDate: new Date(),


                /*
                |--------------------------------------------------------------------------
                | Weekday Names
                |--------------------------------------------------------------------------
                */

                weekdays: [
                    'Sun',
                    'Mon',
                    'Tue',
                    'Wed',
                    'Thu',
                    'Fri',
                    'Sat'
                ],


                /*
                |--------------------------------------------------------------------------
                | Current Month Name
                |--------------------------------------------------------------------------
                */

                get monthName() {

                    return this.currentDate.toLocaleDateString(
                        'en-US', {
                            month: 'short',
                            year: 'numeric'
                        }
                    );

                },


                /*
                |--------------------------------------------------------------------------
                | Generate Calendar Days
                |--------------------------------------------------------------------------
                */

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


                    /*
                    |--------------------------------------------------------------------------
                    | Previous Month Days
                    |--------------------------------------------------------------------------
                    */

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


                    /*
                    |--------------------------------------------------------------------------
                    | Current Month Days
                    |--------------------------------------------------------------------------
                    */

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


                    /*
                    |--------------------------------------------------------------------------
                    | Next Month Days
                    |--------------------------------------------------------------------------
                    |
                    | 42 cells = 6 weeks × 7 days
                    |
                    */

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


                /*
                |--------------------------------------------------------------------------
                | Create Calendar Day
                |--------------------------------------------------------------------------
                */

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


                    const today = new Date();


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


                /*
                |--------------------------------------------------------------------------
                | Format Date
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | Previous Month
                |--------------------------------------------------------------------------
                */

                previousMonth() {

                    this.currentDate =
                        new Date(
                            this.currentDate.getFullYear(),
                            this.currentDate.getMonth() - 1,
                            1
                        );

                },


                /*
                |--------------------------------------------------------------------------
                | Next Month
                |--------------------------------------------------------------------------
                */

                nextMonth() {

                    this.currentDate =
                        new Date(
                            this.currentDate.getFullYear(),
                            this.currentDate.getMonth() + 1,
                            1
                        );

                },


                /*
                |--------------------------------------------------------------------------
                | Go To Today
                |--------------------------------------------------------------------------
                */

                goToToday() {

                    this.currentDate =
                        new Date();

                }

            };

        }
    </script>
@endpush
