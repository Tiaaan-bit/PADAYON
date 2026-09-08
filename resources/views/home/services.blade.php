
@extends('layouts.home')

@section('title', 'Padayon Massage Center - Services')

@section('content')

    <section class="relative bg-[#F4EDDB] py-20 px-4 overflow-hidden">

        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 opacity-10">
            <img
                src="{{ asset('images/bamboo.png') }}"
                class="w-72"
                alt="">
        </div>

        <div class="max-w-6xl mx-auto relative z-10">

            {{-- Hero Title --}}
            <div class="text-center mb-16">

                <h1
                    class="text-5xl md:text-6xl font-extrabold bg-[#849753] bg-clip-text text-transparent uppercase">

                    Our Services

                </h1>

                <p class="mt-5 text-black text-lg md:text-xl">

                    Magpahinga • Magpagaling • Magpadayon

                </p>

            </div>


            {{-- Services Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">


                {{-- ================================================= --}}
                {{-- BED MASSAGE --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

                    <div class="bg-[#849753] py-5">

                        <h2
                            class="text-3xl font-bold text-center text-white uppercase">

                            Bed Massage

                        </h2>

                    </div>


                    <div class="p-8 space-y-5">

                        {{-- 30 Minutes --}}
                        <div
                            class="flex justify-between items-center border-b pb-3">

                            <div>

                                <h3 class="text-xl font-semibold">
                                    Half Body
                                </h3>

                                <p class="text-gray-500 text-sm">
                                    30 Minutes
                                </p>

                            </div>

                            <span
                                class="text-[#6F4E37] font-bold text-2xl">

                                ₱300

                            </span>

                        </div>


                        {{-- 60 Minutes --}}
                        <div
                            class="flex justify-between items-center border-b pb-3">

                            <div>

                                <h3 class="text-xl font-semibold">
                                    Whole Body
                                </h3>

                                <p class="text-gray-500 text-sm">
                                    60 Minutes
                                </p>

                            </div>

                            <span
                                class="text-[#6F4E37] font-bold text-2xl">

                                ₱400

                            </span>

                        </div>


                        {{-- 90 Minutes --}}
                        <div
                            class="flex justify-between items-center border-b pb-3">

                            <div>

                                <h3 class="text-xl font-semibold">
                                    Whole Body
                                </h3>

                                <p class="text-gray-500 text-sm">
                                    90 Minutes
                                </p>

                            </div>

                            <span
                                class="text-[#6F4E37] font-bold text-2xl">

                                ₱550

                            </span>

                        </div>


                        {{-- 120 Minutes --}}
                        <div
                            class="flex justify-between items-center">

                            <div>

                                <h3 class="text-xl font-semibold">
                                    Whole Body
                                </h3>

                                <p class="text-gray-500 text-sm">
                                    120 Minutes
                                </p>

                            </div>

                            <span
                                class="text-[#6F4E37] font-bold text-2xl">

                                ₱750

                            </span>

                        </div>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- SITTING MASSAGE --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

                    <div class="bg-[#849753] py-5">

                        <h2
                            class="text-3xl font-bold text-center text-white uppercase">

                            Sitting Massage

                        </h2>

                    </div>


                    <div class="p-8 space-y-5">

                        {{-- 30 Minutes --}}
                        <div
                            class="flex justify-between items-center border-b pb-3">

                            <div>

                                <h3 class="text-xl font-semibold">
                                    Half Body
                                </h3>

                                <p class="text-gray-500 text-sm">
                                    30 Minutes
                                </p>

                            </div>

                            <span
                                class="text-[#6F4E37] font-bold text-2xl">

                                ₱250

                            </span>

                        </div>


                        {{-- 60 Minutes --}}
                        <div
                            class="flex justify-between items-center border-b pb-3">

                            <div>

                                <h3 class="text-xl font-semibold">
                                    Whole Body
                                </h3>

                                <p class="text-gray-500 text-sm">
                                    60 Minutes
                                </p>

                            </div>

                            <span
                                class="text-[#6F4E37] font-bold text-2xl">

                                ₱350

                            </span>

                        </div>


                        {{-- 90 Minutes --}}
                        <div
                            class="flex justify-between items-center border-b pb-3">

                            <div>

                                <h3 class="text-xl font-semibold">
                                    Whole Body
                                </h3>

                                <p class="text-gray-500 text-sm">
                                    90 Minutes
                                </p>

                            </div>

                            <span
                                class="text-[#6F4E37] font-bold text-2xl">

                                ₱500

                            </span>

                        </div>


                        {{-- 120 Minutes --}}
                        <div
                            class="flex justify-between items-center">

                            <div>

                                <h3 class="text-xl font-semibold">
                                    Whole Body
                                </h3>

                                <p class="text-gray-500 text-sm">
                                    120 Minutes
                                </p>

                            </div>

                            <span
                                class="text-[#6F4E37] font-bold text-2xl">

                                ₱700

                            </span>

                        </div>

                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- REFLEXOLOGY --}}
            {{-- ================================================= --}}

            <div
                class="mt-12 bg-white rounded-3xl shadow-2xl overflow-hidden">

                <div class="bg-[#849753] py-5">

                    <h2
                        class="text-3xl font-bold text-center text-white uppercase">

                        Reflexology & Massage Rates

                    </h2>

                </div>


                <div class="p-8 space-y-5">

                    {{-- 30 Minutes Reflexology --}}
                    <div
                        class="flex justify-between border-b pb-3">

                        <span class="font-semibold text-lg">

                            30 Minutes Reflexology

                        </span>

                        <span
                            class="text-[#6F4E37] font-bold text-xl">

                            ₱500

                        </span>

                    </div>


                    {{-- 60 Minutes Reflexology --}}
                    <div
                        class="flex justify-between border-b pb-3">

                        <span class="font-semibold text-lg">

                            60 Minutes Reflexology

                        </span>

                        <span
                            class="text-[#6F4E37] font-bold text-xl">

                            ₱950

                        </span>

                    </div>


                    {{-- Massage Combination --}}
                    <div
                        class="flex justify-between border-b pb-3">

                        <span class="font-semibold text-lg">

                            60 Minutes Normal Massage
                            + 30 Minutes Normal Massage

                        </span>

                        <span
                            class="text-[#6F4E37] font-bold text-xl">

                            ₱1,250

                        </span>

                    </div>


                    {{-- Massage + Reflexology --}}
                    <div
                        class="flex justify-between">

                        <span class="font-semibold text-lg">

                            60 Minutes Normal Massage
                            + 60 Minutes Reflexology

                        </span>

                        <span
                            class="text-[#6F4E37] font-bold text-xl">

                            ₱1,450

                        </span>

                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- ADD ONS --}}
            {{-- ================================================= --}}

            <div
                class="mt-12 bg-[#849753] rounded-3xl shadow-2xl p-10 text-center">

                <h2
                    class="text-4xl font-extrabold text-white uppercase">

                    Add Ons

                </h2>


                <p
                    class="text-white text-2xl mt-4 font-semibold">

                    Hotstone Massage

                </p>


                <p
                    class="text-white text-2xl mt-4 font-semibold">

                    30 Minutes

                </p>


                <p
                    class="text-5xl font-extrabold text-[#6F4E37] mt-5">

                    ₱350

                </p>

            </div>

        </div>

    </section>

@endsection
