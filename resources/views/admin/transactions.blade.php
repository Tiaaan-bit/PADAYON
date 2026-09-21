@extends('layouts.admin')

@section('title', 'Padayon Massage Center - Transactions')

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
                    Transaction History
                </h2>

                <p class="text-sm text-gray-500 mt-0.5">
                    Review all transaction records and payment information.
                </p>

            </div>


            {{-- ====================================================== --}}
            {{-- SUCCESS MESSAGE --}}
            {{-- ====================================================== --}}

            @if (session('success'))

                <div
                    class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">

                    <svg
                        class="w-4 h-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">

                        <path d="M5 13l4 4L19 7" />

                    </svg>

                    {{ session('success') }}

                </div>

            @endif


            {{-- ====================================================== --}}
            {{-- ERROR MESSAGE --}}
            {{-- ====================================================== --}}

            @if (session('error'))

                <div
                    class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">

                    <svg
                        class="w-4 h-4 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">

                        <path d="M6 18L18 6M6 6l12 12" />

                    </svg>

                    {{ session('error') }}

                </div>

            @endif


            {{-- ====================================================== --}}
            {{-- STATISTICS --}}
            {{-- ====================================================== --}}

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

                {{-- Total Service Price --}}

                <div
                    class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                    <div class="text-center">

                        <p class="text-xl sm:text-2xl font-bold text-gray-800">

                            ₱{{ number_format($totalServicePrice ?? 0, 2) }}

                        </p>

                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Total Service Price
                        </p>

                    </div>

                </div>


                {{-- Total Add-on Price --}}

                <div
                    class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                    <div class="text-center">

                        <p class="text-xl sm:text-2xl font-bold text-gray-800">

                            ₱{{ number_format($totalAddOnPrice ?? 0, 2) }}

                        </p>

                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Total Add-on Price
                        </p>

                    </div>

                </div>


                {{-- Total Amount Paid --}}

                <div
                    class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">

                    <div class="text-center">

                        <p class="text-xl sm:text-2xl font-bold text-gray-800">

                            ₱{{ number_format($totalAmountPaid ?? 0, 2) }}

                        </p>

                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Total Amount Paid
                        </p>

                    </div>

                </div>

            </div>


            {{-- ====================================================== --}}
            {{-- FILTERS --}}
            {{-- ====================================================== --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 mb-6">

                <form
                    method="GET"
                    action="{{ route('admin.transactions') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- Date --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date
                        </label>

                        <input
                            type="date"
                            name="date"
                            value="{{ request('date') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                    </div>


                    {{-- Payment Method --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Payment Method
                        </label>

                        <select
                            name="payment_method"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">
                                All Methods
                            </option>

                            <option
                                value="branch"
                                {{ request('payment_method') === 'branch' ? 'selected' : '' }}>

                                Pay at Counter

                            </option>

                            <option
                                value="gcash"
                                {{ request('payment_method') === 'gcash' ? 'selected' : '' }}>

                                GCash

                            </option>

                        </select>

                    </div>


                    {{-- Amount --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Amount
                        </label>

                        <input
                            type="number"
                            name="amount"
                            step="0.01"
                            value="{{ request('amount') }}"
                            placeholder="0.00"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                    </div>


                    {{-- Buttons --}}

                    <div class="flex items-center gap-3">

                        <button
                            type="submit"
                            class="rounded-lg bg-[#849753] px-6 py-2 text-white hover:bg-[#6F4E37] text-sm w-40">

                            Filter

                        </button>

                        <a
                            href="{{ route('admin.transactions') }}"
                            class="rounded-lg border border-gray-300 px-6 py-2 text-sm text-gray-700 hover:bg-gray-50 w-40 text-center">

                            Reset

                        </a>

                    </div>

                </form>

            </div>


            {{-- ====================================================== --}}
            {{-- TRANSACTION TABLE --}}
            {{-- ====================================================== --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- Table Header --}}

                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">

                    <h3 class="font-semibold text-gray-800">
                        Transactions Table
                    </h3>

                    <span class="text-xs text-gray-400">
                        {{ $transactions->total() }} total
                    </span>

                </div>


                @if ($transactions->isEmpty())

                    {{-- ================================================== --}}
                    {{-- NO TRANSACTIONS --}}
                    {{-- ================================================== --}}

                    <div class="py-16 text-center text-gray-400">

                        <svg
                            class="w-12 h-12 mx-auto mb-3 text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            viewBox="0 0 24 24">

                            <path
                                d="M9 14l2-2 4 4m5 0V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2h11l5-5z" />

                        </svg>

                        <p class="text-sm">
                            No transactions found.
                        </p>

                    </div>

                @else

                    <div class="overflow-x-auto">

                        <table class="w-full text-sm">

                            <thead>

                                <tr class="bg-gray-50 text-left">

                                    {{-- ID --}}

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">

                                        #

                                    </th>


                                    {{-- User --}}

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">

                                        User

                                    </th>


                                    {{-- Service --}}

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">

                                        Service Details

                                    </th>


                                    {{-- Add-on --}}

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">

                                        Add-on Details

                                    </th>


                                    {{-- Therapist --}}

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">

                                        Therapist

                                    </th>


                                    {{-- Appointment --}}

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">

                                        Appointment Time

                                    </th>


                                    {{-- Payment Details --}}

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">

                                        Payment Details

                                    </th>


                                    {{-- Status --}}

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">

                                        Status

                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-gray-100">

                                @forelse ($transactions as $transaction)

                                    {{-- ================================================= --}}
                                    {{-- CLICKABLE TRANSACTION ROW --}}
                                    {{-- ================================================= --}}

                                    <tr
                                        class="transaction-row hover:bg-gray-50 transition align-top cursor-pointer"

                                        onclick="openTransactionModal(this)"

                                        data-id="{{ $transaction->id }}"

                                        data-user-name="{{ $transaction->user->name ?? 'N/A' }}"

                                        data-user-email="{{ $transaction->user->email ?? 'N/A' }}"

                                        data-service-name="{{ $transaction->service->name ?? 'N/A' }}"

                                        data-service-description="{{ $transaction->service->description ?? 'N/A' }}"

                                        data-service-duration="{{ $transaction->service->duration_minutes ?? 'N/A' }}"

                                        data-service-price="{{ number_format($transaction->service->price ?? 0, 2) }}"

                                        data-level="{{ $transaction->level ?? 'N/A' }}"

                                        data-addon-name="{{ $transaction->addOn->name ?? 'None' }}"

                                        data-addon-duration="{{ $transaction->addOn ? $transaction->addOn->duration_minutes . ' mins' : 'No add-on selected' }}"

                                        data-addon-price="{{ number_format($transaction->addons_price ?? 0, 2) }}"

                                        data-therapist="{{ $transaction->therapist->name ?? 'N/A' }}"

                                        data-date="{{ $transaction->appointment_date ? $transaction->appointment_date->format('Y-m-d') : 'N/A' }}"

                                        data-time-start="{{ $transaction->appointment_time ? \Carbon\Carbon::parse($transaction->appointment_time)->format('h:i A') : 'N/A' }}"

                                        data-time-end="{{ $transaction->appointment_end_time ? \Carbon\Carbon::parse($transaction->appointment_end_time)->format('h:i A') : 'N/A' }}"

                                        data-payment-method="{{ $transaction->payment_method === 'branch'
                                            ? 'Pay at Counter'
                                            : ($transaction->payment_method === 'gcash'
                                                ? 'GCash'
                                                : ucfirst($transaction->payment_method ?? 'N/A')) }}"

                                        data-amount-paid="{{ number_format($transaction->amount_paid ?? 0, 2) }}"

                                        {{-- STATUS IS THE ONLY ENUM --}}
                                        data-status="{{ $transaction->status?->value ?? 'N/A' }}">


                                        {{-- ================================================= --}}
                                        {{-- ID --}}
                                        {{-- ================================================= --}}

                                        <td class="px-5 py-4 text-gray-600 whitespace-nowrap">

                                            {{ $transaction->id }}

                                        </td>


                                        {{-- ================================================= --}}
                                        {{-- USER --}}
                                        {{-- ================================================= --}}

                                        <td class="px-5 py-4">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="w-9 h-9 rounded-full bg-[#849753] flex items-center justify-center text-white font-bold text-sm shrink-0">

                                                    {{ strtoupper(substr($transaction->user->name ?? 'N', 0, 1)) }}

                                                </div>

                                                <div>

                                                    <p class="font-semibold text-gray-800 whitespace-nowrap">

                                                        {{ $transaction->user->name ?? 'N/A' }}

                                                    </p>

                                                    <p class="text-xs text-gray-400">

                                                        {{ $transaction->user->email ?? '' }}

                                                    </p>

                                                </div>

                                            </div>

                                        </td>


                                        {{-- ================================================= --}}
                                        {{-- SERVICE --}}
                                        {{-- ================================================= --}}

                                        <td class="px-5 py-4 text-gray-600">

                                            <div class="space-y-1">

                                                <p class="font-semibold text-gray-800">

                                                    {{ $transaction->service->name ?? 'N/A' }}

                                                </p>

                                                <p class="text-xs text-gray-500">

                                                    {{ $transaction->service->description ?? 'N/A' }}

                                                </p>

                                                <p class="text-xs text-gray-500">

                                                    Duration:

                                                    {{ $transaction->service->duration_minutes ?? 'N/A' }}

                                                    mins

                                                </p>

                                                <p class="text-xs font-medium text-gray-500">

                                                    Price:

                                                    ₱{{ number_format($transaction->service->price ?? 0, 2) }}

                                                </p>

                                                <p class="text-xs font-medium text-gray-500 capitalize">

                                                    Level:

                                                    {{ $transaction->level ?? 'N/A' }}

                                                </p>

                                            </div>

                                        </td>


                                        {{-- ================================================= --}}
                                        {{-- ADD-ON --}}
                                        {{-- ================================================= --}}

                                        <td class="px-5 py-4 text-gray-600">

                                            <div class="space-y-1">

                                                <p class="font-semibold text-gray-800">

                                                    {{ $transaction->addOn->name ?? 'None' }}

                                                </p>

                                                <p class="text-xs text-gray-500">

                                                    {{ $transaction->addOn
                                                        ? $transaction->addOn->duration_minutes . ' mins'
                                                        : 'No add-on selected' }}

                                                </p>

                                                <p class="text-xs font-medium text-gray-500">

                                                    Price:

                                                    ₱{{ number_format($transaction->addons_price ?? 0, 2) }}

                                                </p>

                                            </div>

                                        </td>


                                        {{-- ================================================= --}}
                                        {{-- THERAPIST --}}
                                        {{-- ================================================= --}}

                                        <td class="px-5 py-4">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="w-9 h-9 rounded-full bg-[#6F4E37] flex items-center justify-center text-white font-bold text-sm shrink-0">

                                                    {{ strtoupper(substr($transaction->therapist->name ?? 'N', 0, 1)) }}

                                                </div>

                                                <div>

                                                    <p class="font-semibold text-gray-800 whitespace-nowrap">

                                                        {{ $transaction->therapist->name ?? 'N/A' }}

                                                    </p>

                                                </div>

                                            </div>

                                        </td>


                                        {{-- ================================================= --}}
                                        {{-- APPOINTMENT TIME --}}
                                        {{-- ================================================= --}}

                                        <td class="px-5 py-4 text-gray-600 whitespace-nowrap">

                                            <div class="space-y-1">

                                                <p class="text-sm font-semibold text-gray-800">

                                                    {{ $transaction->appointment_date
                                                        ? $transaction->appointment_date->format('Y-m-d')
                                                        : 'N/A' }}

                                                </p>

                                                <p class="text-xs text-gray-500">

                                                    {{ $transaction->appointment_time
                                                        ? \Carbon\Carbon::parse($transaction->appointment_time)->format('h:i A')
                                                        : 'N/A' }}

                                                    -

                                                    {{ $transaction->appointment_end_time
                                                        ? \Carbon\Carbon::parse($transaction->appointment_end_time)->format('h:i A')
                                                        : 'N/A' }}

                                                </p>

                                            </div>

                                        </td>


                                        {{-- ================================================= --}}
                                        {{-- PAYMENT DETAILS --}}
                                        {{-- ================================================= --}}

                                        <td class="px-5 py-4">

                                            <div class="space-y-2">

                                                @if ($transaction->payment_method === 'branch')

                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-semibold rounded-full whitespace-nowrap">

                                                        Pay at Counter

                                                    </span>

                                                @elseif($transaction->payment_method === 'gcash')

                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                        GCash

                                                    </span>

                                                @else

                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-semibold rounded-full whitespace-nowrap">

                                                        {{ ucfirst($transaction->payment_method ?? 'N/A') }}

                                                    </span>

                                                @endif


                                                <p class="text-sm font-bold text-gray-800">

                                                    ₱{{ number_format($transaction->amount_paid ?? 0, 2) }}

                                                </p>

                                            </div>

                                        </td>


                                        {{-- ================================================= --}}
                                        {{-- STATUS --}}
                                        {{-- ================================================= --}}

                                        <td class="px-5 py-4">

                                            @if ($transaction->status === AppointmentStatus::CONFIRMED)

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                                    Confirm

                                                </span>

                                            @elseif($transaction->status === AppointmentStatus::PENDING)

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>

                                                    Pending

                                                </span>

                                            @elseif($transaction->status === AppointmentStatus::REJECTED)

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>

                                                    Rejected

                                                </span>

                                            @elseif($transaction->status === AppointmentStatus::CANCELLED)

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>

                                                    Cancelled

                                                </span>

                                            @elseif($transaction->status === AppointmentStatus::NO_SHOW)

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 border border-orange-200 text-orange-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>

                                                    No Show

                                                </span>

                                            @else

                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    {{ $transaction->status?->value ?? 'N/A' }}

                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="9"
                                            class="px-5 py-10 text-center text-gray-400">

                                            No transactions found.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    {{-- ====================================================== --}}
                    {{-- PAGINATION --}}
                    {{-- ====================================================== --}}

                    @if ($transactions->hasPages())

                        <div class="px-5 py-4 border-t border-gray-100">

                            {{ $transactions->links() }}

                        </div>

                    @endif

                @endif

            </div>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- TRANSACTION DETAILS MODAL --}}
    {{-- ================================================================ --}}

    <div
        id="transactionModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-labelledby="transactionModalTitle">


        {{-- ============================================================ --}}
        {{-- DARK OVERLAY --}}
        {{-- ============================================================ --}}

        <div
            class="fixed inset-0 bg-black/50 backdrop-blur-sm"
            onclick="closeTransactionModal()">
        </div>


        {{-- ============================================================ --}}
        {{-- MODAL POSITION --}}
        {{-- ============================================================ --}}

        <div class="relative min-h-screen flex items-center justify-center p-4">


            {{-- ======================================================== --}}
            {{-- MODAL BOX --}}
            {{-- ======================================================== --}}

            <div
                class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto"
                onclick="event.stopPropagation()">


                {{-- ==================================================== --}}
                {{-- MODAL HEADER --}}
                {{-- ==================================================== --}}

                <div class="sticky top-0 bg-white z-10 px-6 py-5 border-b border-gray-100">

                    <div class="flex items-start justify-between">

                        <div>

                            <p class="text-xs font-semibold text-[#849753] uppercase tracking-wide">
                                Transaction Details
                            </p>

                            <h2
                                id="transactionModalTitle"
                                class="text-2xl font-bold text-gray-800 mt-1">

                                Transaction #--

                            </h2>

                        </div>


                        {{-- Close --}}

                        <button
                            type="button"
                            onclick="closeTransactionModal()"
                            class="text-gray-400 hover:text-gray-700 text-3xl font-bold leading-none ml-4"
                            aria-label="Close">

                            &times;

                        </button>

                    </div>

                </div>


                {{-- ==================================================== --}}
                {{-- MODAL CONTENT --}}
                {{-- ==================================================== --}}

                <div class="p-6 space-y-6">


                    {{-- ================================================= --}}
                    {{-- CUSTOMER INFORMATION --}}
                    {{-- ================================================= --}}

                    <div>

                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                            Customer Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4">

                            <div class="flex items-center gap-4">

                                <div
                                    id="modalUserInitial"
                                    class="w-12 h-12 rounded-full bg-[#849753] flex items-center justify-center text-white font-bold text-lg shrink-0">

                                    N

                                </div>

                                <div>

                                    <p
                                        id="modalUserName"
                                        class="font-bold text-gray-800">

                                        N/A

                                    </p>

                                    <p
                                        id="modalUserEmail"
                                        class="text-sm text-gray-500">

                                        N/A

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- TRANSACTION INFORMATION --}}
                    {{-- ================================================= --}}

                    <div>

                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                            Transaction Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                            {{-- Date --}}

                            <div class="bg-gray-50 rounded-xl p-4">

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Appointment Date
                                </p>

                                <p
                                    id="modalDate"
                                    class="mt-1 font-semibold text-gray-800">

                                    N/A

                                </p>

                            </div>


                            {{-- Time --}}

                            <div class="bg-gray-50 rounded-xl p-4">

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Appointment Time
                                </p>

                                <p
                                    id="modalTime"
                                    class="mt-1 font-semibold text-gray-800">

                                    N/A

                                </p>

                            </div>


                            {{-- Therapist --}}

                            <div class="bg-gray-50 rounded-xl p-4">

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Therapist
                                </p>

                                <p
                                    id="modalTherapist"
                                    class="mt-1 font-semibold text-gray-800">

                                    N/A

                                </p>

                            </div>


                            {{-- Status --}}

                            <div class="bg-gray-50 rounded-xl p-4">

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Status
                                </p>

                                <p
                                    id="modalStatus"
                                    class="mt-1 font-semibold text-gray-800">

                                    N/A

                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- SERVICE INFORMATION --}}
                    {{-- ================================================= --}}

                    <div>

                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                            Service Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4 space-y-3">


                            {{-- Service --}}

                            <div>

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Service
                                </p>

                                <p
                                    id="modalServiceName"
                                    class="mt-1 font-semibold text-gray-800">

                                    N/A

                                </p>

                            </div>


                            {{-- Description --}}

                            <div>

                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Description
                                </p>

                                <p
                                    id="modalServiceDescription"
                                    class="mt-1 text-sm text-gray-600">

                                    N/A

                                </p>

                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">


                                {{-- Duration --}}

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Duration
                                    </p>

                                    <p
                                        id="modalServiceDuration"
                                        class="mt-1 font-semibold text-gray-800">

                                        N/A

                                    </p>

                                </div>


                                {{-- Price --}}

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Price
                                    </p>

                                    <p
                                        id="modalServicePrice"
                                        class="mt-1 font-semibold text-gray-800">

                                        ₱0.00

                                    </p>

                                </div>


                                {{-- Level --}}

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Level
                                    </p>

                                    <p
                                        id="modalLevel"
                                        class="mt-1 font-semibold text-gray-800 capitalize">

                                        N/A

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- ADD-ON INFORMATION --}}
                    {{-- ================================================= --}}

                    <div>

                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                            Add-on Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4">

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">


                                {{-- Add-on --}}

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Add-on
                                    </p>

                                    <p
                                        id="modalAddonName"
                                        class="mt-1 font-semibold text-gray-800">

                                        None

                                    </p>

                                </div>


                                {{-- Duration --}}

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Duration
                                    </p>

                                    <p
                                        id="modalAddonDuration"
                                        class="mt-1 font-semibold text-gray-800">

                                        N/A

                                    </p>

                                </div>


                                {{-- Price --}}

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Price
                                    </p>

                                    <p
                                        id="modalAddonPrice"
                                        class="mt-1 font-semibold text-gray-800">

                                        ₱0.00

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- PAYMENT INFORMATION --}}
                    {{-- ================================================= --}}

                    <div>

                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                            Payment Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                                {{-- Payment Method --}}

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Method
                                    </p>

                                    <p
                                        id="modalPaymentMethod"
                                        class="mt-1 font-semibold text-gray-800">

                                        N/A

                                    </p>

                                </div>


                                {{-- Amount Paid --}}

                                <div>

                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Amount Paid
                                    </p>

                                    <p
                                        id="modalAmountPaid"
                                        class="mt-1 font-semibold text-gray-800">

                                        ₱0.00

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ==================================================== --}}
                {{-- MODAL FOOTER --}}
                {{-- ==================================================== --}}

                <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end">

                    <button
                        type="button"
                        onclick="closeTransactionModal()"
                        class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">

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

        const transactionModal =
            document.getElementById('transactionModal');


        // ============================================================
        // OPEN TRANSACTION MODAL
        // ============================================================

        function openTransactionModal(row) {

            // ========================================================
            // GET DATA FROM ROW
            // ========================================================

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


            const therapist =
                row.dataset.therapist;


            const date =
                row.dataset.date;

            const timeStart =
                row.dataset.timeStart;

            const timeEnd =
                row.dataset.timeEnd;


            const paymentMethod =
                row.dataset.paymentMethod;

            const amountPaid =
                row.dataset.amountPaid;


            /*
             * status comes from:
             *
             * data-status="{{ $transaction->status?->value ?? 'N/A' }}"
             *
             * Therefore JavaScript receives:
             *
             * confirm
             * pending
             * rejected
             * cancelled
             * no show
             */

            const status =
                row.dataset.status;


            // ========================================================
            // MODAL TITLE
            // ========================================================

            document.getElementById('transactionModalTitle').textContent =
                'Transaction #' + id;


            // ========================================================
            // CUSTOMER
            // ========================================================

            document.getElementById('modalUserName').textContent =
                userName;

            document.getElementById('modalUserEmail').textContent =
                userEmail;


            // User initial

            const initial =
                userName !== 'N/A'
                    ? userName.charAt(0).toUpperCase()
                    : 'N';


            document.getElementById('modalUserInitial').textContent =
                initial;


            // ========================================================
            // TRANSACTION INFORMATION
            // ========================================================

            document.getElementById('modalDate').textContent =
                date;

            document.getElementById('modalTime').textContent =
                timeStart + ' - ' + timeEnd;

            document.getElementById('modalTherapist').textContent =
                therapist;


            /*
             * Format enum status for display.
             */

            let formattedStatus = status;

            switch (status) {

                case 'confirm':
                    formattedStatus = 'Confirm';
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


            document.getElementById('modalStatus').textContent =
                formattedStatus;


            // ========================================================
            // SERVICE
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
            // ADD-ON
            // ========================================================

            document.getElementById('modalAddonName').textContent =
                addonName;

            document.getElementById('modalAddonDuration').textContent =
                addonDuration;

            document.getElementById('modalAddonPrice').textContent =
                '₱' + addonPrice;


            // ========================================================
            // PAYMENT
            // ========================================================

            document.getElementById('modalPaymentMethod').textContent =
                paymentMethod;

            document.getElementById('modalAmountPaid').textContent =
                '₱' + amountPaid;


            // ========================================================
            // SHOW MODAL
            // ========================================================

            transactionModal.classList.remove('hidden');


            // Prevent background scrolling

            document.body.classList.add('overflow-hidden');

        }


        // ============================================================
        // CLOSE MODAL
        // ============================================================

        function closeTransactionModal() {

            transactionModal.classList.add('hidden');

            // Allow page scrolling again

            document.body.classList.remove('overflow-hidden');

        }


        // ============================================================
        // ESCAPE KEY
        // ============================================================

        document.addEventListener('keydown', function(event) {

            if (event.key === 'Escape') {

                closeTransactionModal();

            }

        });

    </script>

@endpush