@extends('layouts.admin')

@section('title', 'Manajemen Peserta Magang')
@section('header', 'Peserta Magang')

@section('content')
<div class="space-y-6" x-data="{ empty:false }">
    <x-card>
        <div class="flex flex-wrap items-center gap-3">
            <div class="min-w-56 flex-1">
                <x-input placeholder="Cari nama peserta atau instansi..." />
            </div>
            <select class="rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option>Semua Status</option>
                <option>Aktif</option>
                <option>Nonaktif</option>
            </select>
            <x-button type="button" x-on:click="$dispatch('open-modal', 'add-intern')">Tambah Peserta</x-button>
        </div>
    </x-card>

    <x-table :headers="['Nama', 'Instansi', 'Status', 'Aksi']" x-show="!empty">
        <tr class="bg-white">
            <td class="px-4 py-3">
                <p class="font-medium text-gray-900">Alex Rivers</p>
                <p class="text-xs text-gray-500">alex@internhub.test</p>
            </td>
            <td class="px-4 py-3 text-gray-700">Politeknik Negeri Jakarta</td>
            <td class="px-4 py-3"><x-badge variant="success">Aktif</x-badge></td>
            <td class="px-4 py-3">
                <div class="flex gap-2">
                    <x-button variant="secondary" class="px-3 py-2">Ubah</x-button>
                    <x-button variant="danger" class="px-3 py-2" x-on:click="$dispatch('open-modal', 'delete-intern')">Hapus</x-button>
                </div>
            </td>
        </tr>
        <tr class="bg-white">
            <td class="px-4 py-3">
                <p class="font-medium text-gray-900">Sarah Jenkins</p>
                <p class="text-xs text-gray-500">sarah@internhub.test</p>
            </td>
            <td class="px-4 py-3 text-gray-700">Universitas Indonesia</td>
            <td class="px-4 py-3"><x-badge variant="warning">Nonaktif</x-badge></td>
            <td class="px-4 py-3">
                <div class="flex gap-2">
                    <x-button variant="secondary" class="px-3 py-2">Ubah</x-button>
                    <x-button variant="danger" class="px-3 py-2" x-on:click="$dispatch('open-modal', 'delete-intern')">Hapus</x-button>
                </div>
            </td>
        </tr>
    </x-table>

    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center" x-show="empty">
        <p class="text-sm text-gray-500">Data peserta belum tersedia. Tambahkan peserta pertama Anda.</p>
    </div>

    <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm">
        <p class="text-gray-500">Menampilkan 1-10 dari 48 peserta</p>
        <div class="flex items-center gap-2">
            <button class="rounded-lg border border-gray-200 px-3 py-1.5 text-gray-600 hover:bg-gray-50">Sebelumnya</button>
            <button class="rounded-lg bg-indigo-600 px-3 py-1.5 text-white">1</button>
            <button class="rounded-lg border border-gray-200 px-3 py-1.5 text-gray-600 hover:bg-gray-50">2</button>
            <button class="rounded-lg border border-gray-200 px-3 py-1.5 text-gray-600 hover:bg-gray-50">Berikutnya</button>
        </div>
    </div>

    <x-modal name="add-intern" maxWidth="lg">
        <div class="p-6" x-data>
            <h3 class="text-lg font-semibold text-gray-900">Tambah Peserta Magang</h3>
            <div class="mt-4 grid gap-3">
                <x-input placeholder="Nama lengkap" />
                <x-input type="email" placeholder="Email" />
                <x-input placeholder="Instansi" />
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'add-intern')">Batal</x-button>
                <x-button x-on:click="$dispatch('close-modal', 'add-intern'); $dispatch('notify', { message: 'Data peserta berhasil ditambahkan', type: 'success' })">Simpan</x-button>
            </div>
        </div>
    </x-modal>

    <x-modal name="delete-intern" maxWidth="md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Hapus Peserta Magang</h3>
            <p class="mt-2 text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan. Yakin ingin menghapus?</p>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'delete-intern')">Batal</x-button>
                <x-button variant="danger" x-on:click="$dispatch('close-modal', 'delete-intern'); $dispatch('notify', { message: 'Data peserta berhasil dihapus', type: 'error' })">Hapus</x-button>
            </div>
        </div>
    </x-modal>
</div>
@endsection
