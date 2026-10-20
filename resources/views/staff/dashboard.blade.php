@extends('layouts.staff')

@section('title', 'Padayon Massage Center - Dashboard')

@section('content')
    @php
        $money = fn($amount) => '₱' . number_format((float) $amount, 2);

        $customerName = function ($appointment) {
            return $appointment->user?->name ?? ($appointment->walkInCustomer?->name ?? 'Walk-in Customer');
        };

        $statusValue = function ($status) {
            return $status instanceof \App\Enums\Admin\Appointment\AppointmentStatus
                ? $status->value
                : (string) $status;
        };

        $statusLabel = function ($appointment) {
            return $appointment->status instanceof \App\Enums\Admin\Appointment\AppointmentStatus
                ? $appointment->status->label()
                : ucfirst((string) $appointment->status);
        };

        $statusClasses = function ($status) use ($statusValue) {
            return match ($statusValue($status)) {
                'confirm' => 'bg-green-100 text-green-800',
                'pending' => 'bg-amber-100 text-amber-800',
                'rejected', 'failed' => 'bg-red-100 text-red-800',
                'cancelled', 'no show' => 'bg-gray-100 text-gray-700',
                default => 'bg-gray-100 text-gray-700',
            };
        };

        $maxAppointmentCount = max(1, (int) $appointmentStatistics->max('count'));

        $paymentStatusLabel = function ($status) {
            $status = strtolower((string) ($status ?? 'unpaid'));

            return match ($status) {
                'paid' => 'Paid',
                'unpaid' => 'Unpaid',
                'pending' => 'Pending',
                'failed' => 'Failed',
                default => ucfirst($status),
            };
        };

        $paymentStatusClasses = function ($status) {
            return match (strtolower((string) ($status ?? 'unpaid'))) {
                'paid' => 'bg-green-100 text-green-800',
                'unpaid' => 'bg-orange-100 text-orange-800',
                'pending' => 'bg-amber-100 text-amber-800',
                'failed' => 'bg-red-100 text-red-800',
                default => 'bg-gray-100 text-gray-700',
            };
        };

        $paymentMethodLabel = function ($method) {
            return match (strtolower((string) ($method ?? ''))) {
                'gcash' => 'GCash',
                'branch' => 'Pay at Counter',
                default => ucfirst((string) ($method ?? 'Not specified')),
            };
        };

        $paymentTypeLabel = function ($type) {
            return match (strtolower((string) ($type ?? ''))) {
                'full' => 'Full Payment',
                'downpayment' => 'Downpayment',
                default => ucfirst((string) ($type ?? 'Not specified')),
            };
        };

    @endphp

    <div class="min-h-screen bg-[#D6BB9E] px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">

            {{-- HEADER --}}
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="text-sm font-medium text-[#6F4E37]">
                        {{ $now->format('l, F j, Y') }}
                    </p>

                    <h1 class="mt-1 text-2xl font-bold text-gray-900 sm:text-3xl">
                        Good {{ $now->hour < 12 ? 'morning' : ($now->hour < 18 ? 'afternoon' : 'evening') }},
                        Staff
                    </h1>

                    <p class="mt-1 text-sm text-gray-600">
                        Here's what's happening today at {{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}.
                    </p>
                </div>

                <a href="{{ route('staff.quick-book') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#849753] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#718343]">
                    <span class="text-lg leading-none">+</span>
                    Quick Book
                </a>
            </div>

            {{-- SUMMARY CARDS --}}

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">


                {{-- Today's Appointments --}}
                <div
                    class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-5 text-center shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Today's Appointments
                    </p>

                    <p class="mt-3 text-3xl font-bold text-gray-900">
                        {{ $todayAppointmentsCount }}
                    </p>
                </div>

                {{-- Pending Appointments --}}
                <div
                    class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-5 text-center shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Pending Appointments
                    </p>

                    <p class="mt-3 text-3xl font-bold text-gray-900">
                        {{ $pendingAppointmentsCount }}
                    </p>
                </div>


                {{-- Therapists Available --}}
                <div
                    class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-5 text-center shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Therapists Available
                    </p>

                    <p class="mt-3 text-3xl font-bold text-gray-900">
                        {{ $availableTherapistsCount }}
                    </p>
                </div>

                {{-- Current Time --}}
                <div x-data="{
                    now: new Date(),
                    init() {
                        this.updateTime();
                        setInterval(() => this.updateTime(), 1000);
                    },
                    updateTime() {
                        this.now = new Date(
                            new Date().toLocaleString('en-US', {
                                timeZone: 'Asia/Manila'
                            })
                        );
                    },
                    get time() {
                        return this.now.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        });
                    }
                }"
                    class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-5 text-center shadow-sm">

                    <p class="text-sm font-medium text-gray-500">
                        Current Time
                    </p>

                    <p x-text="time" class="mt-3 text-2xl font-bold tabular-nums text-black">
                    </p>
                </div>


            </div>


            {{-- SCHEDULE --}}
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

                {{-- TODAY'S SCHEDULE --}}
                <section x-data="{ expanded: false }"
                    class="min-w-0 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                        <div>
                            <h2 class="font-bold text-gray-900">Today's Schedule</h2>
                        </div>

                        <a href="{{ route('staff.appointments') }}"
                            class="text-sm font-semibold text-[#718343] hover:underline">
                            View all
                        </a>
                    </div>

                    @if ($todayAppointments->isEmpty())
                        <div class="px-5 py-12 text-center">
                            <p class="font-semibold text-gray-800">
                                No appointments scheduled today
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                New bookings will appear here.
                            </p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($todayAppointments as $appointment)
                                <div x-show="expanded || {{ $loop->index }} < 5"
                                    class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-start gap-3">
                                        <div class="min-w-19 rounded-xl bg-[#F4EDDB] px-3 py-2 text-center">
                                            <p class="text-sm font-bold text-[#6F4E37]">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                                -
                                                {{ \Carbon\Carbon::parse($appointment->appointment_end_time)->format('g:i A') }}
                                            </p>
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900">
                                                {{ $customerName($appointment) }}
                                            </p>

                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ $appointment->service?->name ?? 'Service not specified' }} -
                                                {{ $appointment->service?->description ?? 'Service not specified' }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                Therapist:
                                                {{ $appointment->therapist?->name ?? 'Not assigned' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="sm:text-right">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($appointment->status) }}">
                                            {{ $statusLabel($appointment) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($todayAppointments->count() > 5)
                            <div class="border-t border-gray-100 px-5 py-3">
                                <button type="button" @click="expanded = !expanded"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg py-2 text-sm font-semibold text-[#718343] transition hover:bg-[#849753]/10">
                                    <span
                                        x-text="expanded ? 'Show less' : 'See all {{ $todayAppointments->count() }} appointments'"></span>

                                    <svg class="h-4 w-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="m6 9 6 6 6-6" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    @endif
                </section>

                {{-- TODAY'S TRANSACTIONS --}}
                <section x-data="{ expanded: false }"
                    class="min-w-0 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                        <div>
                            <h2 class="font-bold text-gray-900">Today's Transaction</h2>
                        </div>

                        <a href="{{ route('staff.transactions') }}"
                            class="text-sm font-semibold text-[#718343] hover:underline">
                            View all
                        </a>
                    </div>

                    @if ($todayAppointments->isEmpty())
                        <div class="px-5 py-12 text-center">
                            <p class="font-semibold text-gray-800">
                                No appointments Transactions today
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                New Transactions will appear here.
                            </p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($todayAppointments as $appointment)
                                @php
                                    $totalAmount =
                                        (float) $appointment->service_price + (float) $appointment->addons_price;

                                    $amountPaid = (float) ($appointment->amount_paid ?? 0);

                                    $remainingBalance = max(0, $totalAmount - $amountPaid);

                                    $paymentStatus = $paymentStatusLabel($appointment->payment_status);
                                @endphp

                                <div x-show="expanded || {{ $loop->index }} < 5"
                                    class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-start gap-3">
                                        <div class="min-w-19 rounded-xl bg-[#F4EDDB] px-3 py-2 text-center">
                                            <p class="text-sm font-bold text-[#6F4E37]">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                                -
                                                {{ \Carbon\Carbon::parse($appointment->appointment_end_time)->format('g:i A') }}
                                            </p>
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900">
                                                {{ $customerName($appointment) }}
                                            </p>

                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ $appointment->service?->name ?? 'Service not specified' }} -
                                                {{ $appointment->service?->description ?? 'Service not specified' }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                Therapist:
                                                {{ $appointment->therapist?->name ?? 'Not assigned' }}
                                            </p>
                                        </div>
                                    </div>


                                    <div class="mt-2 space-y-2 text-sm sm:mt-0 sm:text-right">

                                        {{-- Payment Status --}}
                                        <div>
                                            <div>
                                                <span
                                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $paymentStatusClasses($appointment->payment_status) }}">
                                                    {{ $paymentStatus }} -
                                                    {{ $paymentMethodLabel($appointment->payment_method) }}-{{ $paymentTypeLabel($appointment->payment_type) }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Remaining Balance --}}
                                        <p class="text-gray-600">
                                            <span class="font-medium">Remaining Balance:</span>
                                            <span
                                                class="font-semibold
            {{ $remainingBalance > 0 ? 'text-red-600' : 'text-green-700' }}">
                                                {{ $money($remainingBalance) }}
                                            </span>
                                        </p>

                                        {{-- Total Appointment Cost --}}
                                        <p class="border-t border-gray-100 pt-2 font-bold text-gray-900">
                                            Total: {{ $money($totalAmount) }}
                                        </p>

                                    </div>

                                </div>
                            @endforeach
                        </div>

                        @if ($todayAppointments->count() > 5)
                            <div class="border-t border-gray-100 px-5 py-3">
                                <button type="button" @click="expanded = !expanded"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg py-2 text-sm font-semibold text-[#718343] transition hover:bg-[#849753]/10">
                                    <span
                                        x-text="expanded ? 'Show less' : 'See all {{ $todayAppointments->count() }} appointments'"></span>

                                    <svg class="h-4 w-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="m6 9 6 6 6-6" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    @endif
                </section>
            </div>

            {{-- + APPOINTMENT STATISTICS --}}
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

                {{-- NEEDS ATTENTION --}}
                <section x-data="{ expanded: false }"
                    class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                        <div>
                            <h2 class="font-bold text-gray-900">
                                Needs Attention
                            </h2>
                        </div>

                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">
                            {{ $needsAttention->count() }}
                        </span>
                    </div>

                    @if ($needsAttention->isEmpty())
                        <div class="px-5 py-10 text-center">
                            <p class="mt-1 text-sm text-gray-500">
                                No pending bookings or payment issues found.
                            </p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($needsAttention as $item)
                                @php
                                    $paymentStatus = strtolower((string) $item->payment_status);

                                    $isFailedPayment = $paymentStatus === 'failed';
                                    $isPendingPayment = $paymentStatus === 'pending';
                                    $isPendingBooking =
                                        $item->status === \App\Enums\Admin\Appointment\AppointmentStatus::PENDING;
                                @endphp

                                <div x-show="expanded || {{ $loop->index }} < 5" class="px-5 py-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="mt-0.5 rounded-lg p-2
                                        {{ $isFailedPayment
                                            ? 'bg-red-50 text-red-700'
                                            : ($isPendingPayment
                                                ? 'bg-amber-50 text-amber-700'
                                                : 'bg-[#849753]/10 text-[#718343]') }}">
                                            @if ($isFailedPayment)
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 9v4m0 4h.01M10.3 3.9L1.9 19a2 2 0 001.7 3h16.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"
                                                        stroke-width="1.6" stroke-linecap="round" />
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke-width="1.6" />
                                                    <path d="M12 7v5l3 2" stroke-width="1.6" stroke-linecap="round" />
                                                </svg>
                                            @endif
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $customerName($item) }}
                                            </p>

                                            <p class="mt-1 text-xs leading-5 text-gray-500">
                                                @if ($isFailedPayment)
                                                    Payment failed — review payment status.
                                                @elseif ($isPendingPayment)
                                                    Payment pending — check payment progress.
                                                @elseif ($isPendingBooking)
                                                    New appointment awaiting confirmation.
                                                @else
                                                    Appointment requires review.
                                                @endif
                                            </p>

                                            <p class="mt-2 text-xs font-medium text-gray-600">
                                                {{ $item->service?->name ?? 'Appointment' }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $item->appointment_date?->format('M j, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>



                {{-- APPOINTMENT STATISTICS --}}
                <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div>
                        <h2 class="font-bold text-gray-900">
                            Today's Appointment Statistics
                        </h2>
                    </div>

                    <div class="mt-5 space-y-4">
                        @foreach ($appointmentStatistics as $stat)
                            @php
                                $percentage =
                                    $todayAppointmentsCount > 0 ? ($stat['count'] / $todayAppointmentsCount) * 100 : 0;

                                $barColor = match ($stat['status']) {
                                    'confirm' => 'bg-green-600',
                                    'pending' => 'bg-amber-500',
                                    'rejected', 'failed' => 'bg-red-500',
                                    'cancelled', 'no show' => 'bg-gray-400',
                                    default => 'bg-[#849753]',
                                };
                            @endphp

                            <div>
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <span class="text-sm text-gray-600">
                                        {{ $stat['label'] }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-900">
                                        {{ $stat['count'] }}
                                    </span>
                                </div>

                                <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                    <div class="h-full rounded-full {{ $barColor }}"
                                        style="width: {{ $percentage }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div>
                        <h2 class="font-bold text-gray-900">
                            Therapist Status
                        </h2>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                            {{ $availableTherapistsCount }} Available
                        </span>

                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                            {{ $busyTherapistsCount }} Busy
                        </span>

                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                            {{ $offlineTherapistsCount }} Offline
                        </span>
                    </div>
                    @if ($therapists->isEmpty())
                        <p class="py-8 text-center text-sm text-gray-500">
                            No therapists found.
                        </p>
                    @else
                        <div class="mt-4 divide-y divide-gray-100">
                            @foreach ($therapists as $therapist)
                                @php
                                    $therapistStatusClasses = match ($therapist->dashboard_status) {
                                        'available' => 'bg-green-100 text-green-800',
                                        'busy' => 'bg-amber-100 text-amber-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <div class="flex items-center justify-between gap-3 py-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        @if ($therapist->image)
                                            <img src="{{ \Illuminate\Support\Str::startsWith($therapist->image, ['http://', 'https://'])
                                                ? $therapist->image
                                                : asset('storage/' . $therapist->image) }}"
                                                alt="{{ $therapist->name }}" class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-[#849753]/10 font-bold text-[#6F4E37]">
                                                {{ strtoupper(substr($therapist->name, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-900">
                                                {{ $therapist->name }}
                                            </p>
                                            <p class="mt-1 truncate text-xs text-gray-500">
                                                {{ $therapist->specialty ?? 'Massage Therapist' }}
                                            </p>
                                        </div>
                                    </div>

                                    <span
                                        class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $therapistStatusClasses }}">
                                        {{ ucfirst($therapist->dashboard_status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </section>
            </div>

            {{-- RECENT ACTIVITY --}}
            <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="font-bold text-gray-900">
                        Recent Activity
                    </h2>
                </div>

                @if ($recentActivity->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <p class="text-sm text-gray-500">
                            No recent activity available.
                        </p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($recentActivity as $activity)
                            <div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-start gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $customerName($activity) }}
                                            <span class="font-normal text-gray-600">
                                                booked {{ $activity->service?->name ?? 'a service' }} -
                                                {{ $activity->service?->description ?? 'a description' }} -
                                                {{ $activity->service?->duration_minutes ?? 'a service' }} Minutes
                                            </span>
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $activity->created_at?->timezone('Asia/Manila')->format('M j, Y g:i A') }}
                                        </p>
                                    </div>
                                </div>

                                <span
                                    class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($activity->status) }}">
                                    {{ $statusLabel($activity) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

        </div>
    </div>
@endsection
