@extends('layouts.user')

@section('title', 'Padayon Massage Center - Appointments History')

@section('content')


    @php
        use App\Enums\Admin\Appointment\AppointmentStatus;
    @endphp

    <div class="p-4 sm:p-6" x-data="{
        showAppointmentModal: false,
        selectedAppointment: null,
    
        openAppointmentModal(appointment) {
            this.selectedAppointment = appointment;
            this.showAppointmentModal = true;
            document.body.classList.add('overflow-hidden');
        },
    
        closeAppointmentModal() {
            this.showAppointmentModal = false;
            this.selectedAppointment = null;
            document.body.classList.remove('overflow-hidden');
        }
    }" @keydown.escape.window="closeAppointmentModal()">

        {{-- PAGE HEADER --}}
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">
                Appointment Overview
            </h2>
        </div>

        {{-- STATISTICS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">

            {{-- TOTAL --}}
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
                <div class="text-center min-w-0">
                    <p class="text-xl sm:text-2xl font-bold text-gray-800">
                        {{ $totalAppointments ?? 0 }}
                    </p>

                    <p class="text-xs text-gray-400 font-medium mt-0.5">
                        Total Appointments
                    </p>
                </div>
            </div>

            {{-- PENDING --}}
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
                <div class="text-center min-w-0">
                    <p class="text-xl sm:text-2xl font-bold text-gray-800">
                        {{ $pendingAppointments ?? 0 }}
                    </p>

                    <p class="text-xs text-gray-400 font-medium mt-0.5">
                        Pending
                    </p>
                </div>
            </div>

            {{-- CONFIRMED --}}
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
                <div class="text-center min-w-0">
                    <p class="text-xl sm:text-2xl font-bold text-gray-800">
                        {{ $confirmedAppointments ?? 0 }}
                    </p>

                    <p class="text-xs text-gray-400 font-medium mt-0.5">
                        Confirmed
                    </p>
                </div>
            </div>

            {{-- REJECTED --}}
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
                <div class="text-center min-w-0">
                    <p class="text-xl sm:text-2xl font-bold text-gray-800">
                        {{ $rejectedAppointments ?? 0 }}
                    </p>

                    <p class="text-xs text-gray-400 font-medium mt-0.5">
                        Rejected
                    </p>
                </div>
            </div>

            {{-- CANCELLED --}}
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
                <div class="text-center min-w-0">
                    <p class="text-xl sm:text-2xl font-bold text-gray-800">
                        {{ $cancelledAppointments ?? 0 }}
                    </p>

                    <p class="text-xs text-gray-400 font-medium mt-0.5">
                        Cancelled
                    </p>
                </div>
            </div>

        </div>

        {{-- FILTERS --}}
        <form method="GET" action="{{ route('user.my-appointments') }}"
            class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 mb-6">

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

                {{-- SEARCH --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Search
                    </label>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Service, therapist, level, ID"
                        class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]">
                </div>

                {{-- STATUS --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>

                    <select name="status"
                        class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]">
                        <option value="">
                            All Status
                        </option>

                        <option value="{{ AppointmentStatus::PENDING->value }}" @selected(request('status') === AppointmentStatus::PENDING->value)>
                            {{ AppointmentStatus::PENDING->label() }}
                        </option>

                        <option value="{{ AppointmentStatus::CONFIRMED->value }}" @selected(request('status') === AppointmentStatus::CONFIRMED->value)>
                            {{ AppointmentStatus::CONFIRMED->label() }}
                        </option>

                        <option value="{{ AppointmentStatus::REJECTED->value }}" @selected(request('status') === AppointmentStatus::REJECTED->value)>
                            {{ AppointmentStatus::REJECTED->label() }}
                        </option>

                        <option value="{{ AppointmentStatus::CANCELLED->value }}" @selected(request('status') === AppointmentStatus::CANCELLED->value)>
                            {{ AppointmentStatus::CANCELLED->label() }}
                        </option>

                        <option value="{{ AppointmentStatus::NO_SHOW->value }}" @selected(request('status') === AppointmentStatus::NO_SHOW->value)>
                            {{ AppointmentStatus::NO_SHOW->label() }}
                        </option>
                        <option value="{{ AppointmentStatus::FAILED->value }}" @selected(request('status') === AppointmentStatus::FAILED->value)>
                            {{ AppointmentStatus::FAILED->label() }}
                        </option>
                    </select>
                </div>

                {{-- APPOINTMENT DATE --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Appointment Date
                    </label>

                    <input type="date" name="appointment_date" value="{{ request('appointment_date') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-[#849753] focus:ring-[#849753]">
                </div>

                {{-- BUTTONS --}}
                <div class="flex items-center gap-3">

                    <button type="submit"
                        class="rounded-xl bg-[#849753] px-6 py-2 text-white font-medium hover:bg-[#6F4E37] transition w-40">
                        Filter
                    </button>

                    <a href="{{ route('user.my-appointments') }}"
                        class="rounded-xl border border-gray-300 px-6 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 w-40 text-center">
                        Reset
                    </a>

                </div>

            </div>
        </form>

        {{-- APPOINTMENT TABLE --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">

                <h3 class="font-semibold text-gray-800">
                    My Appointment List
                </h3>

                <span class="text-xs text-gray-400">
                    {{ $appointments->total() }} total
                </span>

            </div>

            @if ($appointments->isEmpty())

                <div class="py-16 text-center text-gray-400">

                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                        stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>

                    <p class="text-sm">
                        You have no appointments yet.
                    </p>

                </div>
            @else
                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead>
                            <tr class="bg-gray-50 text-left">

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    #
                                </th>

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    Date
                                </th>

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    Time
                                </th>

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    End Time
                                </th>

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    Service Details
                                </th>

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    Add-on Details
                                </th>

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    Therapist
                                </th>

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    Status
                                </th>

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @foreach ($appointments as $appointment)
                                @php
                                    $status = $appointment->status;
                                @endphp

                                <tr class="appointment-row hover:bg-gray-50 transition align-top cursor-pointer"
                                    @click="openAppointmentModal({
                                    id: @js($appointment->id),
                                    date: @js($appointment->appointment_date ? $appointment->appointment_date->format('Y-m-d') : 'N/A'),
                                    time: @js($appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : 'N/A'),
                                    endTime: @js($appointment->appointment_end_time ? \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A') : 'N/A'),
                                    serviceName: @js($appointment->service?->name ?? 'N/A'),
                                    serviceDescription: @js($appointment->service?->description ?? 'N/A'),
                                    serviceDuration: @js($appointment->service?->duration_minutes ?? '0'),
                                    servicePrice: @js(number_format($appointment->service_price ?? 0, 2)),
                                    level: @js($appointment->level ?? 'N/A'),
                                    addonName: @js($appointment->addOn?->name ?? 'None'),
                                    addonDuration: @js($appointment->addOn ? $appointment->addOn->duration_minutes . ' mins' : 'No add-on selected'),
                                    addonPrice: @js(number_format($appointment->addons_price ?? 0, 2)),
                                    therapist: @js($appointment->therapist?->name ?? 'N/A'),
                                    status: @js($status?->label() ?? 'N/A'),
                                    previousOperations: @js($appointment->has_previous_operations ?? 'N/A'),
                                    bodyProblem: @js($appointment->body_problem ?? 'N/A')
                                })">

                                    {{-- ID --}}
                                    <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                        {{ $appointment->id }}
                                    </td>

                                    {{-- DATE --}}
                                    <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                        {{ $appointment->appointment_date ? $appointment->appointment_date->format('Y-m-d') : 'N/A' }}
                                    </td>

                                    {{-- TIME --}}
                                    <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                        {{ $appointment->appointment_time
                                            ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A')
                                            : 'N/A' }}
                                    </td>

                                    {{-- END TIME --}}
                                    <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                        {{ $appointment->appointment_end_time
                                            ? \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A')
                                            : 'N/A' }}
                                    </td>

                                    {{-- SERVICE --}}
                                    <td class="px-5 py-4 text-gray-600">

                                        <div class="space-y-1 min-w-0">

                                            <p class="font-semibold text-gray-800 wrap-break-word">
                                                {{ $appointment->service?->name ?? 'N/A' }}
                                            </p>

                                            <p class="text-xs text-gray-500 wrap-break-word">
                                                {{ $appointment->service?->description ?? 'N/A' }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                Duration:
                                                {{ $appointment->service?->duration_minutes ?? '0' }}
                                                mins
                                            </p>

                                            <p class="text-xs font-medium text-gray-500 capitalize">
                                                Level:
                                                {{ $appointment->level ?? 'N/A' }}
                                            </p>

                                        </div>

                                    </td>

                                    {{-- ADD-ON --}}
                                    <td class="px-5 py-4 text-gray-600">

                                        <div class="space-y-1 min-w-0">

                                            <p class="font-semibold text-gray-800 wrap-break-word">
                                                {{ $appointment->addOn?->name ?? 'None' }}
                                            </p>

                                            <p class="text-xs text-gray-500 wrap-break-word">
                                                {{ $appointment->addOn ? $appointment->addOn->duration_minutes . ' mins' : 'No add-on selected' }}
                                            </p>

                                        </div>

                                    </td>

                                    {{-- THERAPIST --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3 min-w-0">

                                            <div
                                                class="w-9 h-9 rounded-full bg-[#6F4E37] flex items-center justify-center text-white font-bold text-sm shrink-0">
                                                {{ strtoupper(substr($appointment->therapist?->name ?? 'N', 0, 1)) }}
                                            </div>

                                            <div class="min-w-0">

                                                <p class="font-semibold text-gray-800 wrap-break-word">
                                                    {{ $appointment->therapist?->name ?? 'N/A' }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-5 py-4">

                                        @if ($status === AppointmentStatus::CONFIRMED)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                {{ $status->label() }}
                                            </span>
                                        @elseif ($status === AppointmentStatus::PENDING)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                                                {{ $status->label() }}
                                            </span>
                                        @elseif ($status === AppointmentStatus::REJECTED)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                {{ $status->label() }}
                                            </span>
                                        @elseif ($status === AppointmentStatus::CANCELLED)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-semibold rounded-full whitespace-nowrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                                {{ $status->label() }}
                                            </span>
                                        @elseif ($status === AppointmentStatus::NO_SHOW)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-semibold rounded-full whitespace-nowrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                                {{ $status->label() }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full whitespace-nowrap">
                                                {{ $status?->label() ?? 'N/A' }}
                                            </span>
                                        @endif

                                    </td>

                                    {{-- ACTION --}}
                                    <td class="px-5 py-4" @click.stop>

                                        @if ($appointment->payment_method === 'gcash' && in_array($appointment->payment_status, ['failed', 'pending']))
                                            {{-- PAY AGAIN --}}
                                            <form
                                                action="{{ route('user.appointments.payment.retry', $appointment->id) }}"
                                                method="POST">
                                                @csrf

                                                <button type="submit"
                                                    class="rounded-lg bg-[#849753] px-4 py-2 text-white hover:bg-[#6F4E37] text-sm font-medium transition">
                                                    Pay Again
                                                </button>
                                            </form>
                                        @elseif ($status === AppointmentStatus::PENDING)
                                            {{-- CANCEL --}}
                                            <form action="{{ route('user.my-appointments.cancel', $appointment->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')

                                                <button type="submit"
                                                    onclick="return confirm('Cancel this appointment?')"
                                                    class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700 text-sm">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">
                                                No action
                                            </span>
                                        @endif

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- PAGINATION --}}
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $appointments->links() }}
                </div>

            @endif

        </div>


        {{-- ================================================================ --}}
        {{-- APPOINTMENT DETAILS MODAL --}}
        {{-- ================================================================ --}}

        <div x-show="showAppointmentModal" x-cloak x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true" aria-labelledby="appointmentModalTitle"
            @click.self="closeAppointmentModal()">

            {{-- DARK OVERLAY --}}
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeAppointmentModal()"></div>

            {{-- MODAL POSITION --}}
            <div class="relative min-h-screen flex items-center justify-center p-4">

                {{-- MODAL BOX --}}
                <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto"
                    @click.stop>

                    {{-- MODAL HEADER --}}
                    <div class="sticky top-0 bg-white z-10 px-6 py-5 border-b border-gray-100">

                        <div class="flex items-start justify-between">

                            <div>

                                <p class="text-xs font-semibold text-[#849753] uppercase tracking-wide">
                                    Appointment Details
                                </p>

                                <h2 id="appointmentModalTitle" class="text-2xl font-bold text-gray-800 mt-1"
                                    x-text="selectedAppointment ? 'Appointment #' + selectedAppointment.id : 'Appointment #--'">
                                    Appointment #--
                                </h2>

                            </div>

                            <button type="button" @click="closeAppointmentModal()"
                                class="text-gray-400 hover:text-gray-700 text-3xl font-bold leading-none ml-4"
                                aria-label="Close">
                                &times;
                            </button>

                        </div>

                    </div>


                    {{-- MODAL CONTENT --}}
                    <div class="p-6 space-y-6">

                        {{-- APPOINTMENT INFORMATION --}}
                        <div>

                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                                Appointment Information
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Date
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="selectedAppointment?.date ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>


                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Time
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="
                                        selectedAppointment
                                            ? selectedAppointment.time + ' - ' + selectedAppointment.endTime
                                            : 'N/A'
                                    ">
                                        N/A
                                    </p>

                                </div>


                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Therapist
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="selectedAppointment?.therapist ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>


                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Status
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="selectedAppointment?.status ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- SERVICE INFORMATION --}}
                        <div>

                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                                Service Information
                            </h3>

                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Service
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="selectedAppointment?.serviceName ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Description
                                    </p>

                                    <p class="mt-1 text-sm text-gray-600"
                                        x-text="selectedAppointment?.serviceDescription ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>


                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                                    <div>

                                        <p class="text-xs text-gray-400 uppercase font-semibold">
                                            Duration
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-800"
                                            x-text="
                                            selectedAppointment
                                                ? selectedAppointment.serviceDuration + ' mins'
                                                : 'N/A'
                                        ">
                                            N/A
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs text-gray-400 uppercase font-semibold">
                                            Price
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-800"
                                            x-text="
                                            selectedAppointment
                                                ? '₱' + selectedAppointment.servicePrice
                                                : '₱0.00'
                                        ">
                                            ₱0.00
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs text-gray-400 uppercase font-semibold">
                                            Level
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-800 capitalize"
                                            x-text="selectedAppointment?.level ?? 'N/A'">
                                            N/A
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- ADD-ON INFORMATION --}}
                        <div>

                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                                Add-on Information
                            </h3>

                            <div class="bg-gray-50 rounded-xl p-4">

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                                    <div>

                                        <p class="text-xs text-gray-400 uppercase font-semibold">
                                            Add-on
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-800"
                                            x-text="selectedAppointment?.addonName ?? 'None'">
                                            None
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs text-gray-400 uppercase font-semibold">
                                            Duration
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-800"
                                            x-text="selectedAppointment?.addonDuration ?? 'N/A'">
                                            N/A
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs text-gray-400 uppercase font-semibold">
                                            Price
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-800"
                                            x-text="
                                            selectedAppointment
                                                ? '₱' + selectedAppointment.addonPrice
                                                : '₱0.00'
                                        ">
                                            ₱0.00
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- CUSTOMER CONDITION --}}
                        <div>

                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                                Customer Condition
                            </h3>

                            <div class="bg-gray-50 rounded-xl p-4 space-y-4">

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Previous Operations
                                    </p>

                                    <p class="mt-1 text-gray-800 capitalize"
                                        x-text="selectedAppointment?.previousOperations ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Body Problem
                                    </p>

                                    <p class="mt-1 text-gray-700 whitespace-pre-line"
                                        x-text="selectedAppointment?.bodyProblem ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- MODAL FOOTER --}}
                    <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end">

                        <button type="button" @click="closeAppointmentModal()"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            Close
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
