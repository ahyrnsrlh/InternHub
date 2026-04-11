@extends('layouts.admin')

@section('title', 'Beranda Admin')
@section('header', 'Beranda')

@section('content')
<div
    class="space-y-6"
    data-admin-attendance-url="{{ route('internhub.admin.dashboard.charts.attendance') }}"
    data-admin-validation-url="{{ route('internhub.admin.dashboard.charts.validation') }}"
    data-admin-trend-url="{{ route('internhub.admin.dashboard.charts.trend') }}"
    data-admin-top-interns-url="{{ route('internhub.admin.dashboard.charts.top-interns') }}"
>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-card class="!border-blue-500 !bg-blue-600">
            <p class="text-sm font-medium text-white">Total Peserta Magang</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $summary['totalInterns'] }}</p>
            <p class="mt-1 text-xs text-blue-100">Peserta aktif: {{ $summary['activeInterns'] }}</p>
        </x-card>
        <x-card class="!border-blue-500 !bg-blue-600">
            <p class="text-sm font-medium text-white">Kehadiran Hari Ini</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $summary['attendanceToday'] }}</p>
            <p class="mt-1 text-xs text-blue-100">{{ $summary['attendanceRate'] }}% valid</p>
        </x-card>
        <x-card class="!border-blue-500 !bg-blue-600">
            <p class="text-sm font-medium text-white">Ringkasan Validasi</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $summary['validAttendanceToday'] }} / {{ $summary['invalidAttendanceToday'] }}</p>
            <p class="mt-1 text-xs text-blue-100">Valid / Tidak valid (hari ini)</p>
        </x-card>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="Total Attendance per Day" subtitle="Agregasi semua pengguna untuk 7 hari terakhir.">
            <div class="h-80">
                <canvas id="adminAttendanceBarChart"></canvas>
            </div>
        </x-card>

        <x-card title="Valid vs Invalid" subtitle="Statistik validasi presensi global.">
            <div class="h-80">
                <canvas id="adminValidationDonutChart"></canvas>
            </div>
        </x-card>
    </section>

    <section class="grid gap-4 xl:grid-cols-3">
        <x-card class="xl:col-span-2" title="Monthly Attendance Trend" subtitle="Pertumbuhan kehadiran bulanan seluruh peserta.">
            <div class="h-80">
                <canvas id="adminMonthlyTrendLineChart"></canvas>
            </div>
        </x-card>

        <x-card title="Top Active Interns" subtitle="Skor berdasarkan attendance + activity logs.">
            <div class="h-80">
                <canvas id="adminTopInternsHorizontalChart"></canvas>
            </div>
        </x-card>
    </section>

    <x-card title="Aktivitas Terbaru" subtitle="Presensi masuk terbaru yang telah terverifikasi.">
        <ul class="space-y-3">
            @foreach ($recentCheckIns as $activity)
                <li class="flex items-center justify-between rounded-xl border border-gray-200 p-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $activity['name'] }} melakukan presensi masuk</p>
                        <p class="text-xs text-gray-500">{{ $activity['time'] }} · {{ $activity['department'] }}</p>
                    </div>
                    <x-badge :variant="$activity['gps_status'] === 'valid' ? 'success' : 'danger'">
                        {{ $activity['gps_status'] === 'valid' ? 'GPS Valid' : 'GPS Tidak Valid' }}
                    </x-badge>
                </li>
            @endforeach
        </ul>
    </x-card>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', async () => {
        const container = document.querySelector('[data-admin-attendance-url]');
        if (!container || typeof Chart === 'undefined') {
            return;
        }

        const fetchJson = async (url) => {
            const response = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            if (!response.ok) {
                throw new Error('Failed fetching admin chart data');
            }

            return response.json();
        };

        try {
            const [attendanceStats, validationStats, trendStats, topInterns] = await Promise.all([
                fetchJson(container.dataset.adminAttendanceUrl),
                fetchJson(container.dataset.adminValidationUrl),
                fetchJson(container.dataset.adminTrendUrl),
                fetchJson(container.dataset.adminTopInternsUrl),
            ]);

            new Chart(document.getElementById('adminAttendanceBarChart'), {
                type: 'bar',
                data: {
                    labels: attendanceStats.labels,
                    datasets: [{
                        label: 'Total Attendance',
                        data: attendanceStats.values,
                        backgroundColor: '#4f46e5',
                        borderRadius: 8,
                        maxBarThickness: 40,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 850, easing: 'easeOutQuart' },
                    plugins: { tooltip: { enabled: true }, legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                },
            });

            new Chart(document.getElementById('adminValidationDonutChart'), {
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
                    animation: { duration: 800, easing: 'easeOutCubic' },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { enabled: true },
                    },
                },
            });

            new Chart(document.getElementById('adminMonthlyTrendLineChart'), {
                type: 'line',
                data: {
                    labels: trendStats.labels,
                    datasets: [{
                        label: 'Monthly Attendance',
                        data: trendStats.values,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.18)',
                        fill: true,
                        tension: 0.3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { enabled: true },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                },
            });

            new Chart(document.getElementById('adminTopInternsHorizontalChart'), {
                type: 'bar',
                data: {
                    labels: topInterns.labels,
                    datasets: [
                        {
                            label: 'Attendance Count',
                            data: topInterns.attendance_values,
                            backgroundColor: '#4f46e5',
                            borderRadius: 6,
                        },
                        {
                            label: 'Activity Log Count',
                            data: topInterns.activity_values,
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                        },
                    ],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    plugins: {
                        tooltip: { enabled: true, mode: 'nearest' },
                        legend: { position: 'bottom' },
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                },
            });
        } catch (error) {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    message: 'Gagal memuat visualisasi dashboard admin.',
                    type: 'error',
                },
            }));
        }
    });
</script>
@endpush
