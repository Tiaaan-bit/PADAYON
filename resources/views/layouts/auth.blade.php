<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ !empty($websiteSettings?->logo_path) ? asset('storage/' . $websiteSettings->logo_path) : asset('build/assets/images/logo.png') }}">


    <title>
        @yield('title')
    </title>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>


<body class="min-h-screen bg-[#D6BB9E] flex items-center justify-center p-4"style="background-image: url('{{ asset('images/background.png') }}');">


    @include('components.PageLoading')

    

            @yield('content')


    @stack('scripts')


</body>

</html>
