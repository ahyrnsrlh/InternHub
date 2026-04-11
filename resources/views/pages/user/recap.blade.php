@extends('layouts.user')

@section('title', 'Recap')
@section('header', 'Monthly Recap')

@section('content')
<div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm text-gray-500">Total Presence</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">22 Days</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Late Check-In</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">2 Days</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Activity Entries</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">30 Logs</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Completion Rate</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">91%</p>
        </x-card>
    </section>

    <x-card title="Export Summary" subtitle="Download monthly recap in PDF format.">
        <x-button type="button" x-on:click="$dispatch('notify', { message: 'Export PDF started (UI only)', type: 'success' })">
            Export PDF
        </x-button>
    </x-card>
</div>
@endsection
