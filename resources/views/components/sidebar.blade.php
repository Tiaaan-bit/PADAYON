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

    <nav class="sidebar-scroll flex-1 overflow-y-auto px-3 py-4 space-y-1">
        {{-- ===================================================== --}}
        {{-- DASHBOARD --}}
        {{-- ===================================================== --}}

        <a href="{{ route('admin.dashboard') }}" wire:navigate
            class="flex items-center rounded-xl text-sm font-medium transition-all
                {{ request()->routeIs('admin.dashboard') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
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

        <a href="{{ route('admin.quick-book') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.quick-book') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
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

        <a href="{{ route('admin.appointments') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.appointments*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
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

        <a href="{{ route('admin.transactions') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.transactions*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
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

        <a href="{{ route('admin.calendar') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.calendar*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

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
        {{-- ANNOUNCEMENTS --}}
        {{-- ===================================================== --}}

        <a href="{{ route('admin.posts') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.posts*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M4 5h16v14H4z" />
                <path d="M7 9h10M7 13h7" />
            </svg>

            <span x-show="sidebarOpen" x-cloak class="truncate">
                Announcements
            </span>

        </a>



        {{-- ===================================================== --}}
        {{-- CUSTOMERS --}}
        {{-- ===================================================== --}}

        <a href="{{ route('admin.users') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.users*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
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
                Customers
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- SERVICES --}}
        {{-- ===================================================== --}}

        <a href="{{ route('admin.services') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.services*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <path d="M6 3h12v18H6z" />
                <path d="M9 7h6M9 11h6M9 15h4" />
            </svg>

            <span x-show="sidebarOpen" x-cloak class="truncate">
                Services
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- ADD-ONS --}}
        {{-- ===================================================== --}}

        <a href="{{ route('admin.addons') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.addons*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" />
                <path d="M12 8v8M8 12h8" />
            </svg>

            <span x-show="sidebarOpen" x-cloak class="truncate">
                Add-Ons
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- THERAPIST --}}
        {{-- ===================================================== --}}

        <a href="{{ route('admin.therapists') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.therapists*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="3" />

                <path d="M5 21a7 7 0 0114 0" />
            </svg>

            <span x-show="sidebarOpen" x-cloak class="truncate">
                Therapist
            </span>

        </a>




        {{-- ===================================================== --}}
        {{-- REPORTS --}}
        {{-- ===================================================== --}}

        <a href="{{ route('admin.reports') }}" wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.reports*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'">

            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <path d="M4 19V5" />
                <path d="M4 19h16" />

                <path d="M7 16v-4" />
                <path d="M11 16V8" />
                <path d="M15 16v-6" />
                <path d="M19 16V5" />
            </svg>

            <span x-show="sidebarOpen" x-cloak class="truncate">
                Reports
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- SETTINGS WITH SUBMENU --}}
        {{-- ===================================================== --}}

        <div x-data="{
            settingsOpen: {{ request()->routeIs('admin.profile*', 'admin.website-settings.*', 'admin.home-slider.*') ? 'true' : 'false' }}
        }" class="space-y-1">
            {{-- SETTINGS BUTTON --}}
            <button type="button"
                @click="
    if (!sidebarOpen) {
        sidebarOpen = true;
        settingsOpen = true;
    } else {
        settingsOpen = !settingsOpen;
    }
"
                :aria-expanded="settingsOpen.toString()"
                class="w-full flex items-center rounded-xl text-sm font-medium transition-all
    {{ request()->routeIs('admin.profile*', 'admin.website-settings.*', 'admin.home-slider.*')
        ? 'bg-[#6F4E37] text-white'
        : 'text-white hover:bg-white/10' }}"
                :class="sidebarOpen
                    ?
                    'gap-3 px-3 py-2.5' :
                    'justify-center px-2 py-3'"
                title="Settings">
                {{-- SETTINGS ICON --}}
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                </svg>

                <span x-show="sidebarOpen" x-cloak class="flex-1 text-left truncate">
                    Settings
                </span>

                {{-- ARROW --}}
                <svg x-show="sidebarOpen" x-cloak class="w-4 h-4 shrink-0 transition-transform duration-200"
                    :class="settingsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                </svg>
            </button>

            {{-- SUBMENU --}}
            <div x-cloak x-show="settingsOpen && sidebarOpen" x-transition
                class="ml-5 space-y-1 border-l border-white/30 pl-3">

                {{-- WEBSITE SETTINGS --}}
                <a href="{{ route('admin.website-settings.edit') }}" wire:navigate
                    class="block rounded-lg px-3 py-2.5 text-sm transition
        {{ request()->routeIs('admin.website-settings.*')
            ? 'bg-[#6F4E37] text-white'
            : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    Website Settings
                </a>

                {{-- HOME PAGE SLIDER --}}
                <a href="{{ route('admin.home-slider.index') }}" wire:navigate
                    class="block rounded-lg px-3 py-2.5 text-sm transition
        {{ request()->routeIs('admin.home-slider.*')
            ? 'bg-[#6F4E37] text-white'
            : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    Home Page Settings
                </a>

                {{-- About Page--}}
                <a href="{{ route('admin.about-settings.edit') }}" wire:navigate
                    class="block rounded-lg px-3 py-2.5 text-sm transition
        {{ request()->routeIs('admin.about-settings.*')
            ? 'bg-[#6F4E37] text-white'
            : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    About Page Settings
                </a>
            </div>
        </div>


        <a href="{{ route('admin.help') }}" wire:navigate title="{{ !($sidebarOpen ?? true) ? 'Help Center' : '' }}"
            class="flex items-center rounded-xl text-sm font-medium transition-all
    {{ request()->routeIs('admin.help*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
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

            <a href="{{ route('admin.dashboard') }}"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       transition-all
                       min-w-0
                       text-white">

                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path d="M3 10.5L12 3l9 7.5" />
                    <path d="M5 9.5V21h14V9.5" />
                    <path d="M9 21v-6h6v6" />
                </svg>

                <span class="text-[10px] font-medium leading-tight">
                    Dashboard
                </span>

                @if (request()->routeIs('admin.dashboard'))
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

            <a href="{{ route('admin.appointments') }}"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       transition-all
                       min-w-0
                       text-white">

                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="17" rx="2" />

                    <path d="M16 2v4M8 2v4M3 10h18" />

                    <path d="M12 13v4M10 15h4" />
                </svg>

                <span class="text-[10px] font-medium leading-tight">
                    Appointments
                </span>

                @if (request()->routeIs('admin.appointments*'))
                    <span
                        class="w-1 h-1
                               rounded-full
                               bg-[#6F4E37]
                               mt-0.5">
                    </span>
                @endif

            </a>


            <a href="{{ route('admin.transactions') }}"
                class="flex flex-col items-center gap-0.5
                   px-3 py-1.5
                   rounded-xl
                   transition-all
                   min-w-0
                   text-white">

                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
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

            <a href="{{ route('admin.calendar') }}"
                class="flex flex-col items-center gap-0.5
               px-3 py-1.5
               rounded-xl
               transition-all
               min-w-0
               text-white">

                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">

                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path stroke-linecap="round" d="M16 3v4M8 3v4M3 11h18" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 15h.01M12 15h.01M16 15h.01M8 18h.01M12 18h.01" />
                </svg>

                <span class="text-[10px] font-medium leading-tight">
                    Calendar
                </span>

                @if (request()->routeIs('admin.calendar*'))
                    <span
                        class="w-1 h-1
                       rounded-full
                       bg-[#6F4E37]
                       mt-0.5">
                    </span>
                @endif

            </a>

            <a href="{{ route('admin.reports') }}"
                class="flex flex-col items-center gap-0.5
               px-3 py-1.5
               rounded-xl
               transition-all
               min-w-0
               text-white">

                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path d="M4 19V5" />
                    <path d="M4 19h16" />

                    <path d="M7 16v-4" />
                    <path d="M11 16V8" />
                    <path d="M15 16v-6" />
                    <path d="M19 16V5" />
                </svg>
                <span class="text-[10px] font-medium leading-tight">
                    Reports
                </span>

                @if (request()->routeIs('admin.reports*'))
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


            {{-- Announcements --}}
            <a href="{{ route('admin.posts') }}" onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
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
                    <path d="M4 5h16v14H4z" />
                    <path d="M7 9h10M7 13h7" />
                </svg>

                Announcements

            </a>

            <a href="{{ route('admin.users') }}" onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
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
                    <path d="M4 5h16v14H4z" />
                    <path d="M7 9h10M7 13h7" />
                </svg>

                Customers

            </a>


            {{-- Services --}}
            <a href="{{ route('admin.services') }}" onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
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
                    <path d="M6 3h12v18H6z" />
                    <path d="M9 7h6M9 11h6M9 15h4" />
                </svg>

                Services

            </a>


            {{-- Add-Ons --}}
            <a href="{{ route('admin.addons') }}" onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
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
                    <path d="M12 8v8M8 12h8" />
                </svg>

                Add-Ons

            </a>


            {{-- Therapist --}}
            <a href="{{ route('admin.therapists') }}" onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
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
                    <circle cx="12" cy="8" r="3" />
                    <path d="M5 21a7 7 0 0114 0" />
                </svg>

                Therapist

            </a>

            <a href="{{ route('admin.profile') }}" onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
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
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                </svg>

                Settings

            </a>

            <a href="{{ route('admin.help') }}" onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
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
                           bg-[#6F4E37]
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
