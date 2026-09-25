<header class="sticky top-0 z-40
                   bg-[#D6BB9E]">

    <div
        class="flex items-center justify-between
                       h-20
                       px-4 sm:px-6 lg:px-8">

        {{-- ================================================= --}}
        {{-- LOGO + BRAND --}}
        {{-- ================================================= --}}

        <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 min-w-0">

            {{-- Logo --}}
            <img src="{{ asset('images/logo.webp') }}" alt="Padayon Massage Center Logo"
                class="h-12 w-12 object-contain shrink-0">

            {{-- Brand Text --}}
            <div class="leading-tight min-w-0">

                <h1
                    class="font-semibold
                                   text-[#2F2420]
                                   text-lg
                                   truncate">
                    Padayon Massage Center
                </h1>

                <p
                    class="text-xs
                                   text-gray-500
                                   truncate">
                    Blind Massage Specialists
                </p>

            </div>

        </a>


        {{-- ================================================= --}}
        {{-- HEADER RIGHT SIDE --}}
        {{-- ================================================= --}}

        <div class="flex items-center gap-3 sm:gap-4">

            {{-- ============================================= --}}
            {{-- ADMIN PROFILE --}}
            {{-- ============================================= --}}
            <div
                class="flex items-center
                               gap-3
                               pl-3
                               border-l border-gray-200">

                {{-- Avatar --}}
                <div
                    class="w-10 h-10
                                   rounded-full
                                   bg-[#6F4E37]
                                   flex items-center justify-center
                                   text-white
                                   font-bold
                                   text-sm
                                   shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>


                {{-- User Information --}}
                <div class="hidden sm:block">

                    <p
                        class="text-sm
                                       font-semibold
                                       text-gray-800">
                        {{ auth()->user()->name }}
                    </p>

                </div>

            </div>

        </div>

    </div>

</header>
