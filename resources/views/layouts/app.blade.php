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
    </head>
    <body>
        @include('layouts.navigation')

        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-light border-bottom py-3">
                <div class="container">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>
    </body>
</html>
