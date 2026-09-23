@extends('layouts.home')

@section('title', 'Padayon Massage Center - Services')

@section('content')

    <section class="relative bg-[#F4EDDB] py-20 px-4 overflow-hidden">

        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 opacity-10">
            <img src="{{ asset('images/bamboo.png') }}" class="w-72" alt="">
        </div>

        <div class="max-w-6xl mx-auto relative z-10">

            {{-- Hero Title --}}
            <div class="text-center mb-16">

                <h1 class="text-5xl md:text-6xl font-extrabold bg-[#849753] bg-clip-text text-transparent uppercase">

                    Our Services

                </h1>

                <p class="mt-5 text-black text-lg md:text-xl">

                    Magpahinga • Magpagaling • Magpadayon

                </p>

            </div>


            {{-- Services Grid --}}

            <div class="overflow-x-auto rounded-3xl shadow-2xl">

                <table class="w-full bg-white border-collapse">

                    {{-- Table Header --}}
                    <thead>
                        <tr class="bg-[#849753] text-white">

                            <th class="px-8 py-6 text-left text-lg md:text-xl font-bold uppercase">
                                Service
                            </th>

                            <th class="px-8 py-6 text-left text-lg md:text-xl font-bold uppercase">
                                Description
                            </th>

                            <th class="px-8 py-6 text-center text-lg md:text-xl font-bold uppercase">
                                Price
                            </th>

                            <th class="px-8 py-6 text-center text-lg md:text-xl font-bold uppercase">
                                Duration
                            </th>

                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody>

                        @forelse ($services as $serviceName => $serviceItems)

                            @foreach ($serviceItems as $service)
                                <tr class="border-b border-gray-200 last:border-b-0">

                                    {{-- Service Category --}}
                                    <td class="px-8 py-6 align-middle">

                                        @if ($loop->first)
                                            <h2 class="text-lg md:text-xl font-bold text-[#6F4E37] uppercase">
                                                {{ $serviceName }}
                                            </h2>
                                        @endif

                                    </td>

                                    {{-- Description --}}
                                    <td class="px-8 py-6">

                                        <h3 class="text-lg md:text-xl font-semibold text-black">
                                            {{ $service->description }}
                                        </h3>

                                    </td>

                                    {{-- Price --}}
                                    <td class="px-8 py-6 text-center">

                                        <span class="text-xl md:text-2xl font-bold text-[#6F4E37] whitespace-nowrap">
                                            ₱{{ number_format((float) $service->price, 0) }}
                                        </span>

                                    </td>

                                    {{-- Duration --}}
                                    <td class="px-8 py-6 text-center">

                                        <span class="text-gray-500 text-base md:text-lg whitespace-nowrap">
                                            {{ $service->duration_minutes }} Minutes
                                        </span>

                                    </td>

                                </tr>
                            @endforeach

                        @empty

                            <tr>
                                <td colspan="4" class="px-8 py-16 text-center">
                                    <p class="text-gray-500 text-lg">
                                        No services are currently available.
                                    </p>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>



            {{-- ================================================= --}}
            {{-- ADD ONS --}}
            {{-- ================================================= --}}

            {{-- ADD-ONS --}}
            <div class="mt-12 overflow-x-auto rounded-3xl shadow-2xl">

                <table class="w-full bg-white border-collapse">

                    {{-- Add-ons Header --}}
                    <thead>
                        <tr class="bg-[#6F4E37] text-white">



                        </tr>

                        <tr class="bg-[#849753] text-white">

                            <th class="px-8 py-5 text-left text-lg font-bold uppercase">
                                Add-On
                            </th>

                            <th class="px-8 py-5 text-center text-lg font-bold uppercase">
                                Price
                            </th>

                            <th class="px-8 py-5 text-center text-lg font-bold uppercase">
                                Duration
                            </th>

                        </tr>
                    </thead>

                    {{-- Add-ons Body --}}
                    <tbody>

                        @forelse ($addOns as $addOn)
                            <tr class="border-b border-gray-200 last:border-b-0">

                                {{-- Add-On Name --}}
                                <td class="px-8 py-6">

                                    <h3 class="text-lg md:text-xl font-semibold text-black">
                                        {{ $addOn->name }}
                                    </h3>

                                </td>

                                {{-- Price --}}
                                <td class="px-8 py-6 text-center">

                                    <span class="text-xl md:text-2xl font-bold text-[#6F4E37] whitespace-nowrap">
                                        ₱{{ number_format((float) $addOn->price, 0) }}
                                    </span>

                                </td>

                                {{-- Duration --}}
                                <td class="px-8 py-6 text-center">

                                    <span class="text-gray-500 text-base md:text-lg whitespace-nowrap">
                                        {{ $addOn->duration_minutes }} Minutes
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="3" class="px-8 py-10 text-center">

                                    <p class="text-gray-500">
                                        No add-ons are currently available.
                                    </p>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </section>

@endsection
