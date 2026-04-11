@extends('layouts.user')

@section('title', 'Lokasi Magang')
@section('header', 'Lokasi Magang')

@section('content')
<div class="space-y-6" x-data="{ submitting: false }">
    <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
        Setiap pengguna hanya dapat memiliki satu lokasi magang.
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->has('location'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('location') }}
        </div>
    @endif

    <x-card :title="$hasLocation ? 'Edit Lokasi Magang' : 'Tambah Lokasi Magang'" subtitle="Anda hanya dapat mengatur satu lokasi magang.">
        <form
            method="POST"
            action="{{ $hasLocation ? route('user.locations.update', $userLocation->id) : route('user.locations.store') }}"
            class="grid gap-4 lg:grid-cols-2"
            @submit="submitting = true"
        >
            @csrf
            @if ($hasLocation)
                @method('PUT')
            @endif
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Nama Lokasi</label>
                <x-input name="name" :value="old('name', $userLocation->name ?? '')" placeholder="Head Office Jakarta" required />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Alamat</label>
                <x-input name="address" :value="old('address', $userLocation->address ?? '')" placeholder="Jl. Jendral Sudirman No. 1" required />
                @error('address')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Latitude</label>
                <x-input type="number" step="0.0000001" name="latitude" :value="old('latitude', $userLocation->latitude ?? '')" placeholder="-6.2000000" />
                @error('latitude')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Longitude</label>
                <x-input type="number" step="0.0000001" name="longitude" :value="old('longitude', $userLocation->longitude ?? '')" placeholder="106.8166660" />
                @error('longitude')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="lg:col-span-2">
                <x-button type="submit" x-bind:disabled="submitting">
                    <span x-show="!submitting">{{ $hasLocation ? 'Perbarui Lokasi' : 'Simpan Lokasi' }}</span>
                    <span x-show="submitting">Memproses...</span>
                </x-button>
            </div>
        </form>

        @if ($hasLocation)
            <div class="mt-3 flex items-center gap-3 text-xs">
                <button type="button" class="inline-flex cursor-not-allowed rounded-lg border border-gray-200 bg-gray-100 px-3 py-1.5 text-gray-500" disabled>
                    Tambah Lokasi Dinonaktifkan
                </button>
                <span class="text-gray-500">Anda sudah memiliki lokasi. Silakan gunakan mode edit.</span>
            </div>
        @endif
    </x-card>

    <x-card title="Lokasi Anda" subtitle="Lokasi magang yang terdaftar pada akun Anda.">
        @if ($hasLocation)
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Alamat</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Koordinat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="px-4 py-3 text-gray-900">{{ $userLocation->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $userLocation->address }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $userLocation->latitude }}, {{ $userLocation->longitude }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <p class="text-sm text-gray-500">Belum ada lokasi tersimpan. Tambahkan satu lokasi untuk memulai presensi.</p>
            </div>
        @endif
    </x-card>
</div>
@endsection
