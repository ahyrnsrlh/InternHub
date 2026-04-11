@extends('layouts.admin')

@section('title', 'Location Management')
@section('header', 'Locations')

@push('styles')
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
/>
@endpush

@section('content')
@php
    $locationPayload = $locations->map(function ($location) {
        return [
            'id' => (int) $location->id,
            'name' => $location->name,
            'address' => $location->address,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'radius_meters' => (int) ($location->radius_meters ?? 100),
            'status' => $location->status ?? 'active',
        ];
    })->values();
@endphp

<script type="application/json" id="admin-locations-json">@json($locationPayload)</script>

<div class="space-y-6" x-data="adminLocationsManager()" x-init="init()">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Internship Locations</h3>
                <p class="text-sm text-gray-500">Manage all approved work locations for GPS validation.</p>
            </div>
            <x-button x-on:click="$dispatch('open-modal', 'add-location')">Add Location</x-button>
        </div>
    </x-card>

    <x-card>
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <span class="font-semibold text-gray-700">Marker Filter:</span>
            <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600" x-model="showActive" @change="renderMap()">
                <span class="text-gray-700">Active</span>
            </label>
            <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600" x-model="showInactive" @change="renderMap()">
                <span class="text-gray-700">Inactive</span>
            </label>

            <div class="ml-auto flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-green-600"></span>
                    <span class="text-gray-600">Active</span>
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-gray-500"></span>
                    <span class="text-gray-600">Inactive</span>
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full border border-blue-600 bg-blue-100"></span>
                    <span class="text-gray-600">Radius Circle</span>
                </span>
            </div>
        </div>
    </x-card>

    <x-table :headers="['Location Name', 'Address', 'Coordinates', 'Radius', 'Status', 'Action']">
        @forelse ($locations as $location)
            <tr class="bg-white">
                <td class="px-4 py-3 font-medium text-gray-900">{{ $location->name }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $location->address }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $location->latitude }}, {{ $location->longitude }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $location->radius_meters ?? 100 }} m</td>
                <td class="px-4 py-3">
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ ($location->status ?? 'active') === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($location->status ?? 'active') }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <x-button
                            type="button"
                            variant="secondary"
                            class="px-3 py-2"
                            x-on:click="openEdit({ id: {{ $location->id }}, name: @js($location->name), address: @js($location->address), latitude: {{ (float) $location->latitude }}, longitude: {{ (float) $location->longitude }}, radius_meters: {{ (int) ($location->radius_meters ?? 100) }}, status: @js($location->status ?? 'active') })"
                        >Edit</x-button>
                        <x-button
                            type="button"
                            variant="danger"
                            class="px-3 py-2"
                            x-on:click="openDelete({ id: {{ $location->id }}, name: @js($location->name) })"
                        >Delete</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="bg-white">
                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No locations available.</td>
            </tr>
        @endforelse
    </x-table>

    <x-card title="Map Preview" subtitle="Leaflet marker overview of all registered locations.">
        @if ($locations->count())
            <div id="admin-locations-map" class="h-72 rounded-xl border border-gray-200"></div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500">
                No location data to preview on map.
            </div>
        @endif
    </x-card>

    <x-modal name="add-location" maxWidth="lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Add Location</h3>
            <form method="POST" action="{{ route('internhub.admin.locations.store') }}" class="mt-4 space-y-3">
                @csrf
                <x-input name="name" placeholder="Location name" required />
                <x-input name="address" placeholder="Address" required />
                <div class="grid grid-cols-2 gap-3">
                    <x-input name="latitude" type="number" step="0.0000001" placeholder="Latitude" required />
                    <x-input name="longitude" type="number" step="0.0000001" placeholder="Longitude" required />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-input name="radius_meters" type="number" placeholder="Radius meters" value="100" required />
                    <select name="status" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'add-location')">Cancel</x-button>
                    <x-button type="submit">Save</x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-location" maxWidth="lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Edit Location</h3>
            <form method="POST" :action="editFormAction" class="mt-4 space-y-3">
                @csrf
                @method('PUT')
                <x-input name="name" x-model="editForm.name" placeholder="Location name" required />
                <x-input name="address" x-model="editForm.address" placeholder="Address" required />
                <div class="grid grid-cols-2 gap-3">
                    <x-input name="latitude" x-model="editForm.latitude" type="number" step="0.0000001" placeholder="Latitude" required />
                    <x-input name="longitude" x-model="editForm.longitude" type="number" step="0.0000001" placeholder="Longitude" required />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-input name="radius_meters" x-model="editForm.radius_meters" type="number" placeholder="Radius meters" required />
                    <select name="status" x-model="editForm.status" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'edit-location')">Cancel</x-button>
                    <x-button type="submit">Update</x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="delete-location" maxWidth="md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Delete Location</h3>
            <p class="mt-2 text-sm text-gray-500">Are you sure to remove <span class="font-semibold text-gray-700" x-text="deleteForm.name"></span>?</p>
            <form method="POST" :action="deleteFormAction" class="mt-6 flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'delete-location')">Cancel</x-button>
                <x-button type="submit" variant="danger">Delete</x-button>
            </form>
        </div>
    </x-modal>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    function adminLocationsManager() {
        return {
            locations: [],
            showActive: true,
            showInactive: true,
            map: null,
            layers: [],
            editForm: {
                id: null,
                name: '',
                address: '',
                latitude: '',
                longitude: '',
                radius_meters: 100,
                status: 'active',
            },
            deleteForm: {
                id: null,
                name: '',
            },
            get editFormAction() {
                return this.editForm.id ? `/internhub/admin/locations/${this.editForm.id}` : '#';
            },
            get deleteFormAction() {
                return this.deleteForm.id ? `/internhub/admin/locations/${this.deleteForm.id}` : '#';
            },
            init() {
                const payloadElement = document.getElementById('admin-locations-json');
                if (!payloadElement) {
                    return;
                }

                try {
                    this.locations = JSON.parse(payloadElement.textContent || '[]');
                } catch (error) {
                    this.locations = [];
                }

                this.renderMap();
            },
            getFilteredLocations() {
                return this.locations.filter((location) => {
                    if (location.status === 'active' && this.showActive) {
                        return true;
                    }

                    if (location.status === 'inactive' && this.showInactive) {
                        return true;
                    }

                    return false;
                });
            },
            renderMap() {
                const mapElement = document.getElementById('admin-locations-map');
                if (!mapElement || typeof L === 'undefined') {
                    return;
                }

                const points = this.getFilteredLocations();

                if (!this.map) {
                    const initial = this.locations[0] || { latitude: -6.2, longitude: 106.816666 };
                    this.map = L.map(mapElement).setView([initial.latitude, initial.longitude], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors',
                    }).addTo(this.map);
                }

                this.layers.forEach((layer) => this.map.removeLayer(layer));
                this.layers = [];

                if (!points.length) {
                    return;
                }

                const bounds = [];
                points.forEach((location) => {
                    const latLng = [location.latitude, location.longitude];
                    bounds.push(latLng);

                    const markerColor = location.status === 'active' ? '#16a34a' : '#6b7280';

                    const marker = L.circleMarker(latLng, {
                        radius: 8,
                        color: markerColor,
                        fillColor: markerColor,
                        fillOpacity: 0.85,
                        weight: 2,
                    })
                        .addTo(this.map)
                        .bindPopup(`<strong>${location.name}</strong><br>${location.address}<br>Status: ${location.status}<br>Radius: ${location.radius_meters} m`);

                    const radiusCircle = L.circle(latLng, {
                        radius: location.radius_meters,
                        color: '#2563eb',
                        fillColor: '#60a5fa',
                        fillOpacity: 0.12,
                        weight: 1.5,
                    }).addTo(this.map);

                    this.layers.push(marker, radiusCircle);
                });

                if (bounds.length === 1) {
                    this.map.setView(bounds[0], 15);
                } else {
                    this.map.fitBounds(bounds, { padding: [30, 30] });
                }
            },
            openEdit(location) {
                this.editForm = { ...location };
                this.$dispatch('open-modal', 'edit-location');
            },
            openDelete(location) {
                this.deleteForm = { ...location };
                this.$dispatch('open-modal', 'delete-location');
            },
        };
    }

    window.addEventListener('DOMContentLoaded', () => {
        // Alpine component bootstraps map rendering.
    });
</script>
@endpush
