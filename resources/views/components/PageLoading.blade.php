<div id="page-loading-screen"
    class="fixed inset-0 z-99999 hidden items-center justify-center bg-transparent backdrop-blur-sm" role="status"
    aria-live="polite" aria-label="Processing your request" aria-hidden="true">
    <div class="flex flex-col items-center gap-4 px-6 text-center">
        <div class="h-14 w-14 animate-spin rounded-full border-4 border-[#849753]/25 border-t-[#6F4E37]"
            aria-hidden="true"></div>

        <h2 class="text-lg font-semibold text-[#6F4E37]">
            Please wait do not close or refresh this page
        </h2>
    </div>
</div>

<script>
    (() => {
        const loaderId = 'page-loading-screen';

        // Prevent duplicate initialization.
        if (window.__padayonPageLoaderInitialized) return;
        window.__padayonPageLoaderInitialized = true;

        let activeRequests = 0;
        let navigationInProgress = false;
        let loaderTimer = null;

        function showLoader() {
            const loader = document.getElementById(loaderId);

            if (!loader) return;

            clearTimeout(loaderTimer);

            loader.classList.remove('hidden');
            loader.classList.add('flex');
            loader.setAttribute('aria-hidden', 'false');
        }

        function hideLoader(force = false) {
            const loader = document.getElementById(loaderId);

            if (!loader) return;

            if (force) {
                activeRequests = 0;
                navigationInProgress = false;
            }

            // Do not hide while another request is still running.
            if (activeRequests > 0 || navigationInProgress) return;

            loaderTimer = setTimeout(() => {
                if (activeRequests > 0 || navigationInProgress) return;

                loader.classList.add('hidden');
                loader.classList.remove('flex');
                loader.setAttribute('aria-hidden', 'true');
            }, 150);
        }

        // Normal internal link navigation.
        document.addEventListener('click', (event) => {
            const target = event.target;

            if (!(target instanceof Element)) return;

            const link = target.closest('a[href]');

            if (!link) return;

            if (
                event.button !== 0 ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey ||
                event.defaultPrevented
            ) return;

            if (
                link.target === '_blank' ||
                link.hasAttribute('download') ||
                link.hasAttribute('data-no-loader') ||
                link.hasAttribute('data-bs-toggle') ||
                link.getAttribute('href')?.startsWith('#') ||
                link.getAttribute('href')?.startsWith('javascript:')
            ) return;

            if (link.getAttribute('aria-disabled') === 'true') return;

            let url;

            try {
                url = new URL(link.href, window.location.href);
            } catch {
                return;
            }

            if (
                url.origin !== window.location.origin ||
                url.href === window.location.href
            ) return;

            showLoader();
        }, true);

        // Regular Blade form submissions.
        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement)) return;

            if (
                event.defaultPrevented ||
                form.hasAttribute('data-no-loader')
            ) return;

            // Livewire requests are handled by Livewire's request hook.
            if (form.hasAttribute('wire:submit')) return;

            // Do not show a loader for forms that open another tab.
            if (form.target === '_blank') return;

            activeRequests++;
            showLoader();
        }, true);

        // Livewire 3 request and navigation support.
        function attachLivewireHooks() {
            if (!window.Livewire || window.__padayonLivewireHooksAttached) {
                return;
            }

            window.__padayonLivewireHooksAttached = true;

            Livewire.hook('request', ({
                succeed,
                fail
            }) => {
                activeRequests++;
                showLoader();

                let completed = false;

                const finishRequest = () => {
                    if (completed) return;

                    completed = true;
                    activeRequests = Math.max(0, activeRequests - 1);

                    hideLoader();
                };

                succeed(() => finishRequest());
                fail(() => finishRequest());
            });
        }

        document.addEventListener('livewire:init', attachLivewireHooks);

        // Support cases where Livewire initialized before this script.
        attachLivewireHooks();

        // Livewire wire:navigate events.
        document.addEventListener('livewire:navigate', () => {
            navigationInProgress = true;
            showLoader();
        });

        document.addEventListener('livewire:navigated', () => {
            navigationInProgress = false;
            hideLoader();
        });

        // Hide when a normal page finishes loading or is restored.
        window.addEventListener('pageshow', () => {
            hideLoader(true);
        });

        // Reset the overlay state when leaving a normal page.
        window.addEventListener('pagehide', () => {
            clearTimeout(loaderTimer);
        });

    })();
</script>
