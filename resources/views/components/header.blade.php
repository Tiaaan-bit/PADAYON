<nav
    class="h-17.5 relative w-full px-6 md:px-16 lg:px-24 xl:px-32 flex items-center justify-between z-30 bg-[#849753] border-b-2 border-[#6F4E37] transition-all">

    <div>
        <h1 class =" text-white">Padayon Massage Center</h1>
        <span class="font-bold text-white text-xs">Blind Massage Specialists | Powered by DiverseCare Wellness Hub</span>
    </div>



    <ul class="items-center hidden gap-10 text-white md:flex">
        <li><a class="transition hover:border-b-2 border-[#2F2420]" href="{{ route('home.showHomePage') }}">Home</a></li>
        <li><a class="transition hover:border-b-2 border-[#2F2420]" href="{{ route('home.showServicesPage') }}">Services</a>
        </li>
        <li><a class="transition hover:border-b-2 border-[#2F2420]" href="{{ route('home.showAboutPage') }}">About</a></li>
        <li><a class="transition hover:border-b-2 border-[#2F2420]" href="{{ route('home.showContactPage') }}">Contacts</a>
        </li>
    </ul>

    <a href="{{ route('login') }}"
        class="hidden w-40 text-sm text-white transition-all bg-[#6F4E37] rounded-full md:inline hover:opacity-90 active:scale-95 h-11 text-center leading-11 font-medium">
        Login
    </a>

    <button aria-label="menu-btn" type="button" class="inline-block transition menu-btn md:hidden active:scale-90">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="#fff">
            <path
                d="M3 7a1 1 0 1 0 0 2h24a1 1 0 1 0 0-2zm0 7a1 1 0 1 0 0 2h24a1 1 0 1 0 0-2zm0 7a1 1 0 1 0 0 2h24a1 1 0 1 0 0-2z" />
        </svg>
    </button>

    <div class="mobile-menu absolute top-17.5 left-0 w-full bg-[#849753] p-6 hidden md:hidden z-50 shadow-xl">
        <ul class="flex flex-col space-y-4 text-lg text-white">
            <li>
                <a href="{{ route('home.showHomePage') }}"
                    class="block py-3 px-4 text-base font-medium hover:bg-[#6F4E37]/50 rounded-lg transition-all duration-200 hover:scale-[1.02]">
                    Home
                </a>
            </li>
            <li>
                <a href="{{ route('home.showServicesPage') }}"
                    class="block py-3 px-4 text-base font-medium hover:bg-[#6F4E37]/50 rounded-lg transition-all duration-200 hover:scale-[1.02]">
                    Services
                </a>
            </li>
            <li>
                <a href="{{ route('home.showAboutPage') }}"
                    class="block py-3 px-4 text-base font-medium hover:bg-[#6F4E37]/50 rounded-lg transition-all duration-200 hover:scale-[1.02]">
                    About
                </a>
            </li>
            <li>
                <a href="{{ route('home.showContactPage') }}"
                    class="block py-3 px-4 text-base font-medium hover:bg-[#6F4E37]/50 rounded-lg transition-all duration-200 hover:scale-[1.02]">
                    Contacts
                </a>
            </li>
            <li class="mt-6 pt-4 border-t border-[#6F4E37]/50">
                <a href="{{ route('login') }}"
                    class="block w-full text-base font-semibold text-white transition-all duration-200 bg-[#6F4E37] rounded-full py-4 px-6 text-center hover:bg-[#5a3f2d] active:scale-95 hover:shadow-lg">
                    Login
                </a>
            </li>
        </ul>
    </div>
</nav>

<script>
    const menuButtons = document.querySelectorAll('.menu-btn');
    const mobileMenus = document.querySelectorAll('.mobile-menu');

    menuButtons.forEach((btn, index) => {
        btn.addEventListener('click', () => {
            mobileMenus[index].classList.toggle('hidden');
        });
    });
</script>
