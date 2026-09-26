@extends('layouts.admin')

@section('title', 'Padayon Massage Center - Transactions')

@section('content')

    @php
        use App\Enums\Admin\Appointment\AppointmentStatus;
    @endphp

    <div class="p-4 sm:p-6">

        {{-- PAGE HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#2F2420]">
                Transaction History
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                View and monitor all appointment payment transactions.
            </p>
        </div>

        {{-- STATISTICS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

            {{-- TOTAL SERVICE PRICE --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">
                    Total Service Price
                </p>

                <p class="text-2xl font-bold text-[#6F4E37] mt-1">
                    ₱{{ number_format($totalServicePrice, 2) }}
                </p>
            </div>

            {{-- TOTAL ADD-ON PRICE --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">
                    Total Add-on Price
                </p>

                <p class="text-2xl font-bold text-[#849753] mt-1">
                    ₱{{ number_format($totalAddOnPrice, 2) }}
                </p>
            </div>

            {{-- TOTAL AMOUNT PAID --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">
                    Total Amount Paid
                </p>

                <p class="text-2xl font-bold text-green-600 mt-1">
                    ₱{{ number_format($totalAmountPaid, 2) }}
                </p>
            </div>

        </div>

        {{-- FILTERS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">

            <form method="GET" action="{{ route('admin.transactions') }}"
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

                    <a href="{{ route('admin.transactions') }}"
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

                        <tr>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                ID
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                User
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Service Details
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Add-on Details
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Therapist
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Appointment Time
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Payment Details
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Status
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
                                data-addon-name="{{ $transaction->addOn?->name ?? 'N/A' }}"
                                data-addon-duration="{{ $transaction->addons_duration_minutes ?? ($transaction->addOn?->duration_minutes ?? 'N/A') }}"
                                data-addon-price="{{ number_format((float) $transaction->addons_price, 2) }}"
                                data-therapist="{{ $transaction->therapist?->name ?? 'N/A' }}"
                                data-date="{{ $transaction->appointment_date?->format('M d, Y') ?? 'N/A' }}"
                                data-start-time="{{ $transaction->appointment_time ?? 'N/A' }}"
                                data-end-time="{{ $transaction->appointment_end_time ?? 'N/A' }}"
                                data-payment-method="{{ $paymentMethodLabel }}"
                                data-payment-type="{{ $paymentTypeLabel }}"
                                data-payment-amount="{{ number_format((float) ($transaction->payment_amount ?? 0), 2) }}"
                                data-amount-paid="{{ number_format((float) ($transaction->amount_paid ?? 0), 2) }}"
                                data-payment-status="{{ $paymentStatus }}"
                                data-payment-status-label="{{ $paymentStatusLabel }}"
                                data-payment-reference="{{ $transaction->paymongo_reference_number ?? 'N/A' }}"
                                data-paid-at="{{ $transaction->paid_at?->format('M d, Y h:i A') ?? 'Not paid' }}"
                                data-status="{{ $status }}">

                                {{-- ID --}}
                                <td class="px-4 py-4 text-gray-700 font-medium">
                                    #{{ $transaction->id }}
                                </td>

                                {{-- USER --}}
                                <td class="px-4 py-4">

                                    <div class="font-medium text-gray-800">
                                        {{ $transaction->user?->name ?? 'N/A' }}
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

                                    @if ($transaction->level)
                                        <div class="text-xs text-gray-500 mt-1">
                                            Level: {{ ucfirst($transaction->level) }}
                                        </div>
                                    @endif

                                </td>

                                {{-- ADD-ON --}}
                                <td class="px-4 py-4">

                                    @if ($transaction->addOn)
                                        <div class="font-medium text-gray-800">
                                            {{ $transaction->addOn->name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->addons_duration_minutes ?? ($transaction->addOn->duration_minutes ?? 'N/A') }}
                                            minutes
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            ₱{{ number_format((float) $transaction->addons_price, 2) }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">
                                            No Add-on
                                        </span>
                                    @endif

                                </td>

                                {{-- THERAPIST --}}
                                <td class="px-4 py-4 text-gray-700">
                                    {{ $transaction->therapist?->name ?? 'N/A' }}
                                </td>

                                {{-- APPOINTMENT TIME --}}
                                <td class="px-4 py-4">

                                    <div class="font-medium text-gray-800">
                                        {{ $transaction->appointment_date?->format('M d, Y') ?? 'N/A' }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $transaction->appointment_time ?? 'N/A' }}
                                        -
                                        {{ $transaction->appointment_end_time ?? 'N/A' }}
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

                                    @if ($paymentStatus === 'paid')
                                        <span
                                            class="inline-flex mt-2 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Paid
                                        </span>
                                    @elseif($paymentStatus === 'pending')
                                        <span
                                            class="inline-flex mt-2 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            Pending
                                        </span>
                                    @elseif($paymentStatus === 'failed')
                                        <span
                                            class="inline-flex mt-2 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Failed
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex mt-2 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Unpaid
                                        </span>
                                    @endif

                                </td>

                                {{-- APPOINTMENT STATUS --}}
                                <td class="px-4 py-4">

                                    @switch($status)
                                        @case(AppointmentStatus::CONFIRMED->value)
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                Confirmed
                                            </span>
                                        @break

                                        @case(AppointmentStatus::PENDING->value)
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                Pending
                                            </span>
                                        @break

                                        @case(AppointmentStatus::REJECTED->value)
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                Rejected
                                            </span>
                                        @break

                                        @case(AppointmentStatus::CANCELLED->value)
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                Cancelled
                                            </span>
                                        @break

                                        @case(AppointmentStatus::NO_SHOW->value)
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                                No Show
                                            </span>
                                        @break

                                        @case(AppointmentStatus::FAILED->value)
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                Failed
                                            </span>
                                        @break

                                        @default
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                {{ ucfirst($status) }}
                                            </span>
                                    @endswitch

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
        <div id="transactionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">

            <div class="bg-white w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-xl">

                {{-- MODAL HEADER --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">

                    <div>

                        <h2 class="text-xl font-bold text-[#2F2420]">
                            Transaction Details
                        </h2>

                        <p class="text-sm text-gray-500">
                            Transaction #<span id="modalTransactionId">N/A</span>
                        </p>

                    </div>

                    <button type="button" onclick="closeTransactionModal()"
                        class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition">
                        ✕
                    </button>

                </div>


                {{-- MODAL CONTENT --}}
                <div class="p-6 space-y-6">

                    {{-- CUSTOMER INFORMATION --}}
                    <div>

                        <h3 class="text-sm font-semibold text-[#6F4E37] mb-3">
                            Customer Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <p class="text-xs text-gray-500">
                                    Name
                                </p>

                                <p id="modalUserName" class="font-medium text-gray-800">
                                    N/A
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">
                                    Email
                                </p>

                                <p id="modalUserEmail" class="font-medium text-gray-800">
                                    N/A
                                </p>
                            </div>

                        </div>

                    </div>


                    {{-- TRANSACTION INFORMATION --}}
                    <div>

                        <h3 class="text-sm font-semibold text-[#6F4E37] mb-3">
                            Transaction Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                            <div>
                                <p class="text-xs text-gray-500">
                                    Appointment Date
                                </p>

                                <p id="modalDate" class="font-medium text-gray-800">
                                    N/A
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">
                                    Appointment Time
                                </p>

                                <p id="modalTime" class="font-medium text-gray-800">
                                    N/A
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">
                                    Therapist
                                </p>

                                <p id="modalTherapist" class="font-medium text-gray-800">
                                    N/A
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">
                                    Appointment Status
                                </p>

                                <div id="modalStatus">
                                    N/A
                                </div>
                            </div>

                        </div>

                    </div>


                    {{-- SERVICE INFORMATION --}}
                    <div>

                        <h3 class="text-sm font-semibold text-[#6F4E37] mb-3">
                            Service Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <p class="text-xs text-gray-500">
                                    Service
                                </p>

                                <p id="modalService" class="font-medium text-gray-800">
                                    N/A
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">
                                    Massage Level
                                </p>

                                <p id="modalLevel" class="font-medium text-gray-800">
                                    N/A
                                </p>
                            </div>

                            <div class="sm:col-span-2">

                                <p class="text-xs text-gray-500">
                                    Description
                                </p>

                                <p id="modalServiceDescription" class="text-gray-700">
                                    N/A
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-gray-500">
                                    Duration
                                </p>

                                <p id="modalServiceDuration" class="font-medium text-gray-800">
                                    N/A
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-gray-500">
                                    Service Price
                                </p>

                                <p id="modalServicePrice" class="font-medium text-gray-800">
                                    ₱0.00
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- ADD-ON INFORMATION --}}
                    <div>

                        <h3 class="text-sm font-semibold text-[#6F4E37] mb-3">
                            Add-on Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Add-on
                                </p>

                                <p id="modalAddon" class="font-medium text-gray-800">
                                    N/A
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-gray-500">
                                    Duration
                                </p>

                                <p id="modalAddonDuration" class="font-medium text-gray-800">
                                    N/A
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-gray-500">
                                    Add-on Price
                                </p>

                                <p id="modalAddonPrice" class="font-medium text-gray-800">
                                    ₱0.00
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- PAYMENT INFORMATION --}}
                    <div>

                        <h3 class="text-sm font-semibold text-[#6F4E37] mb-3">
                            Payment Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                            {{-- PAYMENT METHOD --}}
                            <div>

                                <p class="text-xs text-gray-500">
                                    Payment Method
                                </p>

                                <p id="modalPaymentMethod" class="font-medium text-gray-800">
                                    N/A
                                </p>

                            </div>

                            {{-- PAYMENT TYPE --}}
                            <div>

                                <p class="text-xs text-gray-500">
                                    Payment Type
                                </p>

                                <p id="modalPaymentType" class="font-medium text-gray-800">
                                    N/A
                                </p>

                            </div>

                            {{-- PAYMENT AMOUNT --}}
                            <div>

                                <p class="text-xs text-gray-500">
                                    Payment Amount
                                </p>

                                <p id="modalPaymentAmount" class="font-medium text-gray-800">
                                    ₱0.00
                                </p>

                            </div>

                            {{-- AMOUNT PAID --}}
                            <div>

                                <p class="text-xs text-gray-500">
                                    Amount Paid
                                </p>

                                <p id="modalAmountPaid" class="font-medium text-green-600">
                                    ₱0.00
                                </p>

                            </div>

                            {{-- PAYMENT STATUS --}}
                            <div>

                                <p class="text-xs text-gray-500">
                                    Payment Status
                                </p>

                                <div id="modalPaymentStatus">
                                    N/A
                                </div>

                            </div>

                            {{-- PAYMENT REFERENCE --}}
                            <div>

                                <p class="text-xs text-gray-500">
                                    Payment Reference
                                </p>

                                <p id="modalPaymentReference" class="font-medium text-gray-800 break-all">
                                    N/A
                                </p>

                            </div>

                            {{-- PAID AT --}}
                            <div>

                                <p class="text-xs text-gray-500">
                                    Paid At
                                </p>

                                <p id="modalPaidAt" class="font-medium text-gray-800">
                                    Not paid
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- MODAL FOOTER --}}
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">

                    <button type="button" onclick="closeTransactionModal()"
                        class="px-5 py-2.5 rounded-lg bg-[#6F4E37] text-white font-medium hover:bg-[#5c402e] transition">
                        Close
                    </button>

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
                    row.dataset.addonName || 'N/A';

                document.getElementById('modalAddonDuration').textContent =
                    row.dataset.addonDuration &&
                    row.dataset.addonDuration !== 'N/A' ?
                    `${row.dataset.addonDuration} minutes` :
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
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            Confirmed
                        </span>
                    `;

                        break;


                    case 'pending':

                        html = `
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            Pending
                        </span>
                    `;

                        break;


                    case 'rejected':

                        html = `
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            Rejected
                        </span>
                    `;

                        break;


                    case 'cancelled':

                        html = `
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            Cancelled
                        </span>
                    `;

                        break;


                    case 'no show':
                    case 'no_show':

                        html = `
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                            No Show
                        </span>
                    `;

                        break;


                    case 'failed':

                        html = `
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
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
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            Paid
                        </span>
                    `;

                        break;


                    case 'pending':

                        html = `
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            Pending
                        </span>
                    `;

                        break;


                    case 'failed':

                        html = `
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            Failed
                        </span>
                    `;

                        break;


                    default:

                        html = `
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
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
        </script>

    @endsection
