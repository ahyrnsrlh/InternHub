@extends('layouts.user')

@section('title', 'Laporan Kehadiran')
@section('header', 'Laporan Kehadiran')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <x-card>
        <form method="GET" action="{{ route('user.reports.index') }}" class="flex flex-wrap items-end justify-between gap-3">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Filter Tanggal</label>
                    <x-input type="date" name="date" :value="$filterDate" />
                </div>
                <x-button variant="secondary" type="submit">Terapkan Filter</x-button>
                @if($filterDate)
                    <a href="{{ route('user.reports.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Reset</a>
                @endif
            </div>

            <div>
                <a href="{{ route('user.reports.export.pdf', ['date' => $filterDate]) }}" class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800">
                    Unduh PDF
                </a>
            </div>
        </form>
    </x-card>

    <section class="grid gap-4 sm:grid-cols-2">
        <x-card>
            <p class="text-sm text-gray-500">Total Kehadiran</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['total_attendance'] }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Kehadiran Valid</p>
            <p class="mt-2 text-2xl font-bold text-green-700">{{ $summary['valid_attendance'] }}</p>
        </x-card>
    </section>

    <x-card title="Tabel Kehadiran" subtitle="Data kehadiran berdasarkan filter tanggal dan akun pengguna.">
        @if ($reports->count())
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Lokasi</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Presensi Masuk</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Presensi Pulang</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($reports as $report)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ optional($report->check_in_time)->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $report->location->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ optional($report->check_in_time)->format('H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ optional($report->check_out_time)->format('H:i') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $report->status === 'valid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $reports->links() }}</div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <p class="text-sm text-gray-500">Tidak ada data laporan pada rentang tanggal yang dipilih.</p>
            </div>
        @endif
    </x-card>
</div>
@endsection
