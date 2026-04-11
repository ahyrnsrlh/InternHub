@extends('layouts.user')

@section('title', 'Attendance Reports')
@section('header', 'Attendance Reports')

@section('content')
<div class="space-y-6" x-data="{ empty: false }">
    <x-card>
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Filter by Date</label>
                <x-input type="date" />
            </div>
            <x-button variant="secondary" type="button">Apply Filter</x-button>
        </div>
    </x-card>

    <x-card title="Attendance Table" subtitle="Daily attendance records with verification status.">
        <div class="overflow-hidden rounded-xl border border-gray-200" x-show="!empty">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <tr>
                        <td class="px-4 py-3 text-gray-700">2026-04-11</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Present</span></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-700">2026-04-10</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Present</span></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-700">2026-04-09</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-600">Absent</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center" x-show="empty">
            <p class="text-sm text-gray-500">No report data for the selected date range.</p>
        </div>
    </x-card>
</div>
@endsection
