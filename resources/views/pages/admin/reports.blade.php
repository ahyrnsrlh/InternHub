@extends('layouts.admin')

@section('title', 'Laporan & Analitik')
@section('header', 'Laporan')

@section('content')
<div class="space-y-6">
    <x-card>
        <form method="GET" action="{{ route('internhub.admin.reports') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Dari Tanggal</label>
                <x-input type="date" name="start_date" :value="$startDate" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <x-input type="date" name="end_date" :value="$endDate" />
            </div>
            <x-button type="submit" variant="secondary">Terapkan Filter</x-button>
        </form>
    </x-card>

    <section class="grid gap-4 sm:grid-cols-3">
        <x-card>
            <p class="text-sm text-gray-500">Total Kehadiran</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['total_attendance'] }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Persentase Kehadiran</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $attendanceRate }}%</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Valid vs Tidak Valid</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['valid_attendance'] }} / {{ $summary['invalid_attendance'] }}</p>
        </x-card>
    </section>

    <x-card>
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Pratinjau Laporan</h3>
                <p class="text-sm text-gray-500">Ringkasan berdasarkan tanggal dan status validasi GPS.</p>
            </div>
        </div>

        <div class="mt-4">
            <x-table :headers="['Tanggal', 'Total Presensi Masuk', 'GPS Valid', 'GPS Tidak Valid']">
                @forelse($dailyReports as $report)
                    <tr class="bg-white">
                        <td class="px-4 py-3 text-gray-700">{{ \Illuminate\Support\Carbon::parse($report->report_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $report->total_attendance }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $report->valid_attendance }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $report->invalid_attendance }}</td>
                    </tr>
                @empty
                    <tr class="bg-white">
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada data laporan pada rentang tanggal yang dipilih.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </x-card>
</div>
@endsection
