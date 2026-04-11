@extends('layouts.admin')

@section('title', 'Monitoring Kehadiran')
@section('header', 'Kehadiran')

@section('content')
<div class="space-y-6" x-data="{ empty:false }">
    <x-card>
        <form method="GET" action="{{ route('internhub.admin.attendance') }}" class="grid gap-3 sm:grid-cols-4">
            <x-input type="date" name="date" :value="$filterDate" />
            <select name="user_id" class="rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="">Semua Peserta Magang</option>
                @foreach ($internOptions as $intern)
                    <option value="{{ $intern->id }}" @selected((string) $filterUserId === (string) $intern->id)>{{ $intern->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="">Semua Status</option>
                <option value="valid" @selected($filterStatus === 'valid')>GPS Valid</option>
                <option value="invalid" @selected($filterStatus === 'invalid')>GPS Tidak Valid</option>
            </select>
            <x-button variant="secondary" type="submit">Terapkan Filter</x-button>
        </form>
    </x-card>

    <x-table :headers="['Peserta', 'Tanggal', 'Lokasi', 'Status', 'Koordinat']" x-show="!empty">
        @foreach ($attendances as $attendance)
            <tr class="bg-white">
                <td class="px-4 py-3 text-gray-900">{{ $attendance->user?->name ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-700">{{ optional($attendance->check_in_time)->format('d M Y H:i') ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $attendance->location?->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    @if ($attendance->status === 'valid')
                        <x-badge variant="success">GPS Valid</x-badge>
                    @else
                        <x-badge variant="danger">GPS Tidak Valid</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $attendance->latitude }}, {{ $attendance->longitude }}</td>
            </tr>
        @endforeach
    </x-table>

    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center" x-show="{{ $attendances->count() ? 'false' : 'true' }}">
        <p class="text-sm text-gray-500">Tidak ada data kehadiran untuk filter yang dipilih.</p>
    </div>

    <div x-show="{{ $attendances->count() ? 'true' : 'false' }}">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
