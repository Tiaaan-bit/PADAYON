<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Padayon Massage Center')
    </title>

    <link rel="icon" type="image/png"
        href="{{ !empty($websiteSettings?->logo_path)
            ? asset('storage/' . $websiteSettings->logo_path)
            : asset('build/assets/images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>


<body class="bg-[#F4EDDB]  min-h-screen bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url('{{ asset('images/background.png') }}');">

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
