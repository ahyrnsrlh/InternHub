<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InternHub Administrator')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased" style="font-family: 'Manrope', sans-serif;">
    <div class="min-h-screen" x-data="{ sidebarOpen: false, profileOpen: false }">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-gray-900/40 lg:hidden" @click="sidebarOpen = false"></div>

        <aside
            class="fixed inset-y-0 left-0 z-40 w-72 transform border-r border-gray-200 bg-white transition-transform duration-300 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center justify-between border-b border-gray-200 px-6">
                <div class="min-w-0">
                    <img src="{{ asset('logo-internhub.png') }}" alt="Logo InternHub" class="h-12 w-auto object-contain">
                                    </div>
                <button class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" @click="sidebarOpen = false">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="space-y-1 p-4">
                @php
                    $menu = [
                        ['label' => 'Beranda', 'route' => 'internhub.admin.dashboard', 'icon' => 'home'],
                        ['label' => 'Peserta Magang', 'route' => 'internhub.admin.interns', 'icon' => 'users'],
                        ['label' => 'Kehadiran', 'route' => 'internhub.admin.attendance', 'icon' => 'clock'],
                        ['label' => 'Lokasi Magang', 'route' => 'internhub.admin.locations', 'icon' => 'pin'],
                        ['label' => 'Laporan', 'route' => 'internhub.admin.reports', 'icon' => 'chart'],
                    ];
                @endphp

                @foreach ($menu as $item)
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs($item['route']) ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        @if ($item['icon'] === 'home')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-10.5z" /></svg>
                        @elseif ($item['icon'] === 'users')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 19a4 4 0 00-8 0"/><circle cx="12" cy="11" r="3" stroke-width="1.75"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 19a4 4 0 00-2.5-3.7M18 19a4 4 0 012.5-3.7"/></svg>
                        @elseif ($item['icon'] === 'clock')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.75"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 7v5l3 2"/></svg>
                        @elseif ($item['icon'] === 'pin')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 21s7-5.5 7-11a7 7 0 10-14 0c0 5.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5" stroke-width="1.75"/></svg>
                        @elseif ($item['icon'] === 'chart')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 20V10m7 10V4m7 16v-7"/></svg>
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
                        <button class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" @click="sidebarOpen = true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h2 class="text-lg font-semibold text-gray-900">@yield('header', 'Beranda Admin')</h2>
                    </div>

                    <div class="relative" x-data>
                        <button class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm hover:border-indigo-200" @click="profileOpen = !profileOpen">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700"></div>
                            <span class="hidden font-medium text-gray-700 sm:block">Admin</span>
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="profileOpen" x-transition class="absolute right-0 z-30 mt-2 w-48 rounded-xl border border-gray-200 bg-white p-2 shadow-sm" @click.outside="profileOpen = false" style="display: none;">
                            <a href="{{ route('internhub.admin.profile') }}" class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-100">Pengaturan Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Keluar</button>
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
