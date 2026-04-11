@extends('layouts.user')

@section('title', 'Profil')
@section('header', 'Pengaturan Profil')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <x-card class="lg:col-span-1" title="Foto Profil" subtitle="Unggah foto profil Anda.">
        <div class="space-y-4">
            <div class="mx-auto h-24 w-24 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700"></div>
            <label class="block text-center text-sm font-medium text-indigo-600 hover:text-indigo-700">
                <input type="file" class="hidden">
                Unggah Foto
            </label>
        </div>
    </x-card>

    <x-card class="lg:col-span-2" title="Informasi Pribadi" subtitle="Perbarui data profil Anda secara berkala.">
        <form class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                <x-input placeholder="John Doe" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <x-input type="email" placeholder="john@example.com" />
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Instansi</label>
                <x-input placeholder="Universitas / Sekolah" />
            </div>
            <div class="sm:col-span-2">
                <x-button type="button" x-on:click="$dispatch('notify', { message: 'Profil berhasil diperbarui', type: 'success' })">Simpan Perubahan</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
