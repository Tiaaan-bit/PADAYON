@unless (request()->routeIs('user.ai.*'))
    <a href="{{ route('user.ai.index') }}" wire:navigate aria-label="Open Aira AI Assistant" title="Talk to Aira"
        class="fixed z-9999 bottom-24 right-4
               block h-24 w-24
               transition-transform duration-300 hover:scale-105
               sm:bottom-6 sm:right-6 sm:h-28 sm:w-28"
        style="position: fixed; bottom: 24px; right: 16px;">
        <img src="{{ asset('images/aira/idel1.gif') }}" alt="Aira AI Assistant"
            class="block h-full w-full object-contain bg-transparent drop-shadow-lg">
    </a>
@endunless
