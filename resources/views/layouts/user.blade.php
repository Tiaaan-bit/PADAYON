<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Padayon Massage Center')
    </title>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire --}}
    @livewireStyles

    @stack('styles')
</head>

<body class="bg-[#D6BB9E] font-sans overflow-x-hidden">

    {{-- ========================================================= --}}
    {{-- FULL SCREEN NAVIGATION LOADING --}}
    {{-- ========================================================= --}}

    <div
        id="page-loading-screen"
        class="fixed inset-0 z-99999 hidden items-center justify-center bg-black/30 backdrop-blur-sm"
    >

        <div class="flex flex-col items-center gap-4">

            {{-- Spinner --}}
            <div
                class="w-14 h-14 rounded-full border-4 border-white/40 border-t-[#6F4E37] animate-spin">
            </div>

            {{-- Loading text --}}
            <p class="text-white font-semibold text-sm drop-shadow-lg">
                Loading...
            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- USER SIDEBAR --}}
    {{-- ========================================================= --}}

    @include('components.userSidebar')


    {{-- ========================================================= --}}
    {{-- MAIN PAGE CONTENT --}}
    {{-- ========================================================= --}}

    <main class="min-h-screen lg:pl-64 pb-20 lg:pb-6">

        @yield('content')

    </main>


    {{-- ========================================================= --}}
    {{-- PAGE SPECIFIC JAVASCRIPT --}}
    {{-- ========================================================= --}}

    @stack('scripts')


    {{-- ========================================================= --}}
    {{-- LIVEWIRE --}}
    {{-- ========================================================= --}}

    @livewireScripts


    {{-- ========================================================= --}}
    {{-- LIVEWIRE NAVIGATION LOADING --}}
    {{-- ========================================================= --}}

    <script>

        let loadingTimer = null;

        document.addEventListener('livewire:navigate', () => {

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


        document.addEventListener('livewire:navigated', () => {

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

</body>

</html>