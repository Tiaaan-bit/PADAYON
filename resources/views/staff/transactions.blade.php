@extends('layouts.staff')

@section('title', 'Padayon Massage Center - Transactions')

@section('content')

    @php
        use App\Enums\Admin\Appointment\AppointmentStatus;
    @endphp

    <div class="p-4 sm:p-6">

        {{-- PAGE HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#2F2420]">
                Manage Transactions
            </h1>
        </div>

        {{-- STATISTICS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">


            <div class="bg-white rounded-xl border border-gray-200
            shadow-sm p-4 sm:p-5 flex-1 min-w-35">

                <div class="text-center">

                    <p class="text-xl sm:text-2xl font-bold text-gray-800">
                        ₱{{ number_format($totalServicePrice, 2) }}
                    </p>

                    <p class="text-xs text-gray-400 font-medium mt-0.5">
                        Total Service Price
                    </p>

                </div>

            </div>



            <div class="bg-white rounded-xl border border-gray-200
            shadow-sm p-4 sm:p-5 flex-1 min-w-35">

                <div class="text-center">

                    <p class="text-xl sm:text-2xl font-bold text-gray-800">
                        ₱{{ number_format($totalAddOnPrice, 2) }}
                    </p>

                    <p class="text-xs text-gray-400 font-medium mt-0.5">
                        Total Add-on Price
                    </p>

                </div>

            </div>


            <div class="bg-white rounded-xl border border-gray-200
            shadow-sm p-4 sm:p-5 flex-1 min-w-35">

                <div class="text-center">

                    <p class="text-xl sm:text-2xl font-bold text-gray-800">
                        ₱{{ number_format($totalAmountPaid, 2) }}
                    </p>

                    <p class="text-xs text-gray-400 font-medium mt-0.5">
                        Total Amount Paid
                    </p>

                </div>

            </div>

        </div>

        {{-- FILTERS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">

            <form method="GET" action="{{ route('staff.transactions') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- DATE --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date
                    </label>

                    <input type="date" name="date" value="{{ request('date') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-[#849753] focus:ring-[#849753]">
                </div>

                {{-- PAYMENT METHOD --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Payment Method
                    </label>

                    <select name="payment_method"
                        class="w-full rounded-lg border-gray-300 focus:border-[#849753] focus:ring-[#849753]">
                        <option value="">All Payment Methods</option>

                        <option value="branch" {{ request('payment_method') === 'branch' ? 'selected' : '' }}>
                            Pay at Counter
                        </option>

                        <option value="gcash" {{ request('payment_method') === 'gcash' ? 'selected' : '' }}>
                            GCash
                        </option>
                    </select>
                </div>

                {{-- AMOUNT --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Amount
                    </label>

                    <input type="number" name="amount" step="0.01" min="0" value="{{ request('amount') }}"
                        placeholder="Enter amount"
                        class="w-full rounded-lg border-gray-300 focus:border-[#849753] focus:ring-[#849753]">
                </div>

                {{-- BUTTONS --}}
                <div class="flex items-end gap-2">

                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-lg bg-[#849753] text-white font-medium hover:bg-[#718441] transition">
                        Filter
                    </button>

                    <a href="{{ route('staff.transactions') }}"
                        class="px-4 py-2.5 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition">
                        Reset
                    </a>

                </div>

            </form>

        </div>

        {{-- TRANSACTION TABLE --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr class="bg-gray-50 text-left">

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                ID
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                CUSTOMER
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                SERVICE DETAILS
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                ADDO-ON DETAILS
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                THERAPIST
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                APPOINTMENT TIME
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                PAYMENT DETAILS
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                PAYMENT STATUS
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                ACTION
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($transactions as $transaction)
                            @php
                                $status = $transaction->status?->value ?? 'N/A';

                                $paymentMethod = $transaction->payment_method ?? null;

                                $paymentMethodLabel = match ($paymentMethod) {
                                    'branch' => 'Pay at Counter',
                                    'gcash' => 'GCash',
                                    default => $paymentMethod ? ucfirst($paymentMethod) : 'N/A',
                                };

                                $paymentTypeLabel = match ($transaction->payment_type ?? null) {
                                    'full' => 'Full Payment',
                                    'downpayment' => 'Downpayment',
                                    default => 'N/A',
                                };

                                $paymentStatus = strtolower($transaction->payment_status ?? 'unpaid');

                                $paymentStatusLabel = match ($paymentStatus) {
                                    'paid' => 'Paid',
                                    'pending' => 'Pending',
                                    'failed' => 'Failed',
                                    default => 'Unpaid',
                                };
                            @endphp

                            <tr class="hover:bg-gray-50 cursor-pointer transition" onclick="openTransactionModal(this)"
                                data-id="{{ $transaction->id }}" data-user-name="{{ $transaction->user?->name ?? 'N/A' }}"
                                data-user-email="{{ $transaction->user?->email ?? 'N/A' }}"
                                data-service-name="{{ $transaction->service?->name ?? 'N/A' }}"
                                data-service-description="{{ $transaction->service?->description ?? 'N/A' }}"
                                data-service-duration="{{ $transaction->service_duration_minutes ?? ($transaction->service?->duration_minutes ?? 'N/A') }}"
                                data-service-price="{{ number_format((float) $transaction->service_price, 2) }}"
                                data-level="{{ $transaction->level ?? 'N/A' }}"
                                data-addon-name="{{ $transaction->addOn?->name ?? 'None' }}"
                                data-addon-duration="{{ $transaction->addOn ? $transaction->addOn->duration_minutes . ' mins' : 'No add-on selected' }}"
                                data-addon-price="{{ number_format((float) $transaction->addons_price, 2) }}"
                                data-therapist="{{ $transaction->therapist?->name ?? 'N/A' }}"
                                data-date="{{ $transaction->appointment_date?->format('Y-m-d') ?? 'N/A' }}"
                                data-start-time="{{ $transaction->appointment_time
                                    ? \Carbon\Carbon::parse($transaction->appointment_time)->format('h:i A')
                                    : 'N/A' }}"
                                data-end-time="{{ $transaction->appointment_end_time
                                    ? \Carbon\Carbon::parse($transaction->appointment_end_time)->format('h:i A')
                                    : 'N/A' }}"
                                data-payment-method="{{ $paymentMethodLabel }}"
                                data-payment-type="{{ $paymentTypeLabel }}"
                                data-payment-amount="{{ number_format((float) ($transaction->payment_amount ?? 0), 2) }}"
                                data-amount-paid="{{ number_format((float) ($transaction->amount_paid ?? 0), 2) }}"
                                data-payment-status="{{ $paymentStatus }}"
                                data-payment-status-label="{{ $paymentStatusLabel }}"
                                data-payment-reference="{{ $transaction->paymongo_reference_number ?? 'N/A' }}"
                                data-paid-at="{{ $transaction->paid_at ? $transaction->paid_at->timezone('Asia/Manila')->format('Y-m-d h:i A') : 'N/A' }}"
                                data-status="{{ $status }}">

                                {{-- ID --}}
                                <td class="px-4 py-4 text-gray-700 font-medium">
                                    {{ $transaction->id }}
                                </td>

                                {{-- USER --}}
                                <td class="px-4 py-4">

                                    <div class="font-medium text-gray-800">
                                        {{ $transaction->walkInCustomer?->name ?? ($transaction->user?->name ?? 'N/A') }}

                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $transaction->user?->email ?? 'N/A' }}
                                    </div>

                                </td>

                                {{-- SERVICE --}}
                                <td class="px-4 py-4">

                                    <div class="font-medium text-gray-800">
                                        {{ $transaction->service?->name ?? 'N/A' }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $transaction->service_duration_minutes ?? ($transaction->service?->duration_minutes ?? 'N/A') }}
                                        minutes
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        ₱{{ number_format((float) $transaction->service_price, 2) }}
                                    </div>


                                </td>

                                {{-- ADD-ON --}}
                                <td class="px-4 py-4">


                                    <div class="space-y-1">

                                        <p class="font-semibold text-gray-800">

                                            {{ $transaction->addOn->name ?? 'None' }}

                                        </p>

                                        <p class="text-xs text-gray-500">

                                            {{ $transaction->addOn ? $transaction->addOn->duration_minutes . ' mins' : 'No add-on selected' }}

                                        </p>


                                    </div>

                                </td>

                                {{-- THERAPIST --}}
                                <td class="px-4 py-4 text-gray-700">
                                    {{ $transaction->therapist?->name ?? 'N/A' }}
                                </td>

                                {{-- APPOINTMENT TIME --}}
                                <td class="px-4 py-4">

                                    <div class="font-medium text-gray-800">
                                        {{ $transaction->appointment_date?->format('Y-m-d') ?? 'N/A' }}
                                    </div>

                                    <div class="text-xs text-gray-500">

                                        {{ $transaction->appointment_time
                                            ? \Carbon\Carbon::parse($transaction->appointment_time)->format('h:i A')
                                            : 'N/A' }}
                                        -
                                        {{ $transaction->appointment_end_time
                                            ? \Carbon\Carbon::parse($transaction->appointment_end_time)->format('h:i A')
                                            : 'N/A' }}
                                    </div>

                                </td>


                                {{-- PAYMENT DETAILS --}}
                                <td class="px-4 py-4">
                                    <div class="font-medium text-gray-800">
                                        {{ $paymentMethodLabel }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $paymentTypeLabel }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        Paid:
                                        <span class="font-medium text-gray-700">
                                            ₱{{ number_format((float) ($transaction->amount_paid ?? 0), 2) }}
                                        </span>
                                    </div>

                                    @php
                                        $servicePrice = (float) ($transaction->service_price ?? 0);
                                        $addonPrice = (float) ($transaction->addons_price ?? 0);

                                        $totalAppointmentAmount = $servicePrice + $addonPrice;

                                        $amountPaid = (float) ($transaction->amount_paid ?? 0);

                                        $remainingBalance = max(0, $totalAppointmentAmount - $amountPaid);

                                        $paymentStatus = strtolower((string) ($transaction->payment_status ?? ''));
                                    @endphp

                                    @if (!in_array($paymentStatus, ['unpaid', 'failed']) && $remainingBalance > 0)
                                        <div class="text-xs mt-1 font-semibold text-red-600">
                                            Remaining:
                                            ₱{{ number_format($remainingBalance, 2) }}
                                        </div>
                                    @endif
                                </td>



                                {{-- PAYMENT STATUS --}}
                                <td class="px-4 py-4">

                                    <div class="mt-2">
                                        @if ($paymentStatus === 'paid')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 
                                            text-green-700 text-xs
                                            font-semibold rounded-full
                                        whitespace-nowrap">

                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                                Paid

                                            </span>
                                        @elseif ($paymentStatus === 'pending')
                                            <span
                                                class="inline-flex items-center gap-1.5
                                        px-2.5 py-1 bg-yellow-50
                                        border border-yellow-200
                                        text-yellow-700 text-xs
                                        font-semibold rounded-full
                                        whitespace-nowrap">

                                                <span
                                                    class="w-1.5 h-1.5 rounded-full
                                            bg-yellow-400"></span>

                                                Pending

                                            </span>
                                        @elseif ($paymentStatus === 'failed')
                                            <span
                                                class="inline-flex items-center gap-1.5
                                px-2.5 py-1 bg-gray-100
                                border border-gray-200
                                text-gray-600 text-xs
                                font-semibold rounded-full
                                whitespace-nowrap">

                                                <span
                                                    class="w-1.5 h-1.5 rounded-full
                                    bg-gray-500"></span>

                                                Failed

                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5
                                px-2.5 py-1 bg-gray-100
                                border border-gray-200
                                text-gray-600 text-xs
                                font-semibold rounded-full
                                whitespace-nowrap">

                                                <span
                                                    class="w-1.5 h-1.5 rounded-full
                                    bg-gray-500"></span>

                                                Unpaid

                                            </span>
                                        @endif
                                    </div>

                                </td>


                                <td>

                                    @php
                                        $isConfirmed = $transaction->status === AppointmentStatus::CONFIRMED;

                                        $isCounterPayment =
                                            strtolower((string) ($transaction->payment_method ?? '')) === 'branch';

                                        $isDownpayment =
                                            strtolower((string) ($transaction->payment_type ?? '')) === 'downpayment';

                                        $canCompletePayment =
                                            $isConfirmed &&
                                            $remainingBalance > 0 &&
                                            ($isCounterPayment || $isDownpayment);
                                    @endphp


                                    {{-- PAYMENT ACTION --}}
                                    @if ($canCompletePayment)
                                        <form method="POST"
                                            action="{{ route('staff.transactions.mark-paid', $transaction) }}"
                                            class="mt-3" onclick="event.stopPropagation()"
                                            onsubmit="return confirmPaymentAction('{{ $isDownpayment ? 'the remaining balance' : 'the counter payment' }}')">

                                            @csrf

                                            <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-[#849753] 
                                                text-white text-xs font-semibold hover:bg-[#718441] transition">

                                                @if ($isDownpayment)
                                                    Full Paid
                                                @elseif ($isCounterPayment)
                                                    Paid at Counter
                                                @else
                                                    Mark as Paid
                                                @endif
                                            </button>
                                        </form>
                                    @else
                                        <span> No Action</span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                    No transactions found.
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            @if (method_exists($transactions, 'links'))
                <div class="px-4 py-4 border-t border-gray-200">
                    {{ $transactions->links() }}
                </div>
            @endif

        </div>

    </div>



    {{-- TRANSACTION MODAL --}}
    {{-- TRANSACTION MODAL --}}
    <div id="transactionModal" class="hidden fixed inset-0 z-9999 overflow-y-auto" role="dialog" aria-modal="true"
        aria-labelledby="transactionModalTitle">
        {{-- Dark Overlay --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeTransactionModal()"></div>

        {{-- Modal Position: ONE wrapper only --}}
        <div class="fixed inset-0 z-10 flex items-center justify-center overflow-y-auto p-4">

            {{-- Modal Box --}}
            <div class="relative my-auto w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl"
                onclick="event.stopPropagation()">
                {{-- Modal Header --}}
                <div class="sticky top-0 bg-white z-10 px-6 py-5
                   border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-semibold text-[#849753]
                              uppercase tracking-wide">
                                Transaction Details
                            </p>

                            <h2 id="transactionModalTitle" class="text-2xl font-bold text-gray-800 mt-1">
                                Transaction #<span id="modalTransactionId">N/A</span>
                            </h2>
                        </div>

                        <button type="button" onclick="closeTransactionModal()"
                            class="text-gray-400 hover:text-gray-700
                           text-3xl font-bold leading-none ml-4"
                            aria-label="Close">
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
                           uppercase tracking-wide mb-3">
                            Customer Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center gap-4">



                                <div class="min-w-0">
                                    <p id="modalUserName" class="font-bold text-gray-800 wrap-break-word">
                                        N/A
                                    </p>

                                    <p id="modalUserEmail" class="text-sm text-gray-500 break-all">
                                        N/A
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TRANSACTION INFORMATION --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-gray-800
                           uppercase tracking-wide mb-3">
                            Transaction Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Appointment Date
                                </p>
                                <p id="modalDate" class="mt-1 font-semibold text-gray-800">
                                    N/A
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Appointment Time
                                </p>
                                <p id="modalTime" class="mt-1 font-semibold text-gray-800">
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

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Appointment Status
                                </p>
                                <div id="modalStatus" class="mt-1 font-semibold text-gray-800">
                                    N/A
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- SERVICE INFORMATION --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-gray-800
                           uppercase tracking-wide mb-3">
                            Service Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4 space-y-4">

                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Service
                                </p>
                                <p id="modalService" class="mt-1 font-semibold text-gray-800">
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
                                    <p id="modalAddon" class="mt-1 font-semibold text-gray-800">
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

                    {{-- PAYMENT INFORMATION --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-gray-800
                           uppercase tracking-wide mb-3">
                            Payment Information
                        </h3>

                        <div class="bg-gray-50 rounded-xl p-4">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Method
                                    </p>
                                    <p id="modalPaymentMethod" class="mt-1 font-semibold text-gray-800">
                                        N/A
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Type
                                    </p>
                                    <p id="modalPaymentType" class="mt-1 font-semibold text-gray-800">
                                        N/A
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Amount
                                    </p>
                                    <p id="modalPaymentAmount" class="mt-1 font-semibold text-gray-800">
                                        ₱0.00
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Amount Paid
                                    </p>
                                    <p id="modalAmountPaid" class="mt-1 font-semibold text-gray-800">
                                        ₱0.00
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Payment Status
                                    </p>
                                    <div id="modalPaymentStatus" class="mt-1">
                                        N/A
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">
                                        Paid Date
                                    </p>
                                    <p id="modalPaidAt" class="mt-1 font-semibold text-gray-800">
                                        Not paid
                                    </p>
                                </div>

                            </div>

                            <div class="mt-4">
                                <p class="text-xs text-gray-400 uppercase font-semibold">
                                    Payment Reference
                                </p>
                                <p id="modalPaymentReference" class="mt-1 text-sm font-medium text-gray-800 break-all">
                                    N/A
                                </p>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div
                    class="sticky bottom-0 bg-white border-t border-gray-100
                   px-6 py-4 flex justify-end">
                    <button type="button" onclick="closeTransactionModal()"
                        class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300
                       text-gray-700 font-semibold rounded-lg transition">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>
    </div>



    <script>
        function openTransactionModal(row) {

            const modal = document.getElementById('transactionModal');

            // Transaction
            document.getElementById('modalTransactionId').textContent =
                row.dataset.id || 'N/A';

            // Customer
            document.getElementById('modalUserName').textContent =
                row.dataset.userName || 'N/A';

            document.getElementById('modalUserEmail').textContent =
                row.dataset.userEmail || 'N/A';

            // Appointment
            document.getElementById('modalDate').textContent =
                row.dataset.date || 'N/A';

            document.getElementById('modalTime').textContent =
                `${row.dataset.startTime || 'N/A'} - ${row.dataset.endTime || 'N/A'}`;

            document.getElementById('modalTherapist').textContent =
                row.dataset.therapist || 'N/A';

            // Service
            document.getElementById('modalService').textContent =
                row.dataset.serviceName || 'N/A';

            document.getElementById('modalServiceDescription').textContent =
                row.dataset.serviceDescription || 'N/A';

            document.getElementById('modalServiceDuration').textContent =
                row.dataset.serviceDuration ?
                `${row.dataset.serviceDuration} minutes` :
                'N/A';

            document.getElementById('modalServicePrice').textContent =
                `₱${row.dataset.servicePrice || '0.00'}`;

            document.getElementById('modalLevel').textContent =
                row.dataset.level ?
                capitalize(row.dataset.level) :
                'N/A';

            // Add-on
            document.getElementById('modalAddon').textContent =
                row.dataset.addonName || 'None';

            document.getElementById('modalAddonDuration').textContent =
                row.dataset.addonDuration &&
                row.dataset.addonDuration !== 'N/A' ?
                `${row.dataset.addonDuration} ` :
                'N/A';

            document.getElementById('modalAddonPrice').textContent =
                `₱${row.dataset.addonPrice || '0.00'}`;

            // Payment
            document.getElementById('modalPaymentMethod').textContent =
                row.dataset.paymentMethod || 'N/A';

            document.getElementById('modalPaymentType').textContent =
                row.dataset.paymentType || 'N/A';

            document.getElementById('modalPaymentAmount').textContent =
                `₱${row.dataset.paymentAmount || '0.00'}`;

            document.getElementById('modalAmountPaid').textContent =
                `₱${row.dataset.amountPaid || '0.00'}`;

            document.getElementById('modalPaymentReference').textContent =
                row.dataset.paymentReference || 'N/A';

            document.getElementById('modalPaidAt').textContent =
                row.dataset.paidAt || 'Not paid';

            // Appointment Status
            setAppointmentStatus(
                document.getElementById('modalStatus'),
                row.dataset.status
            );

            // Payment Status
            setPaymentStatus(
                document.getElementById('modalPaymentStatus'),
                row.dataset.paymentStatus
            );

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.body.classList.add('overflow-hidden');
        }


        function closeTransactionModal() {

            const modal = document.getElementById('transactionModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            document.body.classList.remove('overflow-hidden');
        }


        function setAppointmentStatus(element, status) {

            status = (status || '').toLowerCase();

            let html = '';

            switch (status) {

                case 'confirm':
                case 'confirmed':

                    html = `
                        <span>
                            Confirmed
                        </span>
                    `;

                    break;


                case 'pending':

                    html = `
                        <span >
                            Pending
                        </span>
                    `;

                    break;


                case 'rejected':

                    html = `
                        <span >
                            Rejected
                        </span>
                    `;

                    break;


                case 'cancelled':

                    html = `
                        <span>
                            Cancelled
                        </span>
                    `;

                    break;


                case 'no show':
                case 'no_show':

                    html = `
                        <span >
                            No Show
                        </span>
                    `;

                    break;


                case 'failed':

                    html = `
                        <span >
                            Failed
                        </span>
                    `;

                    break;


                default:

                    html = `
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            ${capitalize(status || 'N/A')}
                        </span>
                    `;

                    break;
            }

            element.innerHTML = html;
        }


        function setPaymentStatus(element, status) {

            status = (status || 'unpaid').toLowerCase();

            let html = '';

            switch (status) {

                case 'paid':

                    html = `
                        <span >
                            Paid
                        </span>
                    `;

                    break;


                case 'pending':

                    html = `
                        <span>
                            Pending
                        </span>
                    `;

                    break;


                case 'failed':

                    html = `
                        <span> 
                            Failed
                        </span>
                    `;

                    break;


                default:

                    html = `
                        <span >
                            Unpaid
                        </span>
                    `;

                    break;
            }

            element.innerHTML = html;
        }


        function capitalize(value) {

            if (!value) {
                return 'N/A';
            }

            return value
                .replace(/_/g, ' ')
                .replace(/\b\w/g, character => character.toUpperCase());
        }


        // Close when clicking outside modal
        document.getElementById('transactionModal')
            .addEventListener('click', function(event) {

                if (event.target === this) {
                    closeTransactionModal();
                }

            });


        // Close with ESC
        document.addEventListener('keydown', function(event) {

            if (event.key === 'Escape') {
                closeTransactionModal();
            }

        });


        function confirmPaymentAction(paymentType) {
            return confirm(
                `Are you sure you want to mark ${paymentType} as fully paid?\n\n` +
                `This will change the payment type to "Full Payment" and record the full amount paid.`
            );
        }
    </script>

@endsection
