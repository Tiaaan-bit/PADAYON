{{-- ============================================================= --}}
{{-- FLOATING ADMIN AI ASSISTANT --}}
{{-- ============================================================= --}}

@unless (request()->routeIs('admin.ai.*'))
    <a href="{{ route('admin.ai.index') }}"
        wire:navigate
        aria-label="Open Admin AI Assistant"
        title="Open Admin AI Assistant"
        class="fixed z-9999 bottom-24 right-4
               block h-24 w-24
               transition-transform duration-300 hover:scale-105
               sm:bottom-6 sm:right-6 sm:h-28 sm:w-28">

        <img src="{{ asset('images/aira/idel1.gif') }}"
            alt="Admin AI Assistant"
            class="block h-full w-full object-contain bg-transparent drop-shadow-lg">
    </a>
@endunless