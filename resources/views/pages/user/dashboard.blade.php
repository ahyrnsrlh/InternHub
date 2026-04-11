@extends('layouts.user')

@section('title', 'Beranda Peserta')
@section('header', 'Beranda')

@section('content')
<div
    class="space-y-6"
    data-attendance-trend-url="{{ route('user.dashboard.charts.attendance-trend') }}"
    data-validation-url="{{ route('user.dashboard.charts.validation') }}"
    data-activity-url="{{ route('user.dashboard.charts.activity') }}"
>
    @if (!(bool) ($summary['checked_in_today'] ?? false))
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
            Pengingat: Anda belum melakukan presensi hari ini.
            <a href="{{ route('user.attendance.index') }}" class="ml-1 font-semibold text-amber-800 underline">Lakukan presensi sekarang</a>
        </div>
    @endif

    <section class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-600 to-indigo-500 p-6 text-white shadow-sm">
        <p class="text-sm text-indigo-100">Selamat datang kembali, Peserta Magang</p>
        <h1 class="mt-1 text-2xl font-bold">Sistem Monitoring Magang Berbasis GPS</h1>
        <p class="mt-2 max-w-2xl text-sm text-indigo-100">Kelola presensi, Laporan Harian, dan perkembangan magang dalam satu portal terintegrasi.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2">
        <x-card>
            <p class="text-sm text-gray-500">Total Kehadiran</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['total_attendance'] ?? 0 }}</h3>
            <p class="mt-2 text-xs text-green-500">{{ $summary['attendance_rate'] ?? 0 }}% valid</p>
        </x-card>

        <x-card>
            <p class="text-sm text-gray-500">Total Aktivitas</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['total_activities'] ?? 0 }}</h3>
            <p class="mt-2 text-xs text-green-500">{{ $summary['activities_this_week'] ?? 0 }} aktivitas tercatat minggu ini</p>
        </x-card>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-3">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Aksi Cepat</h3>
                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-600">Hari Ini</span>
            </div>
            <div class="grid gap-3 sm:grid-cols-3">
                <a href="{{ route('user.attendance.index') }}" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Buka Presensi</a>
                <a href="{{ route('user.logbook.index') }}" class="inline-flex w-full justify-center rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">Tambah Catatan</a>
                <a href="{{ route('user.reports.index') }}" class="inline-flex w-full justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">Buka Laporan</a>
            </div>
        </x-card>
    </section>

    <section class="grid gap-4 xl:grid-cols-3">
        <x-card class="xl:col-span-2" title="Attendance Trend (7 Hari)" subtitle="Status hadir vs tidak hadir per hari.">
            <div class="h-80">
                <canvas id="userAttendanceTrendChart"></canvas>
            </div>
        </x-card>

        <x-card title="Validasi Presensi" subtitle="Perbandingan presensi valid dan invalid.">
            <div class="h-80">
                <canvas id="userValidationDonutChart"></canvas>
            </div>
        </x-card>
    </section>

    <section class="grid gap-4 xl:grid-cols-3">
        <x-card class="xl:col-span-2" title="Aktivitas Harian" subtitle="Jumlah catatan aktivitas per hari (7 hari terakhir).">
            <div class="h-80">
                <canvas id="userActivityBarChart"></canvas>
            </div>
        </x-card>

        <x-card title="Kehadiran Terbaru" subtitle="Log presensi terbaru Anda.">
            @if(($recentAttendances ?? collect())->count())
                <ul class="space-y-2 text-sm text-gray-600">
                    @foreach(($recentAttendances ?? collect()) as $attendance)
                        <li class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2">
                            <span>{{ optional($attendance->check_in_time)->format('d M Y H:i') ?? '-' }}</span>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $attendance->status === 'valid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($attendance->status) }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500">Belum ada data kehadiran.</p>
            @endif
        </x-card>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', async () => {
        const container = document.querySelector('[data-attendance-trend-url]');
        if (!container || typeof Chart === 'undefined') {
            return;
        }

        const fetchJson = async (url) => {
            const response = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            if (!response.ok) {
                throw new Error('Failed fetching chart data');
            }

            return response.json();
        };

        try {
            const [attendanceTrend, validationStats, activityStats] = await Promise.all([
                fetchJson(container.dataset.attendanceTrendUrl),
                fetchJson(container.dataset.validationUrl),
                fetchJson(container.dataset.activityUrl),
            ]);

            new Chart(document.getElementById('userAttendanceTrendChart'), {
                type: 'line',
                data: {
                    labels: attendanceTrend.labels,
                    datasets: [
                        {
                            label: 'Present',
                            data: attendanceTrend.present,
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.2)',
                            tension: 0.35,
                            fill: true,
                        },
                        {
                            label: 'Absent',
                            data: attendanceTrend.absent,
                            borderColor: '#dc2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.12)',
                            tension: 0.35,
                            fill: true,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false },
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 1,
                            ticks: { stepSize: 1 },
                            title: { display: true, text: 'Status (1 = hadir, 0 = tidak hadir)' },
                        },
                    },
                },
            });

            new Chart(document.getElementById('userValidationDonutChart'), {
                type: 'doughnut',
                data: {
                    labels: validationStats.labels,
                    datasets: [{
                        data: validationStats.values,
                        backgroundColor: ['#16a34a', '#dc2626'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 850, easing: 'easeOutCubic' },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { enabled: true },
                    },
                },
            });

            new Chart(document.getElementById('userActivityBarChart'), {
                type: 'bar',
                data: {
                    labels: activityStats.labels,
                    datasets: [{
                        label: 'Jumlah Aktivitas',
                        data: activityStats.values,
                        backgroundColor: '#4f46e5',
                        borderRadius: 8,
                        maxBarThickness: 38,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                        },
                    },
                },
            });
        } catch (error) {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    message: 'Gagal memuat visualisasi dashboard pengguna.',
                    type: 'error',
                },
            }));
        }
    });
</script>
@endpush
