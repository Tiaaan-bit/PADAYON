<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Padayon Massage Center - Dashboard')
    </title>

    {{-- ========================================================= --}}
    {{-- VITE --}}
    {{-- ========================================================= --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ========================================================= --}}
    {{-- LIVEWIRE --}}
    {{-- ========================================================= --}}

    @livewireStyles

    {{-- ========================================================= --}}
    {{-- PAGE SPECIFIC STYLES --}}
    {{-- ========================================================= --}}

    @stack('styles')
</head>


<body class="bg-[#D6BB9E] font-sans overflow-x-hidden" x-data="{ sidebarOpen: false }" @close-sidebar.window="sidebarOpen = false">

    {{-- ========================================================= --}}
    {{-- FULL SCREEN NAVIGATION LOADING --}}
    {{-- ========================================================= --}}

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


    {{-- ========================================================= --}}
    {{-- ADMIN SIDEBAR --}}
    {{-- ========================================================= --}}

    @include('components.sidebar')


    {{-- ========================================================= --}}
    {{-- MAIN PAGE AREA --}}
    {{-- ========================================================= --}}

    <div class="min-h-screen transition-all duration-300" :class="sidebarOpen ? 'lg:ml-60' : 'lg:ml-20'">

        {{-- ===================================================== --}}
        {{-- HEADER --}}
        {{-- ===================================================== --}}

        <header class="sticky top-0 z-40
                   bg-[#D6BB9E]
                   border-b border-gray-200">

            <div
                class="flex items-center justify-between
                       h-20
                       px-4 sm:px-6 lg:px-8">

                {{-- ================================================= --}}
                {{-- LOGO + BRAND --}}
                {{-- ================================================= --}}

                <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 min-w-0">

                    {{-- Logo --}}
                    <img src="{{ asset('build/assets/images/logo.png') }}" alt="Padayon Massage Center Logo"
                        class="h-12 w-12 object-contain shrink-0">

                    {{-- Brand Text --}}
                    <div class="leading-tight min-w-0">

                        <h1
                            class="font-semibold
                                   text-[#2F2420]
                                   text-lg
                                   truncate">
                            Padayon Massage Center
                        </h1>

                        <p
                            class="text-xs
                                   text-gray-500
                                   truncate">
                            Blind Massage Specialists
                        </p>

                    </div>

                </a>


                {{-- ================================================= --}}
                {{-- HEADER RIGHT SIDE --}}
                {{-- ================================================= --}}

                <div class="flex items-center gap-3 sm:gap-4">

                    {{-- ============================================= --}}
                    {{-- ADMIN PROFILE --}}
                    {{-- ============================================= --}}
                    <div
                        class="flex items-center
                               gap-3
                               pl-3
                               border-l border-gray-200"
                    >
                    
                        {{-- Avatar --}}
                        <div
                            class="w-10 h-10
                                   rounded-full
                                   bg-[#6F4E37]
                                   flex items-center justify-center
                                   text-white
                                   font-bold
                                   text-sm
                                   shrink-0"
                        >
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    
                    
                        {{-- User Information --}}
                        <div class="hidden sm:block">
                    
                            <p
                                class="text-sm
                                       font-semibold
                                       text-gray-800"
                            >
                                {{ auth()->user()->name }}
                            </p>
    
                        </div>
                    
                    </div>
                
                </div>

            </div>

        </header>


        {{-- ===================================================== --}}
        {{-- PAGE CONTENT --}}
        {{-- ===================================================== --}}

        <main class="min-h-screen
                   pb-20
                   lg:pb-6">

            @yield('content')

        </main>

    </div>


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

</body>

</html>
