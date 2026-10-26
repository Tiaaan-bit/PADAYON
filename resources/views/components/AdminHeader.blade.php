<header class="sticky top-0 z-40 bg-[#D6BB9E]">

    <div class="flex items-center justify-between h-20 px-4 sm:px-6 lg:px-8">

        {{-- LOGO + BRAND --}}
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 min-w-0">

            <img src="{{ !empty($websiteSettings?->logo_path)
                ? asset('storage/' . $websiteSettings->logo_path)
                : asset('build/assets/images/logo.png') }}"
                alt="{{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}"
                class="h-12 w-12 object-contain shrink-0">

            <div class="leading-tight min-w-0">
                <h1 class="font-semibold text-[#2F2420] text-sm sm:text-lg truncate">
                    {{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}
                </h1>

                <span class="hidden sm:block font-bold text-[#2F2420] text-xs truncate">
                    {{ $websiteSettings?->website_subname ?? 'SubName' }}
                </span>
            </div>
        </a>

        {{-- USER PROFILE + DROPDOWN --}}
        <div class="flex items-center gap-3 sm:gap-4">

            <div x-data="{ open: false }" class="relative flex items-center gap-3 pl-3 border-l border-[#6F4E37]/30">

                {{-- AVATAR --}}
                <div
                    class="w-10 h-10 rounded-full bg-[#6F4E37]
                            flex items-center justify-center
                            text-white font-bold text-sm shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                {{-- USER NAME --}}
                <div class="hidden sm:block">
                    <p class="text-sm font-semibold text-[#2F2420]">
                        {{ auth()->user()->name }}
                    </p>
                </div>

                {{-- DOWN ARROW BUTTON --}}
                <button type="button" @click="open = !open" @click.outside="open = false"
                    @keydown.escape.window="open = false" :aria-expanded="open" aria-haspopup="true"
                    aria-label="Open account menu"
                    class="flex items-center justify-center
                           w-8 h-8 rounded-full
                           text-[#6F4E37] hover:bg-[#849753]/20
                           transition">

                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </button>

                {{-- DROPDOWN MENU --}}
                <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false"
                    class="absolute right-0 top-full mt-3 w-48
                           rounded-xl bg-white border border-gray-200
                           shadow-lg py-2 z-50">

                    {{-- SETTINGS --}}
                    <a href="{{ route('admin.profile') }}" wire:navigate @click="open = false"
                        class="flex items-center gap-3 px-4 py-3
                               text-sm text-[#2F2420]
                               hover:bg-[#F4EDDB] transition">

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
                                class="w-full flex items-center gap-3
                                       px-4 py-3 text-sm 
                                       hover:bg-red-50 transition text-left">

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
</header>
