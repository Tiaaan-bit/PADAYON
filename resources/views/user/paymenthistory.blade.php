@extends('layouts.user')

@section('title', 'Padayon Massage Center - Payment History')

@section('content')

    @php
        use App\Enums\Admin\Appointment\AppointmentStatus;
    @endphp

    <div class="p-4 sm:p-6" x-data="{
        showPaymentModal: false,
        selectedPayment: null,
    
        openPayment(payment) {
            this.selectedPayment = payment;
            this.showPaymentModal = true;
            document.body.classList.add('overflow-hidden');
        },
    
        closePayment() {
            this.showPaymentModal = false;
            this.selectedPayment = null;
            document.body.classList.remove('overflow-hidden');
        }
    }" @keydown.escape.window="closePayment()">

        {{-- ====================================================== --}}
        {{-- PAGE HEADER --}}
        {{-- ====================================================== --}}

        <div class="mb-6">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800">
                Payment Overview
            </h2>
        </div>


        {{-- ====================================================== --}}
        {{-- SUMMARY CARDS --}}
        {{-- ====================================================== --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">

            {{-- Total Service Price --}}
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
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
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
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
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
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
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
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
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center min-w-0">
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
            <div
                class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h3 class="font-semibold text-gray-800">
                    My Payment Records
                </h3>

                <span class="text-xs text-gray-400">
                    {{ $payments->total() }} total
                </span>
            </div>


            @if ($payments->isEmpty())

                {{-- ================================================== --}}
                {{-- NO PAYMENT RECORDS --}}
                {{-- ================================================== --}}

                <div class="py-16 text-center text-gray-400">

                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                        stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M9 14l2-2 4 4m5 0V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2h11l5-5z" />
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
                                    Therapist
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
                                    Payment Details
                                </th>

                                <th
                                    class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                    Payment Status
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach ($payments as $payment)
                                @php
                                    $paymentStatus = $payment->payment_status;
                                    $appointmentStatus = $payment->status;
                                @endphp

                                {{-- ================================================== --}}
                                {{-- CLICKABLE ROW --}}
                                {{-- ================================================== --}}

                                <tr class="payment-row hover:bg-gray-50 transition align-top cursor-pointer"
                                    @click="openPayment({

                                        id: @js($payment->id),

                                        date: @js($payment->appointment_date ? $payment->appointment_date->format('Y-m-d') : 'N/A'),

                                        timeStart: @js($payment->appointment_time ? \Carbon\Carbon::parse($payment->appointment_time)->format('h:i A') : 'N/A'),

                                        timeEnd: @js($payment->appointment_end_time ? \Carbon\Carbon::parse($payment->appointment_end_time)->format('h:i A') : 'N/A'),

                                        therapist: @js($payment->therapist?->name ?? 'N/A'),

                                        serviceName: @js($payment->service?->name ?? 'N/A'),

                                        serviceDescription: @js($payment->service?->description ?? 'N/A'),

                                        serviceDuration: @js($payment->service?->duration_minutes ?? 'N/A'),

                                        servicePrice: @js(number_format($payment->service_price ?? 0, 2)),

                                        level: @js($payment->level ?? 'N/A'),

                                        addonName: @js($payment->addOn?->name ?? 'None'),

                                        addonDuration: @js($payment->addOn ? $payment->addOn->duration_minutes . ' mins' : 'No add-on selected'),

                                        addonPrice: @js(number_format($payment->addons_price ?? 0, 2)),

                                        paymentAmount: @js(number_format($payment->payment_amount ?? 0, 2)),

                                        amountPaid: @js(number_format($payment->amount_paid ?? 0, 2)),

                                        paymentType: @js($payment->payment_type ? ucfirst($payment->payment_type) : 'N/A'),

                                        paymentMethod: @js($payment->payment_method ?? 'N/A'),

                                        paymentStatus: @js($payment->payment_status ?? 'N/A'),

                                        paymentReference: @js($payment->paymongo_reference_number ?? 'N/A'),

                                        paidAt: @js($payment->paid_at ? $payment->paid_at->format('Y-m-d h:i A') : 'Not yet paid'),

                                        appointmentStatus: @js($appointmentStatus?->label() ?? 'N/A')

                                    })">

                                    {{-- ================================================== --}}
                                    {{-- ID --}}
                                    {{-- ================================================== --}}

                                    <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                        {{ $payment->id }}
                                    </td>


                                    {{-- ================================================== --}}
                                    {{-- DATE --}}
                                    {{-- ================================================== --}}

                                    <td class="px-5 py-4 text-gray-600 whitespace-nowrap">

                                        <div class="space-y-1">

                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $payment->appointment_date ? $payment->appointment_date->format('Y-m-d') : 'N/A' }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                {{ $payment->appointment_time ? \Carbon\Carbon::parse($payment->appointment_time)->format('h:i A') : 'N/A' }}

                                                -

                                                {{ $payment->appointment_end_time
                                                    ? \Carbon\Carbon::parse($payment->appointment_end_time)->format('h:i A')
                                                    : 'N/A' }}
                                            </p>

                                        </div>

                                    </td>


                                    {{-- ================================================== --}}
                                    {{-- THERAPIST --}}
                                    {{-- ================================================== --}}

                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3 min-w-0">

                                            <div
                                                class="w-9 h-9 rounded-full bg-[#6F4E37] flex items-center justify-center text-white font-bold text-sm shrink-0">
                                                {{ strtoupper(substr($payment->therapist?->name ?? 'N', 0, 1)) }}
                                            </div>

                                            <div class="min-w-0">

                                                <p class="font-semibold text-gray-800 whitespace-nowrap">
                                                    {{ $payment->therapist?->name ?? 'N/A' }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- ================================================== --}}
                                    {{-- SERVICE DETAILS --}}
                                    {{-- ================================================== --}}

                                    <td class="px-5 py-4 text-gray-600">

                                        <div class="space-y-1 min-w-0">

                                            <p class="font-semibold text-gray-800">
                                                {{ $payment->service?->name ?? 'N/A' }}
                                            </p>

                                            <p class="text-xs text-gray-500 max-w-60">
                                                {{ $payment->service?->description ?? 'N/A' }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                Duration:
                                                {{ $payment->service?->duration_minutes ?? '0' }}
                                                mins
                                            </p>

                                            <p class="text-xs font-medium text-gray-500 capitalize">
                                                Level:
                                                {{ $payment->level ?? 'N/A' }}
                                            </p>

                                        </div>

                                    </td>


                                    {{-- ================================================== --}}
                                    {{-- ADD-ON DETAILS --}}
                                    {{-- ================================================== --}}

                                    <td class="px-5 py-4 text-gray-600">

                                        <div class="space-y-1 min-w-0">

                                            <p class="font-semibold text-gray-800">
                                                {{ $payment->addOn?->name ?? 'None' }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                {{ $payment->addOn ? $payment->addOn->duration_minutes . ' mins' : 'No add-on selected' }}
                                            </p>

                                            <p class="text-xs font-medium text-gray-500">
                                                Price:
                                                ₱{{ number_format($payment->addons_price ?? 0, 2) }}
                                            </p>

                                        </div>

                                    </td>


                                    {{-- ================================================== --}}
                                    {{-- PAYMENT DETAILS --}}
                                    {{-- ================================================== --}}

                                    <td class="px-5 py-4">

                                        <div class="space-y-2">

                                            {{-- Payment Amount --}}
                                            <div>

                                                <p class="text-xs text-gray-400">
                                                    Payment Amount
                                                </p>

                                                <p class="font-semibold text-gray-800">
                                                    ₱{{ number_format($payment->payment_amount ?? 0, 2) }}
                                                </p>

                                            </div>


                                            {{-- Amount Paid --}}
                                            <div>

                                                <p class="text-xs text-gray-400">
                                                    Amount Paid
                                                </p>

                                                <p class="font-semibold text-gray-800">
                                                    ₱{{ number_format($payment->amount_paid ?? 0, 2) }}
                                                </p>

                                            </div>


                                            {{-- Payment Type --}}
                                            <div>

                                                <p class="text-xs text-gray-400">
                                                    Payment Type
                                                </p>

                                                <p class="text-xs font-medium text-gray-600 capitalize">
                                                    {{ $payment->payment_type ? ucfirst($payment->payment_type) : 'N/A' }}
                                                </p>

                                            </div>


                                            {{-- Payment Method --}}
                                            <div>

                                                <p class="text-xs text-gray-400">
                                                    Method
                                                </p>

                                                @if ($payment->payment_method === 'branch')
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                        Pay at Counter
                                                    </span>
                                                @elseif ($payment->payment_method === 'gcash')
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                        GCash
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-semibold rounded-full whitespace-nowrap">
                                                        {{ ucfirst($payment->payment_method ?? 'N/A') }}
                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- ================================================== --}}
                                    {{-- PAYMENT STATUS --}}
                                    {{-- ================================================== --}}

                                    <td class="px-5 py-4">

                                        @if ($paymentStatus === 'paid')
                                            {{-- PAID --}}
                                            <div class="space-y-1">

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                    Paid
                                                </span>

                                                <p class="text-xs text-gray-500">
                                                    Payment confirmed
                                                </p>

                                            </div>
                                        @elseif ($paymentStatus === 'pending')
                                            {{-- PENDING --}}
                                            <div class="space-y-2">

                                                <div class="space-y-1">

                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                                                        Payment Pending
                                                    </span>

                                                    <p class="text-xs text-gray-500">
                                                        Your payment is waiting for confirmation.
                                                    </p>

                                                </div>

                                                {{-- PAY AGAIN --}}
                                                @if ($payment->payment_method === 'gcash')
                                                    <form
                                                        action="{{ route('user.appointments.payment.retry', $payment->id) }}"
                                                        method="POST" @click.stop>
                                                        @csrf

                                                        <button type="submit"
                                                            class="rounded-lg bg-[#849753] px-4 py-2 text-white hover:bg-[#6F4E37] text-xs font-medium transition">
                                                            Pay Again
                                                        </button>

                                                    </form>
                                                @endif

                                            </div>
                                        @elseif ($paymentStatus === 'failed')
                                            {{-- FAILED --}}
                                            <div class="space-y-2">

                                                <div class="space-y-1">

                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                        Payment Failed
                                                    </span>

                                                    <p class="text-xs text-gray-500">
                                                        Payment was not completed.
                                                    </p>

                                                </div>

                                                {{-- PAY AGAIN --}}
                                                @if ($payment->payment_method === 'gcash')
                                                    <form
                                                        action="{{ route('user.appointments.payment.retry', $payment->id) }}"
                                                        method="POST" @click.stop>
                                                        @csrf

                                                        <button type="submit"
                                                            class="rounded-lg bg-[#849753] px-4 py-2 text-white hover:bg-[#6F4E37] text-xs font-medium transition">
                                                            Pay Again
                                                        </button>

                                                    </form>
                                                @endif

                                            </div>
                                        @elseif ($payment->payment_method === 'branch')
                                            {{-- BRANCH --}}
                                            <div class="space-y-1">

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-full whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    Pay at Counter
                                                </span>

                                                <p class="text-xs text-gray-500">
                                                    Payment will be made at the branch.
                                                </p>

                                            </div>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full whitespace-nowrap">
                                                {{ $paymentStatus ?: 'N/A' }}
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

                @if ($payments->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $payments->links() }}
                    </div>
                @endif

            @endif

        </div>


        {{-- ================================================================ --}}
        {{-- PAYMENT DETAILS MODAL --}}
        {{-- ================================================================ --}}

        <div x-show="showPaymentModal" x-cloak x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle" @click.self="closePayment()">

            {{-- DARK OVERLAY --}}
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closePayment()"></div>


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
                                    Payment Details
                                </p>

                                <h2 id="paymentModalTitle" class="text-2xl font-bold text-gray-800 mt-1"
                                    x-text="selectedPayment ? 'Payment #' + selectedPayment.id : 'Payment #--'">
                                    Payment #--
                                </h2>

                            </div>


                            <button type="button" @click="closePayment()"
                                class="text-gray-400 hover:text-gray-700 text-3xl font-bold leading-none ml-4"
                                aria-label="Close">
                                &times;
                            </button>

                        </div>

                    </div>


                    {{-- MODAL CONTENT --}}
                    <div class="p-6 space-y-6">


                        {{-- ================================================== --}}
                        {{-- PAYMENT INFORMATION --}}
                        {{-- ================================================== --}}

                        <div>

                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                                Payment Information
                            </h3>


                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                                {{-- Date --}}
                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Appointment Date
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800" x-text="selectedPayment?.date ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>


                                {{-- Time --}}
                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Appointment Time
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="
                                            selectedPayment
                                                ? selectedPayment.timeStart + ' - ' + selectedPayment.timeEnd
                                                : 'N/A'
                                        ">
                                        N/A
                                    </p>

                                </div>


                                {{-- Payment Amount --}}
                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Amount
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="
                                            selectedPayment
                                                ? '₱' + selectedPayment.paymentAmount
                                                : '₱0.00'
                                        ">
                                        ₱0.00
                                    </p>

                                </div>


                                {{-- Amount Paid --}}
                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Amount Paid
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="
                                            selectedPayment
                                                ? '₱' + selectedPayment.amountPaid
                                                : '₱0.00'
                                        ">
                                        ₱0.00
                                    </p>

                                </div>


                                {{-- Payment Type --}}
                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Type
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800 capitalize"
                                        x-text="selectedPayment?.paymentType ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>


                                {{-- Payment Method --}}
                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Method
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="
                                            selectedPayment
                                                ? (
                                                    selectedPayment.paymentMethod === 'branch'
                                                        ? 'Pay at Counter'
                                                        : selectedPayment.paymentMethod === 'gcash'
                                                            ? 'GCash'
                                                            : selectedPayment.paymentMethod
                                                )
                                                : 'N/A'
                                        ">
                                        N/A
                                    </p>

                                </div>


                                {{-- Payment Status --}}
                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Status
                                    </p>

                                    <div class="mt-1">

                                        <template x-if="selectedPayment?.paymentStatus === 'paid'">

                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                Paid
                                            </span>

                                        </template>


                                        <template x-if="selectedPayment?.paymentStatus === 'pending'">

                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                                                Payment Pending
                                            </span>

                                        </template>


                                        <template x-if="selectedPayment?.paymentStatus === 'failed'">

                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Payment Failed
                                            </span>

                                        </template>


                                        <template
                                            x-if="
                                            selectedPayment &&
                                            !['paid', 'pending', 'failed'].includes(selectedPayment.paymentStatus)
                                        ">

                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full"
                                                x-text="selectedPayment.paymentStatus || 'N/A'">
                                            </span>

                                        </template>

                                    </div>

                                </div>


                                {{-- Paid Date --}}
                                <div class="bg-gray-50 rounded-xl p-4">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Paid Date
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="selectedPayment?.paidAt ?? 'Not yet paid'">
                                        Not yet paid
                                    </p>

                                </div>


                                {{-- Payment Reference --}}
                                <div class="bg-gray-50 rounded-xl p-4 sm:col-span-2"
                                    x-show="
                                        selectedPayment &&
                                        selectedPayment.paymentReference !== 'N/A'
                                    ">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Reference
                                    </p>

                                    <p class="mt-1 font-mono text-sm font-semibold text-gray-800 break-all"
                                        x-text="selectedPayment?.paymentReference ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>


                                {{-- Therapist --}}
                                <div class="bg-gray-50 rounded-xl p-4 sm:col-span-2">

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Therapist
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800"
                                        x-text="selectedPayment?.therapist ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- ================================================== --}}
                        {{-- SERVICE INFORMATION --}}
                        {{-- ================================================== --}}

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
                                        x-text="selectedPayment?.serviceName ?? 'N/A'">
                                        N/A
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Description
                                    </p>

                                    <p class="mt-1 text-sm text-gray-600"
                                        x-text="selectedPayment?.serviceDescription ?? 'N/A'">
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
                                                selectedPayment
                                                    ? selectedPayment.serviceDuration + ' mins'
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
                                                selectedPayment
                                                    ? '₱' + selectedPayment.servicePrice
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
                                            x-text="selectedPayment?.level ?? 'N/A'">
                                            N/A
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- ================================================== --}}
                        {{-- ADD-ON INFORMATION --}}
                        {{-- ================================================== --}}

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
                                            x-text="selectedPayment?.addonName ?? 'None'">
                                            None
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs text-gray-400 uppercase font-semibold">
                                            Duration
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-800"
                                            x-text="selectedPayment?.addonDuration ?? 'N/A'">
                                            N/A
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs text-gray-400 uppercase font-semibold">
                                            Price
                                        </p>

                                        <p class="mt-1 font-semibold text-gray-800"
                                            x-text="
                                                selectedPayment
                                                    ? '₱' + selectedPayment.addonPrice
                                                    : '₱0.00'
                                            ">
                                            ₱0.00
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================== --}}
                    {{-- MODAL FOOTER --}}
                    {{-- ================================================== --}}

                    <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end">

                        <button type="button" @click="closePayment()"
                            class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                            Close
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
