@extends('layouts.user')

@section('title', 'Rekap')
@section('header', 'Rekap Bulanan')

@section('content')
<div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm text-gray-500">Total Kehadiran</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">22 Hari</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Terlambat Presensi Masuk</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">2 Hari</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Entri Aktivitas</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">30 Catatan</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Completion Rate</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">91%</p>
        </x-card>
    </section>

    <x-card title="Ekspor Rekap" subtitle="Unduh rekap bulanan dalam format PDF.">
        <x-button type="button" x-on:click="$dispatch('notify', { message: 'Ekspor PDF dimulai', type: 'success' })">
            Unduh PDF
        </x-button>
    </x-card>
</div>
@endsection
