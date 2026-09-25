<header class="sticky top-0 z-40
                   bg-[#D6BB9E]
                   ">

    <div
        class="flex items-center justify-between
                       h-20
                       px-4 sm:px-6 lg:px-8">


        {{-- ================================================= --}}
        {{-- LOGO + BRAND --}}
        {{-- ================================================= --}}
        <a href="{{ route('user.dashboard') }}" wire:navigate class="flex items-center gap-3 min-w-0">

            {{-- Logo --}}
            <img src="{{ asset('build/assets/images/logo.png') }}" alt="Padayon Massage Center Logo"
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
        <div class="flex items-center
                           gap-3 sm:gap-4">


            {{-- ============================================= --}}
            {{-- NOTIFICATIONS --}}
            {{-- ============================================= --}}
            <a href="{{ route('user.notifications') }}" wire:navigate
                class="relative
                               flex items-center justify-center
                               w-10 h-10
                               rounded-full
                               text-[#6F4E37]
                               hover:bg-[#849753]/10
                               transition-all"
                aria-label="Notifications">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                    <path d="M12 22a2 2 0 002-2h-4a2 2 0 002 2z" />

                    <path d="M18 16H6l1.2-1.2A2 2 0 008 13.4V10a4 4 0 118 0v3.4a2 2 0 00.8 1.4L18 16z" />

                </svg>


                {{-- Unread Notification Indicator --}}
                @if (auth()->user()->unreadNotifications->count() > 0)
                    <span
                        class="absolute
                                       top-1.5 right-1.5
                                       w-2 h-2
                                       bg-red-500
                                       rounded-full"></span>
                @endif

            </a>


            {{-- ============================================= --}}
            {{-- USER PROFILE --}}
            {{-- ============================================= --}}
            <a href="{{ route('user.profile') }}" wire:navigate
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

                    <p class="text-xs
                                       text-gray-500">
                        My Profile
                    </p>

                </div>

            </a>

        </div>

    </div>

</header>
