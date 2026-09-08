@extends('layouts.user')

@section('title', 'Padayon Massage Center - Therapists')

@section('content')

<div class="min-h-screen p-4 sm:p-6">


<div
    class="max-w-7xl mx-auto"
    x-data="{
        showFeedbackModal: false,
        selectedTherapist: null,

        openFeedback(therapist) {
            this.selectedTherapist = therapist;
            this.showFeedbackModal = true;
            document.body.classList.add('overflow-hidden');
        },

        closeFeedback() {
            this.showFeedbackModal = false;
            this.selectedTherapist = null;
            document.body.classList.remove('overflow-hidden');
        }
    }"
>

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Our Therapists
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Meet our professional therapy team and leave your feedback.
        </p>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- THERAPISTS GRID --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">

        @forelse($therapists as $therapist)

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition">

                {{-- Therapist Image --}}
                <div class="relative h-48 bg-gray-100">

                    @if($therapist->image)

                        <img
                            src="{{ asset('storage/' . $therapist->image) }}"
                            alt="{{ $therapist->name }}"
                            class="w-full h-full object-cover"
                        >

                    @else

                        <div class="w-full h-full flex items-center justify-center">

                            <svg
                                class="w-16 h-16 text-gray-300"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                            >
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>

                        </div>

                    @endif

                </div>


                {{-- Card Content --}}
                <div class="p-4">

                    <h3 class="font-semibold text-gray-800 text-lg">
                        {{ $therapist->name }}
                    </h3>

                    <p class="text-sm text-[#849753] font-medium">
                        {{ $therapist->specialty }}
                    </p>


                    {{-- Rating --}}
                    <div class="mt-3 flex items-center gap-2">

                        <div class="flex items-center">

                            @for($i = 1; $i <= 5; $i++)

                                @if($i <= round($therapist->averageRating()))

                                    <svg
                                        class="w-4 h-4 text-yellow-400"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>

                                @else

                                    <svg
                                        class="w-4 h-4 text-gray-300"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034a1 1 0 00-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>

                                @endif

                            @endfor

                        </div>

                        <span class="text-xs text-gray-500">
                            ({{ $therapist->feedbacks_count }})
                        </span>

                    </div>


                    {{-- Buttons --}}
                    <div class="mt-4 flex items-center gap-2">

                        <a
                            href="{{ route('user.appointment') }}"
                            class="flex-1 text-center rounded-lg bg-[#849753] px-4 py-2 text-white text-sm font-semibold hover:bg-[#6F4E37] transition"
                        >
                            Book Now
                        </a>


                        {{-- Feedback Button --}}
                        <button
                            type="button"
                            @click="openFeedback({
                                id: {{ $therapist->id }},
                                name: {{ \Illuminate\Support\Js::from($therapist->name) }},
                                specialty: {{ \Illuminate\Support\Js::from($therapist->specialty) }},
                                image: {{ \Illuminate\Support\Js::from(
                                    $therapist->image
                                        ? asset('storage/' . $therapist->image)
                                        : null
                                ) }},
                                rating: {{ round($therapist->averageRating()) }},
                                feedbackCount: {{ $therapist->feedbacks_count }}
                            })"
                            class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition"
                        >
                            Feedback
                        </button>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-span-full text-center py-16">
                <p class="text-gray-500">
                    No therapists available at the moment.
                </p>
            </div>

        @endforelse

    </div>


    {{-- ========================================================= --}}
    {{-- FEEDBACK MODAL --}}
    {{-- ========================================================= --}}

    <div
        x-show="showFeedbackModal"
        x-transition.opacity
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="closeFeedback()"
    >

        {{-- Overlay --}}
        <div
            class="absolute inset-0 bg-black/50"
            @click="closeFeedback()"
        ></div>


        {{-- Modal --}}
        <div
            x-show="showFeedbackModal"
            x-transition
            @click.stop
            class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto"
        >

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">

                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Therapist Feedback
                    </h2>

                    <p class="text-xs text-gray-500 mt-0.5">
                        Share your experience
                    </p>
                </div>


                <button
                    type="button"
                    @click="closeFeedback()"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

            </div>


            {{-- Modal Body --}}
            <div class="p-5">

                {{-- Selected Therapist Information --}}
                <div class="flex flex-col items-center text-center mb-6">

                    {{-- Therapist Image --}}
                    <div class="w-24 h-24 rounded-full bg-gray-100 overflow-hidden mb-3">

                        <template x-if="selectedTherapist && selectedTherapist.image">

                            <img
                                :src="selectedTherapist.image"
                                :alt="selectedTherapist.name"
                                class="w-full h-full object-cover"
                            >

                        </template>


                        <template x-if="selectedTherapist && !selectedTherapist.image">

                            <div class="w-full h-full flex items-center justify-center">

                                <svg
                                    class="w-12 h-12 text-gray-300"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />
                                </svg>

                            </div>

                        </template>

                    </div>


                    <h3
                        class="text-xl font-bold text-gray-800"
                        x-text="selectedTherapist ? selectedTherapist.name : ''"
                    ></h3>


                    <p
                        class="text-sm text-[#849753] font-medium"
                        x-text="selectedTherapist ? selectedTherapist.specialty : ''"
                    ></p>


                    {{-- Rating --}}
                    <div class="mt-2 flex items-center gap-2">

                        <div class="flex items-center">

                            <template x-for="i in 5" :key="i">

                                <svg
                                    class="w-4 h-4"
                                    :class="
                                        selectedTherapist &&
                                        i <= selectedTherapist.rating
                                            ? 'text-yellow-400'
                                            : 'text-gray-300'
                                    "
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>

                            </template>

                        </div>


                        <span
                            class="text-xs text-gray-500"
                            x-text="
                                selectedTherapist
                                    ? '(' + selectedTherapist.feedbackCount + ' reviews)'
                                    : ''
                            "
                        ></span>

                    </div>

                </div>


                {{-- Feedback Forms --}}
                @foreach($therapists as $therapist)

                    @php

                        $userFeedback = \App\Models\TherapistFeedback::where('user_id', Auth::id())
                            ->where('therapist_id', $therapist->id)
                            ->first();

                        $hasAppointment = \App\Models\UsersAppointments::where('user_id', Auth::id())
                            ->where('therapist_id', $therapist->id)
                            ->where('status', 'confirm')
                            ->exists();

                        $canLeaveFeedback = $hasAppointment && !$userFeedback;

                    @endphp


                    <div
                        x-show="
                            selectedTherapist &&
                            selectedTherapist.id === {{ $therapist->id }}
                        "
                    >

                        @if($canLeaveFeedback)

                            <form
                                method="POST"
                                action="{{ route('user.therapists.feedback', $therapist) }}"
                            >

                                @csrf


                                {{-- Rating --}}
                                <div class="mb-4">

                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Rating
                                    </label>

                                    <div class="flex gap-2">

                                        @for($i = 1; $i <= 5; $i++)

                                            <label class="cursor-pointer">

                                                <input
                                                    type="radio"
                                                    name="rating"
                                                    value="{{ $i }}"
                                                    class="hidden peer"
                                                    required
                                                >

                                                <svg
                                                    class="w-7 h-7 text-gray-300 peer-checked:text-yellow-400"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>

                                            </label>

                                        @endfor

                                    </div>

                                </div>


                                {{-- Comment --}}
                                <div class="mb-4">

                                    <label
                                        for="comment-{{ $therapist->id }}"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Comment (Optional)
                                    </label>

                                    <textarea
                                        name="comment"
                                        id="comment-{{ $therapist->id }}"
                                        rows="4"
                                        class="w-full rounded-lg border-gray-300 focus:border-[#849753] focus:ring-[#849753] text-sm"
                                        placeholder="Share your experience..."
                                    ></textarea>

                                </div>


                                {{-- Submit --}}
                                <button
                                    type="submit"
                                    class="w-full rounded-lg bg-[#849753] px-4 py-3 text-white text-sm font-semibold hover:bg-[#6F4E37] transition"
                                >
                                    Submit Feedback
                                </button>

                            </form>


                        @elseif($userFeedback)

                            <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-center">

                                <p class="text-sm text-green-700 font-medium">
                                    ✓ You have already submitted feedback
                                </p>

                            </div>


                        @else

                            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 text-center">

                                <p class="text-sm text-gray-600">
                                    Book an appointment to leave feedback
                                </p>

                            </div>

                        @endif

                    </div>

                @endforeach

            </div>


            {{-- Modal Footer --}}
            <div class="px-5 py-4 border-t border-gray-100 flex justify-end">

                <button
                    type="button"
                    @click="closeFeedback()"
                    class="rounded-lg bg-gray-200 px-5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition"
                >
                    Close
                </button>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ALL REVIEWS SECTION --}}
    {{-- ========================================================= --}}

    <div class="mb-10">

        <h2 class="text-xl font-bold text-gray-800 mb-4">
            All Reviews
        </h2>


        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

            <div class="space-y-4">

                @php

                    $allFeedbacks = \App\Models\TherapistFeedback::with([
                        'user',
                        'therapist'
                    ])
                    ->latest()
                    ->take(10)
                    ->get();

                @endphp


                @forelse($allFeedbacks as $feedback)

                    <div class="border-b border-gray-100 pb-4 last:border-0">

                        {{-- Review Header --}}
                        <div class="flex items-center justify-between mb-2">

                            <div class="flex items-center gap-3">

                                {{-- User Avatar --}}
                                <div class="w-9 h-9 rounded-full bg-[#849753] flex items-center justify-center text-white font-bold text-xs">
                                    {{ strtoupper(substr($feedback->user->name ?? 'U', 0, 1)) }}
                                </div>


                                {{-- User Information --}}
                                <div>

                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $feedback->user->name ?? 'Anonymous' }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        for
                                        {{ $feedback->therapist->name ?? 'Unknown Therapist' }}
                                    </p>

                                </div>

                            </div>


                            {{-- Review Rating --}}
                            <div class="flex items-center">

                                @for($i = 1; $i <= 5; $i++)

                                    @if($i <= $feedback->rating)

                                        <svg
                                            class="w-4 h-4 text-yellow-400"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>

                                    @else

                                        <svg
                                            class="w-4 h-4 text-gray-300"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l-1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>

                                    @endif

                                @endfor

                            </div>

                        </div>


                        {{-- Comment --}}
                        @if($feedback->comment)

                            <p class="text-sm text-gray-600">
                                {{ $feedback->comment }}
                            </p>

                        @endif


                        {{-- Date --}}
                        <p class="text-xs text-gray-400 mt-2">
                            {{ $feedback->created_at->format('M d, Y') }}
                        </p>

                    </div>

                @empty

                    <p class="text-sm text-gray-500 text-center py-8">
                        No reviews yet. Be the first to leave feedback!
                    </p>

                @endforelse

            </div>

        </div>

    </div>

</div>


</div>

@endsection
