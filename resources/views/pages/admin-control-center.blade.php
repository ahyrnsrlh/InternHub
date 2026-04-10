@extends('layouts.app')

@section('title', 'Admin Control Center')
@section('active_menu', 'administration')
@section('nav_title', 'Control Center')
@section('search_placeholder', 'Search internships...')

@section('content')
<section class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
    <div>
        <h1 class="text-5xl font-black tracking-tight text-content">Control Center</h1>
        <p class="text-content-muted mt-2">System-wide administration and oversight.</p>
    </div>
    <div class="flex gap-3">
        <x-button variant="secondary">Export Audit Log</x-button>
        <x-button>Configure System</x-button>
    </div>
</section>

<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach ($stats as $stat)
        <x-card>
            <p class="text-xs uppercase tracking-widest text-content-muted font-bold">{{ $stat['label'] }}</p>
            <div class="flex items-end gap-3 mt-3">
                <p class="text-4xl font-black text-content">{{ $stat['value'] }}</p>
                <span class="internhub-chip {{ str_contains($stat['delta'], '-') ? 'bg-danger-soft text-danger' : 'bg-success-soft text-success' }}">{{ $stat['delta'] }}</span>
            </div>
        </x-card>
    @endforeach
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <x-card class="lg:col-span-2" title="User Management">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-xs uppercase tracking-widest text-content-muted border-b border-line">
                        <th class="py-3">User Identity</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Placement</th>
                        <th class="py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="py-4 font-semibold">{{ $user->name }}</td>
                            <td class="py-4 text-content-muted">{{ str($user->role)->headline() }}</td>
                            <td class="py-4 text-content-muted">{{ $user->placement ?? '-' }}</td>
                            <td class="py-4">
                                <span class="internhub-chip {{ $user->status === 'active' ? 'bg-success-soft text-success' : ($user->status === 'pending' ? 'bg-warning-soft text-warning' : 'bg-primary-soft text-content-muted') }}">{{ str($user->status)->headline() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-sm text-content-muted">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-card class="bg-primary text-content-inverse border-primary-hover" title="Programs">
        <div class="space-y-3">
            @forelse ($programs as $program)
                <div class="rounded-lg bg-surface/10 p-3">{{ $program->name }} {{ $program->cohort }} {{ $program->quarter }}</div>
            @empty
                <p class="text-sm text-content-inverse/70">No active programs available.</p>
            @endforelse
        </div>
        <x-button variant="secondary" class="!bg-surface !text-content w-full mt-6">Add New Program</x-button>
    </x-card>
</section>
@endsection
