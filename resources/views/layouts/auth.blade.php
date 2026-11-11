<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png"
        href="{{ !empty($websiteSettings?->logo_path) ? asset('storage/' . $websiteSettings->logo_path) : asset('build/assets/images/logo.png') }}">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#849753">


    <title>
        @yield('title')
    </title>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>


<body
    class="min-h-screen bg-[#D6BB9E] flex items-center justify-center p-4"style="background-image: url('{{ asset('images/background.png') }}');">


    @include('components.AuthLoading')



    @yield('content')


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
