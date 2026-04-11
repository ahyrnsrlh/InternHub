@extends('layouts.user')

@section('title', 'Attendance')
@section('header', 'GPS Attendance')

@section('content')
<div class="space-y-6" x-data="{ checkedIn: false, status: 'invalid', lat: '-', lng: '-' }">
    <x-card>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Attendance Control</h3>
                <p class="mt-1 text-sm text-gray-500">Record your check in and check out with location validation.</p>
            </div>
            <x-button
                x-on:click="checkedIn = !checkedIn; status = checkedIn ? 'valid' : 'invalid'; lat = checkedIn ? '-6.200000' : '-'; lng = checkedIn ? '106.816666' : '-'; $dispatch('notify', { message: checkedIn ? 'Checked in successfully' : 'Checked out successfully', type: 'success' })"
                x-text="checkedIn ? 'Check Out' : 'Check In'"
            >Check In</x-button>
        </div>
    </x-card>

    <div class="grid gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <h3 class="text-base font-semibold text-gray-900">Map Preview</h3>
            <div class="mt-4 h-72 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4">
                <div class="flex h-full items-center justify-center rounded-lg bg-white text-center text-sm text-gray-500">
                    Google Maps Container Placeholder
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-base font-semibold text-gray-900">GPS Status</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Latitude</dt>
                    <dd class="font-medium text-gray-900" x-text="lat"></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Longitude</dt>
                    <dd class="font-medium text-gray-900" x-text="lng"></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Validation</dt>
                    <dd>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold"
                              :class="status === 'valid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                              x-text="status === 'valid' ? 'Valid' : 'Invalid'"></span>
                    </dd>
                </div>
            </dl>
        </x-card>
    </div>
</div>
@endsection
