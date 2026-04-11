@extends('layouts.admin')

@section('title', 'Detail Peserta')
@section('header', 'Detail Peserta')

@section('content')
<div class="space-y-6">
    <x-card>
        <div class="flex flex-wrap items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700"></div>
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Alex Rivers</h3>
                <p class="text-sm text-gray-500">alex@internhub.test · Politeknik Negeri Jakarta</p>
                <div class="mt-2"><x-badge variant="success">Aktif</x-badge></div>
            </div>
        </div>
    </x-card>

    <x-card title="Riwayat Kehadiran">
        <x-table :headers="['Tanggal', 'Presensi Masuk', 'Status']">
            <tr class="bg-white">
                <td class="px-4 py-3 text-gray-700">2026-04-11</td>
                <td class="px-4 py-3 text-gray-700">08:57</td>
                <td class="px-4 py-3"><x-badge variant="success">GPS Valid</x-badge></td>
            </tr>
            <tr class="bg-white">
                <td class="px-4 py-3 text-gray-700">2026-04-10</td>
                <td class="px-4 py-3 text-gray-700">09:10</td>
                <td class="px-4 py-3"><x-badge variant="danger">GPS Tidak Valid</x-badge></td>
            </tr>
        </x-table>
    </x-card>

    <x-card title="Linimasa Aktivitas">
        <ol class="relative ml-3 space-y-5 border-l border-gray-200 pl-6">
            <li class="relative">
                <span class="absolute -left-[31px] top-1.5 h-3 w-3 rounded-full bg-indigo-600"></span>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">11 Apr 2026</p>
                <p class="mt-1 text-sm text-gray-700">Mengirim laporan integrasi antarmuka beranda.</p>
            </li>
            <li class="relative">
                <span class="absolute -left-[31px] top-1.5 h-3 w-3 rounded-full bg-indigo-300"></span>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">10 Apr 2026</p>
                <p class="mt-1 text-sm text-gray-700">Menyelesaikan pengujian validasi GPS pada presensi.</p>
            </li>
        </ol>
    </x-card>
</div>
@endsection
