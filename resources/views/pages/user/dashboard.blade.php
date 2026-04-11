@extends('layouts.user')

@section('title', 'Beranda Peserta')
@section('header', 'Beranda')

@section('content')
<div class="space-y-6" x-data="{ loading: false }">
    @if (!(bool) ($summary['checked_in_today'] ?? false))
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
            Pengingat: Anda belum melakukan presensi hari ini.
            <a href="{{ route('user.attendance.index') }}" class="ml-1 font-semibold text-amber-800 underline">Lakukan presensi sekarang</a>
        </div>
    @endif

    <section class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-600 to-indigo-500 p-6 text-white shadow-sm">
        <p class="text-sm text-indigo-100">Selamat datang kembali, Peserta Magang</p>
        <h1 class="mt-1 text-2xl font-bold">Sistem Monitoring Magang Berbasis GPS</h1>
        <p class="mt-2 max-w-2xl text-sm text-indigo-100">Kelola presensi, catatan harian, dan perkembangan magang dalam satu portal terintegrasi.</p>
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
        <x-card class="lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Aksi Cepat</h3>
                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-600">Hari Ini</span>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <a href="{{ route('user.attendance.index') }}" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Buka Presensi</a>
                <a href="{{ route('user.logbook.index') }}" class="inline-flex w-full justify-center rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">Tambah Catatan</a>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-base font-semibold text-gray-900">Kehadiran Terbaru</h3>
            @if(($recentAttendances ?? collect())->count())
                <ul class="mt-4 space-y-2 text-sm text-gray-600">
                    @foreach(($recentAttendances ?? collect()) as $attendance)
                        <li class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2">
                            <span>{{ optional($attendance->check_in_time)->format('d M Y H:i') ?? '-' }}</span>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $attendance->status === 'valid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($attendance->status) }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-sm text-gray-500">Belum ada data kehadiran.</p>
            @endif
        </x-card>
    </section>
</div>
@endsection
