{{-- ============================================================= --}}
{{-- DESKTOP SIDEBAR --}}
{{-- ============================================================= --}}

<aside
    class="hidden lg:flex
           fixed top-0 left-0
           h-screen
           bg-[#849753]
           flex-col
           z-50
           transition-all duration-300"
    :class="sidebarOpen ? 'w-60' : 'w-20'">


    {{-- ========================================================= --}}
    {{-- SIDEBAR HEADER --}}
    {{-- ========================================================= --}}

    <div class="h-20
               flex items-center
               border-b border-white/10
               transition-all duration-300"
        :class="sidebarOpen
            ?
            'px-4 gap-3' :
            'justify-center px-2'">


        {{-- ===================================================== --}}
        {{-- HAMBURGER BUTTON --}}
        {{-- ===================================================== --}}

        <button type="button" @click="sidebarOpen = !sidebarOpen"
            class="w-10 h-10
                   shrink-0
                   flex items-center justify-center
                   rounded-xl
                   text-white
                   hover:bg-white/10
                   transition-all"
            aria-label="Toggle sidebar">

            {{-- Always Hamburger --}}
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />

            </svg>

        </button>


        {{-- ===================================================== --}}
        {{-- SIDEBAR LOGO + TEXT --}}
        {{-- ===================================================== --}}

        <div x-show="sidebarOpen" x-cloak class="flex items-center
                   gap-2
                   min-w-0">

            <img src="{{ asset('build/assets/images/logo.png') }}" alt="Padayon Massage Center Logo"
                class="h-10 w-10
                       object-contain
                       shrink-0">


            <div class="leading-tight min-w-0">

                <h1
                    class="text-white
                           font-bold
                           text-sm
                           truncate">
                    Padayon Massage Center
                </h1>

                <p
                    class="text-white/70
                           text-[10px]
                           leading-tight">
                    Blind Massage Specialists
                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- USER INFORMATION --}}
    {{-- ========================================================= --}}

    <div class="flex items-center
               border-b border-white/10
               py-4"
        :class="sidebarOpen
            ?
            'gap-3 px-5' :
            'justify-center px-2'">


        {{-- Avatar --}}
        <div
            class="w-9 h-9
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
        <div x-show="sidebarOpen" x-cloak class="overflow-hidden">

            <p
                class="text-white
                       text-sm
                       font-semibold
                       truncate">
                {{ auth()->user()->name }}
            </p>

            <p class="text-white/70
                       text-xs
                       truncate">
                {{ auth()->user()->email }}
            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- NAVIGATION --}}
    {{-- ========================================================= --}}

    <nav class="flex-1
               overflow-y-auto
               px-3
               py-4
               space-y-1">


        {{-- ===================================================== --}}
        {{-- DASHBOARD --}}
        {{-- ===================================================== --}}

        <a href="{{ route('user.dashboard') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('user.dashboard') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                <path d="M3 10.5L12 3l9 7.5" />

                <path d="M5 9.5V21h14V9.5" />

                <path d="M9 21v-6h6v6" />

            </svg>


            <span x-show="sidebarOpen" x-cloak class="truncate">
                Dashboard
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- BOOK APPOINTMENT --}}
        {{-- ===================================================== --}}

        <a href="{{ route('user.appointment') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('user.appointment*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                <rect x="3" y="4" width="18" height="17" rx="2" />

                <path d="M16 2v4M8 2v4M3 10h18" />

                <path d="M12 13v4M10 15h4" />

            </svg>


            <span x-show="sidebarOpen" x-cloak class="truncate">
                Book Appointment
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- APPOINTMENTS HISTORY --}}
        {{-- ===================================================== --}}

        <a href="{{ route('user.my-appointments') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('user.my-appointments*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                <path d="M4 5h16v15H4z" />

                <path d="M8 9h8M8 13h8M8 17h5" />

            </svg>


            <span x-show="sidebarOpen" x-cloak class="truncate">
                Appointments History
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- PAYMENTS HISTORY --}}
        {{-- ===================================================== --}}

        <a href="{{ route('user.payment-history') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('user.payment-history*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                <rect x="3" y="5" width="18" height="14" rx="2" />

                <path d="M3 10h18" />

                <path d="M7 15h4" />

            </svg>


            <span x-show="sidebarOpen" x-cloak class="truncate">
                Payments History
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- THERAPISTS --}}
        {{-- ===================================================== --}}

        <a href="{{ route('user.therapists.index') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('user.therapists.index*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                <circle cx="12" cy="8" r="3" />

                <path d="M5 21a7 7 0 0114 0" />

            </svg>


            <span x-show="sidebarOpen" x-cloak class="truncate">
                Therapists
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- DIVIDER --}}
        {{-- ===================================================== --}}

        <div class="my-3
                   border-t
                   border-white/10"></div>


        {{-- ===================================================== --}}
        {{-- NOTIFICATIONS --}}
        {{-- ===================================================== --}}

        <a href="{{ route('user.notifications') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('user.notifications*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                <path d="M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />

                <path d="M10 21h4" />

            </svg>


            <span x-show="sidebarOpen" x-cloak class="truncate">
                Notifications
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- PROFILE --}}
        {{-- ===================================================== --}}

        <a href="{{ route('user.profile') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('user.profile*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3" />
                <path
                    d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
            </svg>


            <span x-show="sidebarOpen" x-cloak class="truncate">
                Profile Settings
            </span>

        </a>

    </nav>


    {{-- ========================================================= --}}
    {{-- LOGOUT --}}
    {{-- ========================================================= --}}

    <div class="px-3
               py-4
               border-t
               border-white/10">

        <form method="POST" action="{{ route('logout') }}">

            @csrf

            <button type="submit"
                class="w-full
                       flex items-center
                       rounded-xl
                       text-sm
                       font-medium
                       bg-[#6F4E37]
                       text-white
                       hover:bg-red-500
                       transition-all"
                :class="sidebarOpen
                    ?
                    'gap-3 px-3 py-2.5' :
                    'justify-center px-2 py-3'">

                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">

                    <path d="M10 17l5-5-5-5" />

                    <path d="M15 12H3" />

                    <path d="M21 19V5a2 2 0 00-2-2h-6" />

                </svg>


                <span x-show="sidebarOpen" x-cloak>
                    Logout
                </span>

            </button>

        </form>

    </div>

</aside>


{{-- ============================================================= --}}
{{-- MOBILE BOTTOM NAVIGATION --}}
{{-- ============================================================= --}}

<div
    class="lg:hidden
           fixed bottom-0 left-0 right-0
           z-50
           bg-[#849753]
           border-t border-white/10">

    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#849753] border-t border-white/10">

        {{-- Primary 5 tabs --}}
        <div class="flex items-center justify-around px-1 py-2">

            {{-- Dashboard --}}
            <a href="{{ route('user.dashboard') }}"
                class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-all min-w-0
                        {{ request()->routeIs('user.dashboard') ? 'text-white' : 'text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">

                    <path d="M3 10.5L12 3l9 7.5" />

                    <path d="M5 9.5V21h14V9.5" />

                    <path d="M9 21v-6h6v6" />

                </svg>
                <span class="text-[10px] font-medium leading-tight">Home</span>
                @if (request()->routeIs('user.dashboard'))
                    <span class="w-1 h-1 rounded-full bg-[#6F4E37] mt-0.5"></span>
                @endif
            </a>

            {{-- Appointments --}}
            <a href="{{ route('user.my-appointments') }}"
                class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-all min-w-0
                        {{ request()->routeIs('user.my-appointments*') ? 'text-white' : 'text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">

                    <path d="M4 5h16v15H4z" />

                    <path d="M8 9h8M8 13h8M8 17h5" />

                </svg>
                <span class="text-[10px] font-medium leading-tight">Appts</span>
                @if (request()->routeIs('user.my-appointments*'))
                    <span class="w-1 h-1 rounded-full bg-[#6F4E37] mt-0.5"></span>
                @endif
            </a>

            {{-- Book --}}
            <a href="{{ route('user.appointment') }}"
                class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-all min-w-0
                        {{ request()->routeIs('user.appointment*') ? 'text-white' : 'text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">

                    <rect x="3" y="4" width="18" height="17" rx="2" />

                    <path d="M16 2v4M8 2v4M3 10h18" />

                    <path d="M12 13v4M10 15h4" />

                </svg>
                <span class="text-[10px] font-medium leading-tight">Book</span>
                @if (request()->routeIs('user.appointment*'))
                    <span class="w-1 h-1 rounded-full bg-[#6F4E37] mt-0.5"></span>
                @endif
            </a>

            {{-- Transactions --}}
            <a href="{{ route('user.payment-history') }}"
                class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-all min-w-0
                        {{ request()->routeIs('user.payment-history*') ? 'text-white' : 'text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">

                    <rect x="3" y="5" width="18" height="14" rx="2" />

                    <path d="M3 10h18" />

                    <path d="M7 15h4" />

                </svg>
                <span class="text-[10px] font-medium leading-tight">Pay</span>
                @if (request()->routeIs('user.payment-history*'))
                    <span class="w-1 h-1 rounded-full bg-[#6F4E37] mt-0.5"></span>
                @endif
            </a>

            {{-- More (opens drawer) --}}
            <button onclick="toggleMoreDrawer()"
                class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-white transition-all min-w-0">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <circle cx="5" cy="12" r="1" fill="currentColor" />
                    <circle cx="12" cy="12" r="1" fill="currentColor" />
                    <circle cx="19" cy="12" r="1" fill="currentColor" />
                </svg>
                <span class="text-[10px] font-medium leading-tight">More</span>
            </button>

        </div>
    </nav>

    {{-- ── More Drawer (slides up from bottom on mobile) ── --}}
    <div id="more-drawer" class="lg:hidden fixed inset-0 z-60 pointer-events-none">

        {{-- Backdrop --}}
        <div id="drawer-backdrop" class="absolute inset-0 bg-black/50 opacity-0 transition-opacity duration-300"
            onclick="toggleMoreDrawer()">
        </div>

        {{-- Sheet --}}
        <div id="drawer-sheet"
            class="absolute bottom-0 left-0 right-0 bg-[#849753] rounded-t-2xl translate-y-full transition-transform duration-300 pb-6">

            {{-- Handle --}}
            <div class="flex justify-center pt-3 pb-2">
                <div class="w-10 h-1 rounded-full bg-white/20"></div>
            </div>

            {{-- Admin info --}}
            <div class="flex items-center gap-3 px-5 py-3 border-b border-white/10 mb-2">
                <div class="w-10 h-10 rounded-full bg-[#6F4E37] flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-white text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-white text-xs">{{ auth()->user()->email }}</p>
                </div>
            </div>


            <a href="{{ route('user.therapists.index') }}" onclick="toggleMoreDrawer()"
                class="inline-flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:bg-white/10 hover:text-white transition-all w-full">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">

                    <circle cx="12" cy="8" r="3" />

                    <path d="M5 21a7 7 0 0114 0" />

                </svg>
                Therapists
            </a>



            <a href="{{ route('user.profile') }}" onclick="toggleMoreDrawer()"
                class="inline-flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:bg-white/10 hover:text-white transition-all w-full">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                </svg>
                Profile Settings
            </a>



            <div class="border-t border-white/10 my-2"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium bg-[#6F4E37] text-white hover:bg-red-500/10 hover:text-red-300 transition-all">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
                    </svg>
                    Logout
                </button>
            </form>

        </div>
    </div>
</div>

{{-- ── More drawer script ── --}}
<script>
    function toggleMoreDrawer() {
        const drawer = document.getElementById('more-drawer');
        const backdrop = document.getElementById('drawer-backdrop');
        const sheet = document.getElementById('drawer-sheet');
        const isOpen = !sheet.classList.contains('translate-y-full');

        if (isOpen) {
            sheet.classList.add('translate-y-full');
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            setTimeout(() => drawer.classList.add('pointer-events-none'), 300);
        } else {
            drawer.classList.remove('pointer-events-none');
            requestAnimationFrame(() => {
                sheet.classList.remove('translate-y-full');
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
            });
        }
    }
</script>
