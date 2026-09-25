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
    :class="sidebarOpen ? 'w-60' : 'w-20'"
>

    {{-- ========================================================= --}}
    {{-- SIDEBAR HEADER --}}
    {{-- ========================================================= --}}

    <div
        class="h-20
               flex items-center
               border-b border-white/10
               transition-all duration-300"
        :class="sidebarOpen
            ?
            'px-4 gap-3' :
            'justify-center px-2'"
    >

        {{-- ===================================================== --}}
        {{-- HAMBURGER BUTTON --}}
        {{-- ===================================================== --}}

        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            class="w-10 h-10
                   shrink-0
                   flex items-center justify-center
                   rounded-xl
                   text-white
                   hover:bg-white/10
                   transition-all"
            aria-label="Toggle sidebar"
        >

            {{-- Always Hamburger --}}
            <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>

        </button>


        {{-- ===================================================== --}}
        {{-- SIDEBAR LOGO + TEXT --}}
        {{-- ===================================================== --}}

        <div
            x-show="sidebarOpen"
            x-cloak
            class="flex items-center
                   gap-2
                   min-w-0"
        >

            <img
                src="{{ asset('images/logo.webp') }}"
                alt="Padayon Massage Center Logo"
                class="h-10 w-10
                       object-contain
                       shrink-0"
            >

            <div class="leading-tight min-w-0">

                <h1
                    class="text-white
                           font-bold
                           text-sm
                           truncate"
                >
                    Padayon Massage Center
                </h1>

                <p
                    class="text-white/70
                           text-[10px]
                           leading-tight"
                >
                    Blind Massage Specialists
                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ADMIN INFORMATION --}}
    {{-- ========================================================= --}}

    <div
        class="flex items-center
               border-b border-white/10
               py-4"
        :class="sidebarOpen
            ?
            'gap-3 px-5' :
            'justify-center px-2'"
    >

        {{-- Avatar --}}
        <div
            class="w-9 h-9
                   rounded-full
                   bg-[#6F4E37]
                   flex items-center justify-center
                   text-white
                   font-bold
                   text-sm
                   shrink-0"
        >
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>


        {{-- Admin Information --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            class="overflow-hidden"
        >

            <p
                class="text-white
                       text-sm
                       font-semibold
                       truncate"
            >
                {{ auth()->user()->name }}
            </p>

            <p
                class="text-white/70
                       text-xs
                       truncate"
            >
                {{ auth()->user()->email }}
            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- NAVIGATION --}}
    {{-- ========================================================= --}}

    <nav
        class="flex-1
               overflow-y-auto
               px-3
               py-4
               space-y-1"
    >

        {{-- ===================================================== --}}
        {{-- DASHBOARD --}}
        {{-- ===================================================== --}}

        <a
            href="{{ route('admin.dashboard') }}"
            wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.dashboard') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'"
        >

            <svg
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <path d="M3 10.5L12 3l9 7.5" />
                <path d="M5 9.5V21h14V9.5" />
                <path d="M9 21v-6h6v6" />
            </svg>

            <span
                x-show="sidebarOpen"
                x-cloak
                class="truncate"
            >
                Dashboard
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- ANNOUNCEMENTS --}}
        {{-- ===================================================== --}}

        <a
            href="{{ route('admin.posts') }}"
            wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.posts*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'"
        >

            <svg
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <path d="M4 5h16v14H4z" />
                <path d="M7 9h10M7 13h7" />
            </svg>

            <span
                x-show="sidebarOpen"
                x-cloak
                class="truncate"
            >
                Announcements
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- APPOINTMENTS --}}
        {{-- ===================================================== --}}

        <a
            href="{{ route('admin.appointments') }}"
            wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.appointments*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'"
        >

            <svg
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <rect
                    x="3"
                    y="4"
                    width="18"
                    height="17"
                    rx="2"
                />

                <path d="M16 2v4M8 2v4M3 10h18" />

                <path d="M12 13v4M10 15h4" />
            </svg>

            <span
                x-show="sidebarOpen"
                x-cloak
                class="truncate"
            >
                Appointments
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- CUSTOMERS --}}
        {{-- ===================================================== --}}

        <a
            href="{{ route('admin.users') }}"
            wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.users*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'"
        >

            <svg
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <circle cx="9" cy="8" r="3" />

                <path d="M3 21a6 6 0 0112 0" />

                <path d="M16 11a3 3 0 100-6" />

                <path d="M16 14a5 5 0 015 5" />
            </svg>

            <span
                x-show="sidebarOpen"
                x-cloak
                class="truncate"
            >
                Customers
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- SERVICES --}}
        {{-- ===================================================== --}}

        <a
            href="{{ route('admin.services') }}"
            wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.services*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'"
        >

            <svg
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <path d="M6 3h12v18H6z" />
                <path d="M9 7h6M9 11h6M9 15h4" />
            </svg>

            <span
                x-show="sidebarOpen"
                x-cloak
                class="truncate"
            >
                Services
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- ADD-ONS --}}
        {{-- ===================================================== --}}

        <a
            href="{{ route('admin.addons') }}"
            wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.addons*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'"
        >

            <svg
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <circle cx="12" cy="12" r="9" />
                <path d="M12 8v8M8 12h8" />
            </svg>

            <span
                x-show="sidebarOpen"
                x-cloak
                class="truncate"
            >
                Add-Ons
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- THERAPIST --}}
        {{-- ===================================================== --}}

        <a
            href="{{ route('admin.therapists') }}"
            wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.therapists*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'"
        >

            <svg
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <circle cx="12" cy="8" r="3" />

                <path d="M5 21a7 7 0 0114 0" />
            </svg>

            <span
                x-show="sidebarOpen"
                x-cloak
                class="truncate"
            >
                Therapist
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- TRANSACTIONS --}}
        {{-- ===================================================== --}}

        <a
            href="{{ route('admin.transactions') }}"
            wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.transactions*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'"
        >

            <svg
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <rect
                    x="3"
                    y="5"
                    width="18"
                    height="14"
                    rx="2"
                />

                <path d="M3 10h18" />
                <path d="M7 15h4" />
            </svg>

            <span
                x-show="sidebarOpen"
                x-cloak
                class="truncate"
            >
                Transactions
            </span>

        </a>


        {{-- ===================================================== --}}
        {{-- REPORTS --}}
        {{-- ===================================================== --}}

        <a
            href="{{ route('admin.reports') }}"
            wire:navigate
            class="flex items-center
                   rounded-xl
                   text-sm
                   font-medium
                   transition-all
                   {{ request()->routeIs('admin.reports*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10' }}"
            :class="sidebarOpen
                ?
                'gap-3 px-3 py-2.5' :
                'justify-center px-2 py-3'"
        >

            <svg
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
            >
                <path d="M4 19V5" />
                <path d="M4 19h16" />

                <path d="M7 16v-4" />
                <path d="M11 16V8" />
                <path d="M15 16v-6" />
                <path d="M19 16V5" />
            </svg>

            <span
                x-show="sidebarOpen"
                x-cloak
                class="truncate"
            >
                Reports
            </span>

        </a>

    </nav>


    {{-- ========================================================= --}}
    {{-- LOGOUT --}}
    {{-- ========================================================= --}}

    <div
        class="px-3
               py-4
               border-t
               border-white/10"
    >

        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                type="submit"
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
                    'justify-center px-2 py-3'"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <path d="M10 17l5-5-5-5" />
                    <path d="M15 12H3" />
                    <path d="M21 19V5a2 2 0 00-2-2h-6" />
                </svg>

                <span
                    x-show="sidebarOpen"
                    x-cloak
                >
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
           border-t border-white/10"
>

    <nav
        class="lg:hidden
               fixed bottom-0 left-0 right-0
               z-50
               bg-[#849753]
               border-t border-white/10"
    >

        <div class="flex items-center justify-around px-1 py-2">

            {{-- ================================================= --}}
            {{-- DASHBOARD --}}
            {{-- ================================================= --}}

            <a
                href="{{ route('admin.dashboard') }}"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       transition-all
                       min-w-0
                       text-white"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>

                <span class="text-[10px] font-medium leading-tight">
                    Home
                </span>

                @if (request()->routeIs('admin.dashboard'))

                    <span
                        class="w-1 h-1
                               rounded-full
                               bg-[#6F4E37]
                               mt-0.5"
                    >
                    </span>

                @endif

            </a>


            {{-- ================================================= --}}
            {{-- APPOINTMENTS --}}
            {{-- ================================================= --}}

            <a
                href="{{ route('admin.appointments') }}"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       transition-all
                       min-w-0
                       text-white"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <path
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"
                    />

                    <rect
                        x="9"
                        y="3"
                        width="6"
                        height="4"
                        rx="1"
                    />

                    <path d="M9 12h6M9 16h4" />
                </svg>

                <span class="text-[10px] font-medium leading-tight">
                    Appts
                </span>

                @if (request()->routeIs('admin.appointments*'))

                    <span
                        class="w-1 h-1
                               rounded-full
                               bg-[#6F4E37]
                               mt-0.5"
                    >
                    </span>

                @endif

            </a>


            {{-- ================================================= --}}
            {{-- CUSTOMERS --}}
            {{-- ================================================= --}}

            <a
                href="{{ route('admin.users') }}"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       transition-all
                       min-w-0
                       text-white"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <circle cx="9" cy="8" r="3" />
                    <path d="M3 21a6 6 0 0112 0" />
                    <path d="M16 11a3 3 0 100-6" />
                    <path d="M16 14a5 5 0 015 5" />
                </svg>

                <span class="text-[10px] font-medium leading-tight">
                    Users
                </span>

                @if (request()->routeIs('admin.users*'))

                    <span
                        class="w-1 h-1
                               rounded-full
                               bg-[#6F4E37]
                               mt-0.5"
                    >
                    </span>

                @endif

            </a>


            {{-- ================================================= --}}
            {{-- TRANSACTIONS --}}
            {{-- ================================================= --}}

            <a
                href="{{ route('admin.transactions') }}"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       transition-all
                       min-w-0
                       text-white"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6" />
                </svg>

                <span class="text-[10px] font-medium leading-tight">
                    Pay
                </span>

                @if (request()->routeIs('admin.transactions*'))

                    <span
                        class="w-1 h-1
                               rounded-full
                               bg-[#6F4E37]
                               mt-0.5"
                    >
                    </span>

                @endif

            </a>


            {{-- ================================================= --}}
            {{-- MORE --}}
            {{-- ================================================= --}}

            <button
                type="button"
                onclick="toggleAdminMoreDrawer()"
                class="flex flex-col items-center gap-0.5
                       px-3 py-1.5
                       rounded-xl
                       text-white
                       transition-all
                       min-w-0"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <circle
                        cx="5"
                        cy="12"
                        r="1"
                        fill="currentColor"
                    />

                    <circle
                        cx="12"
                        cy="12"
                        r="1"
                        fill="currentColor"
                    />

                    <circle
                        cx="19"
                        cy="12"
                        r="1"
                        fill="currentColor"
                    />
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

    <div
        id="admin-more-drawer"
        class="lg:hidden
               fixed inset-0
               z-60
               pointer-events-none"
    >

        {{-- Backdrop --}}
        <div
            id="admin-drawer-backdrop"
            class="absolute inset-0
                   bg-black/50
                   opacity-0
                   transition-opacity duration-300"
            onclick="toggleAdminMoreDrawer()"
        >
        </div>


        {{-- Sheet --}}
        <div
            id="admin-drawer-sheet"
            class="absolute
                   bottom-0
                   left-0
                   right-0
                   bg-[#849753]
                   rounded-t-2xl
                   translate-y-full
                   transition-transform duration-300
                   pb-6"
        >

            {{-- Handle --}}
            <div class="flex justify-center pt-3 pb-2">

                <div
                    class="w-10 h-1
                           rounded-full
                           bg-white/20"
                >
                </div>

            </div>


            {{-- Admin Info --}}
            <div
                class="flex items-center gap-3
                       px-5 py-3
                       border-b border-white/10
                       mb-2"
            >

                <div
                    class="w-10 h-10
                           rounded-full
                           bg-[#6F4E37]
                           flex items-center justify-center
                           text-white
                           font-bold"
                >
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


            {{-- Services --}}
            <a
                href="{{ route('admin.services') }}"
                onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
                       px-4 py-3
                       rounded-xl
                       text-sm
                       font-medium
                       text-white
                       hover:bg-white/10
                       transition-all
                       w-full"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <path d="M6 3h12v18H6z" />
                    <path d="M9 7h6M9 11h6M9 15h4" />
                </svg>

                Services

            </a>


            {{-- Add-Ons --}}
            <a
                href="{{ route('admin.addons') }}"
                onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
                       px-4 py-3
                       rounded-xl
                       text-sm
                       font-medium
                       text-white
                       hover:bg-white/10
                       transition-all
                       w-full"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 8v8M8 12h8" />
                </svg>

                Add-Ons

            </a>


            {{-- Therapist --}}
            <a
                href="{{ route('admin.therapists') }}"
                onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
                       px-4 py-3
                       rounded-xl
                       text-sm
                       font-medium
                       text-white
                       hover:bg-white/10
                       transition-all
                       w-full"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <circle cx="12" cy="8" r="3" />
                    <path d="M5 21a7 7 0 0114 0" />
                </svg>

                Therapist

            </a>


            {{-- Reports --}}
            <a
                href="{{ route('admin.reports') }}"
                onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
                       px-4 py-3
                       rounded-xl
                       text-sm
                       font-medium
                       text-white
                       hover:bg-white/10
                       transition-all
                       w-full"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <path d="M4 19V5" />
                    <path d="M4 19h16" />
                    <path d="M7 16v-4" />
                    <path d="M11 16V8" />
                    <path d="M15 16v-6" />
                    <path d="M19 16V5" />
                </svg>

                Reports

            </a>


            {{-- Announcements --}}
            <a
                href="{{ route('admin.posts') }}"
                onclick="toggleAdminMoreDrawer()"
                class="inline-flex items-center gap-3
                       px-4 py-3
                       rounded-xl
                       text-sm
                       font-medium
                       text-white
                       hover:bg-white/10
                       transition-all
                       w-full"
            >

                <svg
                    class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <path d="M4 5h16v14H4z" />
                    <path d="M7 9h10M7 13h7" />
                </svg>

                Announcements

            </a>


            {{-- Divider --}}
            <div class="border-t border-white/10 my-2"></div>


            {{-- Logout --}}
            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
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
                           transition-all"
                >

                    <svg
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                    >
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

        const isOpen =
            !sheet.classList.contains('translate-y-full');


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