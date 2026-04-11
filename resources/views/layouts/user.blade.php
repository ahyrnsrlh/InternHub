<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InternHub User')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --color-primary: #4f46e5;
            --color-bg: #f9fafb;
            --color-text: #1f2937;
            --color-success: #22c55e;
            --color-error: #ef4444;
        }
        body { font-family: 'Manrope', sans-serif; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ sidebarOpen: false, profileOpen: false }">
    <div class="min-h-screen">
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <aside
            class="fixed inset-y-0 left-0 z-40 w-72 transform border-r border-gray-200 bg-white transition-transform duration-300 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center justify-between border-b border-gray-200 px-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">InternHub</p>
                    <h1 class="text-lg font-bold text-gray-900">GPS Internship</h1>
                </div>
                <button class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" @click="sidebarOpen = false" aria-label="Close sidebar">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <nav class="space-y-1 p-4">
                @php
                    $menu = [
                        ['label' => 'Dashboard', 'route' => 'user.dashboard.index', 'icon' => 'home'],
                        ['label' => 'Attendance', 'route' => 'user.attendance.index', 'icon' => 'clock'],
                        ['label' => 'Locations', 'route' => 'user.locations.index', 'icon' => 'pin'],
                        ['label' => 'Map', 'route' => 'user.map.index', 'icon' => 'map'],
                        ['label' => 'Logbook', 'route' => 'user.logbook.index', 'icon' => 'book'],
                        ['label' => 'Reports', 'route' => 'user.reports.index', 'icon' => 'table'],
                        ['label' => 'Recap', 'route' => 'user.recap.index', 'icon' => 'chart'],
                        ['label' => 'Profile', 'route' => 'user.profile.index', 'icon' => 'user'],
                    ];
                @endphp

                @foreach ($menu as $item)
                    @php
                        $active = request()->routeIs($item['route']);
                    @endphp
                    <a
                        href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                        class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                    >
                        @if ($item['icon'] === 'home')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-10.5z" /></svg>
                        @elseif ($item['icon'] === 'clock')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.75"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 7v5l3 2"/></svg>
                        @elseif ($item['icon'] === 'pin')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 21s7-5.5 7-11a7 7 0 10-14 0c0 5.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5" stroke-width="1.75"/></svg>
                        @elseif ($item['icon'] === 'map')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 4l6-2 6 2v16l-6-2-6 2-6-2V4l6 2z"/></svg>
                        @elseif ($item['icon'] === 'book')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 4h12a2 2 0 012 2v12H7a2 2 0 00-2 2V4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 20h12"/></svg>
                        @elseif ($item['icon'] === 'table')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16M8 4v16M16 4v16"/></svg>
                        @elseif ($item['icon'] === 'chart')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 20V10m7 10V4m7 16v-7"/></svg>
                        @else
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-4.5 0-8 2-8 4.5V21h16v-2.5C20 16 16.5 14 12 14z"/></svg>
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="lg:pl-72">
            <header class="sticky top-0 z-20 border-b border-gray-200 bg-white/90 backdrop-blur">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" @click="sidebarOpen = true" aria-label="Open sidebar">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        <h2 class="text-lg font-semibold text-gray-900">@yield('header', 'Intern Dashboard')</h2>
                    </div>

                    <div class="relative flex items-center gap-3">
                        <button class="rounded-full border border-gray-200 bg-white p-2 text-gray-500 hover:border-indigo-200 hover:text-indigo-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0" /></svg>
                        </button>
                        <button class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700" @click="profileOpen = !profileOpen" aria-label="Open profile menu"></button>

                        <div x-show="profileOpen" x-transition class="absolute right-0 top-12 z-30 w-52 rounded-xl border border-gray-200 bg-white p-2 shadow-sm" @click.outside="profileOpen = false" style="display: none;">
                            <a href="{{ route('user.profile.index') }}" class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-100">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <x-toast />

    <div
        id="flash-data"
        hidden
        data-status="{{ session('status', '') }}"
        data-error="{{ session('error') ?? ($errors->any() ? $errors->first() : '') }}"
    ></div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const flash = document.getElementById('flash-data');
            if (!flash) {
                return;
            }

            const status = flash.dataset.status;
            const error = flash.dataset.error;

            if (status) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: status, type: 'success' }
                }));
            }

            if (error) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: error, type: 'error' }
                }));
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
