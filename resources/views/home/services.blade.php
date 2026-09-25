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


            {{-- ================================================= --}}
            {{-- SERVICES --}}
            {{-- ================================================= --}}

            <div x-data="{
                selectedCategory: null
            }" class="space-y-4">

                @forelse ($services as $serviceName => $serviceItems)

                    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

                        {{-- SERVICE CATEGORY --}}
                        <button type="button"
                            @click="
                selectedCategory =
                    selectedCategory === @js($serviceName)
                        ? null
                        : @js($serviceName)
            "
                            class="w-full px-8 py-6
                   flex items-center justify-between
                   bg-[#849753] text-white
                   hover:bg-[#6F4E37]
                   transition duration-300">

                            <h2 class="text-xl md:text-2xl font-bold uppercase">
                                {{ $serviceName }}
                            </h2>

                            <svg class="w-6 h-6 transition-transform duration-300"
                                :class="selectedCategory === @js($serviceName) ?
                                    'rotate-180' :
                                    ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>

                        </button>


                        {{-- SERVICE ITEMS --}}
                        <div x-show="selectedCategory === @js($serviceName)" x-transition x-cloak>

                            <div class="overflow-x-auto">

                                <table class="w-full">

                                    {{-- TABLE HEADER --}}
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-200">


                                            <th class="px-8 py-4 text-left text-sm font-bold uppercase text-gray-600">
                                                Description
                                            </th>

                                            <th class="px-8 py-4 text-center text-sm font-bold uppercase text-gray-600">
                                                Price
                                            </th>

                                            <th class="px-8 py-4 text-center text-sm font-bold uppercase text-gray-600">
                                                Duration
                                            </th>

                                        </tr>
                                    </thead>


                                    {{-- TABLE BODY --}}
                                    <tbody>

                                        @foreach ($serviceItems as $service)
                                            <tr class="border-b border-gray-200 last:border-b-0">
  


                                                {{-- DESCRIPTION --}}
                                                <td class="px-8 py-6">

                                                    <p class="text-gray-700">
                                                        {{ $service->description }}
                                                    </p>

                                                </td>


                                                {{-- PRICE --}}
                                                <td class="px-8 py-6 text-center">

                                                    <span class="text-xl font-bold text-[#6F4E37] whitespace-nowrap">
                                                        ₱{{ number_format((float) $service->price, 0) }}
                                                    </span>

                                                </td>


                                                {{-- DURATION --}}
                                                <td class="px-8 py-6 text-center">

                                                    <span class="text-gray-500 whitespace-nowrap">
                                                        {{ $service->duration_minutes }} Minutes
                                                    </span>

                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="bg-white rounded-3xl shadow-2xl px-8 py-16 text-center">

                        <p class="text-gray-500 text-lg">
                            No services are currently available.
                        </p>

                    </div>

                @endforelse

            </div>



            {{-- ================================================= --}}
            {{-- ADD ONS --}}
            {{-- ================================================= --}}


            <div x-data="{
                showAddOns: false
            }" class="mt-12 bg-white rounded-3xl shadow-2xl overflow-hidden">

                {{-- ADD-ONS HEADER / BUTTON --}}
                <button type="button" @click="showAddOns = !showAddOns"
                    class="w-full px-8 py-6
           flex items-center justify-between
           bg-[#849753] text-white
           hover:bg-[#6F4E37]
           transition duration-300">

                    <h2 class="text-xl md:text-2xl font-bold uppercase">
                        Add-Ons
                    </h2>

                    <svg class="w-6 h-6 transition-transform duration-300" :class="showAddOns ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>

                </button>


                {{-- ADD-ONS TABLE --}}
                <div x-show="showAddOns" x-transition x-cloak>

                    <div class="overflow-x-auto">

                        <table class="w-full">

                            {{-- HEADER --}}
                            <thead>

                                <tr class="bg-gray-50 border-b border-gray-200">

                                    <th class="px-8 py-5 text-left text-sm font-bold uppercase text-gray-600">
                                        Add-On
                                    </th>

                                    <th class="px-8 py-5 text-center text-sm font-bold uppercase text-gray-600">
                                        Price
                                    </th>

                                    <th class="px-8 py-5 text-center text-sm font-bold uppercase text-gray-600">
                                        Duration
                                    </th>

                                </tr>

                            </thead>


                            {{-- BODY --}}
                            <tbody>

                                @forelse ($addOns as $addOn)
                                    <tr class="border-b border-gray-200 last:border-b-0">

                                        {{-- ADD-ON NAME --}}
                                        <td class="px-8 py-6">

                                            <h3 class="text-lg md:text-xl font-semibold text-black">
                                                {{ $addOn->name }}
                                            </h3>

                                        </td>


                                        {{-- PRICE --}}
                                        <td class="px-8 py-6 text-center">

                                            <span class="text-xl md:text-2xl font-bold text-[#6F4E37] whitespace-nowrap">
                                                ₱{{ number_format((float) $addOn->price, 0) }}
                                            </span>

                                        </td>


                                        {{-- DURATION --}}
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

            </div>

        </div>

    </section>

@endsection
