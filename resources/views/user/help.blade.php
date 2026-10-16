{{-- Change layouts.user if your existing user pages use another layout --}}
@extends('layouts.user')

@section('title', 'Help Center')

@section('content')
    <div x-data="{
        search: '',
        category: 'all',
        openFaq: null,
    
        faqs: [{
                category: 'appointments',
                question: 'How do I book an appointment?',
                answer: 'Open Book Appointment, select your massage service, choose an available therapist and schedule, then select your payment method and complete the booking.'
            },
            {
                category: 'appointments',
                question: 'Why is my appointment still pending?',
                answer: 'Your appointment may need further processing. For Pay at Counter bookings, wait for the center to process your appointment. For GCash, check your payment status and booking details before trying again.'
            },
            {
                category: 'appointments',
                question: 'How can I check my appointment status?',
                answer: 'Open Appointments History to review your appointment details and current status.'
            },
            {
                category: 'appointments',
                question: 'Can I reschedule my appointment?',
                answer: 'Open Appointments History, select the eligible appointment, and use the reschedule option if it is available. Choose an available date and time.'
            },
            {
                category: 'payments',
                question: 'What payment methods are available?',
                answer: 'Your booking may offer Pay at Counter or GCash, depending on the options available during checkout.'
            },
            {
                category: 'payments',
                question: 'What is a downpayment?',
                answer: 'A downpayment is an initial payment toward your appointment total. Any remaining balance must be settled according to the center’s payment process.'
            },
            {
                category: 'payments',
                question: 'What should I do if my GCash payment fails?',
                answer: 'Check the payment status in your account first. Avoid repeatedly paying for the same appointment. If the payment remains unclear, contact the center for assistance.'
            },
            {
                category: 'payments',
                question: 'Where can I see my payment history?',
                answer: 'Open Payments History to review the payment information recorded for your appointments.'
            },
            {
                category: 'services',
                question: 'How do I choose a massage service?',
                answer: 'Open Book Appointment to review the available services, descriptions, durations, prices, and add-ons before choosing.'
            },
            {
                category: 'services',
                question: 'Can I choose a therapist?',
                answer: 'You can select from the therapists available during the booking process. Availability depends on the selected date and time.'
            },
            {
                category: 'account',
                question: 'How do I update my profile?',
                answer: 'Open Profile Settings to review and update the profile information you are allowed to change.'
            },
            {
                category: 'account',
                question: 'How do I change my password?',
                answer: 'Open Profile Settings and use the password update section. Follow the required verification and password rules.'
            },
            {
                category: 'account',
                question: 'How do I submit therapist feedback?',
                answer: 'Open Therapists, choose the therapist, and submit feedback if you have an eligible appointment.'
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
    }" class="min-h-screen space-y-6 p-4 sm:p-6">
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
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" d="m16 16 4 4" />
                    </svg>
                </div>

                <h2 class="mt-4 text-xl font-bold sm:text-2xl">
                    How can we help you?
                </h2>

                <div class="relative mt-5">
                    <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none"
                        stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" d="m16 16 4 4" />
                    </svg>

                    <input type="search" x-model="search" placeholder="Search appointments, payments..."
                        aria-label="Search help articles"
                        class="w-full rounded-xl border-0 bg-white py-3 pl-12 pr-4 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 focus:ring-2 focus:ring-[#6F4E37]">
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
                { id: 'payments', label: 'Payments' },
                { id: 'services', label: 'Services' },
                { id: 'account', label: 'Account & Feedback' }
            ]"
                    :key="item.id">
                    <button type="button" @click="category = item.id; openFaq = null"
                        :class="category === item.id ?
                            'bg-[#6F4E37] text-white' :
                            'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                        class="shrink-0 rounded-full px-4 py-2 text-sm font-medium transition" x-text="item.label"></button>
                </template>
            </div>
        </div>

        {{-- FAQ list --}}
        <div class="space-y-3">
            <template x-for="(faq, index) in filteredFaqs" :key="faq.question">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <button type="button" @click="openFaq = openFaq === faq.question ? null : faq.question"
                        :aria-expanded="openFaq === faq.question"
                        class="flex w-full items-center justify-between gap-4 p-4 text-left sm:p-5">
                        <span class="font-semibold text-gray-800" x-text="faq.question"></span>

                        <svg class="h-5 w-5 shrink-0 text-[#849753] transition-transform"
                            :class="openFaq === faq.question ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div x-cloak x-show="openFaq === faq.question" x-transition
                        class="border-t border-gray-100 px-4 pb-5 pt-4 sm:px-5">
                        <p class="text-sm leading-6 text-gray-600" x-text="faq.answer"></p>
                    </div>
                </div>
            </template>

            <div x-cloak x-show="filteredFaqs.length === 0"
                class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5"
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

                <button type="button" @click="search = ''; category = 'all'; openFaq = null"
                    class="mt-4 text-sm font-semibold text-[#6F4E37] hover:underline">
                    Clear filters
                </button>
            </div>
        </div>
    </div>
@endsection
