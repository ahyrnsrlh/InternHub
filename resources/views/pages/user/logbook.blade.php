@extends('layouts.user')

@section('title', 'Logbook')
@section('header', 'Daily Logbook')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <x-card class="lg:col-span-1" title="Add Daily Activity" subtitle="Write what you completed today.">
        <form class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Date</label>
                <x-input type="date" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Activity</label>
                <textarea class="min-h-28 w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Explain today's work progress..."></textarea>
            </div>
            <x-button type="button" class="w-full" x-on:click="$dispatch('notify', { message: 'Logbook submitted', type: 'success' })">Save Log</x-button>
        </form>
    </x-card>

    <x-card class="lg:col-span-2" title="Activity Timeline" subtitle="Your recent internship activities.">
        <ol class="relative ml-3 space-y-6 border-l border-gray-200 pl-6">
            <li class="relative">
                <span class="absolute -left-[31px] top-1.5 h-3 w-3 rounded-full bg-indigo-600"></span>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">11 Apr 2026</p>
                <p class="mt-1 text-sm text-gray-700">Implemented GPS status widget and validated attendance page layout.</p>
            </li>
            <li class="relative">
                <span class="absolute -left-[31px] top-1.5 h-3 w-3 rounded-full bg-indigo-300"></span>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">10 Apr 2026</p>
                <p class="mt-1 text-sm text-gray-700">Drafted internship location mapping and report filters.</p>
            </li>
            <li class="relative">
                <span class="absolute -left-[31px] top-1.5 h-3 w-3 rounded-full bg-indigo-200"></span>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">9 Apr 2026</p>
                <p class="mt-1 text-sm text-gray-700">Completed onboarding and profile setup for system access.</p>
            </li>
        </ol>
    </x-card>
</div>
@endsection
