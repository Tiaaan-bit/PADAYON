<header class="sticky top-0 z-40 bg-[#D6BB9E]">

    <div class="flex items-center justify-between gap-3 h-20 px-3 sm:px-6 lg:px-8">

        {{-- LOGO + BRAND --}}
        <a href="{{ route('user.dashboard') }}" wire:navigate class="flex items-center gap-2 sm:gap-3 min-w-0 shrink-0">

            <img src="{{ !empty($websiteSettings?->logo_path)
                ? asset('storage/' . $websiteSettings->logo_path)
                : asset('build/assets/images/logo.png') }}"
                alt="{{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}"
                class="h-10 w-10 sm:h-12 sm:w-12 object-contain shrink-0">

            <div class="leading-tight min-w-0">
                <h1 class="font-semibold text-[#2F2420] text-sm sm:text-lg truncate">
                    {{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}
                </h1>

                <span class="hidden sm:block font-bold text-[#2F2420] text-xs truncate">
                    {{ $websiteSettings?->website_subname ?? 'SubName' }}
                </span>
            </div>
        </a>

        {{-- DESKTOP SEARCH BAR --}}
        <form action="{{ route('user.search') }}" method="GET"
            class="hidden md:block w-full max-w-xs lg:max-w-sm ml-auto mr-2">

            <div class="relative pt-1">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search..."
                    aria-label="Search appointments, services, therapists, and help"
                    class="w-full h-9 pl-9 pr-3
                           bg-white/85
                           border border-[#6F4E37]/20
                           rounded-full
                           text-sm text-[#2F2420]
                           placeholder:text-[#6F4E37]/70
                           focus:outline-none
                           focus:ring-2 focus:ring-[#849753]
                           focus:border-transparent
                           transition">

                <button type="submit" aria-label="Submit search"
                    class="absolute left-0 top-0 h-10 w-10
                           flex items-center justify-center
                           text-[#6F4E37] hover:text-[#849753]
                           transition">

                    <svg class="pt-1 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" />
                        <path d="m16 16 4 4" />
                    </svg>
                </button>
            </div>
        </form>

        {{-- HEADER RIGHT SIDE --}}
        <div class="flex items-center gap-1 sm:gap-3 shrink-0">

            {{-- MOBILE SEARCH TOGGLE --}}
            <button type="button" id="mobile-search-toggle" aria-label="Open search" aria-controls="mobile-user-search"
                aria-expanded="{{ request()->routeIs('user.search') ? 'true' : 'false' }}"
                class="md:hidden flex items-center justify-center
                       w-10 h-10 rounded-full
                       text-[#6F4E37] hover:bg-[#849753]/10
                       transition">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" />
                    <path d="m16 16 4 4" />
                </svg>
            </button>

            {{-- NOTIFICATIONS --}}
            <a href="{{ route('user.notifications') }}" wire:navigate aria-label="Notifications"
                class="relative flex items-center justify-center
                       w-10 h-10 rounded-full
                       text-[#6F4E37] hover:bg-[#849753]/10
                       transition-all">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M12 22a2 2 0 002-2h-4a2 2 0 002 2z" />
                    <path d="M18 16H6l1.2-1.2A2 2 0 008 13.4V10a4 4 0 118 0v3.4a2 2 0 00.8 1.4L18 16z" />
                </svg>

                @if (auth()->user()->unreadNotifications->count() > 0)
                    <span
                        class="absolute top-1.5 right-1.5
                                 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </a>

            {{-- USER PROFILE DROPDOWN --}}
            <div x-data="{ open: false }" class="relative pl-2 sm:pl-3 border-l border-[#6F4E37]/20">

                {{-- PROFILE BUTTON --}}
                <button type="button" @click="open = !open" @click.outside="open = false"
                    @keydown.escape.window="open = false" :aria-expanded="open" aria-haspopup="true"
                    aria-label="Open user menu"
                    class="flex items-center gap-2 rounded-full p-1
                           hover:bg-[#849753]/10 transition">

                    {{-- PROFILE INITIAL --}}
                    <div
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full
                                bg-[#6F4E37] flex items-center justify-center
                                text-white font-bold text-sm shrink-0">

                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    {{-- USER NAME --}}
                    <div class="hidden sm:block max-w-32">
                        <p class="text-sm font-semibold text-[#2F2420] truncate">
                            {{ auth()->user()->name }}
                        </p>
                    </div>

                    {{-- DOWN ARROW --}}
                    <svg class="w-4 h-4 text-[#6F4E37]
                                transition-transform duration-200"
                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </button>

                {{-- DROPDOWN MENU --}}
                <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false"
                    class="absolute right-0 top-full mt-3 w-56
                           rounded-xl bg-white border border-gray-200
                           shadow-lg py-2 z-50">

                    {{-- SETTINGS / PROFILE --}}
                    <a href="{{ route('user.profile') }}" wire:navigate @click="open = false"
                        class="flex items-center gap-3 px-4 py-3 text-sm
                               text-[#2F2420] hover:bg-[#F4EDDB]
                               transition">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">

                            <circle cx="12" cy="12" r="3" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.4 15a1.7 1.7 0 000-2l1.1-.9-1.5-2.6-1.4.5a7 7 0 00-1.7-1L15.6 7h-3.1l-.3 2a7 7 0 00-1.7 1l-1.4-.5-1.5 2.6 1.1.9a1.7 1.7 0 000 2l-1.1.9 1.5 2.6 1.4-.5a7 7 0 001.7 1l.3 2h3.1l.3-2a7 7 0 001.7-1l1.4.5 1.5-2.6-1.1-.9z" />
                        </svg>

                        <span>Settings</span>
                    </a>

                    {{-- LOGOUT --}}
                    <div class="border-t border-gray-100 mt-1 pt-1">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-3
                                       text-sm hover:bg-red-50
                                       transition text-left">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                    viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10 17l5-5-5-5m5 5H3m9-9h5a2 2 0 012 2v14a2 2 0 01-2 2h-5" />
                                </svg>

                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MOBILE SEARCH BAR --}}
    <div id="mobile-user-search" class="{{ request()->routeIs('user.search') ? '' : 'hidden' }} md:hidden px-4 pb-4">

        <form action="{{ route('user.search') }}" method="GET">
            <div class="relative">

                <input type="search" name="search" value="{{ request('search') }}"
                    placeholder="Search appointments, services, therapists..." aria-label="Search"
                    class="w-full h-11 pl-11 pr-4 rounded-full
                           bg-white border border-[#6F4E37]/20
                           text-sm text-[#2F2420]
                           placeholder:text-[#6F4E37]/70
                           focus:outline-none focus:ring-2
                           focus:ring-[#849753]">

                <button type="submit" aria-label="Submit search"
                    class="absolute left-0 top-0 h-11 w-11
                           flex items-center justify-center
                           text-[#6F4E37] hover:text-[#849753]">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                        <circle cx="11" cy="11" r="7" />
                        <path d="m16 16 4 4" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</header>

{{-- MOBILE SEARCH TOGGLE SCRIPT --}}
<script>
    (() => {
        function initializeMobileSearch() {
            const toggle = document.getElementById('mobile-search-toggle');
            const search = document.getElementById('mobile-user-search');

            if (!toggle || !search || toggle.dataset.initialized === 'true') {
                return;
            }

            toggle.dataset.initialized = 'true';

            toggle.addEventListener('click', () => {
                const isHidden = search.classList.toggle('hidden');

                toggle.setAttribute('aria-expanded', String(!isHidden));
                toggle.setAttribute(
                    'aria-label',
                    isHidden ? 'Open search' : 'Close search'
                );

                if (!isHidden) {
                    search.querySelector('input[type="search"]')?.focus();
                }
            });
        }

        initializeMobileSearch();

        document.addEventListener('livewire:navigated', initializeMobileSearch);
    })();
</script>
