@extends('layouts.home')

@section('title', 'Padayon Massage Center - Home')


@section('content')

    {{-- ========================================================= --}}
    {{-- HERO SECTION --}}
    {{-- ========================================================= --}}

    <section class="px-4 py-16 pt-30    ">

        <div class="flex flex-col lg:flex-row items-center gap-12 max-w-7xl mx-auto">

            {{-- TEXT --}}
            <div class="flex-1 lg:max-w-2xl lg:pr-12 order-1 lg:order-2">

                <h2
                    class="text-4xl sm:text-5xl lg:text-[48px] xl:text-[56px] font-bold bg-[#849753] bg-clip-text text-transparent mt-6 leading-tight">

                    {{ $introSection?->heading }}

                </h2>


                <p
                    class="font-bold max-w-xl mx-auto lg:mx-0 mt-5 text-lg lg:text-xl text-black/70 leading-relaxed px-4 lg:px-0">

                    {{ $introSection?->description}}

                </p>


                {{-- Get Started --}}
                <a href="{{ route('login') }}"
                    class="inline-block px-8 py-4 mt-5 text-base lg:text-lg bg-[#849753] rounded-full text-slate-50 hover:bg-[#2F2420] font-medium shadow-lg hover:shadow-xl transition-all duration-300">

                    {{ $introSection?->button_text }}

                </a>

            </div>


            <div class="flex-1 lg:max-w-2xl order-2 lg:order-1 w-full pt-20">

                <div style="position: relative; top: -20px;"
                    class="w-full max-w-5xl mx-auto lg:mx-0 overflow-hidden rounded-2xl">

                    <div class="flex w-full transition-transform duration-500 ease-in-out" id="slider">

                        @for ($slot = 1; $slot <= 5; $slot++)
                            @php
                                $image = $homeSliderImages->get($slot);

                                $imageUrl = $image
                                    ? asset('storage/' . $image->image_path)
                                    : asset("build/assets/images/image-{$slot}.webp");
                            @endphp

                            <div class="w-full shrink-0 aspect-video overflow-hidden">
                                <img src="{{ $imageUrl }}" class="w-full h-full object-cover object-center rounded-2xl"
                                    alt="Padayon Massage Center"
                                    @if ($slot === 1) fetchpriority="high"
                                    @else
                                        loading="lazy" @endif>
                            </div>
                        @endfor

                    </div>

                </div>



                {{-- SLIDER DOTS --}}
                <div class="flex items-center justify-center space-x-2 mt-6" id="dot-indicators">

                    <span
                        class="dot-indicator w-3 h-3 bg-black/20 rounded-full cursor-pointer hover:bg-black/50 transition-all duration-300"
                        data-slide="0">
                    </span>

                    <span
                        class="dot-indicator w-3 h-3 bg-black/20 rounded-full cursor-pointer hover:bg-black/50 transition-all duration-300"
                        data-slide="1">
                    </span>

                    <span
                        class="dot-indicator w-3 h-3 bg-black/20 rounded-full cursor-pointer hover:bg-black/50 transition-all duration-300"
                        data-slide="2">
                    </span>

                    <span
                        class="dot-indicator w-3 h-3 bg-black/20 rounded-full cursor-pointer hover:bg-black/50 transition-all duration-300"
                        data-slide="3">
                    </span>

                    <span
                        class="dot-indicator w-3 h-3 bg-black/20 rounded-full cursor-pointer hover:bg-black/50 transition-all duration-300"
                        data-slide="4">
                    </span>

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- THERAPISTS SECTION --}}
    {{-- ========================================================= --}}

    <section class="flex items-center px-6 py-16 text-black bg-transparent md:px-16 lg:px-24 xl:px-32 pt-32">
        <div class="grid items-center w-full grid-cols-1 gap-12 mx-auto max-w-7xl lg:grid-cols-2 lg:gap-16">


            {{-- LEFT CONTENT --}}
            <div class="flex flex-col items-center gap-6 lg:items-start">

                <h2
                    class="max-w-lg text-4xl md:text-5xl leading-tight text-center lg:text-left font-bold bg-[#849753] bg-clip-text text-transparent">

                    {{ $heroSection?->heading}}

                </h2>


                <p class="font-bold max-w-md text-xl text-center text-black lg:text-left">

                    {{ $heroSection?->description}}

                </p>

            </div>


            {{-- RIGHT IMAGE GALLERY --}}
            <div x-data="{ images: @js($therapists->filter(fn($therapist) => $therapist->image)->map(fn($therapist) => ['url' => asset('storage/' . $therapist->image), 'alt' => 'Massage Therapist'])->values()), }">

                <div class="grid max-w-md grid-cols-3 gap-4 mx-auto lg:mx-0">
                    <template x-for="(image, index) in images" :key="image.url">
                        <img :src="image.url" :alt="image.alt"
                            class="w-full h-42 rounded-2xl object-cover transition transform hover:-translate-y-1"
                            :class="{
                                'mt-12': index % 3 === 0,
                                'mt-8': index % 3 === 2
                            }">
                    </template>
                </div>
            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- WHY CHOOSE US SECTION --}}
    {{-- ========================================================= --}}

    <section class="py-24 lg:py-32 bg-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


            {{-- HEADER --}}
            <div class="text-center mb-20 lg:mb-24">

                <h1 class="mb-8 text-4xl font-bold leading-tight text-[#849753] md:text-5xl lg:text-6xl">
                    {{ $whyChooseSection?->heading ?? 'Why Choose Padayon?' }}
                </h1>

            </div>


            {{-- TOP 3 BOXES --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 mb-16 lg:mb-20">


                {{-- BOX 1 --}}
                <div
                    class="group bg-white/80 backdrop-blur-sm hover:bg-white rounded-2xl p-6 lg:p-8 border border-slate-100/50 hover:border-[#849753]/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden text-center">

                    <h3 class="text-xl font-bold leading-tight text-slate-900 lg:text-2xl">
                        {{ $whyChooseBoxes['why_choose_box_1']?->heading
                            ?? 'Authentic Filipino healing touch' }}
                    </h3>

                </div>


                {{-- BOX 2 --}}
                <div
                    class="group bg-white/80 backdrop-blur-sm hover:bg-white rounded-2xl p-6 lg:p-8 border border-slate-100/50 hover:border-[#849753]/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden text-center">

                    <h3 class="text-xl font-bold leading-tight text-slate-900 lg:text-2xl">
                        {{ $whyChooseBoxes['why_choose_box_2']?->heading
                            ?? 'Authentic Filipino healing touch' }}
                    </h3>

                </div>


                {{-- BOX 3 --}}
                <div
                    class="group bg-white/80 backdrop-blur-sm hover:bg-white rounded-2xl p-6 lg:p-8 border border-slate-100/50 hover:border-[#849753]/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden text-center">

                    <h3 class="text-xl font-bold leading-tight text-slate-900 lg:text-2xl">
                        {{ $whyChooseBoxes['why_choose_box_3']?->heading
                            ?? 'Authentic Filipino healing touch' }}
                    </h3>

                </div>

            </div>


            {{-- BOTTOM 2 BOXES --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">


                {{-- BOX 4 --}}
                <div
                    class="group bg-white/80 backdrop-blur-sm hover:bg-white rounded-2xl p-6 lg:p-8 border border-slate-100/50 hover:border-[#849753]/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden text-center">

                    <h3 class="text-xl font-bold leading-tight text-slate-900 lg:text-2xl">
                        {{ $whyChooseBoxes['why_choose_box_4']?->heading
                            ?? 'Authentic Filipino healing touch' }}
                    </h3>

                </div>


                {{-- BOX 5 --}}
                <div
                    class="group bg-white/80 backdrop-blur-sm hover:bg-white rounded-2xl p-6 lg:p-8 border border-slate-100/50 hover:border-[#849753]/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden text-center">

                    <h3 class="text-xl font-bold leading-tight text-slate-900 lg:text-2xl">
                        {{ $whyChooseBoxes['why_choose_box_5']?->heading
                            ?? 'Authentic Filipino healing touch' }}
                    </h3>

                </div>

            </div>

        </div>

    </section>

@endsection


{{-- ========================================================= --}}
{{-- HOME PAGE JAVASCRIPT --}}
{{-- ========================================================= --}}

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const slider = document.getElementById('slider');

            const dots = document.querySelectorAll(
                '#dot-indicators .dot-indicator'
            );

            if (!slider || dots.length === 0) {
                return;
            }

            let currentSlide = 0;

            const totalSlides = dots.length;

            let autoSlideInterval;


            // Go to specific slide
            function goToSlide(index) {

                currentSlide = index;

                const translateX = -index * 100;

                slider.style.transform =
                    `translateX(${translateX}%)`;


                // Update active dot
                dots.forEach((dot, i) => {

                    if (i === index) {

                        dot.classList.add(
                            'bg-black',
                            'scale-125'
                        );

                    } else {

                        dot.classList.remove(
                            'bg-black',
                            'scale-125'
                        );

                    }

                });

            }


            // Dot click events
            dots.forEach((dot, index) => {

                dot.addEventListener('click', () => {

                    goToSlide(index);

                    resetAutoSlide();

                });

            });


            // Start automatic slider
            function startAutoSlide() {

                autoSlideInterval = setInterval(() => {

                    currentSlide =
                        (currentSlide + 1) % totalSlides;

                    goToSlide(currentSlide);

                }, 3000);

            }


            // Reset automatic slider
            function resetAutoSlide() {

                clearInterval(autoSlideInterval);

                startAutoSlide();

            }


            // Pause slider when mouse enters
            const sliderContainer =
                slider.closest('.relative');


            if (sliderContainer) {

                sliderContainer.addEventListener(
                    'mouseenter',
                    () => {
                        clearInterval(autoSlideInterval);
                    }
                );


                sliderContainer.addEventListener(
                    'mouseleave',
                    () => {
                        startAutoSlide();
                    }
                );

            }


            // Initialize slider
            goToSlide(currentSlide);

            startAutoSlide();

        });
    </script>
@endpush
