@extends('layouts.admin')

@section('title', 'Beranda Admin')
@section('header', 'Beranda')

@section('content')
<div class="space-y-6" x-data="{ loading: false }">
    @php
        $maxTrend = max(1, collect($attendanceTrend)->max('count'));
    @endphp

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-card>
            <p class="text-sm text-gray-500">Total Peserta Magang</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['totalInterns'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Peserta aktif: {{ $summary['activeInterns'] }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Kehadiran Hari Ini</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['attendanceToday'] }}</p>
            <p class="mt-1 text-xs text-green-500">{{ $summary['attendanceRate'] }}% valid</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Ringkasan Validasi</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['validAttendanceToday'] }} / {{ $summary['invalidAttendanceToday'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Valid / Tidak valid (hari ini)</p>
        </x-card>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="Tren Kehadiran" subtitle="Tren presensi harian pada bulan berjalan.">
            <div class="h-64 rounded-xl border border-dashed border-gray-300 bg-gradient-to-b from-indigo-50 to-white p-4">
                <div class="relative h-full rounded-lg bg-white/70 p-4">
                    <div class="absolute bottom-10 left-4 right-4 h-px bg-gray-200"></div>
                    <div class="absolute bottom-10 left-4 right-4 flex items-end justify-between gap-3">
                        @foreach ($attendanceTrend as $value)
                            @php
                                $ratio = $maxTrend > 0 ? ($value['count'] / $maxTrend) : 0;
                                $heightClass = match (true) {
                                    $ratio >= 0.9 => 'h-40',
                                    $ratio >= 0.8 => 'h-36',
                                    $ratio >= 0.7 => 'h-32',
                                    $ratio >= 0.6 => 'h-28',
                                    $ratio >= 0.5 => 'h-24',
                                    $ratio >= 0.4 => 'h-20',
                                    $ratio >= 0.3 => 'h-16',
                                    $ratio >= 0.2 => 'h-12',
                                    default => 'h-8',
                                };
                            @endphp
                            <div class="flex w-full flex-col items-center gap-1">
                                <div class="w-full max-w-10 rounded-t bg-indigo-500 {{ $heightClass }}"></div>
                                <span class="text-[10px] text-gray-500">{{ $value['date_label'] }}</span>
                                <span class="text-[10px] font-semibold text-gray-700">{{ $value['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-card>

        <x-card title="Aktivitas 7 Hari" subtitle="Ringkasan aktivitas logbook terbaru.">
            <p class="text-3xl font-bold text-gray-900">{{ $summary['recentActivitiesCount'] }}</p>
            <p class="mt-1 text-sm text-gray-500">catatan aktivitas tercatat 7 hari terakhir</p>
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
