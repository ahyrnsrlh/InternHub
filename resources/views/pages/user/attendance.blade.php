@extends('layouts.user')

@section('title', 'Attendance')
@section('header', 'GPS Attendance')

@section('content')
@php
    $locationPayload = $locations->map(function ($location) {
        return [
            'id' => (string) $location->id,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
        ];
    })->values();
@endphp

<script type="application/json" id="attendance-locations">@json($locationPayload)</script>

<div class="space-y-6" x-data="attendanceForm()" x-init="init()">
    @if (session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->has('attendance'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('attendance') }}
        </div>
    @endif

    <x-card>
        <div class="grid gap-4 lg:grid-cols-2">
            <form method="POST" action="{{ route('user.attendance.check-in') }}" class="space-y-3" x-ref="checkInForm" @submit.prevent="submitCheckIn($refs.checkInForm)">
                @csrf
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Check In</h3>
                    <p class="mt-1 text-sm text-gray-500">Record your check in with GPS validation.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Location</label>
                    <select name="location_id" x-model="selectedLocationId" @change="updateDistance()" class="w-full rounded-xl border {{ $errors->has('location_id') ? 'border-red-300 focus:border-red-500 focus:ring-red-100' : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100' }} bg-white px-3.5 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition focus:ring-2" @disabled($locations->isEmpty())>
                        <option value="">Select location</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                {{ $location->name }} - {{ $location->address }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <input type="hidden" name="latitude" x-model="lat">
                <input type="hidden" name="longitude" x-model="lng">
                <input type="hidden" name="allowed_radius_meters" value="10">

                @error('latitude')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('longitude')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror

                @if ($locations->isEmpty())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        No location is registered yet. Please add a location first.
                    </div>
                @endif

                <x-button type="submit" class="w-full justify-center" x-bind:disabled="loadingCheckIn || gpsLoading || {{ $locations->isEmpty() ? 'true' : 'false' }}">
                    <span x-show="!loadingCheckIn && !gpsLoading">Check In</span>
                    <span x-show="gpsLoading">Reading GPS...</span>
                    <span x-show="loadingCheckIn && !gpsLoading">Submitting...</span>
                </x-button>
            </form>

            <form method="POST" action="{{ route('user.attendance.check-out') }}" class="space-y-3" @submit="loadingCheckOut = true">
                @csrf
                @method('PATCH')
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Check Out</h3>
                    <p class="mt-1 text-sm text-gray-500">Close your active attendance session.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-600">
                    @if ($activeAttendance)
                        Active check-in at: {{ optional($activeAttendance->check_in_time)->format('d M Y H:i') }}
                    @else
                        No active check-in.
                    @endif
                </div>

                <x-button type="submit" variant="secondary" class="w-full justify-center" x-bind:disabled="loadingCheckOut || {{ $activeAttendance ? 'false' : 'true' }}">
                    <span x-show="!loadingCheckOut">Check Out</span>
                    <span x-show="loadingCheckOut">Submitting...</span>
                </x-button>
            </form>
        </div>
    </x-card>

    <div class="grid gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="Attendance History">
            @if ($attendances->count())
                <div class="overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Check In</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Check Out</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Coordinates</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($attendances as $attendance)
                                <tr>
                                    <td class="px-4 py-3 text-gray-700">{{ optional($attendance->check_in_time)->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ optional($attendance->check_out_time)->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $attendance->latitude }}, {{ $attendance->longitude }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $attendance->status === 'valid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $attendances->links() }}</div>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500">
                    No attendance data yet.
                </div>
            @endif
        </x-card>

        <x-card>
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-gray-900">GPS Status</h3>
                <x-button type="button" variant="ghost" x-on:click="getGps()" x-bind:disabled="gpsLoading">Refresh GPS</x-button>
            </div>
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
                    <dt class="text-gray-500">State</dt>
                    <dd class="font-medium" :class="gpsReady ? 'text-green-700' : 'text-amber-700'" x-text="gpsReady ? 'Ready' : 'Waiting GPS'"></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Accuracy</dt>
                    <dd class="font-medium text-gray-900" x-text="accuracy ? `${accuracy} m` : '-' "></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Distance</dt>
                    <dd class="font-medium text-gray-900" x-text="distanceToSelected !== null ? `${distanceToSelected} m` : '-' "></dd>
                </div>
            </dl>

            <template x-if="gpsError">
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700" x-text="gpsError"></div>
            </template>

            <template x-if="gpsLoading">
                <div class="mt-4 space-y-2">
                    <x-skeleton class="h-3 w-full" />
                    <x-skeleton class="h-3 w-4/5" />
                </div>
            </template>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
function attendanceForm() {
    return {
        lat: "{{ old('latitude', '-') }}",
        lng: "{{ old('longitude', '-') }}",
        selectedLocationId: "{{ old('location_id', '') }}",
        locations: [],
        gpsReady: false,
        gpsLoading: false,
        gpsError: null,
        accuracy: null,
        distanceToSelected: null,
        loadingCheckIn: false,
        loadingCheckOut: false,
        init() {
            const payloadElement = document.getElementById('attendance-locations');
            this.locations = payloadElement ? JSON.parse(payloadElement.textContent) : [];
            this.gpsReady = this.lat !== '-' && this.lng !== '-';
            this.updateDistance();
        },
        getGps() {
            this.gpsLoading = true;
            this.gpsError = null;

            if (!navigator.geolocation) {
                this.gpsLoading = false;
                this.gpsError = 'Geolocation is not supported by your browser.';
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: this.gpsError, type: 'error' } }));
                return Promise.resolve(false);
            }

            return new Promise((resolve) => {
                navigator.geolocation.getCurrentPosition((position) => {
                    this.lat = position.coords.latitude.toFixed(7);
                    this.lng = position.coords.longitude.toFixed(7);
                    this.accuracy = Math.round(position.coords.accuracy);
                    this.gpsReady = true;
                    this.gpsLoading = false;
                    this.updateDistance();
                    resolve(true);
                }, (error) => {
                    this.gpsLoading = false;
                    this.gpsReady = false;
                    this.gpsError = error.message || 'Unable to get GPS position.';
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: this.gpsError, type: 'error' } }));
                    resolve(false);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                });
            });
        },
        async submitCheckIn(form) {
            this.loadingCheckIn = true;

            if (!this.gpsReady) {
                const success = await this.getGps();
                if (!success) {
                    this.loadingCheckIn = false;
                    this.gpsError = this.gpsError ?? 'GPS is not ready. Please enable location permission and try again.';
                    return;
                }
            }

            form.submit();
        },
        updateDistance() {
            if (!this.gpsReady) {
                this.distanceToSelected = null;
                return;
            }

            const selected = this.locations.find((location) => location.id === String(this.selectedLocationId));
            if (!selected) {
                this.distanceToSelected = null;
                return;
            }

            const distance = this.calculateDistance(
                parseFloat(this.lat),
                parseFloat(this.lng),
                selected.latitude,
                selected.longitude
            );

            this.distanceToSelected = Math.round(distance * 100) / 100;
        },
        calculateDistance(lat1, lng1, lat2, lng2) {
            const toRad = (value) => (value * Math.PI) / 180;
            const earthRadius = 6371000;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);

            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return earthRadius * c;
        }
    }
}
</script>
@endpush
