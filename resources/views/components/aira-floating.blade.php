@unless (request()->routeIs('user.ai.*'))
    <a id="aira-floating-button" href="{{ route('user.ai.index') }}" wire:navigate aria-label="Open Aira AI Assistant"
        title="Talk to Aira"
        class="fixed z-9999 bottom-24 right-4
               block h-24 w-24
               transition-all duration-300 hover:scale-105"
        style="position: fixed; bottom: 53px; right: 5px;">

        <img src="{{ asset('images/aira/idel1.gif') }}" alt="Aira AI Assistant"
            class="block h-full w-full object-contain bg-transparent drop-shadow-lg">
    </a>
@endunless
