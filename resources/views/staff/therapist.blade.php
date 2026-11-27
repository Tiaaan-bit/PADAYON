@extends('layouts.staff')

@section('title', 'Padayon Massage Center - Therapists')


@section('content')

    <div class=" min-h-screen flex flex-col ">

        <div class="flex-1 p-4 sm:p-6">



            {{-- ========================================================= --}}
            {{-- PAGE HEADER --}}
            {{-- ========================================================= --}}

            <div class="mb-6">

                <h2 class="text-xl font-bold text-gray-800">
                    Manage Therapists
                </h2>

            </div>

            {{-- ========================================================= --}}
            {{-- FILTER --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 mb-6">

                <form method="GET" action="{{ route('staff.therapists') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- Search --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Search Name
                        </label>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by therapist name"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                    </div>


                    {{-- Status --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status
                        </label>

                        <select name="status"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">
                                All Status
                            </option>

                            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>
                                Available
                            </option>

                            <option value="unavailable" {{ request('status') === 'unavailable' ? 'selected' : '' }}>
                                Unavailable
                            </option>

                        </select>

                    </div>


                    {{-- Specialty --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Specialty
                        </label>

                        <input type="text" name="specialty" value="{{ request('specialty') }}"
                            placeholder="Search by specialty"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                    </div>


                    {{-- Buttons --}}
                    <div class="flex items-center gap-3">

                        <button type="submit"
                            class="rounded-lg bg-[#849753] px-6 py-2 text-white hover:bg-[#6F4E37] text-sm w-40">
                            Filter
                        </button>

                        <a href="{{ route('staff.therapists') }}"
                            class="rounded-lg border border-gray-300 px-6 py-2 text-sm text-gray-700 hover:bg-gray-50 w-40 text-center">
                            Reset
                        </a>

                    </div>

                </form>

            </div>


            {{-- ========================================================= --}}
            {{-- THERAPISTS --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5">
                {{-- Empty --}}
                @if ($therapists->isEmpty())

                    <div class="py-16 text-center text-gray-400">

                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>

                        <p class="text-sm">
                            No therapists found.
                        </p>

                    </div>
                @else
                    {{-- ================================================= --}}
                    {{-- THERAPIST CARDS --}}
                    {{-- ================================================= --}}

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                        @foreach ($therapists as $therapist)
                            <div
                                class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition">

                                {{-- Image --}}
                                <div class="relative h-60 bg-gray-100">

                                    @if ($therapist->image)
                                        <img src="{{ asset('storage/' . $therapist->image) }}" alt="{{ $therapist->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">

                                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor"
                                                stroke-width="1.5" viewBox="0 0 24 24">
                                                <path
                                                    d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />
                                            </svg>

                                        </div>
                                    @endif

                                </div>


                                {{-- Card Content --}}
                                <div class="p-4 sm:p-5">

                                    <div class="flex items-start justify-between gap-3 mb-3">

                                        <div>

                                            <h4 class="font-semibold text-gray-800 text-lg">
                                                {{ $therapist->name }}
                                            </h4>

                                            <p class="text-sm text-[#849753] font-medium">
                                                {{ $therapist->specialty }}
                                            </p>

                                        </div>


                                        {{-- Status Toggle --}}
                                        <div class="flex flex-col items-end gap-2">

                                            <span
                                                class="text-xs font-semibold {{ $therapist->status === 'available' ? 'text-green-600' : 'text-gray-500' }}">

                                                {{ ucfirst($therapist->status) }}

                                            </span>

                                            <form action="{{ route('staff.therapist.update', $therapist->id) }}"
                                                method="POST" onsubmit="this.querySelector('button').disabled = true;">

                                                @csrf
                                                @method('PUT')

                                                <input type="hidden" name="status"
                                                    value="{{ $therapist->status === 'available' ? 'unavailable' : 'available' }}">

                                                <button type="submit" aria-label="Toggle therapist availability"
                                                    title="Change availability"
                                                    class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 {{ $therapist->status === 'available' ? 'bg-green-500' : 'bg-gray-300' }}">

                                                    <span
                                                        class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ $therapist->status === 'available' ? 'translate-x-6' : 'translate-x-1' }}">
                                                    </span>

                                                </button>

                                            </form>

                                        </div>

                                    </div>


                                    {{-- Description --}}
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">

                                        {{ $therapist->description ?? 'No description available.' }}

                                    </p>

                                </div>

                            </div>
                        @endforeach

                    </div>

                    {{-- ================================================= --}}
                    {{-- PAGINATION --}}
                    {{-- ================================================= --}}

                    @if ($therapists->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100 pagination">

                            {{ $therapists->links() }}

                        </div>
                    @endif

                @endif

            </div>
        </div>

    </div>

@endsection
