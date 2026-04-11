@extends('layouts.admin')

@section('title', 'Laporan & Analitik')
@section('header', 'Laporan')

@section('content')
<div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2">
        <x-card>
            <p class="text-sm text-gray-500">Total Kehadiran</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">2,430</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Persentase Kehadiran</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">91%</p>
        </x-card>
    </section>

    <x-card>
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Pratinjau Laporan</h3>
                <p class="text-sm text-gray-500">Ringkasan berdasarkan tanggal dan status validasi.</p>
            </div>
            <x-button type="button" x-on:click="$dispatch('notify', { message: 'Ekspor PDF dimulai', type: 'success' })">Unduh PDF</x-button>
        </div>

        <div class="mt-4">
            <x-table :headers="['Tanggal', 'Total Presensi Masuk', 'GPS Valid', 'GPS Tidak Valid']">
                <tr class="bg-white">
                    <td class="px-4 py-3 text-gray-700">2026-04-11</td>
                    <td class="px-4 py-3 text-gray-700">96</td>
                    <td class="px-4 py-3 text-gray-700">89</td>
                    <td class="px-4 py-3 text-gray-700">7</td>
                </tr>
                <tr class="bg-white">
                    <td class="px-4 py-3 text-gray-700">2026-04-10</td>
                    <td class="px-4 py-3 text-gray-700">93</td>
                    <td class="px-4 py-3 text-gray-700">86</td>
                    <td class="px-4 py-3 text-gray-700">7</td>
                </tr>
            </x-table>
        </div>
    </x-card>
</div>
@endsection
