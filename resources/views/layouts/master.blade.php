<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Gallery</title>
    <link rel="shortcut icon" href="{{ asset('images/icon/logo.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* * {
            border: 1px solid red;
        } */
    </style>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal flex flex-col min-h-screen">
    @if (!Request::is('auth/*'))
        @include('layouts.header')
    @endif

    <main class="flex-grow">
        @yield('content')
    </main>

    @stack('scripts')

    @if (!Request::is('auth/*'))
        @include('layouts.footer')
    @endif
</body>

</html>
