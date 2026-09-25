<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Padayon Massage Center - Dashboard')
    </title>

    <link rel="icon" type="image/png" href="{{ asset('build/assets/images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>


<body class="bg-[#D6BB9E] font-sans overflow-x-hidden" x-data="{ sidebarOpen: false }"@close-sidebar.window="sidebarOpen = false">


    @include('components.PageLoading')

    @include('components.sidebar')
    
    @include('components.toast-notification')



    <div class="min-h-screen transition-all duration-300" :class="sidebarOpen ? 'lg:ml-60' : 'lg:ml-20'">


        @include('components.AdminHeader')


        <main class="min-h-screenpb-20lg:pb-6">

            @yield('content')

        </main>

    </div>


    @stack('scripts')


    @livewireScripts


</body>

</html>
