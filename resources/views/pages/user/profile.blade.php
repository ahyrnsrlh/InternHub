@extends('layouts.user')

@section('title', 'Profile')
@section('header', 'Profile Settings')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <x-card class="lg:col-span-1" title="Avatar" subtitle="Upload your profile picture.">
        <div class="space-y-4">
            <div class="mx-auto h-24 w-24 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700"></div>
            <label class="block text-center text-sm font-medium text-indigo-600 hover:text-indigo-700">
                <input type="file" class="hidden">
                Upload Avatar
            </label>
        </div>
    </x-card>

    <x-card class="lg:col-span-2" title="Personal Information" subtitle="Keep your profile up to date.">
        <form class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                <x-input placeholder="John Doe" />
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <x-input type="email" placeholder="john@example.com" />
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Institution</label>
                <x-input placeholder="University / School" />
            </div>
            <div class="sm:col-span-2">
                <x-button type="button" x-on:click="$dispatch('notify', { message: 'Profile updated', type: 'success' })">Save Changes</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
