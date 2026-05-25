<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vai Ter Pelada') }}</title>
        @include('partials.favicons')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen flex-col bg-gray-100">
            <main class="flex flex-1 flex-col items-center justify-center px-4 py-8">
                <div>
                    <a href="/">
                        <x-application-logo class="h-24 w-auto" />
                    </a>
                </div>

                <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                    {{ $slot }}
                </div>
            </main>

            @include('partials.legal-footer')
        </div>
    </body>
</html>
