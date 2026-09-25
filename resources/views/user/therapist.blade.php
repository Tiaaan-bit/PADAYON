@extends('layouts.user')

@section('title', 'Padayon Massage Center - Therapists')

@section('content')

    <div class="min-h-screen py-10" x-data="{
        showFeedbackModal: false,
        showLeaveFeedback: false,
        selectedTherapist: null,
    
        openFeedback(therapist) {
            this.selectedTherapist = therapist;
            this.showFeedbackModal = true;
            this.showLeaveFeedback = false;
            document.body.classList.add('overflow-hidden');
        },
    
        closeFeedback() {
            this.showFeedbackModal = false;
            this.showLeaveFeedback = false;
            this.selectedTherapist = null;
            document.body.classList.remove('overflow-hidden');
        },
    
        openLeaveFeedback() {
            this.showLeaveFeedback = true;
        },
    
        closeLeaveFeedback() {
            this.showLeaveFeedback = false;
        }
    }">

        {{-- ========================================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ========================================================= --}}

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-10 text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">
                    Our Therapists
                </h1>
            </div>


            {{-- ========================================================= --}}
            {{-- THERAPIST GRID --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                @forelse($therapists as $therapist)

                    @php
                        $rating = round((float) ($therapist->feedbacks_avg_rating ?? 0));

                        $userFeedback = $userFeedbacks->get($therapist->id);

                        $hasAppointment = $confirmedTherapistIds->contains($therapist->id);

                        $canLeaveFeedback = $hasAppointment && !$userFeedback;
                    @endphp


                    {{-- ================================================= --}}
                    {{-- THERAPIST CARD --}}
                    {{-- ================================================= --}}

                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">

                        {{-- ================================================= --}}
                        {{-- THERAPIST IMAGE --}}
                        {{-- ================================================= --}}

                        <div class="relative h-64 bg-gray-100">

                            @if ($therapist->image)
                                <img src="{{ asset('storage/' . $therapist->image) }}" alt="{{ $therapist->name }}"
                                    class="w-full h-full object-fill">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif

                        </div>


                        {{-- ================================================= --}}
                        {{-- THERAPIST INFORMATION --}}
                        {{-- ================================================= --}}

                        <div class="p-5">

                            {{-- Name --}}
                            <h2 class="text-xl font-bold text-gray-800">
                                {{ $therapist->name }}
                            </h2>


                            {{-- Specialty --}}
                            @if ($therapist->specialty)
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $therapist->specialty }}
                                </p>
                            @endif


                            {{-- ================================================= --}}
                            {{-- RATING --}}
                            {{-- ================================================= --}}

                            <div class="mt-3 flex items-center gap-2">

                                <div class="flex items-center">

                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5
                                                {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921
                                                        1.603-.921 1.902 0l1.07
                                                        3.292a1 1 0 00.95.69h3.462
                                                        c.969 0 1.371 1.24.588
                                                        1.81l-2.8 2.034a1 1 0
                                                        00-.364 1.118l1.07
                                                        3.292c.3.921-.755
                                                        1.688-1.54 1.118l-2.8
                                                        -2.034a1 1 0 00-1.175
                                                        0l-2.8 2.034c-.784.57
                                                        -1.838-.197-1.539-1.118
                                                        l1.07-3.292a1 1 0
                                                        00-.364-1.118L2.98
                                                        8.72c-.783-.57-.38-1.81
                                                        .588-1.81h3.461a1 1 0
                                                        00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor

                                </div>


                                <span class="text-sm text-gray-500">
                                    {{ number_format((float) ($therapist->feedbacks_avg_rating ?? 0), 1) }}
                                </span>


                                <span class="text-sm text-gray-400">
                                    ({{ $therapist->feedbacks_count }})
                                </span>

                            </div>


                            {{-- ================================================= --}}
                            {{-- DESCRIPTION --}}
                            {{-- ================================================= --}}

                            @if ($therapist->description)
                                <p class="mt-4 text-sm text-gray-600 line-clamp-3">
                                    {{ $therapist->description }}
                                </p>
                            @endif


                            {{-- ================================================= --}}
                            {{-- FEEDBACK BUTTON --}}
                            {{-- ================================================= --}}

                            <button type="button"
                                @click="openFeedback({

                                    id: {{ $therapist->id }},

                                    name:
                                        {{ \Illuminate\Support\Js::from($therapist->name) }},

                                    specialty:
                                        {{ \Illuminate\Support\Js::from($therapist->specialty) }},

                                    image:
                                        {{ \Illuminate\Support\Js::from($therapist->image ? asset('storage/' . $therapist->image) : null) }},

                                    rating:
                                        {{ (float) ($therapist->feedbacks_avg_rating ?? 0) }},

                                    feedbackCount:
                                        {{ $therapist->feedbacks_count }},

                                    hasAppointment:
                                        {{ $hasAppointment ? 'true' : 'false' }},

                                    canLeaveFeedback:
                                        {{ $canLeaveFeedback ? 'true' : 'false' }},

                                    alreadyReviewed:
                                        {{ $userFeedback ? 'true' : 'false' }},

                                    feedbacks:
                                        {{ \Illuminate\Support\Js::from(
                                            $therapist->feedbacks->map(function ($feedback) {
                                                    return [
                                                        'user' => $feedback->user->name ?? 'Anonymous',
                                        
                                                        'rating' => (int) $feedback->rating,
                                        
                                                        'comment' => $feedback->comment,
                                        
                                                        'date' => $feedback->created_at->format('M d, Y'),
                                                    ];
                                                })->values(),
                                        ) }}

                                })"
                                class="mt-5 w-full rounded-lg
                                    bg-[#849753]
                                    px-4 py-2.5
                                    text-sm font-semibold
                                    text-white
                                    transition
                                    hover:bg-[#6F4E37]">
                                Feedback
                            </button>

                        </div>

                    </div>

                @empty

                    {{-- ================================================= --}}
                    {{-- NO THERAPISTS --}}
                    {{-- ================================================= --}}

                    <div class="col-span-full py-16 text-center">

                        <p class="text-gray-500">
                            No therapists are currently available.
                        </p>

                    </div>
                @endforelse

            </div>


            {{-- ========================================================= --}}
            {{-- THERAPIST PAGINATION --}}
            {{-- ========================================================= --}}

            @if ($therapists->hasPages())
                <div class="mt-8">

                    {{ $therapists->appends(request()->except('therapists_page'))->links() }}

                </div>
            @endif

        </div>


        {{-- ============================================================= --}}
        {{-- FEEDBACK MODAL --}}
        {{-- ============================================================= --}}

        <div x-show="showFeedbackModal" x-cloak x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto"
            @keydown.escape.window="closeFeedback()">

            {{-- ========================================================= --}}
            {{-- BACKDROP --}}
            {{-- ========================================================= --}}

            <div class="fixed inset-0 bg-black/50" @click="closeFeedback()"></div>


            {{-- ========================================================= --}}
            {{-- MODAL POSITION --}}
            {{-- ========================================================= --}}

            <div class="relative min-h-screen
                    flex items-center justify-center
                    p-4">

                <div x-show="showFeedbackModal" x-transition @click.stop
                    class="relative w-full max-w-2xl
                        rounded-2xl bg-white shadow-xl
                        overflow-hidden">

                    {{-- ================================================= --}}
                    {{-- MODAL HEADER --}}
                    {{-- ================================================= --}}

                    <div
                        class="flex items-center justify-between
                            border-b border-gray-100
                            px-6 py-4">

                        <div>

                            <h2 class="text-xl font-bold text-gray-800">
                                Feedback
                            </h2>

                            <p class="text-sm text-gray-500">
                                Client feedback for this therapist
                            </p>

                        </div>


                        <button type="button" @click="closeFeedback()"
                            class="rounded-full p-2
                                text-gray-400
                                hover:bg-gray-100
                                hover:text-gray-600"
                            aria-label="Close">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>

                        </button>

                    </div>


                    {{-- ================================================= --}}
                    {{-- MODAL BODY --}}
                    {{-- ================================================= --}}

                    <div class="max-h-[80vh] overflow-y-auto">


                        {{-- ================================================= --}}
                        {{-- SELECTED THERAPIST --}}
                        {{-- ================================================= --}}

                        <div class="px-6 pt-6">

                            <div class="flex items-center gap-4">

                                {{-- Therapist Image --}}

                                <div
                                    class="w-16 h-16
                                        rounded-full
                                        overflow-hidden
                                        bg-gray-100
                                        shrink-0">

                                    <template x-if="selectedTherapist?.image">

                                        <img :src="selectedTherapist?.image" :alt="selectedTherapist?.name"
                                            class="w-full h-full
                                                object-cover">

                                    </template>


                                    <template x-if="!selectedTherapist?.image">

                                        <div
                                            class="w-full h-full
                                                flex items-center
                                                justify-center
                                                text-gray-400">

                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6
                                                            3 3 0 000 6zm-7
                                                            9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>

                                        </div>

                                    </template>

                                </div>


                                {{-- Therapist Name --}}

                                <div>

                                    <h3 class="text-lg font-bold text-gray-800" x-text="selectedTherapist?.name"></h3>

                                    <p class="text-sm text-gray-500" x-text="selectedTherapist?.specialty"></p>

                                </div>

                            </div>


                            {{-- ================================================= --}}
                            {{-- RATING SUMMARY --}}
                            {{-- ================================================= --}}

                            <div class="mt-4 flex items-center gap-2">

                                <div class="flex">

                                    <template x-for="i in 5" :key="i">

                                        <svg class="w-5 h-5"
                                            :class="i <= Math.round(
                                                    selectedTherapist?.rating ?? 0
                                                ) ?
                                                'text-yellow-400' :
                                                'text-gray-300'"
                                            fill="currentColor" viewBox="0 0 20 20">

                                            <path d="M9.049 2.927c.3-.921
                                                        1.603-.921 1.902 0l1.07
                                                        3.292a1 1 0 00.95.69h3.462
                                                        c.969 0 1.371 1.24.588
                                                        1.81l-2.8 2.034a1 1 0
                                                        00-.364 1.118l1.07
                                                        3.292c.3.921-.755
                                                        1.688-1.54 1.118l-2.8
                                                        -2.034a1 1 0 00-1.175
                                                        0l-2.8 2.034c-.784.57
                                                        -1.838-.197-1.539-1.118
                                                        l1.07-3.292a1 1 0
                                                        00-.364-1.118L2.98
                                                        8.72c-.783-.57-.38-1.81
                                                        .588-1.81h3.461a1 1 0
                                                        00.951-.69l1.07-3.292z" />

                                        </svg>

                                    </template>

                                </div>


                                <span class="text-sm text-gray-500"
                                    x-text="
                                        Number(
                                            selectedTherapist?.rating ?? 0
                                        ).toFixed(1)
                                    "></span>


                                <span class="text-sm text-gray-400"
                                    x-text="
                                        '(' +
                                        (
                                            selectedTherapist
                                                ?.feedbackCount ?? 0
                                        ) +
                                        ' reviews)'
                                    "></span>

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- USER BOOKING / REVIEW STATUS --}}
                        {{-- ================================================= --}}

                        <div class="px-6 pt-6">

                            {{-- ================================================= --}}
                            {{-- NEVER BOOKED --}}
                            {{-- ================================================= --}}

                            <template
                                x-if="
                                    selectedTherapist
                                    && !selectedTherapist.hasAppointment
                                ">

                                <div
                                    class="mb-5 rounded-xl
                                        border border-gray-200
                                        bg-gray-50
                                        px-4 py-4">

                                    <div
                                        class="flex items-center
                                            justify-between
                                            gap-4">

                                        <div>

                                            <p
                                                class="text-sm
                                                    font-semibold
                                                    text-gray-800">
                                                You haven't booked
                                                this therapist yet.
                                            </p>


                                        </div>


                                        <a href="{{ route('user.appointment') }}"
                                            class="shrink-0
                                                rounded-lg
                                                bg-[#849753]
                                                px-4 py-2.5
                                                text-sm
                                                font-semibold
                                                text-white
                                                transition
                                                hover:bg-[#6F4E37]">
                                            Book Now
                                        </a>

                                    </div>

                                </div>

                            </template>


                            {{-- ================================================= --}}
                            {{-- BOOKED BUT NOT REVIEWED --}}
                            {{-- ================================================= --}}

                            <template
                                x-if="
                                    selectedTherapist?.hasAppointment
                                    && !selectedTherapist?.alreadyReviewed
                                ">

                                <div
                                    class="mb-5 rounded-xl
                                        border border-blue-200
                                        bg-blue-50
                                        px-4 py-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="mt-0.5
                                                flex h-8 w-8
                                                shrink-0
                                                items-center
                                                justify-center
                                                rounded-full
                                                bg-blue-100
                                                text-blue-600">

                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>

                                        </div>


                                        <div>

                                            <p
                                                class="text-sm
                                                    font-semibold
                                                    text-blue-800">
                                                You already booked
                                                this therapist.
                                            </p>

                                            <p
                                                class="mt-1
                                                    text-xs
                                                    text-blue-700">
                                                You can now share your
                                                experience by leaving
                                                feedback.
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </template>


                            {{-- ================================================= --}}
                            {{-- BOOKED AND ALREADY REVIEWED --}}
                            {{-- ================================================= --}}

                            <template
                                x-if="
                                    selectedTherapist?.hasAppointment
                                    && selectedTherapist?.alreadyReviewed
                                ">

                                <div
                                    class="mb-5 rounded-xl
                                        border border-green-200
                                        bg-green-50
                                        px-4 py-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="mt-0.5
                                                flex h-8 w-8
                                                shrink-0
                                                items-center
                                                justify-center
                                                rounded-full
                                                bg-green-100
                                                text-green-600">

                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>

                                        </div>


                                        <div>

                                            <p
                                                class="text-sm
                                                    font-semibold
                                                    text-green-800">
                                                You already booked
                                                and reviewed this therapist.
                                            </p>

                                            <p
                                                class="mt-1
                                                    text-xs
                                                    text-green-700">
                                                Thank you for sharing
                                                your experience.
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </template>

                        </div>


                        {{-- ================================================= --}}
                        {{-- FEEDBACK LIST --}}
                        {{-- ================================================= --}}

                        <div class="px-6 py-6">

                            <div class="flex items-center
                                    justify-between mb-5">

                                <h3 class="text-lg font-bold
                                        text-gray-800">
                                    Client Feedback
                                </h3>


                                {{-- ================================================= --}}
                                {{-- LEAVE FEEDBACK BUTTON --}}
                                {{-- ================================================= --}}

                                <template
                                    x-if="
                                        selectedTherapist
                                            ?.canLeaveFeedback
                                        && !showLeaveFeedback
                                    ">

                                    <button type="button" @click="openLeaveFeedback()"
                                        class="rounded-lg
                                            bg-[#849753]
                                            px-4 py-2
                                            text-sm
                                            font-semibold
                                            text-white
                                            hover:bg-[#6F4E37]
                                            transition">
                                        Leave Feedback
                                    </button>

                                </template>

                            </div>


                            {{-- ================================================= --}}
                            {{-- NO FEEDBACK --}}
                            {{-- ================================================= --}}

                            <template
                                x-if="
                                    !selectedTherapist
                                        ?.feedbacks
                                        ?.length
                                ">

                                <div
                                    class="rounded-xl
                                        bg-gray-50
                                        border border-gray-100
                                        p-8 text-center">

                                    <div
                                        class="flex
                                            justify-center
                                            mb-3">

                                        <svg class="w-10 h-10
                                                text-gray-300"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01
                                                        M16 10h.01M9 16h6m-9
                                                        4h12a2 2 0 002-2V6a2
                                                        2 0 00-2-2H6a2 2 0
                                                        00-2 2v12a2 2 0
                                                        002 2z" />
                                        </svg>

                                    </div>


                                    <p class="text-gray-500 text-sm">
                                        No feedback yet for this therapist.
                                    </p>

                                </div>

                            </template>


                            {{-- ================================================= --}}
                            {{-- FEEDBACK ITEMS --}}
                            {{-- ================================================= --}}

                            <div class="space-y-4">

                                <template
                                    x-for="
                                        (feedback, index)
                                        in (
                                            selectedTherapist
                                                ?.feedbacks ?? []
                                        )
                                    "
                                    :key="index">

                                    <div
                                        class="rounded-xl
                                            border border-gray-100
                                            bg-white
                                            shadow-sm
                                            p-4">

                                        {{-- User + Date --}}

                                        <div
                                            class="flex items-start
                                                justify-between
                                                gap-4">

                                            <div
                                                class="flex items-start
                                                    gap-3">

                                                {{-- Avatar --}}

                                                <div class="w-10 h-10
                                                        rounded-full
                                                        bg-[#849753]
                                                        text-white
                                                        flex items-center
                                                        justify-center
                                                        font-semibold
                                                        shrink-0"
                                                    x-text="
                                                        feedback.user
                                                            ? feedback.user
                                                                .charAt(0)
                                                                .toUpperCase()
                                                            : 'U'
                                                    ">
                                                </div>


                                                {{-- User Info --}}

                                                <div>

                                                    <h4 class="font-semibold
                                                            text-gray-800"
                                                        x-text="feedback.user"></h4>


                                                    {{-- Stars --}}

                                                    <div
                                                        class="flex items-center
                                                            mt-1">

                                                        <template x-for="i in 5" :key="i">

                                                            <svg class="w-4 h-4"
                                                                :class="i <= feedback.rating ?
                                                                    'text-yellow-400' :
                                                                    'text-gray-300'"
                                                                fill="currentColor" viewBox="0 0 20 20">

                                                                <path d="M9.049 2.927c.3-.921
                                                                            1.603-.921 1.902
                                                                            0l1.07 3.292a1
                                                                            1 0 00.95.69h3.462
                                                                            c.969 0 1.371 1.24
                                                                            .588 1.81l-2.8
                                                                            2.034a1 1 0
                                                                            00-.364 1.118l1.07
                                                                            3.292c.3.921-.755
                                                                            1.688-1.54 1.118
                                                                            l-2.8-2.034a1 1
                                                                            0 00-1.175 0l-2.8
                                                                            2.034c-.784.57-1.838
                                                                            -.197-1.539-1.118l1.07
                                                                            -3.292a1 1 0
                                                                            00-.364-1.118L2.98
                                                                            8.72c-.783-.57-.38-1.81
                                                                            .588-1.81h3.461a1 1 0
                                                                            .951-.69l1.07-3.292z" />

                                                            </svg>

                                                        </template>

                                                    </div>

                                                </div>

                                            </div>


                                            {{-- Date --}}

                                            <span
                                                class="text-xs
                                                    text-gray-400
                                                    whitespace-nowrap"
                                                x-text="feedback.date"></span>

                                        </div>


                                        {{-- Comment --}}

                                        <template x-if="feedback.comment">

                                            <p class="mt-4
                                                    text-sm
                                                    text-gray-600
                                                    leading-relaxed"
                                                x-text="feedback.comment"></p>

                                        </template>

                                    </div>

                                </template>

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- LEAVE FEEDBACK FORM --}}
                        {{-- ================================================= --}}

                        <template
                            x-if="
                                showLeaveFeedback
                                && selectedTherapist?.canLeaveFeedback
                            ">

                            <div
                                class="border-t
                                    border-gray-100
                                    bg-gray-50
                                    px-6 py-6">

                                {{-- Form Header --}}

                                <div
                                    class="flex items-center
                                        justify-between mb-5">

                                    <h3
                                        class="text-lg font-bold
                                            text-gray-800">
                                        Leave Feedback
                                    </h3>


                                    <button type="button" @click="closeLeaveFeedback()"
                                        class="text-sm
                                            text-gray-500
                                            hover:text-gray-700">
                                        Back to Reviews
                                    </button>

                                </div>


                                {{-- ================================================= --}}
                                {{-- FORM --}}
                                {{-- ================================================= --}}

                                <form method="POST"
                                    :action="selectedTherapist
                                        ?
                                        '{{ route('user.therapists.feedback', ['therapist' => '__THERAPIST__']) }}'
                                        .replace(
                                            '__THERAPIST__',
                                            selectedTherapist.id
                                        ) :
                                        '#'"
                                    x-data="{
                                        rating: 0
                                    }">

                                    @csrf


                                    {{-- ================================================= --}}
                                    {{-- RATING --}}
                                    {{-- ================================================= --}}

                                    <div class="mb-5">

                                        <label
                                            class="block
                                                text-sm
                                                font-medium
                                                text-gray-700
                                                mb-2">
                                            Your Rating
                                        </label>


                                        <input type="hidden" name="rating" x-model="rating" required>


                                        {{-- Stars --}}

                                        <div
                                            class="flex items-center
                                                gap-2">

                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button"
                                                    @click="
                                                        rating = {{ $i }}
                                                    "
                                                    class="rounded
                                                        focus:outline-none
                                                        focus:ring-2
                                                        focus:ring-yellow-300"
                                                    aria-label="
                                                        Rate {{ $i }} stars
                                                    ">

                                                    <svg class="w-8 h-8
                                                            transition-colors
                                                            duration-150"
                                                        :class="rating >= {{ $i }} ?
                                                            'text-yellow-400' :
                                                            'text-gray-300'"
                                                        fill="currentColor" viewBox="0 0 20 20">

                                                        <path d="M9.049 2.927c.3-.921
                                                                    1.603-.921 1.902 0l1.07
                                                                    3.292a1 1 0 00.95.69h3.462
                                                                    c.969 0 1.371 1.24.588
                                                                    1.81l-2.8 2.034a1 1
                                                                    0 00-.364 1.118l1.07
                                                                    3.292c.3.921-.755
                                                                    1.688-1.54 1.118l-2.8
                                                                    -2.034a1 1 0 00-1.175
                                                                    0l-2.8 2.034c-.784.57-1.838
                                                                    -.197-1.539-1.118l1.07
                                                                    -3.292a1 1 0 00-.364
                                                                    -1.118L2.98 8.72c-.783-.57
                                                                    -.38-1.81.588-1.81h3.461a1
                                                                    1 0 00.951-.69l1.07-3.292z" />

                                                    </svg>

                                                </button>
                                            @endfor

                                        </div>


                                        {{-- Rating Text --}}

                                        <p x-show="rating > 0"
                                            x-text="
                                                rating +
                                                (
                                                    rating === 1
                                                        ? ' star'
                                                        : ' stars'
                                                )
                                            "
                                            class="mt-2
                                                text-xs
                                                text-gray-500">
                                        </p>

                                    </div>


                                    {{-- ================================================= --}}
                                    {{-- COMMENT --}}
                                    {{-- ================================================= --}}

                                    <div class="mb-5">

                                        <label for="comment"
                                            class="block
                                                text-sm
                                                font-medium
                                                text-gray-700
                                                mb-2">
                                            Your Comment
                                        </label>


                                        <textarea id="comment" name="comment" rows="5" maxlength="1000"
                                            placeholder="Tell us about your experience..."
                                            class="w-full
                                                rounded-lg
                                                border
                                                border-gray-300
                                                px-4 py-3
                                                text-sm
                                                text-gray-700
                                                placeholder-gray-400
                                                focus:border-[#849753]
                                                focus:ring-[#849753]"></textarea>


                                        <p
                                            class="mt-1
                                                text-xs
                                                text-gray-400">
                                            Maximum 1000 characters.
                                        </p>

                                    </div>


                                    {{-- ================================================= --}}
                                    {{-- FORM BUTTONS --}}
                                    {{-- ================================================= --}}

                                    <div class="flex gap-3">

                                        <button type="button"
                                            @click="
                                                closeLeaveFeedback()
                                            "
                                            class="flex-1
                                                rounded-lg
                                                border
                                                border-gray-300
                                                px-4 py-3
                                                text-sm
                                                font-semibold
                                                text-gray-700
                                                hover:bg-gray-50">
                                            Cancel
                                        </button>


                                        <button type="submit" :disabled="rating === 0"
                                            :class="rating === 0 ?
                                                'bg-gray-300 cursor-not-allowed' :
                                                'bg-[#849753] hover:bg-[#6F4E37]'"
                                            class="flex-1
                                                rounded-lg
                                                px-4 py-3
                                                text-sm
                                                font-semibold
                                                text-white
                                                transition">
                                            Submit Feedback
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </template>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
