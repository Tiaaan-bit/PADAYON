<div id="page-loading-screen"
    class="fixed inset-0 z-99999 hidden items-center justify-center
               bg-black/30 backdrop-blur-sm">

    <div class="flex flex-col items-center gap-4">

        {{-- Spinner --}}
        <div
            class="w-14 h-14 rounded-full
                       border-4 border-white/40
                       border-t-[#6F4E37]
                       animate-spin">
        </div>

        {{-- Loading Text --}}
        <p class="text-white font-semibold text-sm drop-shadow-lg">
            Loading...
        </p>

    </div>

</div>

<script>
    let loadingTimer = null;


    /*
    |--------------------------------------------------------------------------
    | BEFORE NAVIGATION
    |--------------------------------------------------------------------------
    */

    document.addEventListener('livewire:navigate', () => {

        /*
        |--------------------------------------------------------------------------
        | Always close sidebar when changing page
        |--------------------------------------------------------------------------
        */

        window.dispatchEvent(
            new CustomEvent('close-sidebar')
        );


        /*
        |--------------------------------------------------------------------------
        | Loading screen
        |--------------------------------------------------------------------------
        */

        const loadingScreen =
            document.getElementById('page-loading-screen');

        if (!loadingScreen) {
            return;
        }

        clearTimeout(loadingTimer);

        loadingTimer = setTimeout(() => {

            loadingScreen.classList.remove('hidden');

            loadingScreen.classList.add('flex');

        }, 150);

    });


    /*
    |--------------------------------------------------------------------------
    | AFTER NAVIGATION
    |--------------------------------------------------------------------------
    */

    document.addEventListener('livewire:navigated', () => {

        /*
        |--------------------------------------------------------------------------
        | Make absolutely sure sidebar stays closed
        |--------------------------------------------------------------------------
        */

        window.dispatchEvent(
            new CustomEvent('close-sidebar')
        );


        /*
        |--------------------------------------------------------------------------
        | Hide loading screen
        |--------------------------------------------------------------------------
        */

        const loadingScreen =
            document.getElementById('page-loading-screen');

        clearTimeout(loadingTimer);

        if (!loadingScreen) {
            return;
        }

        loadingScreen.classList.add('hidden');

        loadingScreen.classList.remove('flex');

    });
</script>
