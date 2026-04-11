@extends('layouts.user')

@section('title', 'Presensi')
@section('header', 'Presensi Berbasis GPS')

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
            'id' => (string) $location->id,
            'name' => $location->name,
            'address' => $location->address,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'radius_meters' => (int) ($location->radius_meters ?? 100),
        ];
    })->values();
@endphp

<script type="application/json" id="attendance-locations">@json($locationPayload)</script>
<script type="application/json" id="attendance-face-reference">@json(auth()->user()?->face_descriptor ?? [])</script>

<div class="space-y-6" x-data="attendanceValidation()" x-init="init()">
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

    <form method="POST" action="{{ route('user.attendance.check-in') }}" x-ref="checkInForm" @submit.prevent="submitCheckIn($event)">
        @csrf

        <input type="hidden" name="location_id" x-model="selectedLocationId">
        <input type="hidden" name="latitude" x-model="lat">
        <input type="hidden" name="longitude" x-model="lng">
        <input type="hidden" name="face_descriptor" x-model="capturedDescriptorJson">
        <input type="hidden" name="allowed_radius_meters" x-model="allowedRadius">

        <div class="grid gap-4 lg:grid-cols-2">
            <x-card title="Validasi Lokasi" subtitle="Peta Leaflet dengan validasi radius secara real-time.">
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Lokasi Magang</label>
                        <select x-model="selectedLocationId" @change="onLocationChanged" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" x-bind:disabled="locations.length === 0">
                            <option value="">Pilih lokasi</option>
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

                    <div x-ref="map" class="h-80 w-full rounded-xl border border-gray-200"></div>

                    <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Latitude</p>
                            <p class="mt-1 font-semibold text-gray-900" x-text="lat || '-' "></p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Longitude</p>
                            <p class="mt-1 font-semibold text-gray-900" x-text="lng || '-' "></p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 sm:col-span-2">
                            <p class="text-xs text-gray-500">Jarak dari lokasi magang</p>
                            <p class="mt-1 font-semibold text-gray-900" x-text="distanceMeters !== null ? `${distanceMeters} m` : '-' "></p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold"
                              :class="gpsValid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                            x-text="gpsValid ? 'Di Dalam Area' : 'Di Luar Area'"></span>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700" x-text="`Radius ${allowedRadius}m`"></span>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700" x-show="gpsLoading">Membaca GPS...</span>
                    </div>

                    <template x-if="gpsError">
                        <p class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700" x-text="gpsError"></p>
                    </template>
                </div>
            </x-card>

            <x-card title="Validasi Wajah" subtitle="Deteksi wajah dan pencocokan deskriptor secara real-time.">
                <div class="space-y-4">
                    <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-black">
                        <video x-ref="video" autoplay muted playsinline class="h-80 w-full object-cover"></video>
                        <canvas x-ref="overlay" class="pointer-events-none absolute inset-0 h-full w-full"></canvas>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <x-button type="button" variant="secondary" @click="startCamera" x-bind:disabled="cameraLoading || faceModelLoading">
                            <span x-show="!cameraLoading">Aktifkan Kamera</span>
                            <span x-show="cameraLoading">Memuat Kamera...</span>
                        </x-button>
                        <x-button type="button" @click="captureFace" x-bind:disabled="!faceDetected || cameraLoading || faceModelLoading">Ambil Wajah</x-button>
                    </div>

                    <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Deteksi Wajah</p>
                            <p class="mt-1 font-semibold" :class="faceDetected ? 'text-green-700' : 'text-gray-700'" x-text="faceDetected ? 'Terdeteksi' : 'Tidak terdeteksi'"></p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Status Pencocokan Wajah</p>
                            <p class="mt-1 font-semibold" :class="faceMatched ? 'text-green-700' : 'text-red-700'" x-text="faceMatched ? 'Sesuai' : 'Tidak Sesuai'"></p>
                        </div>
                    </div>

                    <template x-if="faceModelLoading">
                        <p class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-blue-700">Memuat model pengenalan wajah...</p>
                    </template>

                    <template x-if="faceError">
                        <p class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700" x-text="faceError"></p>
                    </template>
                </div>
            </x-card>
        </div>

        <x-card class="mt-4" title="Aksi Presensi Masuk" subtitle="Presensi masuk aktif jika validasi GPS dan wajah sama-sama berhasil.">
            <x-button type="submit" class="w-full justify-center" x-bind:disabled="!canCheckIn() || checkInLoading">
                <span x-show="!checkInLoading">Presensi Masuk</span>
                <span x-show="checkInLoading">Memproses Presensi Masuk...</span>
            </x-button>

            <p class="mt-3 text-xs text-gray-500">
                Syarat: berada dalam radius GPS yang diizinkan dan pencocokan wajah berhasil.
            </p>
        </x-card>
    </form>

    <x-card>
        <form method="POST" action="{{ route('user.attendance.check-out') }}" class="space-y-3" @submit="loadingCheckOut = true">
            @csrf
            @method('PATCH')
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Presensi Pulang</h3>
                <p class="mt-1 text-sm text-gray-500">Tutup sesi presensi aktif Anda.</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-600">
                @if ($activeAttendance)
                    Presensi masuk aktif pada: {{ optional($activeAttendance->check_in_time)->format('d M Y H:i') }}
                @else
                    Tidak ada presensi masuk aktif.
                @endif
            </div>

            <x-button type="submit" variant="secondary" class="w-full justify-center" x-bind:disabled="loadingCheckOut || {{ $activeAttendance ? 'false' : 'true' }}">
                <span x-show="!loadingCheckOut">Presensi Pulang</span>
                <span x-show="loadingCheckOut">Mengirim...</span>
            </x-button>
        </form>
    </x-card>

    <x-card title="Riwayat Kehadiran">
        @if ($attendances->count())
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Presensi Masuk</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Presensi Pulang</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Koordinat</th>
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
                Belum ada data kehadiran.
            </div>
        @endif
    </x-card>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
function attendanceValidation() {
    return {
        lat: "{{ old('latitude', '') }}",
        lng: "{{ old('longitude', '') }}",
        selectedLocationId: "{{ old('location_id', '') }}",
        allowedRadius: 100,
        distanceMeters: null,
        gpsValid: false,
        gpsLoading: false,
        gpsError: null,

        map: null,
        userMarker: null,
        targetMarker: null,
        targetCircle: null,
        watchId: null,

        locations: [],
        referenceDescriptor: [],

        cameraStream: null,
        cameraLoading: false,
        faceModelLoading: false,
        faceError: null,
        faceDetected: false,
        faceMatched: false,
        latestDetection: null,
        detectionInterval: null,
        capturedDescriptorJson: "{{ old('face_descriptor', '') }}",
        checkInLoading: false,
        loadingCheckOut: false,

        init() {
            this.locations = this.readJson('#attendance-locations');
            this.referenceDescriptor = this.readJson('#attendance-face-reference');

            const selectedLocation = this.getSelectedLocation();
            this.allowedRadius = selectedLocation?.radius_meters || 100;

            this.initMap();
            this.startGpsWatcher();

            window.addEventListener('beforeunload', () => {
                this.stopCamera();
                if (this.watchId !== null && navigator.geolocation) {
                    navigator.geolocation.clearWatch(this.watchId);
                }
            });
        },

        readJson(selector) {
            const el = document.querySelector(selector);
            if (!el) {
                return [];
            }

            try {
                return JSON.parse(el.textContent || '[]');
            } catch (error) {
                return [];
            }
        },

        initMap() {
            this.map = L.map(this.$refs.map).setView([-6.2, 106.816666], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(this.map);

            this.userMarker = L.marker([-6.2, 106.816666]).addTo(this.map).bindPopup('Lokasi Anda saat ini');
        },

        startGpsWatcher() {
            if (!navigator.geolocation) {
                this.gpsError = 'GPS tidak didukung oleh browser Anda.';
                return;
            }

            this.gpsLoading = true;
            this.watchId = navigator.geolocation.watchPosition(
                (position) => {
                    this.gpsLoading = false;
                    this.gpsError = null;
                    this.lat = position.coords.latitude.toFixed(7);
                    this.lng = position.coords.longitude.toFixed(7);
                    this.refreshMapOverlay();
                },
                (error) => {
                    this.gpsLoading = false;
                    this.gpsError = error.message || 'Tidak dapat membaca GPS Anda.';
                    this.gpsValid = false;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 2000,
                }
            );
        },

        onLocationChanged() {
            const selectedLocation = this.getSelectedLocation();
            this.allowedRadius = selectedLocation?.radius_meters || 100;
            this.refreshMapOverlay();
        },

        refreshMapOverlay() {
            if (!this.map || !this.lat || !this.lng) {
                return;
            }

            const userLatLng = [parseFloat(this.lat), parseFloat(this.lng)];
            this.userMarker.setLatLng(userLatLng);
            this.map.setView(userLatLng, 16);

            const location = this.getSelectedLocation();
            if (!location) {
                this.gpsValid = false;
                this.distanceMeters = null;
                if (this.targetMarker) {
                    this.map.removeLayer(this.targetMarker);
                    this.targetMarker = null;
                }
                if (this.targetCircle) {
                    this.map.removeLayer(this.targetCircle);
                    this.targetCircle = null;
                }
                return;
            }

            const targetLatLng = [location.latitude, location.longitude];

            if (!this.targetMarker) {
                this.targetMarker = L.marker(targetLatLng).addTo(this.map).bindPopup('Lokasi magang');
            } else {
                this.targetMarker.setLatLng(targetLatLng);
            }

            if (!this.targetCircle) {
                this.targetCircle = L.circle(targetLatLng, {
                    radius: this.allowedRadius,
                    color: '#2563eb',
                    fillColor: '#60a5fa',
                    fillOpacity: 0.2,
                }).addTo(this.map);
            } else {
                this.targetCircle.setLatLng(targetLatLng);
                this.targetCircle.setRadius(this.allowedRadius);
            }

            const distance = this.calculateDistance(
                parseFloat(this.lat),
                parseFloat(this.lng),
                location.latitude,
                location.longitude
            );

            this.distanceMeters = Math.round(distance * 100) / 100;
            this.gpsValid = distance <= this.allowedRadius;
        },

        getSelectedLocation() {
            return this.locations.find((item) => item.id === String(this.selectedLocationId)) || null;
        },

        calculateDistance(lat1, lon1, lat2, lon2) {
            const toRad = (value) => (value * Math.PI) / 180;
            const earthRadius = 6371000;

            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);

            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return earthRadius * c;
        },

        async loadFaceModels() {
            if (window.__internhubFaceModelsLoaded) {
                this.faceModelLoading = false;
                return;
            }

            if (!window.faceapi) {
                this.faceError = 'Pustaka face-api.js gagal dimuat.';
                return;
            }

            this.faceModelLoading = true;
            this.faceError = null;

            const modelSources = [
                '/models/faceapi',
                'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/',
            ];

            let loaded = false;
            for (const source of modelSources) {
                try {
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(source),
                        faceapi.nets.faceLandmark68Net.loadFromUri(source),
                        faceapi.nets.faceRecognitionNet.loadFromUri(source),
                    ]);
                    loaded = true;
                    break;
                } catch (error) {
                    loaded = false;
                }
            }

            this.faceModelLoading = false;

            if (!loaded) {
                this.faceError = 'Model wajah tidak dapat dimuat. Silakan hubungi administrator.';
                return;
            }

            window.__internhubFaceModelsLoaded = true;
        },

        async startCamera() {
            if (!window.__internhubFaceModelsLoaded) {
                await this.loadFaceModels();
            }

            if (!window.__internhubFaceModelsLoaded) {
                return;
            }

            this.cameraLoading = true;
            this.faceError = null;

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                this.cameraStream = stream;
                this.$refs.video.srcObject = stream;
                await this.$refs.video.play();
                this.startFaceDetectionLoop();
            } catch (error) {
                this.faceError = 'Akses kamera ditolak atau tidak tersedia.';
            } finally {
                this.cameraLoading = false;
            }
        },

        stopCamera() {
            if (this.cameraStream) {
                this.cameraStream.getTracks().forEach((track) => track.stop());
            }

            if (this.detectionInterval) {
                clearInterval(this.detectionInterval);
            }
        },

        startFaceDetectionLoop() {
            if (this.detectionInterval) {
                clearInterval(this.detectionInterval);
            }

            this.detectionInterval = setInterval(async () => {
                if (this.faceModelLoading || !this.$refs.video || this.$refs.video.readyState < 2) {
                    return;
                }

                const detection = await faceapi
                    .detectSingleFace(this.$refs.video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                const displaySize = {
                    width: this.$refs.video.videoWidth,
                    height: this.$refs.video.videoHeight,
                };

                faceapi.matchDimensions(this.$refs.overlay, displaySize);
                const resized = detection ? faceapi.resizeResults(detection, displaySize) : null;
                const ctx = this.$refs.overlay.getContext('2d');
                ctx.clearRect(0, 0, this.$refs.overlay.width, this.$refs.overlay.height);

                if (resized) {
                    faceapi.draw.drawDetections(this.$refs.overlay, resized);
                    this.faceDetected = true;
                    this.latestDetection = detection;
                } else {
                    this.faceDetected = false;
                    this.latestDetection = null;
                    this.faceMatched = false;
                    this.capturedDescriptorJson = '';
                }
            }, 500);
        },

        captureFace() {
            if (!this.latestDetection) {
                this.faceError = 'Wajah tidak terdeteksi, silakan posisikan wajah dengan jelas di kamera.';
                this.faceMatched = false;
                this.capturedDescriptorJson = '';
                return;
            }

            const descriptor = Array.from(this.latestDetection.descriptor || []);
            this.capturedDescriptorJson = JSON.stringify(descriptor);

            if (!Array.isArray(this.referenceDescriptor) || this.referenceDescriptor.length === 0) {
                this.faceError = 'Data wajah referensi belum tersedia untuk akun ini.';
                this.faceMatched = false;
                return;
            }

            if (this.referenceDescriptor.length !== descriptor.length) {
                this.faceError = 'Dimensi data wajah tidak sesuai.';
                this.faceMatched = false;
                return;
            }

            const distance = this.euclideanDistance(descriptor, this.referenceDescriptor);
            this.faceMatched = distance < 0.6;
            this.faceError = this.faceMatched ? null : 'Pencocokan wajah tidak sesuai. Silakan ambil ulang.';
        },

        euclideanDistance(a, b) {
            return Math.sqrt(a.reduce((acc, value, index) => {
                const delta = value - b[index];
                return acc + (delta * delta);
            }, 0));
        },

        canCheckIn() {
            return Boolean(this.selectedLocationId)
                && Boolean(this.lat)
                && Boolean(this.lng)
                && this.gpsValid
                && this.faceMatched
                && Boolean(this.capturedDescriptorJson)
                && !this.gpsLoading
                && !this.faceModelLoading;
        },

        submitCheckIn(event) {
            if (!this.canCheckIn()) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        message: 'Presensi masuk ditolak. Pastikan GPS berada dalam radius dan wajah berhasil dicocokkan.',
                        type: 'error',
                    },
                }));
                return;
            }

            this.checkInLoading = true;
            event.target.submit();
        }
    }
}
</script>
@endpush
