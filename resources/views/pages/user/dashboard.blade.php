@extends('layouts.user')

@section('title', 'User Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6" x-data="{ loading: false }">
    <section class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-600 to-indigo-500 p-6 text-white shadow-sm">
        <p class="text-sm text-indigo-100">Welcome back, Intern</p>
        <h1 class="mt-1 text-2xl font-bold">GPS-Based Internship Monitoring System</h1>
        <p class="mt-2 max-w-2xl text-sm text-indigo-100">Track attendance with location precision, keep your daily logbook updated, and monitor progress in one workspace.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2">
        <x-card>
            <p class="text-sm text-gray-500">Total Attendance</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900">128</h3>
            <p class="mt-2 text-xs text-green-500">+6% from previous month</p>
        </x-card>

        <x-card>
            <p class="text-sm text-gray-500">Total Activities</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900">54</h3>
            <p class="mt-2 text-xs text-green-500">12 activities submitted this week</p>
        </x-card>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Quick Actions</h3>
                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-600">Today</span>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <x-button class="w-full justify-center" x-on:click="$dispatch('notify', { message: 'Check In triggered', type: 'success' })">Check In</x-button>
                <x-button variant="secondary" class="w-full justify-center" x-on:click="$dispatch('notify', { message: 'Open Add Log form', type: 'success' })">Add Log</x-button>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-base font-semibold text-gray-900">Loading State</h3>
            <div class="mt-4 space-y-3" x-show="!loading">
                <x-skeleton class="h-3 w-full" />
                <x-skeleton class="h-3 w-5/6" />
                <x-skeleton class="h-3 w-4/6" />
            </div>
        </x-card>
    </section>
</div>
@endsection
