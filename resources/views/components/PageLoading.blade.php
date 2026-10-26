<div
    id="page-loading-screen"
    class="fixed top-0 left-0 z-999999 h-1 w-full overflow-hidden"
    style="display: none;"
    role="status"
    aria-label="Loading page"
    aria-live="polite"
>
    <div
        id="page-loading-progress"
        class="h-full w-0 bg-[#849753]"
        style="width: 0%; transition: width 0.25s ease;"
    ></div>
</div>

<script>
(() => {
    if (window.__padayonPageLoaderInitialized) return;
    window.__padayonPageLoaderInitialized = true;

    let requests = 0;
    let navigating = false;
    let progress = 0;
    let progressTimer;
    let hideTimer;

    function loader() {
        return document.getElementById('page-loading-screen');
    }

    function bar() {
        return document.getElementById('page-loading-progress');
    }

    function showLoader() {
        const screen = loader();
        const progressBar = bar();

        if (!screen || !progressBar) return;

        clearTimeout(hideTimer);
        clearInterval(progressTimer);

        screen.style.display = 'block';

        if (progress === 0) {
            progressBar.style.transition = 'none';
            progressBar.style.width = '0%';

            requestAnimationFrame(() => {
                progressBar.style.transition = 'width 0.25s ease';
                progress = 10;
                progressBar.style.width = progress + '%';
            });
        }

        progressTimer = setInterval(() => {
            if (progress < 90) {
                progress += Math.max(0.5, (90 - progress) * 0.08);
                progressBar.style.width = progress + '%';
            }
        }, 150);
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

            clearInterval(progressTimer);

            const screen = loader();
            const progressBar = bar();

            if (!screen || !progressBar) return;

            progress = 100;
            progressBar.style.width = '100%';

            setTimeout(() => {
                if (requests > 0 || navigating) return;

                screen.style.display = 'none';
                progress = 0;
                progressBar.style.transition = 'none';
                progressBar.style.width = '0%';

                requestAnimationFrame(() => {
                    progressBar.style.transition = 'width 0.25s ease';
                });
            }, 200);
        }, 150);
    }

    // Normal internal links.
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
                showLoader();
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

        requests++;
        showLoader();
    }, true);

    // Livewire 3 requests.
    function attachLivewire() {
        if (
            !window.Livewire ||
            window.__padayonLivewireLoaderAttached
        ) return;

        window.__padayonLivewireLoaderAttached = true;

        Livewire.hook('request', ({ succeed, fail }) => {
            requests++;
            showLoader();

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
        showLoader();
    });

    document.addEventListener('livewire:navigated', () => {
        navigating = false;
        hideLoader();
    });

    // Reset when a page loads or is restored.
    window.addEventListener('pageshow', () => hideLoader(true));

    window.addEventListener('pagehide', () => {
        clearTimeout(hideTimer);
        clearInterval(progressTimer);
    });
})();
</script>
