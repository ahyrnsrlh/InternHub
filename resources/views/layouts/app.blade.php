<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'InternHub'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/internhub.css') }}" rel="stylesheet">

    @stack('styles')
</head>
@php
    $hideChrome = trim($__env->yieldContent('hide_chrome')) === '1';
    $activeMenu = trim($__env->yieldContent('active_menu')) ?: 'dashboard';
    $navTitle = trim($__env->yieldContent('nav_title')) ?: 'Portal Manajemen';
    $searchPlaceholder = trim($__env->yieldContent('search_placeholder')) ?: 'Cari data...';
@endphp
<body class="@yield('body_class', 'internhub-shell text-content antialiased')">
    @if ($hideChrome)
        <main class="@yield('content_container_class', 'min-h-screen')">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    @else
        <div class="min-h-screen">
            @include('components.sidebar', ['active' => $activeMenu])
            @include('components.navbar', ['title' => $navTitle, 'searchPlaceholder' => $searchPlaceholder])

            <main class="pt-24 px-6 pb-10 lg:pl-80 lg:pr-8">
                <div class="max-w-7xl mx-auto space-y-8">
                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </main>

            <div class="lg:pl-72">
                @include('components.footer')
            </div>
        </div>
    @endif

    <script src="{{ asset('js/internhub.js') }}"></script>
    @stack('scripts')
</body>
</html>
