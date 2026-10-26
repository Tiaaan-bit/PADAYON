<nav class="relative z-50 flex h-17.5 w-full items-center justify-between bg-transparent px-4 transition-all md:px-8 lg:px-12 xl:px-16 ">

    {{-- LOGO + BRAND --}}
    <a href="{{ route('home.showHomePage') }}"
        class="inline-flex items-center gap-2 rounded-full bg-[#6F4E37] px-3 py-2 text-sm font-medium text-white transition-all hover:opacity-90 active:scale-95 md:gap-3 md:px-4">

        <img
            src="{{ !empty($websiteSettings?->logo_path)
                ? asset('storage/' . $websiteSettings->logo_path)
                : asset('build/assets/images/logo.png') }}"
            alt="{{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}"
            class="h-10 w-10 object-contain md:h-12 md:w-12">

        <div class="leading-tight">
            <h1 class="text-sm font-semibold text-white">
                {{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}
            </h1>

            <span class="text-xs font-bold text-white">
                {{ $websiteSettings?->website_subname ?? 'SubName' }}
            </span>
        </div>
    </a>

    {{-- DESKTOP NAVIGATION --}}
    <ul class="hidden items-center gap-10 text-[#849753] md:flex">
        <li>
            <a class="font-bold transition hover:border-b-2 hover:border-[#2F2420]"
                href="{{ route('home.showHomePage') }}">Home</a>
        </li>

        <li>
            <a class="font-bold transition hover:border-b-2 hover:border-[#2F2420]"
                href="{{ route('home.showServicesPage') }}">Services</a>
        </li>

        <li>
            <a class="font-bold transition hover:border-b-2 hover:border-[#2F2420]"
                href="{{ route('home.showAboutPage') }}">About</a>
        </li>

        <li>
            <a class="font-bold transition hover:border-b-2 hover:border-[#2F2420]"
                href="{{ route('home.showContactPage') }}">Contacts</a>
        </li>
    </ul>

    {{-- DESKTOP LOGIN --}}
    <a href="{{ route('login') }}"
        class="hidden h-11 w-40 items-center justify-center rounded-full bg-[#6F4E37] text-sm font-medium text-white transition-all hover:opacity-90 active:scale-95 md:flex">
        Login
    </a>

    {{-- MOBILE MENU BUTTON --}}
    <button
        id="mobile-menu-btn"
        type="button"
        aria-label="Open navigation menu"
        aria-controls="mobile-menu"
        aria-expanded="false"
        class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[#6F4E37] text-white transition hover:bg-[#5a3f2d] active:scale-90 md:hidden">

        <svg id="menu-icon"
            xmlns="http://www.w3.org/2000/svg"
            class="h-7 w-7"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            aria-hidden="true">

            <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{-- MOBILE MENU --}}
    <div
        id="mobile-menu"
        class="absolute left-0 top-full z-50 hidden w-full border-t border-white/20 bg-[#6F4E37] p-4 shadow-xl md:hidden">

        <ul class="flex flex-col gap-2 text-lg text-white">

            <li>
                <a href="{{ route('home.showHomePage') }}"
                    class="block rounded-lg px-4 py-3 transition hover:bg-[#6F4E37]/50">
                    Home
                </a>
            </li>

            <li>
                <a href="{{ route('home.showServicesPage') }}"
                    class="block rounded-lg px-4 py-3 transition hover:bg-[#6F4E37]/50">
                    Services
                </a>
            </li>

            <li>
                <a href="{{ route('home.showAboutPage') }}"
                    class="block rounded-lg px-4 py-3 transition hover:bg-[#6F4E37]/50">
                    About
                </a>
            </li>

            <li>
                <a href="{{ route('home.showContactPage') }}"
                    class="block rounded-lg px-4 py-3 transition hover:bg-[#6F4E37]/50">
                    Contacts
                </a>
            </li>

            <li class="mt-2 border-t border-white/20 pt-4">
                <a href="{{ route('login') }}"
                    class="block rounded-full bg-[#849753] px-6 py-3 text-center font-semibold text-white transition hover:bg-[#5a3f2d]">
                    Login
                </a>
            </li>

        </ul>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuButton = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');

        if (!menuButton || !mobileMenu) return;

        menuButton.addEventListener('click', function () {
            const isHidden = mobileMenu.classList.toggle('hidden');

            menuButton.setAttribute('aria-expanded', String(!isHidden));
            menuButton.setAttribute(
                'aria-label',
                isHidden ? 'Open navigation menu' : 'Close navigation menu'
            );

            menuIcon.innerHTML = isHidden
                ? '<path d="M4 6h16M4 12h16M4 18h16" />'
                : '<path d="m18 6-12 12M6 6l12 12" />';
        });

        mobileMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                mobileMenu.classList.add('hidden');
                menuButton.setAttribute('aria-expanded', 'false');
                menuButton.setAttribute('aria-label', 'Open navigation menu');
                menuIcon.innerHTML =
                    '<path d="M4 6h16M4 12h16M4 18h16" />';
            });
        });
    });
</script>
