@extends('layouts.user')

@section('title', 'Laporan Harian')
@section('header', 'Laporan Harian')

@section('content')
@if (session('status'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('status') }}
    </div>
@endif

@if ($errors->has('logbook'))
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first('logbook') }}
    </div>
@endif

<div class="space-y-6">
    <x-card title="Laporan Harian Otomatis" subtitle="Data Laporan Harian diambil dari presensi masuk/pulang, tanpa input manual.">
        <form method="GET" action="{{ route('user.logbook.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Dari Tanggal</label>
                <x-input type="date" name="start_date" :value="request('start_date')" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <x-input type="date" name="end_date" :value="request('end_date')" />
            </div>
            <x-button variant="secondary" type="submit">Filter</x-button>
            <a href="{{ route('user.logbook.export.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800">Unduh PDF</a>
        </form>

        <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
            Laporan Harian disinkronkan dari presensi harian: rencana kegiatan (presensi masuk) dan realisasi kegiatan (presensi pulang).
        </div>

        @if ($logs->count())
            <ol class="relative ml-3 space-y-6 border-l border-gray-200 pl-6">
                @foreach ($logs as $log)
                    <li class="relative">
                        <span class="absolute -left-[31px] top-1.5 h-3 w-3 rounded-full bg-indigo-600"></span>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ optional($log->check_in_time)->translatedFormat('d M Y') }}</p>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $log->status === 'valid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">{{ ucfirst($log->status) }}</span>
                        </div>

                        <p class="mt-1 text-xs text-gray-500">
                            Masuk: {{ optional($log->check_in_time)->format('H:i') ?? '-' }}
                            · Pulang: {{ optional($log->check_out_time)->format('H:i') ?? '-' }}
                            · Lokasi: {{ $log->location?->name ?? '-' }}
                        </p>

                        <div class="mt-3 rounded-xl border border-gray-200 bg-white p-3">
                            <p class="text-xs font-semibold text-gray-700">Rencana Kegiatan (Masuk)</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $log->check_in_note ?: '-' }}</p>
                        </div>

                        <div class="mt-2 rounded-xl border border-gray-200 bg-white p-3">
                            <p class="text-xs font-semibold text-gray-700">Realisasi Kegiatan (Pulang)</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $log->check_out_note ?: '-' }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
            <div class="mt-4">{{ $logs->links() }}</div>
        @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500">
                    Belum ada data presensi yang dapat ditampilkan sebagai Laporan Harian.
            </div>
        @endif
    </x-card>
</div>
@endsection
