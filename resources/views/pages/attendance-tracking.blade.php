@extends('layouts.app')

@section('title', 'Attendance Tracking')
@section('active_menu', 'attendance')
@section('nav_title', 'Executive Portal')
@section('search_placeholder', 'Search records...')

@section('content')
<section class="space-y-2">
    <h1 class="text-5xl font-black tracking-tight text-content">Attendance Tracking</h1>
    <p class="text-content-muted">Monitor your professional presence and time allocation.</p>
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <x-card class="lg:col-span-2" title="Status: Currently Out">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <p class="text-4xl font-black text-content">{{ str($summary['current_status'] ?? 'out')->headline() }}</p>
                <p class="text-content-muted mt-2">Shift: 09:00 AM - 05:00 PM</p>
            </div>
            <div>
                <p class="text-6xl font-black text-content">08:42</p>
                <p class="text-sm uppercase tracking-widest text-content-muted">Monday, Oct 23</p>
            </div>
        </div>
        <div class="mt-8 flex gap-3">
            <x-button>Check In Now</x-button>
            <x-button variant="secondary">Request Leave</x-button>
        </div>
    </x-card>

    <div class="space-y-4">
        <x-card><p class="text-xs uppercase tracking-widest text-content-muted font-bold">Total Days Present</p><p class="text-4xl font-black mt-2">{{ $summary['present_days'] ?? 0 }}</p></x-card>
        <x-card><p class="text-xs uppercase tracking-widest text-content-muted font-bold">Late Arrivals</p><p class="text-4xl font-black mt-2 text-danger">{{ $summary['late_days'] ?? 0 }}</p></x-card>
        <x-card><p class="text-xs uppercase tracking-widest text-content-muted font-bold">Remaining Leaves</p><p class="text-4xl font-black mt-2 text-success">{{ $summary['remaining_leaves'] ?? 0 }}</p></x-card>
    </div>
</section>

<x-card title="Recent Activity">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs uppercase tracking-widest text-content-muted border-b border-line">
                    <th class="py-3">Date</th>
                    <th class="py-3">Check In</th>
                    <th class="py-3">Check Out</th>
                    <th class="py-3">Duration</th>
                    <th class="py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($records as $row)
                    <tr>
                        <td class="py-4 font-semibold">{{ $row->work_date?->format('M d, Y') }}</td>
                        <td class="py-4 text-content-muted">{{ $row->check_in ? \Illuminate\Support\Carbon::parse($row->check_in)->format('h:i A') : '-' }}</td>
                        <td class="py-4 text-content-muted">{{ $row->check_out ? \Illuminate\Support\Carbon::parse($row->check_out)->format('h:i A') : '-' }}</td>
                        <td class="py-4 text-content-muted">{{ $row->duration_minutes ? floor($row->duration_minutes / 60) . 'h ' . ($row->duration_minutes % 60) . 'm' : '-' }}</td>
                        <td class="py-4">
                            @php($statusLabel = str($row->status)->replace('_', ' ')->headline())
                            <span class="internhub-chip {{ $row->status === 'late' ? 'bg-danger-soft text-danger' : ($row->status === 'on_leave' ? 'bg-primary-soft text-content-muted' : 'bg-success-soft text-success') }}">{{ $statusLabel }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-sm text-content-muted">No attendance data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection
