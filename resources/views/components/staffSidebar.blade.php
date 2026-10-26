{{-- ============================================================= --}}
{{-- DESKTOP SIDEBAR --}}
{{-- ============================================================= --}}

<aside class="hidden lg:flex fixed top-0 left-0 h-screen bg-[#849753] flex-col z-50 transition-all duration-300"
    :class="sidebarOpen ? 'w-60' : 'w-20'">

    {{-- ========================================================= --}}
    {{-- SIDEBAR HEADER --}}
    {{-- ========================================================= --}}

    <div class="h-20 w-full flex items-center justify-center border-b border-white/10">
        {{-- ===================================================== --}}
        {{-- HAMBURGER BUTTON --}}
        {{-- ===================================================== --}}

        <button type="button" @click="sidebarOpen = !sidebarOpen"
            class="w-12 h-12 flex items-center justify-center
               rounded-xl
               text-white
               hover:bg-white/10
               transition-all duration-200"
            aria-label="Toggle sidebar">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>



    {{-- ========================================================= --}}
    {{-- NAVIGATION --}}
    {{-- ========================================================= --}}

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        {{-- ===================================================== --}}
        {{-- DASHBOARD --}}
        {{-- ===================================================== --}}

        <a href="{{ route('staff.dashboard') }}" wire:navigate
            class="flex items-center rounded-xl text-sm font-medium transition-all
                {{ request()->routeIs('staff.dashboard') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
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


        <a href="{{ route('staff.quick-book') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('staff.quick-book') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="3" y="4.5" width="18" height="17" rx="2" />
                <path d="M8 2.5v4M16 2.5v4M3 9h18" />
                <circle cx="12" cy="15" r="3.5" />
                <path d="M12 13v2l1.5 1" />
            </svg>

            <span x-show="sidebarOpen" x-cloak class="truncate">
                Quick Book
            </span>

        </a>



        {{-- ===================================================== --}}
        {{-- APPOINTMENTS --}}
        {{-- ===================================================== --}}

        <a href="{{ route('staff.appointments') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('staff.appointments*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
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
                Appointments
            </span>

        </a>

        {{-- ===================================================== --}}
        {{-- TRANSACTIONS --}}
        {{-- ===================================================== --}}

        <a href="{{ route('staff.transactions') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('staff.transactions*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
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
                Transactions
            </span>

        </a>


        <a href="{{ route('staff.calendar') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('staff.calendar*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            {{-- Calendar Icon --}}
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                <rect x="3" y="5" width="18" height="16" rx="2" />
                <path stroke-linecap="round" d="M16 3v4M8 3v4M3 11h18" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 15h.01M12 15h.01M16 15h.01M8 18h.01M12 18h.01" />
            </svg>

            <span x-show="sidebarOpen" x-cloak class="truncate">
                Calendar
            </span>

        </a>




        {{-- ===================================================== --}}
        {{-- THERAPIST --}}
        {{-- ===================================================== --}}

        <a href="{{ route('staff.therapists') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('staff.therapists*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="9" cy="8" r="3" />

                <path d="M3 21a6 6 0 0112 0" />

                <path d="M16 11a3 3 0 100-6" />

                <path d="M16 14a5 5 0 015 5" />
            </svg>

            <span x-show="sidebarOpen" x-cloak class="truncate">
                Therapist
            </span>

        </a>

        <div class="my-3 border-t border-white/10"></div>


        <a href="{{ route('staff.help') }}" wire:navigate title="{{ !($sidebarOpen ?? true) ? 'Help Center' : '' }}"
            class="flex items-center rounded-xl text-sm font-medium transition-all
        {{ request()->routeIs('staff.help*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.6 9a2.5 2.5 0 014.8 1c0 1.5-2.4 2-2.4 3.5" />
                <path stroke-linecap="round" d="M12 17h.01" />
            </svg>

            <span x-show="sidebarOpen" x-cloak class="truncate">
                Help Center
            </span>

        </a>

    </nav>



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

    <nav
        class="lg:hidden
               fixed bottom-0 left-0 right-0
               z-50
               bg-[#849753]
               border-t border-white/10">

        <div class="flex items-center justify-around px-1 py-2">

            {{-- ================================================= --}}
            {{-- DASHBOARD --}}
            {{-- ================================================= --}}

            <a href="{{ route('staff.dashboard') }}"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       transition-all
                       min-w-0
                       text-white">

                       <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M3 10.5L12 3l9 7.5" />
                        <path d="M5 9.5V21h14V9.5" />
                        <path d="M9 21v-6h6v6" />
                    </svg>
        

                <span class="text-[10px] font-medium leading-tight">
                    Home
                </span>

                @if (request()->routeIs('staff.dashboard'))
                    <span
                        class="w-1 h-1
                               rounded-full
                               bg-[#6F4E37]
                               mt-0.5">
                    </span>
                @endif

            </a>


            <a href="{{ route('staff.quick-book') }}"
            class="flex flex-col items-center gap-0.5
                   px-3 py-1.5
                   rounded-xl
                   transition-all
                   min-w-0
                   text-white">

                   <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="4.5" width="18" height="17" rx="2" />
                    <path d="M8 2.5v4M16 2.5v4M3 9h18" />
                    <circle cx="12" cy="15" r="3.5" />
                    <path d="M12 13v2l1.5 1" />
                </svg>

            <span class="text-[10px] font-medium leading-tight">
                Book
            </span>

            @if (request()->routeIs('staff.quick-book'))
                <span
                    class="w-1 h-1
                           rounded-full
                           bg-[#6F4E37]
                           mt-0.5">
                </span>
            @endif

        </a>


            {{-- ================================================= --}}
            {{-- APPOINTMENTS --}}
            {{-- ================================================= --}}

            <a href="{{ route('staff.appointments') }}"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       transition-all
                       min-w-0
                       text-white">

                       <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="17" rx="2" />
        
                        <path d="M16 2v4M8 2v4M3 10h18" />
        
                        <path d="M12 13v4M10 15h4" />
                    </svg>

                <span class="text-[10px] font-medium leading-tight">
                    Appointments
                </span>

                @if (request()->routeIs('staff.appointments*'))
                    <span
                        class="w-1 h-1
                               rounded-full
                               bg-[#6F4E37]
                               mt-0.5">
                    </span>
                @endif

            </a>




            {{-- ================================================= --}}
            {{-- TRANSACTIONS --}}
            {{-- ================================================= --}}

            <a href="{{ route('staff.transactions') }}"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       transition-all
                       min-w-0
                       text-white">

                       <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="14" rx="2" />
        
                        <path d="M3 10h18" />
                        <path d="M7 15h4" />
                    </svg>

                <span class="text-[10px] font-medium leading-tight">
                    Transactions
                </span>

                @if (request()->routeIs('admin.transactions*'))
                    <span
                        class="w-1 h-1
                               rounded-full
                               bg-[#6F4E37]
                               mt-0.5">
                    </span>
                @endif

            </a>


            {{-- ================================================= --}}
            {{-- MORE --}}
            {{-- ================================================= --}}

            <button type="button" onclick="toggleAdminMoreDrawer()"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       text-white
                       transition-all
                       min-w-0">

                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <circle cx="5" cy="12" r="1" fill="currentColor" />

                    <circle cx="12" cy="12" r="1" fill="currentColor" />

                    <circle cx="19" cy="12" r="1" fill="currentColor" />
                </svg>

                <span class="text-[10px] font-medium leading-tight">
                    More
                </span>

            </button>

        </div>

    </nav>


    {{-- ========================================================= --}}
    {{-- MORE DRAWER --}}
    {{-- ========================================================= --}}

    <div id="admin-more-drawer"
        class="lg:hidden
               fixed inset-0
               z-60
               pointer-events-none">

        {{-- Backdrop --}}
        <div id="admin-drawer-backdrop"
            class="absolute inset-0
                   bg-black/50
                   opacity-0
                   transition-opacity duration-300"
            onclick="toggleAdminMoreDrawer()">
        </div>


        {{-- Sheet --}}
        <div id="admin-drawer-sheet"
            class="absolute
                   bottom-0
                   left-0
                   right-0
                   bg-[#849753]
                   rounded-t-2xl
                   translate-y-full
                   transition-transform duration-300
                   pb-6">

            {{-- Handle --}}
            <div class="flex justify-center pt-3 pb-2">

                <div class="w-10 h-1
                           rounded-full
                           bg-white/20">
                </div>

            </div>


            {{-- Admin Info --}}
            <div
                class="flex items-center gap-3
                       px-5 py-3
                       border-b border-white/10
                       mb-2">

                <div
                    class="w-10 h-10
                           rounded-full
                           bg-[#6F4E37]
                           flex items-center justify-center
                           text-white
                           font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div>

                    <p class="text-white text-sm font-semibold">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="text-white text-xs">
                        {{ auth()->user()->email }}
                    </p>

                </div>

            </div>


            <a href="{{ route('staff.calendar') }}" onclick="toggleAdminMoreDrawer()"
            class="inline-flex items-center gap-3
                   px-4 py-3
                   rounded-xl
                   text-sm
                   font-medium
                   text-white
                   hover:bg-white/10
                   transition-all
                   w-full">

                   <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path stroke-linecap="round" d="M16 3v4M8 3v4M3 11h18" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 15h.01M12 15h.01M16 15h.01M8 18h.01M12 18h.01" />
                </svg>

            Calendar

        </a>



            {{-- Therapist --}}
            <a href="{{ route('staff.therapists') }}" onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
                       px-4 py-3
                       rounded-xl
                       text-sm
                       font-medium
                       text-white
                       hover:bg-white/10
                       transition-all
                       w-full">

                       <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="9" cy="8" r="3" />
        
                        <path d="M3 21a6 6 0 0112 0" />
        
                        <path d="M16 11a3 3 0 100-6" />
        
                        <path d="M16 14a5 5 0 015 5" />
                    </svg>

                Therapist

            </a>


            <a href="{{ route('staff.profile') }}" onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
                       px-4 py-3
                       rounded-xl
                       text-sm
                       font-medium
                       text-white
                       hover:bg-white/10
                       transition-all
                       w-full">

                       <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                    </svg>

                Settings

            </a>

            <a href="{{ route('staff.help') }}" wire:navigate onclick="toggleAdminMoreDrawer()"
                class="flex items-center gap-3
           px-4 py-3
           rounded-xl
           text-sm
           font-medium
           text-white
           hover:bg-white/10
           transition-all
           w-full">

                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.6 9a2.5 2.5 0 014.8 1c0 1.5-2.4 2-2.4 3.5" />
                    <path stroke-linecap="round" d="M12 17h.01" />
                </svg>

                Help Center

            </a>



            {{-- Divider --}}
            <div class="border-t border-white/10 my-2"></div>


            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button type="submit"
                    class="w-full
                           flex items-center gap-3
                           px-4 py-3
                           rounded-xl
                           text-sm
                           font-medium
                           text-white
                           hover:bg-red-500/10
                           hover:text-red-300
                           transition-all">

                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                        <path d="M16 17l5-5-5-5" />
                        <path d="M21 12H9" />
                    </svg>

                    Logout

                </button>

            </form>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- MOBILE MORE DRAWER SCRIPT --}}
{{-- ============================================================= --}}

<script>
    function toggleAdminMoreDrawer() {

        const drawer =
            document.getElementById('admin-more-drawer');

        const backdrop =
            document.getElementById('admin-drawer-backdrop');

        const sheet =
            document.getElementById('admin-drawer-sheet');

        if (!drawer || !backdrop || !sheet) {
            return;
        }

        const isOpen = !sheet.classList.contains('translate-y-full');


        if (isOpen) {

            sheet.classList.add('translate-y-full');

            backdrop.classList.remove('opacity-100');

            backdrop.classList.add('opacity-0');

            setTimeout(() => {

                drawer.classList.add(
                    'pointer-events-none'
                );

            }, 300);

        } else {

            drawer.classList.remove(
                'pointer-events-none'
            );

            requestAnimationFrame(() => {

                sheet.classList.remove(
                    'translate-y-full'
                );

                backdrop.classList.remove(
                    'opacity-0'
                );

                backdrop.classList.add(
                    'opacity-100'
                );

            });

        }

    }
</script>
