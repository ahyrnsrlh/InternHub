@extends('layouts.admin')

@section('title', 'Manajemen Lokasi Magang')
@section('header', 'Lokasi Magang')

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
    $updateRouteTemplate = route('internhub.admin.locations.update', ['location' => '__ID__']);
    $deleteRouteTemplate = route('internhub.admin.locations.destroy', ['location' => '__ID__']);
@endphp

<script type="application/json" id="admin-locations-json">@json($locationPayload)</script>
<script type="application/json" id="admin-locations-route-templates">@json(['update' => $updateRouteTemplate, 'delete' => $deleteRouteTemplate])</script>

<div class="space-y-6" x-data="adminLocationsManager()" x-init="init()">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Lokasi Magang</h3>
                <p class="text-sm text-gray-500">Kelola seluruh lokasi kerja yang disetujui untuk validasi GPS.</p>
            </div>
            <x-button x-on:click="$dispatch('open-modal', 'add-location')">Tambah Lokasi</x-button>
        </div>
    </x-card>

    <x-card>
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <span class="font-semibold text-gray-700">Filter Marker:</span>
            <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600" x-model="showActive" @change="renderMap()">
                <span class="text-gray-700">Aktif</span>
            </label>
            <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600" x-model="showInactive" @change="renderMap()">
                <span class="text-gray-700">Tidak Aktif</span>
            </label>

            <div class="ml-auto flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-green-600"></span>
                    <span class="text-gray-600">Aktif</span>
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-gray-500"></span>
                    <span class="text-gray-600">Tidak Aktif</span>
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full border border-blue-600 bg-blue-100"></span>
                    <span class="text-gray-600">Lingkar Radius</span>
                </span>
            </div>
        </div>
    </x-card>

    <x-table :headers="['Nama Lokasi', 'Alamat', 'Koordinat', 'Radius', 'Status', 'Aksi']">
        @forelse ($locations as $location)
            <tr class="bg-white">
                <td class="px-4 py-3 font-medium text-gray-900">{{ $location->name }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $location->address }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $location->latitude }}, {{ $location->longitude }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $location->radius_meters ?? 100 }} m</td>
                <td class="px-4 py-3">
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ ($location->status ?? 'active') === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ ($location->status ?? 'active') === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <x-button
                            type="button"
                            variant="secondary"
                            class="px-3 py-2"
                            x-on:click="openEditById({{ $location->id }})"
                        >Ubah</x-button>
                        <x-button
                            type="button"
                            variant="danger"
                            class="px-3 py-2"
                            x-on:click="openDeleteById({{ $location->id }})"
                        >Hapus</x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="bg-white">
                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada lokasi yang tersedia.</td>
            </tr>
        @endforelse
    </x-table>

    <x-card title="Pratinjau Peta" subtitle="Ikhtisar marker Leaflet untuk seluruh lokasi terdaftar.">
        @if ($locations->count())
            <div id="admin-locations-map" class="h-72 rounded-xl border border-gray-200"></div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500">
                Tidak ada data lokasi untuk ditampilkan pada peta.
            </div>
        @endif
    </x-card>

    <x-modal name="add-location" maxWidth="lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Lokasi</h3>
            <form method="POST" action="{{ route('internhub.admin.locations.store') }}" class="mt-4 space-y-3">
                @csrf
                <x-input name="name" placeholder="Nama lokasi" required />
                <x-input name="address" placeholder="Alamat" required />
                <div class="grid grid-cols-2 gap-3">
                    <x-input name="latitude" type="number" step="0.0000001" placeholder="Lintang" required />
                    <x-input name="longitude" type="number" step="0.0000001" placeholder="Bujur" required />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-input name="radius_meters" type="number" placeholder="Radius (meter)" value="100" required />
                    <select name="status" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'add-location')">Batal</x-button>
                    <x-button type="submit">Simpan</x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-location" maxWidth="lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Ubah Lokasi</h3>
            <form method="POST" :action="editFormAction" class="mt-4 space-y-3">
                @csrf
                @method('PUT')
                <x-input name="name" x-model="editForm.name" placeholder="Nama lokasi" required />
                <x-input name="address" x-model="editForm.address" placeholder="Alamat" required />
                <div class="grid grid-cols-2 gap-3">
                    <x-input name="latitude" x-model="editForm.latitude" type="number" step="0.0000001" placeholder="Lintang" required />
                    <x-input name="longitude" x-model="editForm.longitude" type="number" step="0.0000001" placeholder="Bujur" required />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <x-input name="radius_meters" x-model="editForm.radius_meters" type="number" placeholder="Radius (meter)" required />
                    <select name="status" x-model="editForm.status" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'edit-location')">Batal</x-button>
                    <x-button type="submit">Perbarui</x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="delete-location" maxWidth="md">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">Hapus Lokasi</h3>
            <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin ingin menghapus <span class="font-semibold text-gray-700" x-text="deleteForm.name"></span>?</p>
            <form method="POST" :action="deleteFormAction" class="mt-6 flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'delete-location')">Batal</x-button>
                <x-button type="submit" variant="danger">Hapus</x-button>
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
            routeTemplates: {
                update: '',
                delete: '',
            },
            get editFormAction() {
                if (!this.editForm.id || !this.routeTemplates.update) {
                    return '#';
                }

                return this.routeTemplates.update.replace('__ID__', this.editForm.id);
            },
            get deleteFormAction() {
                if (!this.deleteForm.id || !this.routeTemplates.delete) {
                    return '#';
                }

                return this.routeTemplates.delete.replace('__ID__', this.deleteForm.id);
            },
            init() {
                const payloadElement = document.getElementById('admin-locations-json');
                const routeTemplateElement = document.getElementById('admin-locations-route-templates');

                if (!payloadElement || !routeTemplateElement) {
                    return;
                }

                try {
                    this.locations = JSON.parse(payloadElement.textContent || '[]');
                } catch (error) {
                    this.locations = [];
                }

                try {
                    this.routeTemplates = JSON.parse(routeTemplateElement.textContent || '{}');
                } catch (error) {
                    this.routeTemplates = { update: '', delete: '' };
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
                        .bindPopup(`<strong>${location.name}</strong><br>${location.address}<br>Status: ${location.status === 'active' ? 'Aktif' : 'Tidak Aktif'}<br>Radius: ${location.radius_meters} m`);

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
            openEditById(locationId) {
                const location = this.locations.find((item) => Number(item.id) === Number(locationId));
                if (!location) {
                    return;
                }

                this.editForm = { ...location };
                this.$dispatch('open-modal', 'edit-location');
            },
            openDeleteById(locationId) {
                const location = this.locations.find((item) => Number(item.id) === Number(locationId));
                if (!location) {
                    return;
                }

                this.deleteForm = {
                    id: location.id,
                    name: location.name,
                };
                this.$dispatch('open-modal', 'delete-location');
            },
        };
    }

    window.addEventListener('DOMContentLoaded', () => {
        // Alpine component bootstraps map rendering.
    });
</script>
@endpush
