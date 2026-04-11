@extends('layouts.admin')

@section('title', 'Location Management')
@section('header', 'Locations')

@section('content')
<div class="space-y-6">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Internship Locations</h3>
                <p class="text-sm text-gray-500">Manage all approved work locations for GPS validation.</p>
            </div>
            <x-button x-on:click="$dispatch('open-modal', 'add-location')">Add Location</x-button>
        </div>
    </x-card>

    <x-table :headers="['Location Name', 'Address', 'Action']">
        <tr class="bg-white">
            <td class="px-4 py-3 font-medium text-gray-900">Head Office</td>
            <td class="px-4 py-3 text-gray-700">Jl. Sudirman Kav. 1, Jakarta</td>
            <td class="px-4 py-3">
                <div class="flex gap-2">
                    <x-button variant="secondary" class="px-3 py-2">Edit</x-button>
                    <x-button variant="danger" class="px-3 py-2" x-on:click="$dispatch('open-modal', 'delete-location')">Delete</x-button>
                </div>
            </td>
        </tr>
    </x-table>

    <x-card title="Map Preview" subtitle="Location marker overview (UI placeholder).">
        <div class="h-64 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4">
            <div class="relative flex h-full items-center justify-center rounded-lg bg-white text-sm text-gray-500">
                Map Container Placeholder
                <div class="absolute left-1/2 top-1/2 h-4 w-4 -translate-x-1/2 -translate-y-1/2 rounded-full bg-indigo-600 ring-8 ring-indigo-100"></div>
            </div>
        </div>
    </x-card>

    <x-modal name="add-location" maxWidth="lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Add Location</h3>
            <div class="mt-4 space-y-3">
                <x-input placeholder="Location name" />
                <x-input placeholder="Address" />
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'add-location')">Cancel</x-button>
                <x-button x-on:click="$dispatch('close-modal', 'add-location')">Save</x-button>
            </div>
        </div>
    </x-modal>

    <x-modal name="delete-location" maxWidth="md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Delete Location</h3>
            <p class="mt-2 text-sm text-gray-500">Are you sure to remove this location?</p>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'delete-location')">Cancel</x-button>
                <x-button variant="danger" x-on:click="$dispatch('close-modal', 'delete-location')">Delete</x-button>
            </div>
        </div>
    </x-modal>
</div>
@endsection
