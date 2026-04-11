@extends('layouts.user')

@section('title', 'Peta Lokasi')
@section('header', 'Peta Lokasi')

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
    $locationPayload = $locations->map(static function ($location) {
        return [
            'name' => (string) $location->name,
            'address' => (string) $location->address,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'radius_meters' => (int) ($location->radius_meters ?? 100),
            'status' => (string) ($location->status ?? 'active'),
        ];
    })->values();
@endphp

<script type="application/json" id="user-map-locations-json">@json($locationPayload)</script>

<x-card title="Peta Lokasi Magang" subtitle="Ringkasan titik lokasi magang dan penanda presensi.">
    @if ($locations->count())
        <div class="space-y-4">
            <div class="h-[70vh] overflow-hidden rounded-xl border border-gray-200 bg-white">
                <div id="user-locations-map" class="h-full w-full"></div>
            </div>

            <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Total Lokasi</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $locations->count() }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Status Peta</p>
                    <p class="mt-1 font-semibold text-emerald-700">Aktif</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Sumber</p>
                    <p class="mt-1 font-semibold text-gray-900">OpenStreetMap</p>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-10 text-center">
            <p class="text-sm font-semibold text-gray-700">Data peta belum tersedia</p>
            <p class="mt-1 text-sm text-gray-500">Tambahkan minimal satu lokasi untuk menampilkan penanda pada peta.</p>
            <a href="{{ route('user.locations.index') }}" class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Buka Lokasi Magang</a>
        </div>
    @endif
</x-card>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const mapElement = document.getElementById('user-locations-map');
        const payloadElement = document.getElementById('user-map-locations-json');

        if (!mapElement || !payloadElement || typeof L === 'undefined') {
            return;
        }

        let points = [];
        try {
            points = JSON.parse(payloadElement.textContent || '[]');
        } catch (error) {
            points = [];
        }

        if (!points.length) {
            return;
        }

        const initial = points[0];
        const map = L.map(mapElement).setView([initial.latitude, initial.longitude], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        // Ensure map tiles are properly laid out after initial paint.
        setTimeout(() => map.invalidateSize(), 200);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                const userLatLng = [position.coords.latitude, position.coords.longitude];
                L.marker(userLatLng)
                    .addTo(map)
                    .bindPopup('Lokasi Anda saat ini');
            });
        }

        const bounds = [];
        points.forEach((location) => {
            const latLng = [location.latitude, location.longitude];
            bounds.push(latLng);

            const markerColor = location.status === 'active' ? '#16a34a' : '#6b7280';

            L.circleMarker(latLng, {
                radius: 8,
                color: markerColor,
                fillColor: markerColor,
                fillOpacity: 0.85,
                weight: 2,
            })
                .addTo(map)
                .bindPopup(`<strong>${location.name}</strong><br>${location.address}<br>Status: ${location.status === 'active' ? 'Aktif' : 'Tidak Aktif'}<br>Radius: ${location.radius_meters} m`);

            L.circle(latLng, {
                radius: location.radius_meters,
                color: '#2563eb',
                fillColor: '#60a5fa',
                fillOpacity: 0.12,
                weight: 1.5,
            }).addTo(map);
        });

        if (bounds.length === 1) {
            map.setView(bounds[0], 17);
        } else {
            map.fitBounds(bounds, {
                padding: [30, 30],
                maxZoom: 17,
            });
        }
    });
</script>
@endpush
