@extends('layouts.user')

@section('title', 'Locations')
@section('header', 'Internship Locations')

@section('content')
<div class="space-y-6" x-data="{ hasLocation: true }">
    <x-card title="Add Internship Location" subtitle="Set your official internship location for attendance validation.">
        <form class="grid gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Location Name</label>
                <x-input placeholder="Head Office Jakarta" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Address</label>
                <x-input placeholder="Jl. Jendral Sudirman No. 1" />
            </div>
            <div class="lg:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Map Picker</label>
                <div class="h-48 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4 text-center text-sm text-gray-500">
                    Map Picker UI Placeholder
                </div>
            </div>
            <div class="lg:col-span-2">
                <x-button type="button" x-on:click="$dispatch('notify', { message: 'Location saved', type: 'success' })">Save Location</x-button>
            </div>
        </form>
    </x-card>

    <x-card title="Saved Locations" subtitle="List of internship locations used for attendance.">
        <div class="overflow-hidden rounded-xl border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-show="hasLocation">
                    <tr>
                        <td class="px-4 py-3 text-gray-900">Main Office</td>
                        <td class="px-4 py-3 text-gray-600">Jl. M.H. Thamrin No.10, Jakarta</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-900">Branch Site</td>
                        <td class="px-4 py-3 text-gray-600">Jl. Pajajaran No.12, Bogor</td>
                    </tr>
                </tbody>
            </table>

            <div class="p-8 text-center" x-show="!hasLocation">
                <p class="text-sm text-gray-500">No locations added yet. Add your first location above.</p>
            </div>
        </div>
    </x-card>
</div>
@endsection
