@extends('layouts.admin')

@section('title', 'Padayon Massage Center - Customers')

@section('content')

    @php
        use App\Enums\Admin\User\UserStatus;
    @endphp

    <div class="min-h-screen flex flex-col" x-data="customerModal()" @keydown.escape.window="closeModal()">

        <div class="flex-1 p-4 sm:p-6">

            {{-- Page Header --}}
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800">
                    Manage Customers
                </h2>
            </div>

            {{-- Statistics --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

                {{-- Total Users --}}
                <div
                    class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $stats['total'] }}
                        </p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Total Users
                        </p>
                    </div>
                </div>

                {{-- Active --}}
                <div
                    class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $stats['active'] }}
                        </p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Active
                        </p>
                    </div>
                </div>

                {{-- Pending --}}
                <div
                    class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $stats['pending'] }}
                        </p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Pending
                        </p>
                    </div>
                </div>

                {{-- Online Now --}}
                <div
                    class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">
                            {{ $stats['online'] }}
                        </p>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">
                            Online Now
                        </p>
                    </div>
                </div>

            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 mb-6">

                <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap items-end gap-3">

                    {{-- Search --}}
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Search
                        </label>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name, email, or phone"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Status --}}
                    <div class="w-full sm:w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status
                        </label>

                        <select name="status"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">All</option>

                            <option value="{{ UserStatus::ACTIVE->value }}"
                                {{ request('status') === UserStatus::ACTIVE->value ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="{{ UserStatus::PENDING->value }}"
                                {{ request('status') === UserStatus::PENDING->value ? 'selected' : '' }}>
                                Pending
                            </option>

                        </select>
                    </div>

                    {{-- Joined Date --}}
                    <div class="w-full sm:w-52">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Joined Date
                        </label>

                        <input type="date" name="joined_date" value="{{ request('joined_date') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-2">

                        <button type="submit"
                            class="rounded-lg bg-[#849753] px-4 py-2 text-white hover:bg-[#6F4E37] text-sm">
                            Filter
                        </button>

                        <a href="{{ route('admin.users') }}"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>

                    </div>

                </form>

            </div>

            {{-- Users Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- Empty State --}}
                @if ($users->isEmpty())

                    <div class="py-16 text-center text-gray-400">

                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24">

                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />

                        </svg>

                        <p class="text-sm">
                            No users registered yet.
                        </p>

                    </div>
                @else
                    <div class="overflow-x-auto">

                        <table class="w-full text-sm">

                            {{-- Table Head --}}
                            <thead>
                                <tr class="bg-gray-50 text-left">

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        User
                                    </th>

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Phone
                                    </th>

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Status
                                    </th>

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Online
                                    </th>

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Last Seen
                                    </th>

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Joined
                                    </th>

                                    <th
                                        class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                        Actions
                                    </th>

                                </tr>
                            </thead>

                            {{-- Table Body --}}
                            <tbody class="divide-y divide-gray-100">

                                @foreach ($users as $user)
                                    <tr @click="openCustomer('{{ route('admin.users.appointments', $user) }}')"
                                        class="hover:bg-[#849753]/5 transition cursor-pointer group">
                                        {{-- User --}}
                                        <td class="px-5 py-4">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="w-9 h-9 rounded-full bg-[#849753] flex items-center justify-center text-white font-bold text-sm shrink-0">

                                                    {{ strtoupper(substr($user->name, 0, 1)) }}

                                                </div>

                                                <div>

                                                    <p class="font-semibold text-gray-800 whitespace-nowrap">
                                                        {{ $user->name }}
                                                    </p>

                                                    <p class="text-xs text-gray-400">
                                                        {{ $user->email }}
                                                    </p>

                                                </div>

                                            </div>

                                        </td>
                                        {{-- Phone --}}
                                        <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                            {{ $user->phone ?? '—' }}
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-5 py-4">

                                            @if ($user->status === UserStatus::ACTIVE)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                                    Active

                                                </span>
                                            @elseif ($user->status === UserStatus::PENDING)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>

                                                    Pending

                                                </span>
                                            @endif

                                        </td>

                                        {{-- Online --}}
                                        <td class="px-5 py-4">

                                            @if ($user->is_online)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>

                                                    Online

                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-semibold rounded-full whitespace-nowrap">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>

                                                    Offline

                                                </span>
                                            @endif

                                        </td>

                                        {{-- Last Seen --}}
                                        <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">

                                            {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : '—' }}

                                        </td>

                                        {{-- Joined --}}
                                        <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">

                                            {{ $user->created_at->format('M d, Y') }}

                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-5 py-4" @click.stop>

                                            <div class="flex items-center gap-2">

                                                {{-- Toggle Status --}}
                                                <form method="POST"
                                                    action="{{ route('admin.users.toggle-status', $user) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                        onclick="return confirm('Change status for {{ addslashes($user->name) }}?')"
                                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg transition whitespace-nowrap
                {{ $user->status === UserStatus::ACTIVE
                    ? 'bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100'
                    : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100' }}">

                                                        {{ $user->status === UserStatus::ACTIVE ? 'Deactivate' : 'Activate' }}

                                                    </button>
                                                </form>

                                                {{-- Delete --}}
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        onclick="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')"
                                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition whitespace-nowrap">

                                                        Delete

                                                    </button>
                                                </form>

                                            </div>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                    {{-- Pagination --}}
                    @if ($users->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100 pagination">
                            {{ $users->links() }}
                        </div>
                    @endif

                @endif

            </div>

        </div>

        {{-- ================================================= --}}
        {{-- CUSTOMER APPOINTMENTS MODAL --}}
        {{-- ================================================= --}}

        <div x-cloak x-show="showModal" x-transition.opacity
            class="fixed inset-0 z-100 flex items-center justify-center p-3 sm:p-6">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50" @click="closeModal()">
            </div>

            {{-- Modal Container --}}
            <div x-show="showModal" x-transition @click.stop
                class="relative bg-white w-full max-w-6xl max-h-[90vh]
                    rounded-2xl shadow-xl flex flex-col overflow-hidden">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 shrink-0">

                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            Customer Details
                        </h2>
                    </div>

                    <button type="button" @click="closeModal()"
                        class="w-9 h-9 flex items-center justify-center rounded-full
                            text-gray-500 hover:bg-gray-100 text-2xl">

                        &times;

                    </button>

                </div>

                {{-- Loading --}}
                <div x-show="loading" class="py-16 text-center">

                    <div
                        class="w-8 h-8 border-4 border-gray-200 border-t-[#849753]
                        rounded-full animate-spin mx-auto">
                    </div>

                    <p class="text-sm text-gray-400 mt-3">
                        Loading customer appointments...
                    </p>

                </div>

                {{-- Error --}}
                <div x-show="error" x-text="error" class="p-5 text-sm text-red-500">
                </div>

                {{-- Modal Body --}}
                <div x-show="!loading && customer" class="flex-1 overflow-y-auto p-5 space-y-6">

                    {{-- Customer Information --}}
                    <div class="flex flex-wrap items-center justify-between gap-4">

                        <div class="flex items-center gap-3">

                            <div class="w-12 h-12 rounded-full bg-[#849753]
                                flex items-center justify-center text-white font-bold text-lg shrink-0"
                                x-text="customer?.name?.charAt(0).toUpperCase()">
                            </div>

                            <div>

                                <h3 class="font-bold text-gray-800" x-text="customer?.name">
                                </h3>

                                <p class="text-xs text-gray-500" x-text="customer?.email">
                                </p>

                                <p class="text-xs text-gray-500" x-text="customer?.phone || 'No phone number'">
                                </p>

                            </div>

                        </div>

                        <div class="text-xs text-gray-400">

                            Joined:
                            <span x-text="customer?.joined"></span>

                        </div>

                    </div>

                    {{-- Appointment Statistics --}}
                    <div>

                        <h3 class="text-sm font-bold text-gray-800 mb-3">
                            Appointment Overview
                        </h3>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">

                            <template
                                x-for="item in [
                                {label: 'Total Appointments', key: 'total'},
                                {label: 'Pending', key: 'pending'},
                                {label: 'Confirmed', key: 'confirmed'},
                                {label: 'Rejected', key: 'rejected'},
                                {label: 'Cancelled', key: 'cancelled'},
                                {label: 'No Show', key: 'no_show'},
                                {label: 'Failed', key: 'failed'}
                            ]"
                                :key="item.key">

                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">

                                    <p class="text-xl font-bold text-gray-800" x-text="stats[item.key] ?? 0">
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1" x-text="item.label">
                                    </p>

                                </div>

                            </template>

                        </div>

                    </div>

                    {{-- Appointment History --}}
                    <div>

                        <div class="mb-3">

                            <h3 class="font-bold text-gray-800">
                                Appointment History
                            </h3>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 rounded-xl">

                            <table class="w-full text-sm">

                                {{-- Table Header --}}
                                <thead class="bg-gray-50">

                                    <tr class="text-left">

                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 whitespace-nowrap">
                                            Service
                                        </th>

                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 whitespace-nowrap">
                                            Therapist
                                        </th>

                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 whitespace-nowrap">
                                            Date
                                        </th>

                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 whitespace-nowrap">
                                            Time
                                        </th>

                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 whitespace-nowrap">
                                            Price
                                        </th>

                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 whitespace-nowrap">
                                            Status
                                        </th>

                                    </tr>

                                </thead>

                                {{-- Table Body --}}
                                <tbody class="divide-y divide-gray-100">

                                    <template x-for="appointment in appointments.data || []" :key="appointment.id">

                                        <tr class="hover:bg-gray-50">

                                            <td class="px-4 py-3 font-medium text-gray-700">
                                                <p x-text="appointment.service"></p>

                                                <p class="text-xs text-gray-400" x-text="appointment.description"></p>

                                                <p class="text-xs text-gray-400">
                                                    <span x-text="appointment.duration"></span> minutes
                                                </p>
                                            </td>
                                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap"
                                                x-text="appointment.therapist">
                                            </td>


                                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap"
                                                x-text="formatDate(appointment.date)">
                                            </td>

                                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                                <span x-text="appointment.time"></span>
                                                <span class="text-gray-400"> - </span>
                                                <span x-text="appointment.end_time"></span>
                                            </td>

                                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap"
                                                x-text="formatPrice(appointment.price)">
                                            </td>

                                            <td class="px-4 py-3 whitespace-nowrap">

                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                                                    :class="statusClass(appointment.status)"
                                                    x-text="formatStatus(appointment.status)">
                                                </span>

                                            </td>

                                        </tr>

                                    </template>

                                    {{-- Empty Appointments --}}
                                    <tr x-show="!appointments.data?.length">

                                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">

                                            No appointments found.

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                        {{-- Pagination --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 mt-4">

                            <p class="text-xs text-gray-500">

                                Showing
                                <span x-text="appointments.from ?? 0"></span>

                                to
                                <span x-text="appointments.to ?? 0"></span>

                                of
                                <span x-text="appointments.total ?? 0"></span>

                                appointments

                            </p>

                            <div class="flex gap-2">

                                <button type="button" @click="loadPage(appointments.current_page - 1)"
                                    :disabled="!appointments.prev_page_url || loading"
                                    class="px-3 py-2 text-xs rounded-lg border border-gray-200
                                        text-gray-600 hover:bg-gray-50 disabled:opacity-40">

                                    Previous

                                </button>

                                <button type="button" @click="loadPage(appointments.current_page + 1)"
                                    :disabled="!appointments.next_page_url || loading"
                                    class="px-3 py-2 text-xs rounded-lg border border-gray-200
                                        text-gray-600 hover:bg-gray-50 disabled:opacity-40">

                                    Next

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection


{{-- ===================================================== --}}
{{-- CUSTOMER MODAL SCRIPT --}}
{{-- ===================================================== --}}

@push('scripts')
    <script>
        function customerModal() {
            return {
                showModal: false,
                loading: false,
                error: '',
                customer: null,
                stats: {},
                appointments: {
                    data: []
                },
                currentUrl: '',
                requestNumber: 0,

                async openCustomer(url) {
                    this.showModal = true;
                    this.currentUrl = url;

                    this.customer = null;
                    this.stats = {};
                    this.appointments = {
                        data: []
                    };

                    this.error = '';

                    document.body.classList.add('overflow-hidden');

                    await this.fetchData(1);
                },

                async fetchData(page = 1) {
                    const request = ++this.requestNumber;

                    this.loading = true;
                    this.error = '';

                    try {
                        const url = new URL(
                            this.currentUrl,
                            window.location.origin
                        );

                        url.searchParams.set('page', page);

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(
                                'Unable to load customer appointments.'
                            );
                        }

                        const data = await response.json();

                        if (request !== this.requestNumber) {
                            return;
                        }

                        this.customer = data.customer;
                        this.stats = data.stats;
                        this.appointments = data.appointments;

                    } catch (error) {

                        if (request === this.requestNumber) {
                            this.error = error.message;
                        }

                    } finally {

                        if (request === this.requestNumber) {
                            this.loading = false;
                        }

                    }
                },

                loadPage(page) {
                    if (
                        page < 1 ||
                        page > this.appointments.last_page ||
                        this.loading
                    ) {
                        return;
                    }

                    this.fetchData(page);
                },

                formatDate(date) {
                    if (!date) return '—';

                    return new Date(date + 'T00:00:00')
                        .toLocaleDateString('en-PH', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                },

                formatPrice(price) {
                    return '₱' + Number(price || 0).toLocaleString(
                        'en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    );
                },

                statusClass(status) {
                    return {
                        confirm: 'bg-green-50 text-green-700',
                        pending: 'bg-yellow-50 text-yellow-700',
                        rejected: 'bg-red-50 text-red-700',
                        cancelled: 'bg-red-50 text-red-700',
                        'no show': 'bg-orange-50 text-orange-700',
                        failed: 'bg-red-50 text-red-700'
                    } [status] || 'bg-gray-100 text-gray-600';
                },

                formatStatus(status) {
                    return {
                        confirm: 'Confirmed',
                        pending: 'Pending',
                        rejected: 'Rejected',
                        cancelled: 'Cancelled',
                        'no show': 'No Show',
                        failed: 'Failed'
                    } [status] || status;
                },

                closeModal() {
                    this.requestNumber++;

                    this.showModal = false;
                    this.loading = false;
                    this.customer = null;
                    this.stats = {};

                    this.appointments = {
                        data: []
                    };

                    this.currentUrl = '';
                    this.error = '';

                    document.body.classList.remove('overflow-hidden');
                }
            };
        }
    </script>
@endpush
