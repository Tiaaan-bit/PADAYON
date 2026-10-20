@extends('layouts.user')

@section('title', 'Search | Padayon Massage Center')

@section('content')
    <div class="min-h-screen bg-[#D6BB9E] px-4 py-8 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-6xl">

            {{-- Page heading --}}
            <div class="mb-8">

                <h1 class="mt-2 text-3xl font-bold text-[#2F2420]">
                    Search Results
                </h1>
            </div>

            {{-- Search form --}}
            <form action="{{ route('user.search') }}" method="GET" class="mb-8">

                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <input type="search" name="search" value="{{ $term }}"
                            placeholder="What are you looking for?" maxlength="100" required
                            class="w-full rounded-2xl border border-gray-200
                               bg-white py-4 pl-12 pr-4 text-sm
                               text-[#2F2420] shadow-sm
                               focus:border-[#849753] focus:outline-none
                               focus:ring-2 focus:ring-[#849753]/30">

                        <svg class="absolute left-4 top-4 h-5 w-5 text-[#6F4E37]" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m16 16 4 4" />
                        </svg>
                    </div>

                    <button type="submit"
                        class="rounded-2xl bg-[#6F4E37] px-7 py-4
                           text-sm font-semibold text-white
                           transition hover:bg-[#58402F]">
                        Search
                    </button>
                </div>

                @error('search')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </form>

            {{-- QUICK LINKS --}}
            <section class="mb-8">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-[#2F2420]">
                        Quick Links
                    </h2>
                    <p class="mt-1 text-sm text-[#6F4E37]">
                        Can't find what you're looking for? Try one of these options.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    {{-- My Appointments --}}
                    <a href="{{ route('user.my-appointments') }}" wire:navigate
                        class="group rounded-2xl border border-[#D6BB9E] bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-[#849753]/15 text-[#6F4E37]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <rect x="3" y="5" width="18" height="16" rx="2" />
                                <path d="M16 3v4M8 3v4M3 10h18" />
                            </svg>
                        </div>

                        <h3 class="font-semibold text-[#2F2420]">
                            My Appointments
                        </h3>
                        <p class="mt-2 text-sm leading-5 text-gray-500">
                            View your bookings, schedules, and appointment status.
                        </p>
                        <span
                            class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#849753] group-hover:text-[#6F4E37]">
                            View appointments
                            <span>→</span>
                        </span>
                    </a>

                    {{-- Payment History --}}
                    <a href="{{ route('user.payment-history') }}" wire:navigate
                        class="group rounded-2xl border border-[#D6BB9E] bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-[#849753]/15 text-[#6F4E37]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <rect x="2" y="5" width="20" height="14" rx="2" />
                                <path d="M2 10h20M6 15h4" />
                            </svg>
                        </div>

                        <h3 class="font-semibold text-[#2F2420]">
                            Payment History
                        </h3>
                        <p class="mt-2 text-sm leading-5 text-gray-500">
                            Check your payments, payment methods, and amounts paid.
                        </p>
                        <span
                            class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#849753] group-hover:text-[#6F4E37]">
                            View payments
                            <span>→</span>
                        </span>
                    </a>

                    {{-- Book Services --}}
                    <a href="{{ route('user.appointment') }}" wire:navigate
                        class="group rounded-2xl border border-[#D6BB9E] bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-[#849753]/15 text-[#6F4E37]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path d="M12 3v18M3 12h18" />
                                <circle cx="12" cy="12" r="9" />
                            </svg>
                        </div>

                        <h3 class="font-semibold text-[#2F2420]">
                            Book Massage Services
                        </h3>
                        <p class="mt-2 text-sm leading-5 text-gray-500">
                            Explore massage services, prices, and available durations.
                        </p>
                        <span
                            class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#849753] group-hover:text-[#6F4E37]">
                            Explore services
                            <span>→</span>
                        </span>
                    </a>

                    {{-- Book Therapist --}}
                    <a href="{{ route('user.appointment') }}" wire:navigate
                        class="group rounded-2xl border border-[#D6BB9E] bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-[#849753]/15 text-[#6F4E37]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21v-2a8 8 0 0 1 16 0v2" />
                            </svg>
                        </div>

                        <h3 class="font-semibold text-[#2F2420]">
                            Book a Therapist
                        </h3>
                        <p class="mt-2 text-sm leading-5 text-gray-500">
                            Start booking and choose first services before preferred available therapist.
                        </p>
                        <span
                            class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#849753] group-hover:text-[#6F4E37]">
                            Book a therapist
                            <span>→</span>
                        </span>
                    </a>

                </div>
            </section>

            {{-- BOOKING APPOINTMENT SECTION --}}
            <section class="mb-8 overflow-hidden rounded-3xl border border-[#D6BB9E] bg-white shadow-sm">

                <div class="grid items-center gap-6 p-6 sm:p-8 md:grid-cols-[1fr_auto]">

                    <div>


                        <h2 class="text-xl font-bold text-[#2F2420]">
                            Book an Appointment?
                        </h2>

                    </div>

                    <div class="md:min-w-50">
                        <a href="{{ route('user.appointment') }}" wire:navigate
                            class="inline-flex w-full items-center justify-center gap-2
       rounded-xl bg-[#849753] px-6 py-3.5
       text-sm font-semibold text-white
       transition hover:bg-[#718344]
       focus:outline-none focus:ring-2
       focus:ring-[#849753] focus:ring-offset-2">
                            Book Now
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 12h14m-6-6 6 6-6 6" />
                            </svg>
                        </a>

                    </div>

                </div>
            </section>


            @if ($term === '')
                <div class="rounded-3xl border border-gray-100 bg-white p-10 text-center shadow-sm">
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center
                            rounded-full bg-[#849753]/10 text-[#849753]">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m16 16 4 4" />
                        </svg>
                    </div>

                    <h2 class="mt-4 text-lg font-bold text-[#2F2420]">
                        What can we help you find?
                    </h2>

                    <p class="mt-2 text-sm text-gray-500">
                        Enter a keyword above to search Padayon.
                    </p>
                </div>
            @else
                <p class="mb-6 text-sm text-gray-500">
                    Results for
                    <span class="font-semibold text-[#2F2420]">
                        "{{ $term }}"
                    </span>
                </p>

                {{-- APPOINTMENTS --}}
                <section class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-[#2F2420]">
                            My Appointments
                        </h2>

                        <span
                            class="rounded-full bg-[#849753]/10 px-3 py-1
                                 text-xs font-semibold text-[#6F4E37]">
                            {{ $appointments->count() }} found
                        </span>
                    </div>

                    @forelse ($appointments as $appointment)
                        <div
                            class="mb-3 rounded-2xl border border-gray-100
                                bg-white p-5 shadow-sm">
                            <div class="flex flex-col justify-between gap-3 sm:flex-row">
                                <div>
                                    <h3 class="font-semibold text-[#2F2420]">
                                        {{ $appointment->service?->name ?? 'Massage Appointment' }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $appointment->appointment_date }}
                                        @if ($appointment->appointment_time)
                                            · {{ $appointment->appointment_time }}
                                        @endif
                                    </p>

                                    <p class="mt-2 text-sm text-gray-600">
                                        Booking status:
                                        {{ ucfirst(str_replace('_', ' ', $appointment->status->value)) }} </p>
                                </div>

                                <a href="{{ route('user.my-appointments') }}" wire:navigate
                                    class="self-start text-sm font-semibold text-[#849753] hover:underline">
                                    View appointments →
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-2xl bg-white p-5 text-sm text-gray-500">
                            No matching appointments found.
                        </p>
                    @endforelse
                </section>

                {{-- SERVICES --}}
                <section class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-[#2F2420]">
                            Massage Services
                        </h2>

                        <span
                            class="rounded-full bg-[#849753]/10 px-3 py-1
                                 text-xs font-semibold text-[#6F4E37]">
                            {{ $services->count() }} found
                        </span>
                    </div>

                    @forelse ($services as $service)
                        <div
                            class="mb-3 rounded-2xl border border-gray-100
                                bg-white p-5 shadow-sm">
                            <div class="flex flex-col justify-between gap-3 sm:flex-row">
                                <div>
                                    <h3 class="font-semibold text-[#2F2420]">
                                        {{ $service->name }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $service->description }}
                                    </p>

                                    <p class="mt-2 font-semibold text-[#6F4E37]">
                                        ₱{{ number_format((float) $service->price, 2) }}
                                        @if ($service->duration_minutes)
                                            <span class="font-normal text-gray-500">
                                                · {{ $service->duration_minutes }} minutes
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                <a href="{{ route('user.appointment') }}" wire:navigate
                                    class="self-start text-sm font-semibold text-[#849753] hover:underline">
                                    Book a service →
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-2xl bg-white p-5 text-sm text-gray-500">
                            No matching services found.
                        </p>
                    @endforelse
                </section>

                {{-- PAYMENTS --}}
                <section class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-[#2F2420]">
                            Payment History
                        </h2>

                        <span
                            class="rounded-full bg-[#849753]/10 px-3 py-1
                                 text-xs font-semibold text-[#6F4E37]">
                            {{ $payments->count() }} found
                        </span>
                    </div>

                    @forelse ($payments as $payment)
                        <div
                            class="mb-3 rounded-2xl border border-gray-100
                                bg-white p-5 shadow-sm">
                            <div class="flex flex-col justify-between gap-3 sm:flex-row">
                                <div>
                                    <h3 class="font-semibold text-[#2F2420]">
                                        {{ $payment->service?->name ?? 'Appointment Payment' }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Method: {{ ucfirst(str_replace('_', ' ', (string) $payment->payment_method)) }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Payment status:
                                        {{ ucfirst(str_replace('_', ' ', (string) $payment->payment_status)) }}
                                    </p>

                                    <p class="mt-2 font-semibold text-[#6F4E37]">
                                        Amount paid: ₱{{ number_format((float) $payment->amount_paid, 2) }}
                                    </p>
                                </div>

                                <a href="{{ route('user.payment-history') }}" wire:navigate
                                    class="self-start text-sm font-semibold text-[#849753] hover:underline">
                                    View payments →
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-2xl bg-white p-5 text-sm text-gray-500">
                            No matching payment records found.
                        </p>
                    @endforelse
                </section>


                {{-- THERAPIST SEARCH RESULTS --}}
                @if ($therapists->isNotEmpty())
                    <section class="mt-8">
                        <div class="mb-4">
                            <h2 class="text-lg font-semibold text-[#2F2420]">
                                Massage Therapists
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                Explore our massage specialists.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($therapists as $therapist)
                                <article
                                    class="overflow-hidden rounded-2xl border border-[#D6BB9E]
                           bg-white shadow-sm transition hover:shadow-md">
                                    <div class="flex items-center gap-4 p-4">
                                        {{-- Therapist photo --}}
                                        <div
                                            class="h-20 w-20 shrink-0 overflow-hidden
                                    rounded-xl bg-[#F5EFE8]">
                                            @if ($therapist->image)
                                                <img src="{{ asset('storage/' . $therapist->image) }}"
                                                    alt="{{ $therapist->name }}" class="h-full w-full object-cover">
                                            @else
                                                <div
                                                    class="flex h-full w-full items-center
                                            justify-center text-[#6F4E37]">
                                                    <span class="text-2xl font-semibold">
                                                        {{ strtoupper(substr($therapist->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Therapist details --}}
                                        <div class="min-w-0 flex-1">
                                            <h3 class="truncate font-semibold text-[#2F2420]">
                                                {{ $therapist->name }}
                                            </h3>

                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ $therapist->specialty ?: 'Massage Specialist' }}
                                            </p>

                                            <span
                                                class="mt-2 inline-flex rounded-full px-2.5 py-1
                                       text-xs font-medium
                                       {{ $therapist->status === 'available' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ ucfirst($therapist->status ?? 'unavailable') }}
                                            </span>
                                        </div>
                                    </div>

                                    @if ($therapist->description)
                                        <div class="px-4 pb-4">
                                            <p class="text-sm leading-6 text-gray-600">
                                                {{ \Illuminate\Support\Str::limit($therapist->description, 110) }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="border-t border-[#F0E5DA] p-4">
                                        @if ($therapist->status === 'available')
                                            <a href="{{ route('user.appointment') }}"
                                                class="inline-flex w-full items-center justify-center
                                       rounded-xl bg-[#849753] px-4 py-2.5
                                       text-sm font-semibold text-white
                                       transition hover:bg-[#718344]">
                                                Book an Appointment
                                            </a>
                                        @else
                                            <span
                                                class="block rounded-xl bg-gray-100 px-4 py-2.5
                                       text-center text-sm text-gray-500">
                                                Currently Unavailable
                                            </span>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif


                {{-- HELP TOPICS --}}
                <section class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-[#2F2420]">
                            Help and Support
                        </h2>

                        <span
                            class="rounded-full bg-[#849753]/10 px-3 py-1
                                 text-xs font-semibold text-[#6F4E37]">
                            {{ $helpTopics->count() }} found
                        </span>
                    </div>

                    @forelse ($helpTopics as $topic)
                        <div
                            class="mb-3 rounded-2xl border border-gray-100
                                bg-white p-5 shadow-sm">
                            <h3 class="font-semibold text-[#2F2420]">
                                {{ $topic['title'] }}
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                {{ $topic['description'] }}
                            </p>
                        </div>
                    @empty
                        <p class="rounded-2xl bg-white p-5 text-sm text-gray-500">
                            No matching Help topics found.
                        </p>
                    @endforelse
                </section>

                {{-- Empty state --}}
                @if ($appointments->isEmpty() && $services->isEmpty() && $payments->isEmpty() && $helpTopics->isEmpty())
                    <div class="rounded-3xl bg-white p-8 text-center shadow-sm">
                        <h2 class="font-bold text-[#2F2420]">
                            No results found
                        </h2>

                        <p class="mt-2 text-sm text-gray-500">
                            Try a different keyword, such as "massage",
                            "GCash", "appointment", or "cancel".
                        </p>
                    </div>
                @endif

            @endif
        </div>
    </div>
@endsection
