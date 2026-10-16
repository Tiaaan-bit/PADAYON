@extends('layouts.staff')

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
                question: 'How do I view customer appointments?',
                answer: 'Open Appointments from the staff sidebar to review customer bookings, service details, assigned therapists, appointment dates, times, and statuses.'
            },
            {
                category: 'appointments',
                question: 'How do I create a walk-in booking?',
                answer: 'Open Quick Book, enter the customer information, select the service and available therapist, choose the appointment date and time, and review the details before submitting.'
            },
            {
                category: 'appointments',
                question: 'What should I do if an appointment is missing?',
                answer: 'Check the appointment date and review the available appointment list. Avoid creating a duplicate booking. If the appointment is still missing, contact the administrator.'
            },
            {
                category: 'appointments',
                question: 'How do I check available appointment slots?',
                answer: 'Open Quick Book or Calendar and check the selected date and therapist availability before choosing a time. Make sure the appointment does not conflict with another booking.'
            },
            {
                category: 'appointments',
                question: 'Can I change an appointment status?',
                answer: 'Use the status options permitted by your staff account. Verify the appointment details and follow the center’s procedures before changing a status. Contact the administrator if you cannot make the required change.'
            },
            {
                category: 'transactions',
                question: 'How do I check whether a customer has paid?',
                answer: 'Open Transactions and review the payment status, payment method, amount paid, and any remaining balance. Do not assume an appointment is paid simply because its booking status is confirmed.'
            },
            {
                category: 'transactions',
                question: 'What is the difference between paid and unpaid?',
                answer: 'Paid means the required payment has been recorded as completed. Unpaid means an amount remains outstanding. Verify the transaction details before updating a payment record.'
            },
            {
                category: 'transactions',
                question: 'How do I handle a downpayment?',
                answer: 'Review the total appointment amount, the amount already paid, and the remaining balance. Follow the authorized payment workflow when recording any additional payment.'
            },
            {
                category: 'transactions',
                question: 'What should I do if a GCash payment is pending or failed?',
                answer: 'Check the current transaction status before taking action. Avoid recording the same payment twice. If the payment status is unclear, ask the administrator to verify the transaction.'
            },
            {
                category: 'transactions',
                question: 'Where can I review transaction records?',
                answer: 'Open Transactions from the staff sidebar to review the available payment information and transaction records.'
            },
            {
                category: 'calendar',
                question: 'How do I view appointments for a specific date?',
                answer: 'Open Calendar and navigate to the date you want to review. Check the appointment count and individual booking details for that day.'
            },
            {
                category: 'calendar',
                question: 'Why might a time slot be unavailable?',
                answer: 'The therapist may already have an appointment, the requested time may be outside the available schedule, or the time may have already passed. Check availability before selecting another slot.'
            },
            {
                category: 'services',
                question: 'How do I check massage services and prices?',
                answer: 'Review the service options available in Quick Book. Check the service name, description, duration, price, and available add-ons before creating the booking.'
            },
            {
                category: 'services',
                question: 'How do I check therapist availability?',
                answer: 'Use the therapist and scheduling options provided in Quick Book or review the Calendar. Availability depends on the selected date, time, and existing appointments.'
            },
            {
                category: 'account',
                question: 'How do I update my staff profile?',
                answer: 'Open Settings from the staff sidebar and use the available profile options to review or update your information.'
            },
            {
                category: 'account',
                question: 'What should I do if I cannot log in?',
                answer: 'Verify your login credentials and ensure you are using the correct staff login page. If you still cannot access your account, contact the administrator.'
            },
            {
                category: 'account',
                question: 'Who should I contact if the system has a problem?',
                answer: 'Contact the administrator and explain which page you were using, what you were trying to do, and any error message displayed. Never share your password.'
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
                    placeholder="Search appointments, transactions..."
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
                    { id: 'services', label: 'Services & Therapists' },
                    { id: 'account', label: 'Account' }
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
