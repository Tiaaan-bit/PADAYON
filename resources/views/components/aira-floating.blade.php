@unless (request()->routeIs('user.ai.*'))
    <a id="aira-floating-button" href="{{ route('user.ai.index') }}" wire:navigate aria-label="Open Aira AI Assistant"
        title="Talk to Aira"
        class="fixed z-9999 bottom-24 right-4 block h-24 w-24 transition-all duration-300 hover:scale-105"
        style="position: fixed; bottom: 53px; right: 5px;">

        {{-- Animated Caption --}}
        <span
            class="aira-caption absolute -top-5 right-1 whitespace-nowrap
                   rounded-xl bg-[#6F4E37] px-4 py-2
                   text-xs font-bold text-white shadow-lg">

            Ask me!

            {{-- Speech bubble arrow --}}
            <span class="absolute -bottom-1 right-5 h-3 w-3 rotate-45 bg-[#6F4E37]"></span>
        </span>

        {{-- Aira Memoji --}}
        <img src="{{ asset('images/aira/idel1.gif') }}" alt="Aira AI Assistant"
            class="block h-full w-full object-contain bg-transparent drop-shadow-lg">
    </a>
@endunless
