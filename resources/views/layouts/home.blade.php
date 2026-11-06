<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Padayon Massage Center')
    </title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#849753">

    <link rel="icon" type="image/png"
        href="{{ !empty($websiteSettings?->logo_path)
            ? asset('storage/' . $websiteSettings->logo_path)
            : asset('build/assets/images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>


<body class="bg-[#F4EDDB]  min-h-screen bg-cover bg-center bg-no-repeat bg-fixed"
    style="background-image: url('{{ asset('images/background.png') }}');">

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

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', async () => {
                try {
                    const registration =
                        await navigator.serviceWorker.register('/sw.js');

                    console.log(
                        'Padayon PWA registered:',
                        registration.scope
                    );

                } catch (error) {
                    console.error(
                        'Padayon PWA registration failed:',
                        error
                    );
                }
            });
        }
    </script>
</body>


</html>
