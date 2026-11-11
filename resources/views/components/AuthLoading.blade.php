<div id="page-loading-screen"
    class="fixed inset-0 z-999999 flex items-center justify-center bg-[#F8F6F2]/95 backdrop-blur-sm"
    style="display: none;" role="status" aria-live="polite" aria-label="Processing request">
    <div class="flex flex-col items-center gap-5 px-6 text-center">


        {{-- Loading Message --}}
        <div class="space-y-2">
            <h2 id="page-loading-title" class="text-lg font-semibold text-[#6F4E37]">
                Please wait...
            </h2>
        </div>

        {{-- Animated Dots --}}
        <div class="flex items-center gap-1.5">
            <span class="h-2 w-2 animate-bounce rounded-full bg-[#849753] [animation-delay:-0.3s]"></span>
            <span class="h-2 w-2 animate-bounce rounded-full bg-[#849753] [animation-delay:-0.15s]"></span>
            <span class="h-2 w-2 animate-bounce rounded-full bg-[#849753]"></span>
        </div>

    </div>
</div>

<script>
    (() => {
        if (window.__padayonPageLoaderInitialized) return;
        window.__padayonPageLoaderInitialized = true;

        let requests = 0;
        let navigating = false;
        let hideTimer;

        function loader() {
            return document.getElementById('page-loading-screen');
        }

        function title() {
            return document.getElementById('page-loading-title');
        }

        function message() {
            return document.getElementById('page-loading-message');
        }

        function setLoadingText(heading, description) {
            if (title()) title().textContent = heading;
            if (message()) message().textContent = description;
        }

        function showLoader(heading = 'Please wait...', description = "We're preparing everything for you.") {
            const screen = loader();

            if (!screen) return;

            clearTimeout(hideTimer);

            setLoadingText(heading, description);

            screen.style.display = 'flex';
        }

        function hideLoader(force = false) {
            if (force) {
                requests = 0;
                navigating = false;
            }

            if (requests > 0 || navigating) return;

            clearTimeout(hideTimer);

            hideTimer = setTimeout(() => {
                if (requests > 0 || navigating) return;

                const screen = loader();

                if (screen) {
                    screen.style.display = 'none';
                }
            }, 200);
        }

        // Internal navigation links.
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
                event.defaultPrevented ||
                link.target === '_blank' ||
                link.hasAttribute('download') ||
                link.hasAttribute('data-no-loader') ||
                link.hasAttribute('data-bs-toggle') ||
                link.getAttribute('aria-disabled') === 'true'
            ) return;

            const href = link.getAttribute('href');

            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }

            try {
                const url = new URL(link.href, window.location.href);

                if (
                    url.origin === window.location.origin &&
                    url.href !== window.location.href
                ) {
                    showLoader(
                        'Loading page...',
                        'Please wait while we prepare your page.'
                    );
                }
            } catch {}
        }, true);

        // Standard Blade forms.
        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement)) return;

            if (
                event.defaultPrevented ||
                form.hasAttribute('data-no-loader') ||
                form.hasAttribute('wire:submit') ||
                form.target === '_blank'
            ) return;

            const formId = (form.id || '').toLowerCase();
            const formAction = (form.getAttribute('action') || '').toLowerCase();
            const formText = (form.innerText || '').toLowerCase();

            let heading = 'Processing your request...';
            let description = 'Please wait while we process your information.';

            if (
                formId.includes('register') ||
                formAction.includes('register') ||
                formText.includes('create account') ||
                formText.includes('register')
            ) {
                heading = 'Registering your account...';
                description = 'Please wait while we create your Padayon account.';
            } else if (
                formId.includes('login') ||
                formAction.includes('login') ||
                formText.includes('log in') ||
                formText.includes('sign in')
            ) {
                heading = 'Logging in to your account...';
                description = 'Please wait while we securely sign you in.';
            } else if (
                formId.includes('password') ||
                formAction.includes('password')
            ) {
                heading = 'Updating your password...';
                description = 'Please wait while we process your password request.';
            }

            requests++;
            showLoader(heading, description);
        }, true);

        // Livewire 3 requests.
        function attachLivewire() {
            if (
                !window.Livewire ||
                window.__padayonLivewireLoaderAttached
            ) return;

            window.__padayonLivewireLoaderAttached = true;

            Livewire.hook('request', ({
                succeed,
                fail
            }) => {
                requests++;

                showLoader(
                    'Processing your request...',
                    'Please wait while we update your information.'
                );

                let finished = false;

                const finish = () => {
                    if (finished) return;

                    finished = true;
                    requests = Math.max(0, requests - 1);

                    hideLoader();
                };

                succeed(finish);
                fail(finish);
            });
        }

        document.addEventListener('livewire:init', attachLivewire);
        attachLivewire();

        // Livewire wire:navigate.
        document.addEventListener('livewire:navigate', () => {
            navigating = true;

            showLoader(
                'Loading page...',
                'Please wait while we prepare your page.'
            );
        });

        document.addEventListener('livewire:navigated', () => {
            navigating = false;
            hideLoader();
        });

        // Reset when a page loads or is restored.
        window.addEventListener('pageshow', () => hideLoader(true));

        window.addEventListener('pagehide', () => {
            clearTimeout(hideTimer);
        });
    })();
</script>
