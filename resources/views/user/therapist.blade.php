@extends('layouts.user')

@section('title', 'Padayon Massage Center - Therapists')

@section('content')

    <div class="min-h-screen  py-10" x-data="{
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
            {{-- SUCCESS MESSAGE --}}
            {{-- ========================================================= --}}

            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif


            {{-- ========================================================= --}}
            {{-- ERROR MESSAGE --}}
            {{-- ========================================================= --}}

            @if ($errors->has('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                    {{ $errors->first('error') }}
                </div>
            @endif


            {{-- ========================================================= --}}
            {{-- THERAPIST GRID --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                @forelse($therapists as $therapist)

                    @php
                        $rating = round((float) ($therapist->feedbacks_avg_rating ?? 0));
                    @endphp

                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">

                        {{-- Therapist Image --}}
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


                        {{-- Therapist Information --}}
                        <div class="p-5">

                            <h2 class="text-xl font-bold text-gray-800">
                                {{ $therapist->name }}
                            </h2>

                            @if ($therapist->specialty)
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $therapist->specialty }}
                                </p>
                            @endif


                            {{-- Rating --}}
                            <div class="mt-3 flex items-center gap-2">

                                <div class="flex items-center">

                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5
                                            {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
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


                            {{-- Description --}}
                            @if ($therapist->description)
                                <p class="mt-4 text-sm text-gray-600 line-clamp-3">
                                    {{ $therapist->description }}
                                </p>
                            @endif


                            {{-- Feedback Button --}}
                            @php
                                $userFeedback = $userFeedbacks->get($therapist->id);

                                $hasAppointment = $confirmedTherapistIds->contains($therapist->id);

                                $canLeaveFeedback = $hasAppointment && !$userFeedback;
                            @endphp


                            @if ($canLeaveFeedback)
                                <button type="button"
                                    @click="openFeedback({
                                    id: {{ $therapist->id }},
                                    name: {{ \Illuminate\Support\Js::from($therapist->name) }},
                                    specialty: {{ \Illuminate\Support\Js::from($therapist->specialty) }},
                                    image: {{ \Illuminate\Support\Js::from($therapist->image ? asset('storage/' . $therapist->image) : null) }},
                                    rating: {{ (float) ($therapist->feedbacks_avg_rating ?? 0) }},
                                    feedbackCount: {{ $therapist->feedbacks_count }}
                                })"
                                    class="mt-5 w-full rounded-lg bg-[#849753] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6F4E37]">
                                    Leave Feedback
                                </button>
                            @elseif($userFeedback)
                                <div
                                    class="mt-5 rounded-lg bg-green-50 px-4 py-2.5 text-center text-sm font-medium text-green-700">
                                    You already reviewed this therapist.
                                </div>
                            @else
                                <div class="mt-5 rounded-lg bg-gray-50 px-4 py-2.5 text-center text-sm text-gray-500">
                                    Book an appointment to leave feedback.
                                </div>
                            @endif

                        </div>

                    </div>

                @empty

                    <div class="col-span-full py-16 text-center">
                        <p class="text-gray-500">
                            No therapists are currently available.
                        </p>
                    </div>
                @endforelse

            </div>


            {{-- ========================================================= --}}
            {{-- PAGINATION --}}
            {{-- ========================================================= --}}

            @if ($therapists->hasPages())
                <div class="mt-8">
                    {{ $therapists->appends(request()->except('therapists_page'))->links() }}
                </div>
            @endif


            {{-- ========================================================= --}}
            {{-- ALL REVIEWS --}}
            {{-- ========================================================= --}}

            <section class="mt-16">

                <div class="mb-6">

                    <h2 class="text-2xl font-bold text-gray-800">
                        All Reviews
                    </h2>
                </div>


                <div class="space-y-4">

                    @forelse($allFeedbacks as $feedback)

                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">

                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                                <div class="flex items-start gap-4">

                                    {{-- User Avatar --}}
                                    <div
                                        class="w-11 h-11 rounded-full bg-[#849753] text-white flex items-center justify-center font-semibold shrink-0">
                                        {{ strtoupper(substr($feedback->user->name ?? 'U', 0, 1)) }}
                                    </div>


                                    <div>

                                        <h3 class="font-semibold text-gray-800">
                                            {{ $feedback->user->name ?? 'Anonymous' }}
                                        </h3>

                                        @if ($feedback->therapist)
                                            <p class="text-sm text-gray-500">
                                                Reviewed
                                                <span class="font-medium text-[#6F4E37]">
                                                    {{ $feedback->therapist->name }}
                                                </span>
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                {{-- Rating --}}
                                <div class="flex items-center">

                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4
                                            {{ $i <= (int) $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor

                                </div>

                            </div>


                            @if ($feedback->comment)
                                <p class="mt-4 text-gray-600 leading-relaxed">
                                    {{ $feedback->comment }}
                                </p>
                            @endif


                            <p class="mt-3 text-xs text-gray-400">
                                {{ $feedback->created_at->format('M d, Y') }}
                            </p>

                        </div>

                    @empty

                        <div class="bg-white rounded-xl border border-gray-100 p-10 text-center">
                            <p class="text-gray-500">
                                No reviews yet.
                            </p>
                        </div>

                    @endforelse

                </div>

            </section>

            @if ($therapists->hasPages())
                <div class="mt-8">
                    {{ $allFeedbacks->appends(request()->except('reviews_page'))->links() }}
                </div>
            @endif

        </div>


        {{-- ============================================================= --}}
        {{-- FEEDBACK MODAL --}}
        {{-- ============================================================= --}}

        <div x-show="showFeedbackModal" x-cloak x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto"
            @keydown.escape.window="closeFeedback()">

            {{-- Background --}}
            <div class="fixed inset-0 bg-black/50" @click="closeFeedback()"></div>


            {{-- Modal Container --}}
            <div class="relative min-h-screen flex items-center justify-center p-4">

                <div x-show="showFeedbackModal" x-transition @click.stop
                    class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl">

                    {{-- ================================================= --}}
                    {{-- MODAL HEADER --}}
                    {{-- ================================================= --}}

                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">

                        <h2 class="text-xl font-bold text-gray-800">
                            Leave Feedback
                        </h2>

                        <button type="button" @click="closeFeedback()"
                            class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600" aria-label="Close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                    </div>


                    {{-- ================================================= --}}
                    {{-- SELECTED THERAPIST --}}
                    {{-- ================================================= --}}

                    <div class="px-6 pt-6">

                        <div class="flex items-center gap-4">

                            {{-- Image --}}
                            <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100 shrink-0">

                                <template x-if="selectedTherapist?.image">

                                    <img :src="selectedTherapist?.image" :alt="selectedTherapist?.name"
                                        class="w-full h-full object-cover">

                                </template>

                                <template x-if="!selectedTherapist?.image">

                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>

                                </template>

                            </div>


                            <div>

                                <h3 class="text-lg font-bold text-gray-800" x-text="selectedTherapist?.name"></h3>

                                <p class="text-sm text-gray-500" x-text="selectedTherapist?.specialty"></p>

                            </div>

                        </div>


                        {{-- Current Average Rating --}}
                        <div class="mt-4 flex items-center gap-2">

                            <div class="flex">

                                <template x-for="i in 5" :key="i">

                                    <svg class="w-4 h-4"
                                        :class="i <= Math.round(
                                                selectedTherapist?.rating ?? 0
                                            ) ?
                                            'text-yellow-400' :
                                            'text-gray-300'"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>

                                </template>

                            </div>

                            <span class="text-sm text-gray-500"
                                x-text="Number(selectedTherapist?.rating ?? 0).toFixed(1)"></span>

                            <span class="text-xs text-gray-400"
                                x-text="'(' + (selectedTherapist?.feedbackCount ?? 0) + ' reviews)'"></span>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- FEEDBACK FORM --}}
                    {{-- ================================================= --}}

                    <form method="POST"
                        :action="selectedTherapist
                            ?
                            '{{ url('/therapists') }}/' +
                            selectedTherapist.id +
                            '/feedback' :
                            '#'"
                        class="p-6" x-data="{ rating: 0 }">

                        @csrf


                        {{-- ============================================= --}}
                        {{-- RATING --}}
                        {{-- ============================================= --}}

                        <div class="mb-5">

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Your Rating
                            </label>


                            {{-- Hidden actual form value --}}
                            <input type="hidden" name="rating" x-model="rating" required>


                            {{-- Stars --}}
                            <div class="flex items-center gap-2">

                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}"
                                        class="rounded focus:outline-none focus:ring-2 focus:ring-yellow-300"
                                        aria-label="Rate {{ $i }} stars">

                                        <svg class="w-8 h-8 transition-colors duration-150"
                                            :class="rating >= {{ $i }} ?
                                                'text-yellow-400' :
                                                'text-gray-300'"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>

                                    </button>
                                @endfor

                            </div>


                            {{-- Rating text --}}
                            <p x-show="rating > 0"
                                x-text="
                                rating +
                                (rating === 1 ? ' star' : ' stars')
                            "
                                class="mt-2 text-xs text-gray-500"></p>

                        </div>


                        {{-- ============================================= --}}
                        {{-- COMMENT --}}
                        {{-- ============================================= --}}

                        <div class="mb-5">

                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                Your Comment
                            </label>

                            <textarea id="comment" name="comment" rows="5" maxlength="1000"
                                placeholder="Tell us about your experience..."
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:border-[#849753] focus:ring-[#849753]"></textarea>

                            <p class="mt-1 text-xs text-gray-400">
                                Maximum 1000 characters.
                            </p>

                        </div>


                        {{-- ============================================= --}}
                        {{-- BUTTONS --}}
                        {{-- ============================================= --}}

                        <div class="flex gap-3">

                            <button type="button" @click="closeFeedback()"
                                class="flex-1 rounded-lg border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>

                            <button type="submit" :disabled="rating === 0"
                                :class="rating === 0 ?
                                    'bg-gray-300 cursor-not-allowed' :
                                    'bg-[#849753] hover:bg-[#6F4E37]'"
                                class="flex-1 rounded-lg px-4 py-3 text-sm font-semibold text-white transition">
                                Submit Feedback
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection
