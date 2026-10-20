@extends('layouts.home')

@section('title', 'Padayon Massage Center - About')

@section('content')

    <section class="flex flex-col md:flex-row items-center justify-center gap-10 max-md:px-4 pt-32 pb-40">

        {{-- About Image --}}
        <div class="relative shadow-2xl shadow-indigo-600/40 rounded-2xl overflow-hidden shrink-0">

            @if ($aboutPageSetting->image)
                <img src="{{ asset('storage/' . $aboutPageSetting->image) }}" alt="About Padayon Massage Center"
                    class="w-full max-w-md h-auto object-cover">
            @else
                <div class="w-full max-w-md h-64 bg-stone-200
                    flex items-center justify-center">
                    <span class="text-stone-500">
                        About image not available
                    </span>
                </div>
            @endif

        </div>

        {{-- About Content --}}
        <div class="text-sm text-slate-600 max-w-lg">

            <h1 class="text-xl uppercase font-semibold text-slate-700">
                {{ $aboutPageSetting->heading }}
            </h1>

            <div class="w-24 h-1 rounded-full bg-[#849753]"></div>

            <p class="whitespace-pre-line">
                {{ $aboutPageSetting->description }}
            </p>

        </div>

    </section>

@endsection
