@extends('layouts.user')

@section('title', 'Map View')
@section('header', 'Live Map View')

@section('content')
<x-card title="Internship Map" subtitle="Overview of registered internship point and attendance markers.">
    <div class="h-[70vh] rounded-xl border border-gray-200 bg-gradient-to-br from-gray-100 to-white p-4">
        <div class="relative flex h-full items-center justify-center rounded-xl border border-dashed border-gray-300 bg-white text-center">
            <div>
                <p class="text-sm font-medium text-gray-700">Full-Width Map Container Placeholder</p>
                <p class="mt-1 text-xs text-gray-500">Marker UI: Internship Location (Lat: -6.200000, Lng: 106.816666)</p>
            </div>
            <div class="absolute left-1/2 top-1/2 h-4 w-4 -translate-x-1/2 -translate-y-1/2 rounded-full bg-indigo-600 ring-8 ring-indigo-100"></div>
        </div>
    </div>
</x-card>
@endsection
