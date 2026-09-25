@extends('layouts.admin')

@section('title', 'Padayon Massage Center - Appointments')

@php
    use App\Enums\Admin\Appointment\AppointmentStatus;
@endphp

@section('content')

    <div class="min-h-screen flex flex-col">

        <div class="flex-1 p-4 sm:p-6">

            {{-- ====================================================== --}}
            {{-- PAGE HEADER --}}
            {{-- ====================================================== --}}

            <div class="mb-6">

                <h2 class="text-xl font-bold text-gray-800">
                    Manage Appointments
                </h2>
            </div>


            {{-- ====================================================== --}}
            {{-- STATISTICS --}}
            {{-- ====================================================== --}}

            <div class="flex flex-wrap items-center gap-4 mb-8">

                {{-- Total Appointments --}}

                <div
                    class="bg-white rounded-xl border border-gray-200
                    shadow-sm p-4 sm:p-5 flex-1 min-w-35"
                >

                    <div class="text-center">

                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $totalAppointments }}
                        </p>

                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Total Appointments
                        </p>

                    </div>

                </div>


                {{-- Confirmed --}}

                <div
                    class="bg-white rounded-xl border border-gray-200
                    shadow-sm p-4 sm:p-5 flex-1 min-w-35"
                >

                    <div class="text-center">

                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $totalConfirmed }}
                        </p>

                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Confirmed
                        </p>

                    </div>

                </div>


                {{-- Rejected --}}

                <div
                    class="bg-white rounded-xl border border-gray-200
                    shadow-sm p-4 sm:p-5 flex-1 min-w-35"
                >

                    <div class="text-center">

                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $totalRejected }}
                        </p>

                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Rejected
                        </p>

                    </div>

                </div>


                {{-- Pending --}}

                <div
                    class="bg-white rounded-xl border border-gray-200
                    shadow-sm p-4 sm:p-5 flex-1 min-w-35"
                >

                    <div class="text-center">

                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $totalPending }}
                        </p>

                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Pending
                        </p>

                    </div>

                </div>


                {{-- Cancelled --}}

                <div
                    class="bg-white rounded-xl border border-gray-200
                    shadow-sm p-4 sm:p-5 flex-1 min-w-35"
                >

                    <div class="text-center">

                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $totalCancelled ?? 0 }}
                        </p>

                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Cancelled
                        </p>

                    </div>

                </div>


                {{-- No Show --}}

                <div
                    class="bg-white rounded-xl border border-gray-200
                    shadow-sm p-4 sm:p-5 flex-1 min-w-35"
                >

                    <div class="text-center">

                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $totalNoShow ?? 0 }}
                        </p>

                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            No Show
                        </p>

                    </div>

                </div>

            </div>


            {{-- ====================================================== --}}
            {{-- FILTERS --}}
            {{-- ====================================================== --}}

            <div
                class="bg-white rounded-2xl border border-gray-200
                shadow-sm p-4 sm:p-5 mb-6"
            >

                <form
                    method="GET"
                    action="{{ route('admin.appointments') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4"
                >

                    {{-- Appointment Date --}}

                    <div>

                        <label
                            class="block text-sm font-medium
                            text-gray-700 mb-1"
                        >
                            Appointment Date
                        </label>

                        <input
                            type="date"
                            name="appointment_date"
                            value="{{ request('appointment_date') }}"
                            class="w-full rounded-lg border-gray-300
                            focus:border-indigo-500 focus:ring-indigo-500"
                        >

                    </div>


                    {{-- Status --}}

                    <div>

                        <label
                            class="block text-sm font-medium
                            text-gray-700 mb-1"
                        >
                            Status
                        </label>

                        <select
                            name="status"
                            class="w-full rounded-lg border-gray-300
                            focus:border-indigo-500 focus:ring-indigo-500"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option
                                value="{{ AppointmentStatus::PENDING->value }}"
                                {{ request('status') === AppointmentStatus::PENDING->value ? 'selected' : '' }}
                            >
                                Pending
                            </option>

                            <option
                                value="{{ AppointmentStatus::CONFIRMED->value }}"
                                {{ request('status') === AppointmentStatus::CONFIRMED->value ? 'selected' : '' }}
                            >
                                Confirmed
                            </option>

                            <option
                                value="{{ AppointmentStatus::REJECTED->value }}"
                                {{ request('status') === AppointmentStatus::REJECTED->value ? 'selected' : '' }}
                            >
                                Rejected
                            </option>

                            <option
                                value="{{ AppointmentStatus::CANCELLED->value }}"
                                {{ request('status') === AppointmentStatus::CANCELLED->value ? 'selected' : '' }}
                            >
                                Cancelled
                            </option>

                            <option
                                value="{{ AppointmentStatus::NO_SHOW->value }}"
                                {{ request('status') === AppointmentStatus::NO_SHOW->value ? 'selected' : '' }}
                            >
                                No Show
                            </option>

                        </select>

                    </div>


                    {{-- Service --}}

                    <div>

                        <label
                            class="block text-sm font-medium
                            text-gray-700 mb-1"
                        >
                            Service
                        </label>

                        <select
                            name="service"
                            class="w-full rounded-lg border-gray-300
                            focus:border-indigo-500 focus:ring-indigo-500"
                        >

                            <option value="">
                                All Services
                            </option>

                            @foreach ($services as $service)

                                <option
                                    value="{{ $service->id }}"
                                    {{ request('service') == $service->id ? 'selected' : '' }}
                                >

                                    {{ $service->name }}
                                    -
                                    {{ \Illuminate\Support\Str::limit($service->description ?? 'No description', 40) }}
                                    -
                                    {{ $service->duration_minutes ?? 'N/A' }} mins

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Filter Buttons --}}

                    <div class="flex items-center gap-3">

                        <button
                            type="submit"
                            class="rounded-lg bg-[#849753] px-6 py-2
                            text-white hover:bg-[#6F4E37]
                            text-sm w-40"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('admin.appointments') }}"
                            class="rounded-lg border border-gray-300
                            px-6 py-2 text-sm text-gray-700
                            hover:bg-gray-50 w-40 text-center"
                        >
                            Reset
                        </a>

                    </div>

                </form>

            </div>


            {{-- ====================================================== --}}
            {{-- APPOINTMENT TABLE --}}
            {{-- ====================================================== --}}

            <div
                class="bg-white rounded-2xl border border-gray-200
                shadow-sm overflow-hidden"
            >

                {{-- Table Header --}}

                <div
                    class="px-5 py-4 border-b border-gray-100
                    flex items-center justify-between"
                >

                    <h3 class="font-semibold text-gray-800">
                        Appointment List
                    </h3>

                    <span class="text-xs text-gray-400">
                        {{ $appointments->total() }} total
                    </span>

                </div>


                @if ($appointments->isEmpty())

                    <div class="py-16 text-center text-gray-400">

                        <svg
                            class="w-12 h-12 mx-auto mb-3 text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            viewBox="0 0 24 24"
                        >

                            <path
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />

                        </svg>

                        <p class="text-sm">
                            No appointments found.
                        </p>

                    </div>

                @else

                    <div class="overflow-x-auto">

                        <table class="w-full text-sm">

                            <thead>

                                <tr class="bg-gray-50 text-left">

                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        #
                                    </th>

                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        User
                                    </th>

                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Service Details
                                    </th>

                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Add-on Details
                                    </th>

                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Condition
                                    </th>

                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Therapist
                                    </th>

                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Appointment Time
                                    </th>

                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Status
                                    </th>

                                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-gray-100">

                                @forelse($appointments as $appointment)

                                    {{-- ================================================= --}}
                                    {{-- CLICKABLE APPOINTMENT ROW --}}
                                    {{-- ================================================= --}}

                                    <tr
                                        class="appointment-row hover:bg-gray-50
                                        transition align-top cursor-pointer"

                                        onclick="openAppointmentModal(this)"

                                        data-id="{{ $appointment->id }}"

                                        data-user-name="{{ $appointment->user->name ?? 'N/A' }}"

                                        data-user-email="{{ $appointment->user->email ?? 'N/A' }}"

                                        data-service-name="{{ $appointment->service->name ?? 'N/A' }}"

                                        data-service-description="{{ $appointment->service->description ?? 'N/A' }}"

                                        data-service-duration="{{ $appointment->service->duration_minutes ?? 'N/A' }}"

                                        data-service-price="{{ number_format($appointment->service->price ?? 0, 2) }}"

                                        data-level="{{ $appointment->level?->value ?? 'N/A' }}"

                                        data-addon-name="{{ $appointment->addOn->name ?? 'None' }}"

                                        data-addon-duration="{{ $appointment->addOn
                                            ? $appointment->addOn->duration_minutes . ' mins'
                                            : 'No add-on selected' }}"

                                        data-addon-price="{{ number_format($appointment->addons_price ?? 0, 2) }}"

                                        data-previous-operations="{{ $appointment->has_previous_operations ?? 'N/A' }}"

                                        data-body-problem="{{ $appointment->body_problem ?? 'N/A' }}"

                                        data-therapist="{{ $appointment->therapist->name ?? 'N/A' }}"

                                        data-date="{{ $appointment->appointment_date
                                            ? $appointment->appointment_date->format('Y-m-d')
                                            : 'N/A' }}"

                                        data-time-start="{{ $appointment->appointment_time
                                            ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A')
                                            : 'N/A' }}"

                                        data-time-end="{{ $appointment->appointment_end_time
                                            ? \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A')
                                            : 'N/A' }}"

                                        data-status="{{ $appointment->status?->value ?? 'N/A' }}"
                                    >


                                        {{-- ID --}}

                                        <td class="px-5 py-4 text-gray-600 whitespace-nowrap">

                                            {{ $appointment->id }}

                                        </td>


                                        {{-- USER --}}

                                        <td class="px-5 py-4">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="w-9 h-9 rounded-full
                                                    bg-[#849753] flex items-center
                                                    justify-center text-white
                                                    font-bold text-sm shrink-0"
                                                >

                                                    {{ strtoupper(substr($appointment->user->name ?? 'N', 0, 1)) }}

                                                </div>

                                                <div>

                                                    <p
                                                        class="font-semibold
                                                        text-gray-800 whitespace-nowrap"
                                                    >

                                                        {{ $appointment->user->name ?? 'N/A' }}

                                                    </p>

                                                    <p class="text-xs text-gray-400">

                                                        {{ $appointment->user->email ?? '' }}

                                                    </p>

                                                </div>

                                            </div>

                                        </td>


                                        {{-- SERVICE --}}

                                        <td class="px-5 py-4 text-gray-600">

                                            <div class="space-y-1">

                                                <p class="font-semibold text-gray-800">

                                                    {{ $appointment->service->name ?? 'N/A' }}

                                                </p>

                                                <p class="text-xs text-gray-500">

                                                    {{ $appointment->service->description ?? 'N/A' }}

                                                </p>

                                                <p class="text-xs text-gray-500">

                                                    Duration:

                                                    {{ $appointment->service->duration_minutes ?? 'N/A' }}

                                                    mins

                                                </p>

                                                <p class="text-xs font-medium text-gray-500">

                                                    Price:

                                                    ₱{{ number_format($appointment->service->price ?? 0, 2) }}

                                                </p>

                                                <p
                                                    class="text-xs font-medium
                                                    text-gray-500 capitalize"
                                                >

                                                    Level:

                                                    {{ $appointment->level?->value ?? 'N/A' }}

                                                </p>

                                            </div>

                                        </td>


                                        {{-- ADD-ON --}}

                                        <td class="px-5 py-4 text-gray-600">

                                            <div class="space-y-1">

                                                <p class="font-semibold text-gray-800">

                                                    {{ $appointment->addOn->name ?? 'None' }}

                                                </p>

                                                <p class="text-xs text-gray-500">

                                                    {{ $appointment->addOn
                                                        ? $appointment->addOn->duration_minutes . ' mins'
                                                        : 'No add-on selected' }}

                                                </p>

                                                <p class="text-xs font-medium text-gray-500">

                                                    Price:

                                                    ₱{{ number_format($appointment->addons_price ?? 0, 2) }}

                                                </p>

                                            </div>

                                        </td>


                                        {{-- CONDITION --}}

                                        <td class="px-5 py-4 text-gray-600">

                                            <div class="space-y-1">

                                                <p
                                                    class="text-xs font-semibold
                                                    text-gray-500 uppercase
                                                    tracking-wide"
                                                >
                                                    Previous Operations
                                                </p>

                                                <p class="text-sm capitalize text-gray-800">

                                                    {{ $appointment->has_previous_operations ?? 'N/A' }}

                                                </p>

                                                <p
                                                    class="text-xs font-semibold
                                                    text-gray-500 uppercase
                                                    tracking-wide mt-2"
                                                >
                                                    Body Problem
                                                </p>

                                                <p class="text-sm text-gray-700">

                                                    {{ \Illuminate\Support\Str::limit(
                                                        $appointment->body_problem ?? 'N/A',
                                                        60
                                                    ) }}

                                                </p>

                                            </div>

                                        </td>


                                        {{-- THERAPIST --}}

                                        <td
                                            class="px-5 py-4 text-gray-600
                                            whitespace-nowrap"
                                        >

                                            {{ $appointment->therapist->name ?? 'N/A' }}

                                        </td>


                                        {{-- APPOINTMENT TIME --}}

                                        <td
                                            class="px-5 py-4 text-gray-600
                                            whitespace-nowrap"
                                        >

                                            <div class="space-y-1">

                                                <p class="text-sm font-semibold text-gray-800">

                                                    {{ $appointment->appointment_date
                                                        ? $appointment->appointment_date->format('Y-m-d')
                                                        : 'N/A' }}

                                                </p>

                                                <p class="text-xs text-gray-500">

                                                    {{ $appointment->appointment_time
                                                        ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A')
                                                        : 'N/A' }}

                                                    -

                                                    {{ $appointment->appointment_end_time
                                                        ? \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A')
                                                        : 'N/A' }}

                                                </p>

                                            </div>

                                        </td>


                                        {{-- ================================================= --}}
                                        {{-- STATUS --}}
                                        {{-- ================================================= --}}

                                        <td class="px-5 py-4">

                                            @if ($appointment->status === AppointmentStatus::CONFIRMED)

                                                <span
                                                    class="inline-flex items-center gap-1.5
                                                    px-2.5 py-1 bg-green-50
                                                    border border-green-200
                                                    text-green-700 text-xs
                                                    font-semibold rounded-full
                                                    whitespace-nowrap"
                                                >

                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full
                                                        bg-green-500"
                                                    ></span>

                                                    Confirmed

                                                </span>

                                            @elseif ($appointment->status === AppointmentStatus::REJECTED)

                                                <span
                                                    class="inline-flex items-center gap-1.5
                                                    px-2.5 py-1 bg-red-50
                                                    border border-red-200
                                                    text-red-700 text-xs
                                                    font-semibold rounded-full
                                                    whitespace-nowrap"
                                                >

                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full
                                                        bg-red-500"
                                                    ></span>

                                                    Rejected

                                                </span>

                                            @elseif ($appointment->status === AppointmentStatus::PENDING)

                                                <span
                                                    class="inline-flex items-center gap-1.5
                                                    px-2.5 py-1 bg-yellow-50
                                                    border border-yellow-200
                                                    text-yellow-700 text-xs
                                                    font-semibold rounded-full
                                                    whitespace-nowrap"
                                                >

                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full
                                                        bg-yellow-400"
                                                    ></span>

                                                    Pending

                                                </span>

                                            @elseif ($appointment->status === AppointmentStatus::CANCELLED)

                                                <span
                                                    class="inline-flex items-center gap-1.5
                                                    px-2.5 py-1 bg-gray-100
                                                    border border-gray-200
                                                    text-gray-600 text-xs
                                                    font-semibold rounded-full
                                                    whitespace-nowrap"
                                                >

                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full
                                                        bg-gray-500"
                                                    ></span>

                                                    Cancelled

                                                </span>

                                            @elseif ($appointment->status === AppointmentStatus::NO_SHOW)

                                                <span
                                                    class="inline-flex items-center gap-1.5
                                                    px-2.5 py-1 bg-gray-100
                                                    border border-gray-200
                                                    text-gray-600 text-xs
                                                    font-semibold rounded-full
                                                    whitespace-nowrap"
                                                >

                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full
                                                        bg-gray-500"
                                                    ></span>

                                                    No Show

                                                </span>

                                            @else

                                                <span
                                                    class="inline-flex items-center gap-1.5
                                                    px-2.5 py-1 bg-gray-100
                                                    border border-gray-200
                                                    text-gray-500 text-xs
                                                    font-semibold rounded-full
                                                    whitespace-nowrap"
                                                >

                                                    {{ $appointment->status?->value ?? 'N/A' }}

                                                </span>

                                            @endif

                                        </td>


                                        {{-- ================================================= --}}
                                        {{-- ACTION --}}
                                        {{-- ================================================= --}}

                                        <td
                                            class="px-5 py-4"
                                            onclick="event.stopPropagation()"
                                        >

                                            <form
                                                action="{{ route(
                                                    'admin.appointments.updateStatus',
                                                    $appointment->id
                                                ) }}"
                                                method="POST"
                                                class="flex flex-col gap-2 w-full max-w-35"
                                            >

                                                @csrf

                                                @method('PUT')


                                                <select
                                                    name="status"
                                                    class="rounded-lg border-gray-300
                                                    text-sm focus:border-[#6F4E37]
                                                    focus:ring-[#6F4E37] w-full"

                                                    @if ($appointment->status === AppointmentStatus::CANCELLED)
                                                        disabled
                                                    @endif

                                                    @if ($appointment->status === AppointmentStatus::CONFIRMED)
                                                        disabled
                                                    @endif

                                                    @if ($appointment->status === AppointmentStatus::NO_SHOW)
                                                        disabled
                                                    @endif

                                                    @if ($appointment->status === AppointmentStatus::REJECTED)
                                                    disabled
                                                @endif

                                                >

                                                    <option
                                                        value="{{ AppointmentStatus::PENDING->value }}"
                                                        {{ $appointment->status === AppointmentStatus::PENDING ? 'selected' : '' }}
                                                        
                                                    >
                                                        Pending
                                                    </option>

                                                    <option
                                                        value="{{ AppointmentStatus::CONFIRMED->value }}"
                                                        {{ $appointment->status === AppointmentStatus::CONFIRMED ? 'selected' : '' }}
                                                        
                                                    >
                                                        Confirm
                                                    </option>

                                                    <option
                                                        value="{{ AppointmentStatus::REJECTED->value }}"
                                                        {{ $appointment->status === AppointmentStatus::REJECTED ? 'selected' : '' }}
                                                    >
                                                        Rejected
                                                    </option>

                                                    <option
                                                        value="{{ AppointmentStatus::CANCELLED->value }}"
                                                        {{ $appointment->status === AppointmentStatus::CANCELLED ? 'selected' : '' }}
                                                    >
                                                        Cancelled
                                                    </option>

                                                    <option
                                                        value="{{ AppointmentStatus::NO_SHOW->value }}"
                                                        {{ $appointment->status === AppointmentStatus::NO_SHOW ? 'selected' : '' }}
                                                    >
                                                        No Show
                                                    </option>

                                                </select>


                                                <button
                                                    type="submit"
                                                    class="rounded-lg bg-[#849753]
                                                    px-4 py-2 text-white
                                                    hover:bg-[#6F4E37]
                                                    text-sm disabled:opacity-50
                                                    w-full"

                                                    @if ($appointment->status === AppointmentStatus::CANCELLED)
                                                        disabled
                                                    @endif

                                                    @if ($appointment->status === AppointmentStatus::CONFIRMED)
                                                        disabled
                                                    @endif

                                                    @if ($appointment->status === AppointmentStatus::NO_SHOW)
                                                        disabled
                                                    @endif

                                                    @if ($appointment->status === AppointmentStatus::REJECTED)
                                                        disabled
                                                    @endif
                                                >

                                                    Update

                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="9"
                                            class="px-5 py-10 text-center
                                            text-gray-400"
                                        >

                                            No appointments found.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    {{-- ====================================================== --}}
                    {{-- PAGINATION --}}
                    {{-- ====================================================== --}}

                    @if ($appointments->hasPages())

                        <div class="px-5 py-4 border-t border-gray-100">

                            {{ $appointments->links() }}

                        </div>

                    @endif

                @endif

            </div>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- APPOINTMENT DETAILS MODAL --}}
    {{-- ================================================================ --}}

    <div
        id="appointmentModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-labelledby="appointmentModalTitle"
    >

        {{-- Dark Overlay --}}

        <div
            class="fixed inset-0 bg-black/50 backdrop-blur-sm"
            onclick="closeAppointmentModal()"
        ></div>


        {{-- Modal Position --}}

        <div class="relative min-h-screen flex items-center justify-center p-4">

            {{-- Modal Box --}}

            <div
                class="relative w-full max-w-3xl bg-white rounded-2xl
                shadow-2xl max-h-[90vh] overflow-y-auto"
                onclick="event.stopPropagation()"
            >

                {{-- Modal Header --}}

                <div
                    class="sticky top-0 bg-white z-10 px-6 py-5
                    border-b border-gray-100"
                >

                    <div class="flex items-start justify-between">

                        <div>

                            <p
                                class="text-xs font-semibold text-[#849753]
                                uppercase tracking-wide"
                            >
                                Appointment Details
                            </p>

                            <h2
                                id="appointmentModalTitle"
                                class="text-2xl font-bold text-gray-800 mt-1"
                            >
                                Appointment #--
                            </h2>

                        </div>


                        <button
                            type="button"
                            onclick="closeAppointmentModal()"
                            class="text-gray-400 hover:text-gray-700
                            text-3xl font-bold leading-none ml-4"
                            aria-label="Close"
                        >

                            &times;

                        </button>

                    </div>

                </div>


                {{-- Modal Content --}}

                <div class="p-6 space-y-6">


                    {{-- CUSTOMER INFORMATION --}}

                    <div>

                        <h3
                            class="text-sm font-bold text-gray-800
                            uppercase tracking-wide mb-3"
                        >
                            Customer Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4">

                            <div class="flex items-center gap-4">

                                <div
                                    id="modalUserInitial"
                                    class="w-12 h-12 rounded-full
                                    bg-[#849753] flex items-center
                                    justify-center text-white
                                    font-bold text-lg shrink-0"
                                >
                                    N
                                </div>

                                <div>

                                    <p
                                        id="modalUserName"
                                        class="font-bold text-gray-800"
                                    >
                                        N/A
                                    </p>

                                    <p
                                        id="modalUserEmail"
                                        class="text-sm text-gray-500"
                                    >
                                        N/A
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- APPOINTMENT INFORMATION --}}

                    <div>

                        <h3
                            class="text-sm font-bold text-gray-800
                            uppercase tracking-wide mb-3"
                        >
                            Appointment Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                            {{-- Date --}}

                            <div class="bg-gray-50 rounded-xl p-4">

                                <p
                                    class="text-xs text-gray-400
                                    uppercase font-semibold"
                                >
                                    Date
                                </p>

                                <p
                                    id="modalDate"
                                    class="mt-1 font-semibold text-gray-800"
                                >
                                    N/A
                                </p>

                            </div>


                            {{-- Time --}}

                            <div class="bg-gray-50 rounded-xl p-4">

                                <p
                                    class="text-xs text-gray-400
                                    uppercase font-semibold"
                                >
                                    Time
                                </p>

                                <p
                                    id="modalTime"
                                    class="mt-1 font-semibold text-gray-800"
                                >
                                    N/A
                                </p>

                            </div>


                            {{-- Therapist --}}

                            <div class="bg-gray-50 rounded-xl p-4">

                                <p
                                    class="text-xs text-gray-400
                                    uppercase font-semibold"
                                >
                                    Therapist
                                </p>

                                <p
                                    id="modalTherapist"
                                    class="mt-1 font-semibold text-gray-800"
                                >
                                    N/A
                                </p>

                            </div>


                            {{-- Status --}}

                            <div class="bg-gray-50 rounded-xl p-4">

                                <p
                                    class="text-xs text-gray-400
                                    uppercase font-semibold"
                                >
                                    Status
                                </p>

                                <p
                                    id="modalStatus"
                                    class="mt-1 font-semibold text-gray-800"
                                >
                                    N/A
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- SERVICE INFORMATION --}}

                    <div>

                        <h3
                            class="text-sm font-bold text-gray-800
                            uppercase tracking-wide mb-3"
                        >
                            Service Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4 space-y-3">

                            <div>

                                <p
                                    class="text-xs text-gray-400
                                    uppercase font-semibold"
                                >
                                    Service
                                </p>

                                <p
                                    id="modalServiceName"
                                    class="mt-1 font-semibold text-gray-800"
                                >
                                    N/A
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs text-gray-400
                                    uppercase font-semibold"
                                >
                                    Description
                                </p>

                                <p
                                    id="modalServiceDescription"
                                    class="mt-1 text-sm text-gray-600"
                                >
                                    N/A
                                </p>

                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                        uppercase font-semibold"
                                    >
                                        Duration
                                    </p>

                                    <p
                                        id="modalServiceDuration"
                                        class="mt-1 font-semibold text-gray-800"
                                    >
                                        N/A
                                    </p>

                                </div>


                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                        uppercase font-semibold"
                                    >
                                        Price
                                    </p>

                                    <p
                                        id="modalServicePrice"
                                        class="mt-1 font-semibold text-gray-800"
                                    >
                                        ₱0.00
                                    </p>

                                </div>


                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                        uppercase font-semibold"
                                    >
                                        Level
                                    </p>

                                    <p
                                        id="modalLevel"
                                        class="mt-1 font-semibold
                                        text-gray-800 capitalize"
                                    >
                                        N/A
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ADD-ON INFORMATION --}}

                    <div>

                        <h3
                            class="text-sm font-bold text-gray-800
                            uppercase tracking-wide mb-3"
                        >
                            Add-on Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4">

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                        uppercase font-semibold"
                                    >
                                        Add-on
                                    </p>

                                    <p
                                        id="modalAddonName"
                                        class="mt-1 font-semibold text-gray-800"
                                    >
                                        None
                                    </p>

                                </div>


                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                        uppercase font-semibold"
                                    >
                                        Duration
                                    </p>

                                    <p
                                        id="modalAddonDuration"
                                        class="mt-1 font-semibold text-gray-800"
                                    >
                                        N/A
                                    </p>

                                </div>


                                <div>

                                    <p
                                        class="text-xs text-gray-400
                                        uppercase font-semibold"
                                    >
                                        Price
                                    </p>

                                    <p
                                        id="modalAddonPrice"
                                        class="mt-1 font-semibold text-gray-800"
                                    >
                                        ₱0.00
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- CONDITION INFORMATION --}}

                    <div>

                        <h3
                            class="text-sm font-bold text-gray-800
                            uppercase tracking-wide mb-3"
                        >
                            Customer Condition
                        </h3>

                        <div
                            class="bg-gray-50 rounded-xl p-4 space-y-4"
                        >

                            <div>

                                <p
                                    class="text-xs text-gray-400
                                    uppercase font-semibold"
                                >
                                    Previous Operations
                                </p>

                                <p
                                    id="modalPreviousOperations"
                                    class="mt-1 text-gray-800 capitalize"
                                >
                                    N/A
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs text-gray-400
                                    uppercase font-semibold"
                                >
                                    Body Problem
                                </p>

                                <p
                                    id="modalBodyProblem"
                                    class="mt-1 text-gray-700
                                    whitespace-pre-line"
                                >
                                    N/A
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- MODAL FOOTER --}}

                <div
                    class="sticky bottom-0 bg-white border-t
                    border-gray-100 px-6 py-4 flex justify-end"
                >

                    <button
                        type="button"
                        onclick="closeAppointmentModal()"
                        class="px-6 py-2.5 bg-gray-200
                        hover:bg-gray-300 text-gray-700
                        font-semibold rounded-lg transition"
                    >
                        Close
                    </button>

                </div>

            </div>

        </div>

    </div>

@endsection


@push('scripts')

<script>

    // ============================================================
    // GET MODAL
    // ============================================================

    const appointmentModal =
        document.getElementById('appointmentModal');


    // ============================================================
    // OPEN APPOINTMENT MODAL
    // ============================================================

    function openAppointmentModal(row) {

        // --------------------------------------------------------
        // Get appointment data from data-* attributes
        // --------------------------------------------------------

        const id =
            row.dataset.id;

        const userName =
            row.dataset.userName;

        const userEmail =
            row.dataset.userEmail;

        const serviceName =
            row.dataset.serviceName;

        const serviceDescription =
            row.dataset.serviceDescription;

        const serviceDuration =
            row.dataset.serviceDuration;

        const servicePrice =
            row.dataset.servicePrice;

        const level =
            row.dataset.level;

        const addonName =
            row.dataset.addonName;

        const addonDuration =
            row.dataset.addonDuration;

        const addonPrice =
            row.dataset.addonPrice;

        const previousOperations =
            row.dataset.previousOperations;

        const bodyProblem =
            row.dataset.bodyProblem;

        const therapist =
            row.dataset.therapist;

        const date =
            row.dataset.date;

        const timeStart =
            row.dataset.timeStart;

        const timeEnd =
            row.dataset.timeEnd;

        const status =
            row.dataset.status;


        // --------------------------------------------------------
        // Format status for display
        // --------------------------------------------------------

        let formattedStatus = status;

        switch (status) {

            case 'confirm':
                formattedStatus = 'Confirmed';
                break;

            case 'pending':
                formattedStatus = 'Pending';
                break;

            case 'rejected':
                formattedStatus = 'Rejected';
                break;

            case 'cancelled':
                formattedStatus = 'Cancelled';
                break;

            case 'no show':
                formattedStatus = 'No Show';
                break;

            default:
                formattedStatus = status || 'N/A';
                break;
        }


        // ========================================================
        // MODAL TITLE
        // ========================================================

        document.getElementById(
            'appointmentModalTitle'
        ).textContent =
            'Appointment #' + id;


        // ========================================================
        // CUSTOMER
        // ========================================================

        document.getElementById(
            'modalUserName'
        ).textContent =
            userName;

        document.getElementById(
            'modalUserEmail'
        ).textContent =
            userEmail;


        // User initial

        const initial =
            userName !== 'N/A'
                ? userName.charAt(0).toUpperCase()
                : 'N';


        document.getElementById(
            'modalUserInitial'
        ).textContent =
            initial;


        // ========================================================
        // APPOINTMENT
        // ========================================================

        document.getElementById(
            'modalDate'
        ).textContent =
            date;

        document.getElementById(
            'modalTime'
        ).textContent =
            timeStart + ' - ' + timeEnd;

        document.getElementById(
            'modalTherapist'
        ).textContent =
            therapist;

        document.getElementById(
            'modalStatus'
        ).textContent =
            formattedStatus;


        // ========================================================
        // SERVICE
        // ========================================================

        document.getElementById(
            'modalServiceName'
        ).textContent =
            serviceName;

        document.getElementById(
            'modalServiceDescription'
        ).textContent =
            serviceDescription;

        document.getElementById(
            'modalServiceDuration'
        ).textContent =
            serviceDuration + ' mins';

        document.getElementById(
            'modalServicePrice'
        ).textContent =
            '₱' + servicePrice;

        document.getElementById(
            'modalLevel'
        ).textContent =
            level;


        // ========================================================
        // ADD-ON
        // ========================================================

        document.getElementById(
            'modalAddonName'
        ).textContent =
            addonName;

        document.getElementById(
            'modalAddonDuration'
        ).textContent =
            addonDuration;

        document.getElementById(
            'modalAddonPrice'
        ).textContent =
            '₱' + addonPrice;


        // ========================================================
        // CONDITION
        // ========================================================

        document.getElementById(
            'modalPreviousOperations'
        ).textContent =
            previousOperations;

        document.getElementById(
            'modalBodyProblem'
        ).textContent =
            bodyProblem;


        // ========================================================
        // SHOW MODAL
        // ========================================================

        appointmentModal.classList.remove('hidden');


        // Prevent background page scrolling

        document.body.classList.add(
            'overflow-hidden'
        );

    }


    // ============================================================
    // CLOSE MODAL
    // ============================================================

    function closeAppointmentModal() {

        appointmentModal.classList.add(
            'hidden'
        );


        // Allow page scrolling again

        document.body.classList.remove(
            'overflow-hidden'
        );

    }


    // ============================================================
    // ESCAPE KEY CLOSE
    // ============================================================

    document.addEventListener(
        'keydown',
        function(event) {

            if (event.key === 'Escape') {

                closeAppointmentModal();

            }

        }
    );

</script>

@endpush