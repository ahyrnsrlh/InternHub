<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InternHub Admin')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased" style="font-family: 'Manrope', sans-serif;">
    <div class="min-h-screen lg:grid lg:grid-cols-[280px,1fr]">
        <aside class="border-r border-gray-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">InternHub</p>
            <h1 class="mt-1 text-lg font-bold text-gray-900">Admin Console</h1>

            <nav class="mt-8 space-y-2">
                <a href="{{ route('internhub.admin.dashboard') }}" class="flex items-center rounded-xl px-3 py-2 text-sm font-medium {{ request()->routeIs('internhub.admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    Dashboard
                </a>
            </nav>
        </aside>

        <div>
            <header class="border-b border-gray-200 bg-white px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">@yield('header', 'Admin Dashboard')</h2>
            </header>

            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
