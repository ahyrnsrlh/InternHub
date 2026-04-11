@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6" x-data="{ loading: false }">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-card>
            <p class="text-sm text-gray-500">Total Interns</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['totalInterns'] }}</p>
            <p class="mt-1 text-xs text-green-500">+8 this month</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Attendance Today</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['attendanceToday'] }}</p>
            <p class="mt-1 text-xs text-green-500">{{ $summary['attendanceRate'] }}% completion</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Active Interns</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['activeInterns'] }}</p>
            <p class="mt-1 text-xs text-gray-500">11 inactive</p>
        </x-card>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="Attendance Trends" subtitle="Daily check-in trend for the current month.">
            <div class="h-64 rounded-xl border border-dashed border-gray-300 bg-gradient-to-b from-indigo-50 to-white p-4">
                <div class="relative h-full rounded-lg bg-white/70">
                    <div class="absolute bottom-10 left-8 right-8 h-px bg-gray-200"></div>
                    <div class="absolute bottom-10 left-8 right-8 flex items-end gap-4">
                        @foreach ($attendanceTrend as $value)
                            <div class="w-8 rounded-t bg-indigo-400 {{ $value['heightClass'] }}"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-card>

        <x-card title="Loading" subtitle="Skeleton preview">
            <div class="space-y-3" x-show="!loading">
                <x-skeleton class="h-4 w-full" />
                <x-skeleton class="h-4 w-5/6" />
                <x-skeleton class="h-4 w-4/6" />
                <x-skeleton class="h-24 w-full" />
            </div>
        </x-card>
    </section>

    <x-card title="Recent Activities" subtitle="Latest verified check-ins.">
        <ul class="space-y-3">
            @foreach ($recentCheckIns as $activity)
                <li class="flex items-center justify-between rounded-xl border border-gray-200 p-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $activity['name'] }} checked in</p>
                        <p class="text-xs text-gray-500">{{ $activity['time'] }} · {{ $activity['department'] }}</p>
                    </div>
                    <x-badge :variant="$activity['gps_status'] === 'valid' ? 'success' : 'danger'">
                        {{ $activity['gps_status'] === 'valid' ? 'Valid GPS' : 'Invalid GPS' }}
                    </x-badge>
                </li>
            @endforeach
        </ul>
    </x-card>
</div>
@endsection
