@extends('layouts.user')

@section('title', 'Beranda Peserta')
@section('header', 'Beranda')

@section('content')
<div class="space-y-6" x-data="{ loading: false }">
    <section class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-600 to-indigo-500 p-6 text-white shadow-sm">
        <p class="text-sm text-indigo-100">Selamat datang kembali, Peserta Magang</p>
        <h1 class="mt-1 text-2xl font-bold">Sistem Monitoring Magang Berbasis GPS</h1>
        <p class="mt-2 max-w-2xl text-sm text-indigo-100">Kelola presensi, catatan harian, dan perkembangan magang dalam satu portal terintegrasi.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2">
        <x-card>
            <p class="text-sm text-gray-500">Total Kehadiran</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900">128</h3>
            <p class="mt-2 text-xs text-green-500">+6% dibanding bulan lalu</p>
        </x-card>

        <x-card>
            <p class="text-sm text-gray-500">Total Aktivitas</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900">54</h3>
            <p class="mt-2 text-xs text-green-500">12 aktivitas tercatat minggu ini</p>
        </x-card>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Aksi Cepat</h3>
                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-600">Hari Ini</span>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <x-button class="w-full justify-center" x-on:click="$dispatch('notify', { message: 'Presensi masuk diproses', type: 'success' })">Presensi Masuk</x-button>
                <x-button variant="secondary" class="w-full justify-center" x-on:click="$dispatch('notify', { message: 'Form catatan harian dibuka', type: 'success' })">Tambah Catatan</x-button>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-base font-semibold text-gray-900">Status Pemuatan</h3>
            <div class="mt-4 space-y-3" x-show="!loading">
                <x-skeleton class="h-3 w-full" />
                <x-skeleton class="h-3 w-5/6" />
                <x-skeleton class="h-3 w-4/6" />
            </div>
        </x-card>
    </section>
</div>
@endsection
