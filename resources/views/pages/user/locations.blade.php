@extends('layouts.user')

@section('title', 'Lokasi Magang')
@section('header', 'Lokasi Magang')

@section('content')
<div class="space-y-6" x-data="{ submitting: false }">
    @if (session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <x-card title="Tambah Lokasi Magang" subtitle="Tetapkan lokasi magang resmi untuk validasi presensi.">
        <form method="POST" action="{{ route('user.locations.store') }}" class="grid gap-4 lg:grid-cols-2" @submit="submitting = true">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Nama Lokasi</label>
                <x-input name="name" :value="old('name')" placeholder="Head Office Jakarta" required />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Alamat</label>
                <x-input name="address" :value="old('address')" placeholder="Jl. Jendral Sudirman No. 1" required />
                @error('address')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Latitude</label>
                <x-input type="number" step="0.0000001" name="latitude" :value="old('latitude')" placeholder="-6.2000000" />
                @error('latitude')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Longitude</label>
                <x-input type="number" step="0.0000001" name="longitude" :value="old('longitude')" placeholder="106.8166660" />
                @error('longitude')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="lg:col-span-2">
                <x-button type="submit" x-bind:disabled="submitting">
                    <span x-show="!submitting">Simpan Lokasi</span>
                    <span x-show="submitting">Menyimpan...</span>
                </x-button>
            </div>
        </form>
    </x-card>

    <x-card title="Daftar Lokasi" subtitle="Daftar lokasi magang yang digunakan untuk presensi.">
        @if ($locations->count())
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
                        @foreach ($locations as $location)
                            <tr>
                                <td class="px-4 py-3 text-gray-900">{{ $location->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $location->address }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $location->latitude }}, {{ $location->longitude }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $locations->links() }}</div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <p class="text-sm text-gray-500">Belum ada lokasi tersimpan. Tambahkan lokasi pertama Anda.</p>
            </div>
        @endif
    </x-card>
</div>
@endsection
