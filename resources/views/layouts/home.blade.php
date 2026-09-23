<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Padayon Massage Center')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-[#F4EDDB] font-sans">

    {{-- Header --}}
    <x-header />

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <x-footer />

    {{-- Page-specific JavaScript --}}
    @stack('scripts')

</body>

</html>