@props([
    'active' => 'dashboard',
])

@php
    $menuItems = [
        ['key' => 'dashboard', 'label' => 'Beranda', 'icon' => 'dashboard', 'route' => 'internhub.dashboard'],
        ['key' => 'attendance', 'label' => 'Presensi', 'icon' => 'calendar_today', 'route' => 'internhub.attendance'],
        ['key' => 'logbook', 'label' => 'Laporan Harian', 'icon' => 'auto_stories', 'route' => 'internhub.daily-logbook'],
        ['key' => 'registration', 'label' => 'Pendaftaran Lokasi', 'icon' => 'app_registration', 'route' => 'internhub.registration'],
        ['key' => 'reviews', 'label' => 'Ulasan Pembimbing', 'icon' => 'rate_review', 'route' => 'internhub.mentor-review'],
        ['key' => 'administration', 'label' => 'Pusat Admin', 'icon' => 'admin_panel_settings', 'route' => 'internhub.admin-center'],
        ['key' => 'summary', 'label' => 'Rekap Bulanan', 'icon' => 'summarize', 'route' => 'internhub.monthly-summary'],
    ];
@endphp

<aside class="hidden lg:flex fixed inset-y-0 left-0 w-72 bg-surface-muted/95 border-r border-line px-4 py-6 flex-col">
    <div class="px-3 mb-8">
        <h2 class="text-xl font-black tracking-tight text-content">InternHub</h2>
        <p class="text-[11px] uppercase tracking-[0.2em] font-semibold text-content-muted mt-1">Sistem Monitoring Magang</p>
    </div>

    <nav class="flex-1 space-y-1">
        @foreach ($menuItems as $item)
            @php($isActive = $active === $item['key'])
            <a
                href="{{ route($item['route']) }}"
                class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm transition-all {{ $isActive ? 'bg-surface text-content shadow-sm font-semibold' : 'text-content-muted hover:text-content hover:bg-primary-soft/80 font-medium' }}"
            >
                <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mt-6 border-t border-line pt-4 space-y-2">
        <a href="{{ route('login') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm text-content-muted hover:text-content hover:bg-primary-soft/80">
            <span class="material-symbols-outlined">logout</span>
            <span>Keluar</span>
        </a>
    </div>
</aside>
