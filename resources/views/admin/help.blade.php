@extends('layouts.admin')

@section('title', 'Help Center')

@section('content')
<div
    x-data="{
        search: '',
        category: 'all',
        openFaq: null,

        faqs: [
            {
                category: 'appointments',
                question: 'How do I manage customer appointments?',
                answer: 'Open Appointments to review booking details, customer information, services, assigned therapists, schedules, and appointment statuses. Verify the booking before making changes.'
            },
            {
                category: 'appointments',
                question: 'How do I update an appointment status?',
                answer: 'Open the appointment and use the available status controls according to the appointment workflow. Only pending appointments that meet the system rules should be updated through the authorized status action. If an action is unavailable, verify the appointment status and payment method.'
            },
            {
                category: 'appointments',
                question: 'How do I manage walk-in customers?',
                answer: 'Open Quick Book to register or select a walk-in customer, choose the service and therapist, select an available date and time, and record the required booking and payment information.'
            },
            {
                category: 'appointments',
                question: 'What should I do if an appointment is missing?',
                answer: 'Check the appointment date, filters, and booking records. Avoid creating a duplicate appointment before confirming that the original booking does not exist.'
            },
            {
                category: 'transactions',
                question: 'How do I verify a customer payment?',
                answer: 'Open Transactions and review the appointment status, payment method, payment type, amount paid, and remaining balance. Confirm the payment evidence and recorded transaction details before marking an amount as paid.'
            },
            {
                category: 'transactions',
                question: 'How do I mark a counter payment as paid?',
                answer: 'Locate the eligible confirmed appointment in Transactions and verify the amount due. Use the Mark as Paid action only after receiving and verifying the payment. The system may prevent the action when no balance remains or the appointment is not eligible.'
            },
            {
                category: 'transactions',
                question: 'How do I handle downpayments and remaining balances?',
                answer: 'Review the appointment total, amount already paid, and remaining balance. Record the additional payment through the authorized workflow and verify that the resulting payment status reflects the transaction accurately.'
            },
            {
                category: 'transactions',
                question: 'What should I do if a GCash payment is pending or failed?',
                answer: 'Review the transaction status and payment records before taking action. Do not manually mark an unverified payment as paid or create duplicate payment records. Investigate unclear or delayed transactions before making changes.'
            },
            {
                category: 'transactions',
                question: 'How do I review payment history?',
                answer: 'Open Transactions and use the available records and filters to review appointment payments, amounts, payment methods, and outstanding balances.'
            },
            {
                category: 'calendar',
                question: 'How do I view appointments for a specific date?',
                answer: 'Open Calendar and select the date you want to inspect. Review the appointment count and individual bookings. Check dates carefully for appointments scheduled around midnight.'
            },
            {
                category: 'calendar',
                question: 'Why is a therapist unavailable at a selected time?',
                answer: 'The therapist may already have a booking, the requested time may be outside the available schedule, or the slot may be in the past. Check existing appointments and availability before changing a booking.'
            },
            {
                category: 'users',
                question: 'How do I manage customer accounts?',
                answer: 'Open the Users section and review the available account details and status controls. Verify the correct account before updating any information or account status.'
            },
            {
                category: 'users',
                question: 'How do I manage therapist accounts?',
                answer: 'Open Therapists to review therapist records and available management actions. Verify therapist details and scheduling information before making changes.'
            },
            {
                category: 'services',
                question: 'How do I manage services and add-ons?',
                answer: 'Open the relevant Services or Add-ons section to review available records and use the provided management controls. Check names, descriptions, prices, and durations before saving changes.'
            },
            {
                category: 'services',
                question: 'How do I manage website announcements?',
                answer: 'Open Posts and use the appropriate workflow for the content you want to publish. Check the post details and image before publishing so customers and staff see the intended information.'
            },
            {
                category: 'reports',
                question: 'How do I review reports?',
                answer: 'Open Reports and select the available report or date range. Review the displayed figures and confirm that the underlying appointment and transaction records are correct before relying on the totals.'
            },
            {
                category: 'reports',
                question: 'Why might report totals differ from transaction totals?',
                answer: 'Reports may use different date ranges, statuses, or payment calculations. Check the report filters and the underlying records, including outstanding balances and appointment statuses.'
            },
            {
                category: 'account',
                question: 'How do I update my administrator profile?',
                answer: 'Open Settings and use the profile options available to your account. Follow the required validation rules when updating profile or password information.'
            },
            {
                category: 'account',
                question: 'What should I do if a system action returns an error?',
                answer: 'Review the message, verify the record and your permissions, and try again only when it is safe to do so. For authorization errors or unexpected payment changes, investigate the policy and system logs rather than bypassing access controls.'
            }
        ],

        get filteredFaqs() {
            const term = this.search.trim().toLowerCase();

            return this.faqs.filter(faq => {
                const matchesCategory =
                    this.category === 'all' ||
                    faq.category === this.category;

                const matchesSearch = !term ||
                    faq.question.toLowerCase().includes(term) ||
                    faq.answer.toLowerCase().includes(term);

                return matchesCategory && matchesSearch;
            });
        }
    }"
    class="min-h-screen space-y-6 p-4 sm:p-6"
>
    {{-- Page heading --}}
    <div>
        <h1 class="mt-1 text-2xl font-bold text-gray-800 sm:text-3xl">
            Help Center
        </h1>
    </div>

    {{-- Search banner --}}
    <div class="overflow-hidden rounded-2xl bg-[#849753] p-5 text-white sm:p-8">
        <div class="mx-auto max-w-2xl text-center">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15">
                <svg class="h-7 w-7" fill="none" stroke="currentColor"
                    stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="m16 16 4 4" />
                </svg>
            </div>

            <h2 class="mt-4 text-xl font-bold sm:text-2xl">
                How can we help you?
            </h2>

            <div class="relative mt-5">
                <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="m16 16 4 4" />
                </svg>

                <input
                    type="search"
                    x-model="search"
                    placeholder="Search appointments, reports, transactions..."
                    aria-label="Search help articles"
                    class="w-full rounded-xl border-0 bg-white py-3 pl-12 pr-4 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:ring-2 focus:ring-[#6F4E37]"
                >
            </div>
        </div>
    </div>

    {{-- FAQ categories --}}
    <div class="space-y-3">
        <h2 class="text-lg font-bold text-gray-800">
            Browse by topic
        </h2>

        <div class="flex gap-2 overflow-x-auto pb-2">
            <template
                x-for="item in [
                    { id: 'all', label: 'All topics' },
                    { id: 'appointments', label: 'Appointments' },
                    { id: 'transactions', label: 'Transactions' },
                    { id: 'calendar', label: 'Calendar' },
                    { id: 'users', label: 'Users & Therapists' },
                    { id: 'services', label: 'Services & Posts' },
                    { id: 'reports', label: 'Reports' },
                    { id: 'account', label: 'Account & Settings' }
                ]"
                :key="item.id"
            >
                <button
                    type="button"
                    @click="category = item.id; openFaq = null"
                    :class="category === item.id
                        ? 'bg-[#6F4E37] text-white'
                        : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                    class="shrink-0 rounded-full px-4 py-2 text-sm font-medium transition"
                    x-text="item.label"
                ></button>
            </template>
        </div>
    </div>

    {{-- FAQ list --}}
    <div class="space-y-3">
        <template x-for="(faq, index) in filteredFaqs" :key="faq.question">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

                <button
                    type="button"
                    @click="openFaq = openFaq === faq.question ? null : faq.question"
                    :aria-expanded="openFaq === faq.question"
                    class="flex w-full items-center justify-between gap-4 p-4 text-left sm:p-5"
                >
                    <span class="font-semibold text-gray-800" x-text="faq.question"></span>

                    <svg
                        class="h-5 w-5 shrink-0 text-[#849753] transition-transform"
                        :class="openFaq === faq.question ? 'rotate-180' : ''"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </button>

                <div
                    x-cloak
                    x-show="openFaq === faq.question"
                    x-transition
                    class="border-t border-gray-100 px-4 pb-5 pt-4 sm:px-5"
                >
                    <p class="text-sm leading-6 text-gray-600" x-text="faq.answer"></p>
                </div>
            </div>
        </template>

        {{-- Empty state --}}
        <div
            x-cloak
            x-show="filteredFaqs.length === 0"
            class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center"
        >
            <svg class="mx-auto h-10 w-10 text-gray-400"
                fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7" />
                <path stroke-linecap="round" d="m16 16 4 4" />
            </svg>

            <h3 class="mt-3 font-semibold text-gray-800">
                No matching questions found
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Try another search term or choose a different topic.
            </p>

            <button
                type="button"
                @click="search = ''; category = 'all'; openFaq = null"
                class="mt-4 text-sm font-semibold text-[#6F4E37] hover:underline"
            >
                Clear filters
            </button>
        </div>
    </div>
</div>
@endsection
