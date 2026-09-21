```blade
@extends('layouts.user')

@section('title', 'Padayon Massage Center - Payment History')

@section('content')

@php
    use App\Enums\Admin\Appointment\AppointmentStatus;
@endphp

<div class="p-4 sm:p-6">

    {{-- ====================================================== --}}
    {{-- PAGE HEADER --}}
    {{-- ====================================================== --}}

    <div class="mb-6">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">
            My Payment Overview
        </h2>

        <p class="text-sm text-gray-500 mt-0.5">
            Track your payments.
        </p>
    </div>


    {{-- ====================================================== --}}
    {{-- SUMMARY CARDS --}}
    {{-- ====================================================== --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">

        {{-- Total Service Price --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
            <div class="text-center min-w-0">

                <p class="text-lg sm:text-2xl font-bold text-gray-800">
                    ₱{{ number_format($totalServicePrice ?? 0, 2) }}
                </p>

                <p class="text-xs text-gray-400 font-medium mt-0.5">
                    Total Service Price
                </p>

            </div>
        </div>


        {{-- Total Add-on Price --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
            <div class="text-center min-w-0">

                <p class="text-lg sm:text-2xl font-bold text-gray-800">
                    ₱{{ number_format($totalAddOnPrice ?? 0, 2) }}
                </p>

                <p class="text-xs text-gray-400 font-medium mt-0.5">
                    Total Add-on Price
                </p>

            </div>
        </div>


        {{-- Total Amount Paid --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
            <div class="text-center min-w-0">

                <p class="text-lg sm:text-2xl font-bold text-gray-800">
                    ₱{{ number_format($totalAmountPaid ?? 0, 2) }}
                </p>

                <p class="text-xs text-gray-400 font-medium mt-0.5">
                    Total Amount Paid
                </p>

            </div>
        </div>


        {{-- Pending Payments --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
            <div class="text-center min-w-0">

                <p class="text-lg sm:text-2xl font-bold text-gray-800">
                    {{ $totalPending ?? 0 }}
                </p>

                <p class="text-xs text-gray-400 font-medium mt-0.5">
                    Pending Payments
                </p>

            </div>
        </div>


        {{-- Confirmed --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
            <div class="text-center min-w-0">

                <p class="text-lg sm:text-2xl font-bold text-gray-800">
                    {{ $totalConfirmed ?? 0 }}
                </p>

                <p class="text-xs text-gray-400 font-medium mt-0.5">
                    Confirmed
                </p>

            </div>
        </div>

    </div>


    {{-- ====================================================== --}}
    {{-- PAYMENT RECORDS --}}
    {{-- ====================================================== --}}

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Table Header --}}
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

            <h3 class="font-semibold text-gray-800">
                My Payment Records
            </h3>

            <span class="text-xs text-gray-400">
                {{ $payments->total() }} total
            </span>

        </div>


        @if($payments->isEmpty())

            {{-- ================================================== --}}
            {{-- NO PAYMENT RECORDS --}}
            {{-- ================================================== --}}

            <div class="py-16 text-center text-gray-400">

                <svg
                    class="w-12 h-12 mx-auto mb-3 text-gray-300"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    viewBox="0 0 24 24"
                >
                    <path d="M9 14l2-2 4 4m5 0V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2h11l5-5z"/>
                </svg>

                <p class="text-sm">
                    No payment history found.
                </p>

            </div>

        @else

            {{-- ================================================== --}}
            {{-- TABLE --}}
            {{-- ================================================== --}}

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50 text-left">

                            <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                #
                            </th>

                            <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                Date
                            </th>

                            <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                Therapist
                            </th>

                            <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                Service Details
                            </th>

                            <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                Add-on Details
                            </th>

                            <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                Payment Details
                            </th>

                            <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                Status
                            </th>

                        </tr>
                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($payments as $payment)

                            {{-- ================================================== --}}
                            {{-- CLICKABLE ROW --}}
                            {{-- ================================================== --}}

                            <tr
                                class="payment-row hover:bg-gray-50 transition align-top cursor-pointer"

                                onclick="openPaymentModal(this)"

                                data-id="{{ $payment->id }}"

                                data-date="{{ $payment->appointment_date
                                    ? $payment->appointment_date->format('Y-m-d')
                                    : 'N/A'
                                }}"

                                data-time-start="{{ $payment->appointment_time
                                    ? \Carbon\Carbon::parse($payment->appointment_time)->format('h:i A')
                                    : 'N/A'
                                }}"

                                data-time-end="{{ $payment->appointment_end_time
                                    ? \Carbon\Carbon::parse($payment->appointment_end_time)->format('h:i A')
                                    : 'N/A'
                                }}"

                                data-therapist="{{ $payment->therapist->name ?? 'N/A' }}"

                                data-service-name="{{ $payment->service->name ?? 'N/A' }}"

                                data-service-description="{{ $payment->service->description ?? 'N/A' }}"

                                data-service-duration="{{ $payment->service->duration_minutes ?? 'N/A' }}"

                                data-service-price="{{ number_format($payment->service_price ?? 0, 2) }}"

                                data-level="{{ $payment->level ?? 'N/A' }}"

                                data-addon-name="{{ $payment->addOn->name ?? 'None' }}"

                                data-addon-duration="{{ $payment->addOn
                                    ? $payment->addOn->duration_minutes . ' mins'
                                    : 'No add-on selected'
                                }}"

                                data-addon-price="{{ number_format($payment->addons_price ?? 0, 2) }}"

                                data-amount-paid="{{ number_format($payment->amount_paid ?? 0, 2) }}"

                                data-payment-method="{{ $payment->payment_method ?? 'N/A' }}"

                                data-status="{{ $payment->status?->label() ?? 'N/A' }}"
                            >

                                {{-- ID --}}
                                <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                    {{ $payment->id }}
                                </td>


                                {{-- DATE --}}
                                <td class="px-5 py-4 text-gray-600 whitespace-nowrap">

                                    <div class="space-y-1">

                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $payment->appointment_date
                                                ? $payment->appointment_date->format('Y-m-d')
                                                : 'N/A'
                                            }}
                                        </p>

                                        <p class="text-xs text-gray-500">

                                            {{ $payment->appointment_time
                                                ? \Carbon\Carbon::parse($payment->appointment_time)->format('h:i A')
                                                : 'N/A'
                                            }}

                                            -

                                            {{ $payment->appointment_end_time
                                                ? \Carbon\Carbon::parse($payment->appointment_end_time)->format('h:i A')
                                                : 'N/A'
                                            }}

                                        </p>

                                    </div>

                                </td>


                                {{-- THERAPIST --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3 min-w-0">

                                        <div class="w-9 h-9 rounded-full bg-[#6F4E37] flex items-center justify-center text-white font-bold text-sm shrink-0">
                                            {{ strtoupper(substr($payment->therapist->name ?? 'N', 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">

                                            <p class="font-semibold text-gray-800 whitespace-nowrap">
                                                {{ $payment->therapist->name ?? 'N/A' }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- SERVICE DETAILS --}}
                                <td class="px-5 py-4 text-gray-600">

                                    <div class="space-y-1 min-w-0">

                                        <p class="font-semibold text-gray-800">
                                            {{ $payment->service->name ?? 'N/A' }}
                                        </p>

                                        <p class="text-xs text-gray-500 max-w-60">
                                            {{ $payment->service->description ?? 'N/A' }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            Duration:
                                            {{ $payment->service->duration_minutes ?? '0' }}
                                            mins
                                        </p>

                                        <p class="text-xs font-medium text-gray-500 capitalize">
                                            Level:
                                            {{ $payment->level ?? 'N/A' }}
                                        </p>

                                    </div>

                                </td>


                                {{-- ADD-ON DETAILS --}}
                                <td class="px-5 py-4 text-gray-600">

                                    <div class="space-y-1 min-w-0">

                                        <p class="font-semibold text-gray-800">
                                            {{ $payment->addOn->name ?? 'None' }}
                                        </p>

                                        <p class="text-xs text-gray-500">

                                            {{ $payment->addOn
                                                ? $payment->addOn->duration_minutes . ' mins'
                                                : 'No add-on selected'
                                            }}

                                        </p>

                                        <p class="text-xs font-medium text-gray-500">
                                            Price:
                                            ₱{{ number_format($payment->addons_price ?? 0, 2) }}
                                        </p>

                                    </div>

                                </td>


                                {{-- PAYMENT DETAILS --}}
                                <td class="px-5 py-4">

                                    <div class="space-y-2">

                                        <p class="font-semibold text-gray-800">
                                            ₱{{ number_format($payment->amount_paid ?? 0, 2) }}
                                        </p>


                                        @if($payment->payment_method === 'branch')

                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-semibold rounded-full whitespace-nowrap">
                                                Pay at Counter
                                            </span>

                                        @elseif($payment->payment_method === 'gcash')

                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                GCash
                                            </span>

                                        @else

                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-semibold rounded-full whitespace-nowrap">
                                                {{ ucfirst($payment->payment_method ?? 'N/A') }}
                                            </span>

                                        @endif

                                    </div>

                                </td>


                                {{-- STATUS --}}
                                <td class="px-5 py-4">

                                    @if($payment->status === AppointmentStatus::CONFIRMED)

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                            Confirmed

                                        </span>

                                    @elseif($payment->status === AppointmentStatus::PENDING)

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>

                                            Pending

                                        </span>

                                    @elseif($payment->status === AppointmentStatus::REJECTED)

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>

                                            Rejected

                                        </span>

                                    @elseif($payment->status === AppointmentStatus::CANCELLED)

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-semibold rounded-full whitespace-nowrap">

                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>

                                            Cancelled

                                        </span>

                                    @elseif($payment->status === AppointmentStatus::NO_SHOW)

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 border border-orange-200 text-orange-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>

                                            No Show

                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full whitespace-nowrap">
                                            {{ $payment->status?->label() ?? 'N/A' }}
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- ================================================== --}}
            {{-- PAGINATION --}}
            {{-- ================================================== --}}

            @if($payments->hasPages())

                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $payments->links() }}
                </div>

            @endif

        @endif

    </div>

</div>


{{-- ================================================================ --}}
{{-- PAYMENT DETAILS MODAL --}}
{{-- ================================================================ --}}

<div
    id="paymentModal"
    class="hidden fixed inset-0 z-50 overflow-y-auto"
    role="dialog"
    aria-modal="true"
    aria-labelledby="paymentModalTitle"
>

    {{-- DARK OVERLAY --}}
    <div
        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
        onclick="closePaymentModal()"
    ></div>


    {{-- MODAL POSITION --}}
    <div class="relative min-h-screen flex items-center justify-center p-4">

        {{-- MODAL BOX --}}
        <div
            class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto"
            onclick="event.stopPropagation()"
        >

            {{-- MODAL HEADER --}}
            <div class="sticky top-0 bg-white z-10 px-6 py-5 border-b border-gray-100">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-xs font-semibold text-[#849753] uppercase tracking-wide">
                            Payment Details
                        </p>

                        <h2
                            id="paymentModalTitle"
                            class="text-2xl font-bold text-gray-800 mt-1"
                        >
                            Payment #--
                        </h2>

                    </div>


                    <button
                        type="button"
                        onclick="closePaymentModal()"
                        class="text-gray-400 hover:text-gray-700 text-3xl font-bold leading-none ml-4"
                        aria-label="Close"
                    >
                        &times;
                    </button>

                </div>

            </div>


            {{-- MODAL CONTENT --}}
            <div class="p-6 space-y-6">

                {{-- PAYMENT INFORMATION --}}
                <div>

                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                        Payment Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="bg-gray-50 rounded-xl p-4">

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Date
                            </p>

                            <p id="modalDate" class="mt-1 font-semibold text-gray-800">
                                N/A
                            </p>

                        </div>


                        <div class="bg-gray-50 rounded-xl p-4">

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Time
                            </p>

                            <p id="modalTime" class="mt-1 font-semibold text-gray-800">
                                N/A
                            </p>

                        </div>


                        <div class="bg-gray-50 rounded-xl p-4">

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Amount Paid
                            </p>

                            <p id="modalAmountPaid" class="mt-1 font-semibold text-gray-800">
                                ₱0.00
                            </p>

                        </div>


                        <div class="bg-gray-50 rounded-xl p-4">

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Payment Method
                            </p>

                            <p id="modalPaymentMethod" class="mt-1 font-semibold text-gray-800">
                                N/A
                            </p>

                        </div>


                        <div class="bg-gray-50 rounded-xl p-4">

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Status
                            </p>

                            <p id="modalStatus" class="mt-1 font-semibold text-gray-800">
                                N/A
                            </p>

                        </div>


                        <div class="bg-gray-50 rounded-xl p-4">

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Therapist
                            </p>

                            <p id="modalTherapist" class="mt-1 font-semibold text-gray-800">
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

                            <p id="modalServiceName" class="mt-1 font-semibold text-gray-800">
                                N/A
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-gray-400 uppercase font-semibold">
                                Description
                            </p>

                            <p id="modalServiceDescription" class="mt-1 text-sm text-gray-600">
                                N/A
                            </p>

                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                            <div>

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Duration
                                </p>

                                <p id="modalServiceDuration" class="mt-1 font-semibold text-gray-800">
                                    N/A
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Price
                                </p>

                                <p id="modalServicePrice" class="mt-1 font-semibold text-gray-800">
                                    ₱0.00
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Level
                                </p>

                                <p id="modalLevel" class="mt-1 font-semibold text-gray-800 capitalize">
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

                                <p id="modalAddonName" class="mt-1 font-semibold text-gray-800">
                                    None
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Duration
                                </p>

                                <p id="modalAddonDuration" class="mt-1 font-semibold text-gray-800">
                                    N/A
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Price
                                </p>

                                <p id="modalAddonPrice" class="mt-1 font-semibold text-gray-800">
                                    ₱0.00
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- MODAL FOOTER --}}
            <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end">

                <button
                    type="button"
                    onclick="closePaymentModal()"
                    class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>

@endsection


{{-- ================================================================ --}}
{{-- PAGE JAVASCRIPT --}}
{{-- ================================================================ --}}

@push('scripts')

<script>

    const paymentModal = document.getElementById('paymentModal');


    // ========================================================
    // OPEN PAYMENT MODAL
    // ========================================================

    function openPaymentModal(row) {

        const id = row.dataset.id;
        const date = row.dataset.date;
        const timeStart = row.dataset.timeStart;
        const timeEnd = row.dataset.timeEnd;
        const therapist = row.dataset.therapist;
        const serviceName = row.dataset.serviceName;
        const serviceDescription = row.dataset.serviceDescription;
        const serviceDuration = row.dataset.serviceDuration;
        const servicePrice = row.dataset.servicePrice;
        const level = row.dataset.level;
        const addonName = row.dataset.addonName;
        const addonDuration = row.dataset.addonDuration;
        const addonPrice = row.dataset.addonPrice;
        const amountPaid = row.dataset.amountPaid;
        const paymentMethod = row.dataset.paymentMethod;
        const status = row.dataset.status;


        // ========================================================
        // MODAL TITLE
        // ========================================================

        document.getElementById('paymentModalTitle').textContent =
            'Payment #' + id;


        // ========================================================
        // PAYMENT INFORMATION
        // ========================================================

        document.getElementById('modalDate').textContent =
            date;

        document.getElementById('modalTime').textContent =
            timeStart + ' - ' + timeEnd;

        document.getElementById('modalAmountPaid').textContent =
            '₱' + amountPaid;


        // Format payment method

        let formattedPaymentMethod = paymentMethod;

        if (paymentMethod === 'branch') {

            formattedPaymentMethod = 'Pay at Counter';

        } else if (paymentMethod === 'gcash') {

            formattedPaymentMethod = 'GCash';

        } else if (!paymentMethod || paymentMethod === 'N/A') {

            formattedPaymentMethod = 'N/A';

        } else {

            formattedPaymentMethod =
                paymentMethod.charAt(0).toUpperCase() +
                paymentMethod.slice(1);

        }


        document.getElementById('modalPaymentMethod').textContent =
            formattedPaymentMethod;

        document.getElementById('modalStatus').textContent =
            status;

        document.getElementById('modalTherapist').textContent =
            therapist;


        // ========================================================
        // SERVICE INFORMATION
        // ========================================================

        document.getElementById('modalServiceName').textContent =
            serviceName;

        document.getElementById('modalServiceDescription').textContent =
            serviceDescription;

        document.getElementById('modalServiceDuration').textContent =
            serviceDuration + ' mins';

        document.getElementById('modalServicePrice').textContent =
            '₱' + servicePrice;

        document.getElementById('modalLevel').textContent =
            level;


        // ========================================================
        // ADD-ON INFORMATION
        // ========================================================

        document.getElementById('modalAddonName').textContent =
            addonName;

        document.getElementById('modalAddonDuration').textContent =
            addonDuration;

        document.getElementById('modalAddonPrice').textContent =
            '₱' + addonPrice;


        // ========================================================
        // SHOW MODAL
        // ========================================================

        paymentModal.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');
    }


    // ============================================================
    // CLOSE PAYMENT MODAL
    // ============================================================

    function closePaymentModal() {

        paymentModal.classList.add('hidden');

        document.body.classList.remove('overflow-hidden');
    }


    // ============================================================
    // ESCAPE KEY
    // ============================================================

    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {

            closePaymentModal();

        }

    });

</script>

@endpush

