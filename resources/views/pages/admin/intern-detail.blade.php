@extends('layouts.admin')

@section('title', 'Intern Detail')
@section('header', 'Intern Detail')

@section('content')
<div class="space-y-6">
    <x-card>
        <div class="flex flex-wrap items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700"></div>
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Alex Rivers</h3>
                <p class="text-sm text-gray-500">alex@internhub.test · Politeknik Negeri Jakarta</p>
                <div class="mt-2"><x-badge variant="success">Active</x-badge></div>
            </div>
        </div>
    </x-card>

    <x-card title="Attendance History">
        <x-table :headers="['Date', 'Check In', 'Status']">
            <tr class="bg-white">
                <td class="px-4 py-3 text-gray-700">2026-04-11</td>
                <td class="px-4 py-3 text-gray-700">08:57</td>
                <td class="px-4 py-3"><x-badge variant="success">Valid GPS</x-badge></td>
            </tr>
            <tr class="bg-white">
                <td class="px-4 py-3 text-gray-700">2026-04-10</td>
                <td class="px-4 py-3 text-gray-700">09:10</td>
                <td class="px-4 py-3"><x-badge variant="danger">Invalid GPS</x-badge></td>
            </tr>
        </x-table>
    </x-card>

    <x-card title="Activity Timeline">
        <ol class="relative ml-3 space-y-5 border-l border-gray-200 pl-6">
            <li class="relative">
                <span class="absolute -left-[31px] top-1.5 h-3 w-3 rounded-full bg-indigo-600"></span>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">11 Apr 2026</p>
                <p class="mt-1 text-sm text-gray-700">Submitted UI dashboard integration report.</p>
            </li>
            <li class="relative">
                <span class="absolute -left-[31px] top-1.5 h-3 w-3 rounded-full bg-indigo-300"></span>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">10 Apr 2026</p>
                <p class="mt-1 text-sm text-gray-700">Completed attendance GPS validation testing.</p>
            </li>
        </ol>
    </x-card>
</div>
@endsection
