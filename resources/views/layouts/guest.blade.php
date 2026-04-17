<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('img/logo-new.jpeg') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo-new.jpeg') }}">
        
        <style>
            .w-20 {
                width: 18rem;
            }
            .h-20 {
                height: 16rem;
            }
        </style>
    </head>
    @php($isLoginPage = request()->routeIs('login'))
    <body class="font-sans text-gray-900 antialiased">
        @if ($isLoginPage)
            {{ $slot }}
        @else
            <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
                <div class="mb-6">
                    <a href="/">
                        <img src="{{ asset('img/logo-new.jpeg') }}" alt="Serene Kost Logo" class="w-20 h-20 rounded-2xl shadow-lg">
                    </a>
                </div>

                <div class="w-full sm:max-w-md px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        @endif
    </body>
</html>
