
@extends('layouts.home')

@section('title', 'Padayon Massage Center - About')

@section('content')

    <section
        class="flex flex-col md:flex-row items-center justify-center gap-10 max-md:px-4 pt-32 pb-40">

        {{-- About Image --}}
        <div
            class="relative shadow-2xl shadow-indigo-600/40 rounded-2xl overflow-hidden shrink-0">

            <img
                class="max-w-md w-full object-cover rounded-2xl"
                src="{{ asset('build/assets/images/About-Image-1.webp') }}"
                alt="About Padayon Massage Center">

        </div>


        {{-- About Content --}}
        <div class="text-sm text-slate-600 max-w-lg">

            <h1 class="text-xl uppercase font-semibold text-slate-700">
                What we do?
            </h1>

            <div class="w-24 h-0.75 rounded-full bg-[#849753]"></div>

            <p class="mt-8">
                Our web-based Blind Therapist Massage Appointment System is designed
                to connect clients with professional blind massage therapists through
                a simple, accessible, and convenient online platform.

                We aim to provide quality wellness services while creating employment
                opportunities and promoting the skills of visually impaired massage
                therapists.
            </p>

        </div>

    </section>

@endsection

