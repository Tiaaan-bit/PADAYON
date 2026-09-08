<aside class="hidden lg:flex fixed top-0 left-0 h-screen w-64 bg-[#849753] flex-col z-50">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10 justify-center">
        <div>
            <h1 class="text-white font-bold text-sm leading-tight">Padayon Massage Center</h1>
        </div>
    </div>

    {{-- Staff info --}}
    <div class="flex items-center gap-3 px-6 py-4 border-b border-white/10">
        <div
            class="w-9 h-9 rounded-full bg-[#6F4E37] flex items-center justify-center text-white font-bold text-sm shrink-0">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="overflow-hidden">
            <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
            <p class="text-white text-xs truncate">{{ auth()->user()->email }}</p>
        </div>
    </div>

    {{-- Nav links --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

        <a href="{{ route('staff.dashboard') }}"wire:navigate
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                {{ request()->routeIs('staff.dashboard') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
            </svg>
            Dashboard
        </a>



        <a href="{{ route('staff.appointments') }}"wire:navigate
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                {{ request()->routeIs('staff.appointments*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <path d="M16 2v4M8 2v4M3 10h18" />
                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01" />
            </svg>
            Appointments
        </a>

        <a href="{{ route('staff.transactions') }}"wire:navigate
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                {{ request()->routeIs('staff.transactions*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6" />
            </svg>
            Transactions
        </a>


        <a href="{{ route('staff.therapists') }}"wire:navigate
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                {{ request()->routeIs('staff.therapist*') ? 'bg-[#6F4E37] text-white' : 'text-white hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                <rect x="9" y="3" width="6" height="4" rx="1" />
                <circle cx="12" cy="7" r="4" />
                <path d="M9 12h6M9 16h4" />

            </svg>
            Therapist
        </a>

        

    

    </nav>

    {{-- Logout --}}
    <div class="px-3 py-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-[#6F4E37] text-white hover:bg-red-500 hover:text-white transition-all">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
                </svg>
                Logout
            </button>
        </form>
    </div>

</aside>


{{-- ═══════════════════════════════════════════════════════════
MOBILE BOTTOM NAV BAR  (visible on mobile, hidden lg+)
════════════════════════════════════════════════════════════ --}}
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#849753] border-t border-white/10">

    {{-- Primary 5 tabs --}}
    <div class="flex items-center justify-around px-1 py-2">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
            class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-all min-w-0
                {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
            </svg>
            <span class="text-[10px] font-medium leading-tight">Home</span>
            @if (request()->routeIs('admin.dashboard'))
                <span class="w-1 h-1 rounded-full bg-[#6F4E37] mt-0.5"></span>
            @endif
        </a>

        {{-- Appointments --}}
        <a href="{{ route('admin.appointments') }}"
            class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-all min-w-0
                {{ request()->routeIs('admin.appointments*') ? 'text-white' : 'text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <path d="M16 2v4M8 2v4M3 10h18" />
            </svg>
            <span class="text-[10px] font-medium leading-tight">Appts</span>
            @if (request()->routeIs('admin.appointments*'))
                <span class="w-1 h-1 rounded-full bg-[#6F4E37] mt-0.5"></span>
            @endif
        </a>


        {{-- Transactions --}}
        <a href="{{ route('admin.transactions') }}"
            class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-all min-w-0
                {{ request()->routeIs('admin.transactions*') ? 'text-white' : 'text-white' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                viewBox="0 0 24 24">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6" />
            </svg>
            <span class="text-[10px] font-medium leading-tight">Pay</span>
            @if (request()->routeIs('admin.transactions*'))
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

        <div class="px-3 space-y-0.5">

            <a href="{{ route('admin.services') }}" onclick="toggleMoreDrawer()"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:bg-white/10 hover:text-white transition-all">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                    <rect x="9" y="3" width="6" height="4" rx="1" />
                    <path d="M9 12h6M9 16h4" />
                </svg>
                Services
            </a>

            <a href="{{ route('admin.addons') }}" onclick="toggleMoreDrawer()"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:bg-white/10 hover:text-white transition-all">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path
                        d="M11 4a2 2 0 014 0v1h2a2 2 0 012 2v2h1a2 2 0 010 4h-1v2a2 2 0 01-2 2h-2v1a2 2 0 11-4 0v-1H9a2 2 0 01-2-2v-2H6a2 2 0 010-4h1V7a2 2 0 012-2h2V4z" />
                </svg>
                Add-Ons
            </a>

            <a href="{{ route('admin.therapists') }}" onclick="toggleMoreDrawer()"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:bg-white/10 hover:text-white transition-all">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                Therapist
            </a>

            <a href="{{ route('admin.reports') }}" onclick="toggleMoreDrawer()"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:bg-white/10 hover:text-white transition-all">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                </svg>
                Reports
            </a>

            <a href="{{ route('admin.posts') }}" onclick="toggleMoreDrawer()"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:bg-white/10 hover:text-white transition-all">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 21h4" />
                </svg>
                Announcements
            </a>

            <div class="border-t border-white/10 my-2"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all">
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
