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
        <div class="d-flex min-vh-100 align-items-center bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6">
                        <div class="text-center my-4">
                            <a href="/">{{ config('app.name', 'Laravel') }}</a>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                @isset($slot)
                                    {{ $slot }}
                                @else
                                    @yield('content')
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
