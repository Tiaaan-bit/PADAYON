
<div
    id="page-loading-screen"
    class="fixed inset-0 z-99999 hidden items-center justify-center bg-black/30 backdrop-blur-sm"
    role="status"
    aria-live="polite"
>
    <div class="flex flex-col items-center gap-4">
        <div
            class="h-14 w-14 animate-spin rounded-full border-4 border-white/40 border-t-[#6F4E37]"
        ></div>

        <p class="text-sm font-semibold text-white drop-shadow-lg">
            Loading...
        </p>
    </div>
</div>

<script>
    (() => {
        const loaderId = 'page-loading-screen';

        function showLoader() {
            const loader = document.getElementById(loaderId);
            if (!loader) return;

            loader.classList.remove('hidden');
            loader.classList.add('flex');
        }

        function hideLoader() {
            const loader = document.getElementById(loaderId);
            if (!loader) return;

            loader.classList.add('hidden');
            loader.classList.remove('flex');
        }

        // Show loader for normal internal link navigation.
        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (!link) return;

            // Ignore modified clicks, new tabs, and non-left clicks.
            if (
                event.button !== 0 ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey
            ) return;

            // Ignore links that should not trigger a page loader.
            if (
                link.target === '_blank' ||
                link.hasAttribute('download') ||
                link.hasAttribute('data-no-loader') ||
                link.getAttribute('href')?.startsWith('#') ||
                link.getAttribute('href')?.startsWith('javascript:')
            ) return;

            const url = new URL(link.href, window.location.href);

            if (
                url.origin !== window.location.origin ||
                url.href === window.location.href
            ) return;

            // Allow other click handlers to cancel navigation.
            if (event.defaultPrevented) return;

            showLoader();
        }, true);

        // Hide loader when the new page has loaded.
        window.addEventListener('pageshow', hideLoader);

        // Hide loader when returning through browser back/forward cache.
        window.addEventListener('pagehide', () => {});

    })();
</script>
