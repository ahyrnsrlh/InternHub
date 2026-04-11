@extends('layouts.admin')

@section('title', 'Attendance Monitoring')
@section('header', 'Attendance')

@section('content')
<div class="space-y-6" x-data="{ empty:false }">
    <x-card>
        <div class="grid gap-3 sm:grid-cols-3">
            <x-input type="date" />
            <select class="rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option>All Interns</option>
                <option>Alex Rivers</option>
                <option>Sarah Jenkins</option>
            </select>
            <x-button variant="secondary">Apply Filter</x-button>
        </div>
    </x-card>

    <x-table :headers="['Intern', 'Date', 'Status', 'Coordinate']" x-show="!empty">
        <tr class="bg-white">
            <td class="px-4 py-3 text-gray-900">Alex Rivers</td>
            <td class="px-4 py-3 text-gray-700">2026-04-11</td>
            <td class="px-4 py-3"><x-badge variant="success">Valid GPS</x-badge></td>
            <td class="px-4 py-3 text-gray-600">-6.2000, 106.8166</td>
        </tr>
        <tr class="bg-white">
            <td class="px-4 py-3 text-gray-900">Sarah Jenkins</td>
            <td class="px-4 py-3 text-gray-700">2026-04-11</td>
            <td class="px-4 py-3"><x-badge variant="danger">Invalid GPS</x-badge></td>
            <td class="px-4 py-3 text-gray-600">-6.1751, 106.8650</td>
        </tr>
    </x-table>

    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center" x-show="empty">
        <p class="text-sm text-gray-500">No attendance records for selected filters.</p>
    </div>
</div>
@endsection
