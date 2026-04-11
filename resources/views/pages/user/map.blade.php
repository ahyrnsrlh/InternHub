@extends('layouts.user')

@section('title', 'Peta Lokasi')
@section('header', 'Peta Lokasi')

@section('content')
<x-card title="Peta Lokasi Magang" subtitle="Ringkasan titik lokasi magang dan penanda presensi.">
    @if ($locations->count())
        <div class="h-[70vh] rounded-xl border border-gray-200 bg-gradient-to-br from-gray-100 to-white p-4">
            <div class="relative flex h-full items-center justify-center rounded-xl border border-dashed border-gray-300 bg-white text-center">
                <div>
                    <p class="text-sm font-medium text-gray-700">Area peta penuh (placeholder)</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $locations->count() }} penanda lokasi siap ditampilkan.</p>
                </div>
                <div class="absolute left-1/2 top-1/2 h-4 w-4 -translate-x-1/2 -translate-y-1/2 rounded-full bg-indigo-600 ring-8 ring-indigo-100"></div>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-10 text-center">
            <p class="text-sm font-semibold text-gray-700">Data peta belum tersedia</p>
            <p class="mt-1 text-sm text-gray-500">Tambahkan minimal satu lokasi untuk menampilkan penanda pada peta.</p>
            <a href="{{ route('user.locations.index') }}" class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Buka Lokasi Magang</a>
        </div>
    @endif
</x-card>
@endsection
