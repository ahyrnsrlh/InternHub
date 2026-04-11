@extends('layouts.admin')

@section('title', 'Intern Management')
@section('header', 'Interns')

@section('content')
<div class="space-y-6" x-data="{ empty:false }">
    <x-card>
        <div class="flex flex-wrap items-center gap-3">
            <div class="min-w-56 flex-1">
                <x-input placeholder="Search intern name or institution..." />
            </div>
            <select class="rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option>All Status</option>
                <option>Active</option>
                <option>Inactive</option>
            </select>
            <x-button type="button" x-on:click="$dispatch('open-modal', 'add-intern')">Add Intern</x-button>
        </div>
    </x-card>

    <x-table :headers="['Name', 'Institution', 'Status', 'Action']" x-show="!empty">
        <tr class="bg-white">
            <td class="px-4 py-3">
                <p class="font-medium text-gray-900">Alex Rivers</p>
                <p class="text-xs text-gray-500">alex@internhub.test</p>
            </td>
            <td class="px-4 py-3 text-gray-700">Politeknik Negeri Jakarta</td>
            <td class="px-4 py-3"><x-badge variant="success">Active</x-badge></td>
            <td class="px-4 py-3">
                <div class="flex gap-2">
                    <x-button variant="secondary" class="px-3 py-2">Edit</x-button>
                    <x-button variant="danger" class="px-3 py-2" x-on:click="$dispatch('open-modal', 'delete-intern')">Delete</x-button>
                </div>
            </td>
        </tr>
        <tr class="bg-white">
            <td class="px-4 py-3">
                <p class="font-medium text-gray-900">Sarah Jenkins</p>
                <p class="text-xs text-gray-500">sarah@internhub.test</p>
            </td>
            <td class="px-4 py-3 text-gray-700">Universitas Indonesia</td>
            <td class="px-4 py-3"><x-badge variant="warning">Inactive</x-badge></td>
            <td class="px-4 py-3">
                <div class="flex gap-2">
                    <x-button variant="secondary" class="px-3 py-2">Edit</x-button>
                    <x-button variant="danger" class="px-3 py-2" x-on:click="$dispatch('open-modal', 'delete-intern')">Delete</x-button>
                </div>
            </td>
        </tr>
    </x-table>

    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center" x-show="empty">
        <p class="text-sm text-gray-500">No intern records found. Add your first intern.</p>
    </div>

    <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm">
        <p class="text-gray-500">Showing 1-10 of 48 interns</p>
        <div class="flex items-center gap-2">
            <button class="rounded-lg border border-gray-200 px-3 py-1.5 text-gray-600 hover:bg-gray-50">Prev</button>
            <button class="rounded-lg bg-indigo-600 px-3 py-1.5 text-white">1</button>
            <button class="rounded-lg border border-gray-200 px-3 py-1.5 text-gray-600 hover:bg-gray-50">2</button>
            <button class="rounded-lg border border-gray-200 px-3 py-1.5 text-gray-600 hover:bg-gray-50">Next</button>
        </div>
    </div>

    <x-modal name="add-intern" maxWidth="lg">
        <div class="p-6" x-data>
            <h3 class="text-lg font-semibold text-gray-900">Add Intern</h3>
            <div class="mt-4 grid gap-3">
                <x-input placeholder="Full name" />
                <x-input type="email" placeholder="Email" />
                <x-input placeholder="Institution" />
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'add-intern')">Cancel</x-button>
                <x-button x-on:click="$dispatch('close-modal', 'add-intern'); $dispatch('notify', { message: 'Intern added', type: 'success' })">Save</x-button>
            </div>
        </div>
    </x-modal>

    <x-modal name="delete-intern" maxWidth="md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Delete Intern</h3>
            <p class="mt-2 text-sm text-gray-500">This action cannot be undone. Confirm deletion?</p>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'delete-intern')">Cancel</x-button>
                <x-button variant="danger" x-on:click="$dispatch('close-modal', 'delete-intern'); $dispatch('notify', { message: 'Intern deleted', type: 'error' })">Delete</x-button>
            </div>
        </div>
    </x-modal>
</div>
@endsection
