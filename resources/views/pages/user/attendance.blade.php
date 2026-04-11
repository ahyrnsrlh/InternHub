@extends('layouts.user')

@section('title', 'Presensi')
@section('header', 'Presensi Berbasis GPS')

@push('styles')
<style>
    .face-ring-success {
        animation: facePulseSuccess 1.5s ease-in-out infinite;
    }

    .face-ring-error {
        animation: facePulseError 1.5s ease-in-out infinite;
    }

    @keyframes facePulseSuccess {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.45); }
        70% { transform: scale(1.03); box-shadow: 0 0 0 16px rgba(16, 185, 129, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    @keyframes facePulseError {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { transform: scale(1.03); box-shadow: 0 0 0 16px rgba(239, 68, 68, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
</style>
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

    <div class="grid gap-6">
        <x-card title="Proses Presensi Harian" subtitle="Ikuti alur presensi: validasi lokasi GPS, verifikasi wajah, isi keterangan, lalu simpan sesuai jam presensi.">
            <div class="space-y-4">
                <div class="rounded-xl border px-4 py-3" :class="gpsValid ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-amber-200 bg-amber-50 text-amber-700'">
                    <p class="text-sm font-semibold" x-text="gpsValid ? 'Lokasi dalam radius' : 'Lokasi di luar radius'"></p>
                    <p class="mt-1 text-xs" x-text="gpsValid ? 'Lokasi Anda sesuai titik lokasi magang.' : 'Anda masih bisa lanjut presensi, tetapi status akan dicatat tidak valid.'"></p>
                    <p class="mt-1 text-xs text-gray-600" x-show="distanceMeters !== null" x-text="`Jarak Anda: ${distanceMeters} m (radius: ${allowedRadius} m)`"></p>
                </div>

                <template x-if="gpsError">
                    <p class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" x-text="gpsError"></p>
                </template>

                <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-black shadow-sm">
                    <video x-ref="video" autoplay muted playsinline class="h-[430px] w-full object-cover"></video>
                    <canvas x-ref="overlay" class="pointer-events-none absolute inset-0 h-full w-full"></canvas>

                    <div class="absolute left-4 top-4 z-20">
                        <div class="inline-flex items-center gap-2 rounded-full bg-black/60 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm"
                            :class="faceDetected ? 'ring-1 ring-emerald-300/60' : 'ring-1 ring-rose-300/60'">
                            <span class="h-2.5 w-2.5 rounded-full" :class="faceDetected ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400'"></span>
                            <span x-text="faceDetected ? 'Wajah terdeteksi' : 'Wajah tidak terdeteksi'"></span>
                        </div>
                    </div>

                    <div class="absolute right-4 top-4 z-20">
                        <div class="inline-flex items-center gap-2 rounded-full bg-black/60 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm"
                            :class="faceMatched ? 'ring-1 ring-emerald-300/60' : 'ring-1 ring-rose-300/60'">
                            <span x-text="faceMatched ? '✅ Wajah sesuai' : '⚠️ Wajah tidak sesuai'"></span>
                        </div>
                    </div>

                    <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                        <div class="h-36 w-36 rounded-full border-4 transition-all duration-300"
                            :class="faceMatched ? 'border-emerald-400 face-ring-success' : 'border-rose-400 face-ring-error'"></div>
                    </div>

                    <template x-if="cameraNoticeVisible">
                        <div class="absolute inset-x-4 bottom-4 z-20" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <div class="mx-auto flex max-w-xl items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium shadow-lg backdrop-blur"
                                :class="cameraNoticeType === 'success' ? 'border-emerald-300 bg-emerald-600/85 text-white' : cameraNoticeType === 'warning' ? 'border-amber-300 bg-amber-600/85 text-white' : 'border-rose-300 bg-rose-600/85 text-white'">
                                <span x-text="cameraNoticeType === 'success' ? '✅' : (cameraNoticeType === 'warning' ? '⚠️' : '❌')"></span>
                                <span x-text="cameraNoticeMessage"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="cameraLoading || faceModelLoading">
                        <div class="absolute inset-x-4 top-16 z-20">
                            <div class="mx-auto flex max-w-xl items-center gap-2 rounded-xl border border-indigo-300 bg-indigo-600/85 px-3 py-2 text-sm font-medium text-white shadow-lg backdrop-blur">
                                <span>⏳</span>
                                <span x-show="faceModelLoading">Memuat model pengenalan wajah...</span>
                                <span x-show="cameraLoading">Mengaktifkan kamera...</span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="rounded-xl border px-4 py-3"
                    :class="isProcessReady() ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'">
                    <p class="text-sm font-semibold" x-text="isProcessReady() ? 'Siap untuk presensi' : 'Presensi tidak dapat dilakukan'"></p>
                    <p class="mt-1 text-xs" x-text="processStatusMessage()"></p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <x-button type="button" variant="primary" class="rounded-xl" @click="captureFace" x-bind:disabled="!faceDetected || !faceMatched || cameraLoading || faceModelLoading">Ambil Foto</x-button>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="faceValidationLocked ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'" x-text="faceValidationLocked ? `Validasi selesai (${capturedAt})` : 'Belum ambil foto'"></span>
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    <p class="font-semibold" x-text="attendanceMode === 'check-in' ? 'Mode Presensi Masuk' : attendanceMode === 'check-out' ? 'Mode Presensi Pulang' : 'Di luar jam presensi'"></p>
                    <p class="mt-1 text-xs" x-text="`Jam sekarang: ${currentTimeText}. Jam masuk 06.00-08.59, jam pulang 17.00-18.59.`"></p>
                </div>

                <x-card class="border border-gray-200 bg-gray-50" padding="p-4" title="📝 Catatan Kehadiran" subtitle="Form akan menyesuaikan otomatis dengan jadwal presensi.">
                    <div class="space-y-3">
                        <div x-show="attendanceMode === 'check-in'">
                            <label class="mb-1 block text-xs font-semibold text-gray-700">Presensi Masuk (Rencana Kegiatan)</label>
                            <p class="mb-2 text-xs text-gray-500">Tuliskan rencana singkat kegiatan Kerja Praktik (KP) yang akan Anda lakukan hari ini, sesuai buku panduan KP.</p>
                            <textarea x-model="checkInNote" class="min-h-24 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Contoh: Menyusun modul validasi GPS dan menyiapkan pengujian fitur absensi."></textarea>
                        </div>

                        <div x-show="attendanceMode === 'check-out'">
                            <label class="mb-1 block text-xs font-semibold text-gray-700">Presensi Pulang (Realisasi Kegiatan)</label>
                            <p class="mb-2 text-xs text-gray-500">Tuliskan kegiatan yang telah Anda lakukan hari ini sebagai realisasi dari rencana.</p>
                            <textarea x-model="checkOutNote" class="min-h-24 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Contoh: Validasi GPS selesai diuji, dokumentasi hasil harian disimpan."></textarea>
                        </div>

                        <div x-show="attendanceMode === 'closed'" class="rounded-xl border border-dashed border-gray-300 bg-white px-3 py-4 text-center text-sm text-gray-600">
                            Di luar jam presensi. Form akan aktif otomatis pada jam masuk atau jam pulang.
                        </div>
                    </div>
                </x-card>

                <form method="POST" action="{{ route('user.attendance.check-in') }}" x-ref="checkInForm" class="hidden">
                    @csrf
                    <input type="hidden" name="location_id" x-model="selectedLocationId">
                    <input type="hidden" name="latitude" x-model="lat">
                    <input type="hidden" name="longitude" x-model="lng">
                    <input type="hidden" name="face_descriptor" x-model="capturedDescriptorJson">
                    <input type="hidden" name="allowed_radius_meters" x-model="allowedRadius">
                    <input type="hidden" name="plan_note" x-model="checkInNote">
                </form>

                <div class="space-y-3 pt-2">
                    <template x-if="attendanceMode === 'check-in'">
                        <x-button type="button" class="w-full justify-center rounded-xl" x-bind:disabled="!canCheckIn() || checkInLoading" @click="submitCheckIn()">
                            <span x-show="!checkInLoading">Presensi Masuk</span>
                            <span x-show="checkInLoading">Memproses Presensi Masuk...</span>
                        </x-button>
                    </template>

                    <template x-if="attendanceMode === 'check-out'">
                        <form method="POST" action="{{ route('user.attendance.check-out') }}" @submit="loadingCheckOut = true">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="realization_note" x-model="checkOutNote">

                            <div class="mb-3 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                                @if ($activeAttendance)
                                    Sesi aktif sejak {{ optional($activeAttendance->check_in_time)->format('d M Y H:i') }}
                                @else
                                    Tidak ada sesi presensi aktif.
                                @endif
                            </div>

                            <x-button type="submit" variant="secondary" class="w-full justify-center rounded-xl" x-bind:disabled="!canCheckOut()">
                                <span x-show="!loadingCheckOut">Presensi Pulang</span>
                                <span x-show="loadingCheckOut">Memproses Presensi Pulang...</span>
                            </x-button>
                        </form>
                    </template>

                    <template x-if="attendanceMode === 'closed'">
                        <x-button type="button" variant="ghost" class="w-full cursor-not-allowed justify-center rounded-xl" disabled>
                            Presensi Belum Dibuka
                        </x-button>
                    </template>
                </div>
            </div>
        </x-card>
    </div>

    <x-card title="Riwayat Kehadiran" subtitle="Riwayat presensi terbaru Anda.">
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
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-4 py-3 text-gray-700">{{ optional($attendance->check_in_time)->translatedFormat('d M Y, H:i') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ optional($attendance->check_out_time)->translatedFormat('d M Y, H:i') ?? '-' }}</td>
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
        latestDescriptor: null,
        faceCaptured: false,
        faceValidationLocked: false,
        capturedAt: '',
        faceCaptureError: null,
        cameraNoticeVisible: false,
        cameraNoticeType: 'error',
        cameraNoticeMessage: '',
        cameraNoticeTimer: null,
        lastCameraNoticeKey: '',
        checkInNote: "{{ old('plan_note', '') }}",
        checkOutNote: "{{ old('realization_note', '') }}",
        hasActiveAttendance: "{{ $activeAttendance ? '1' : '0' }}" === '1',
        checkInLoading: false,
        loadingCheckOut: false,
        attendanceMode: 'closed',
        currentTimeText: '--:--',
        timeModeTimer: null,

        init() {
            this.locations = this.readJson('#attendance-locations');
            this.referenceDescriptor = this.readJson('#attendance-face-reference');
            this.selectedLocationId = this.selectedLocationId || (this.locations[0]?.id || '');

            const selectedLocation = this.getSelectedLocation();
            this.allowedRadius = selectedLocation?.radius_meters || 100;

            this.refreshAttendanceMode();
            this.timeModeTimer = setInterval(() => this.refreshAttendanceMode(), 30000);
            this.startGpsWatcher();
            this.startCamera();

            window.addEventListener('beforeunload', () => {
                this.stopCamera();
                if (this.timeModeTimer) {
                    clearInterval(this.timeModeTimer);
                }
                if (this.watchId !== null && navigator.geolocation) {
                    navigator.geolocation.clearWatch(this.watchId);
                }
            });
        },

        refreshAttendanceMode() {
            const now = new Date();
            const hour = now.getHours();
            const minute = now.getMinutes();
            const totalMinutes = (hour * 60) + minute;

            this.currentTimeText = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
            });

            if (totalMinutes >= 360 && totalMinutes < 540) {
                this.attendanceMode = 'check-in';
                return;
            }

            if (totalMinutes >= 1020 && totalMinutes < 1140) {
                this.attendanceMode = 'check-out';
                return;
            }

            this.attendanceMode = 'closed';
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
                    this.refreshGpsValidation();
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
            this.refreshGpsValidation();
        },

        refreshGpsValidation() {
            if (!this.lat || !this.lng) {
                return;
            }

            const location = this.getSelectedLocation();
            if (!location) {
                this.gpsValid = false;
                this.distanceMeters = null;
                return;
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
                this.showCameraNotice('face-lib-error', this.faceError, 'error', 3000);
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
                this.showCameraNotice('face-model-error', this.faceError, 'error', 3000);
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
                this.showCameraNotice('camera-access-error', this.faceError, 'error', 3000);
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

            if (this.cameraNoticeTimer) {
                clearTimeout(this.cameraNoticeTimer);
            }
        },

        startFaceDetectionLoop() {
            if (this.detectionInterval) {
                clearInterval(this.detectionInterval);
            }

            this.detectionInterval = setInterval(async () => {
                if (this.faceValidationLocked) {
                    return;
                }

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

                const wasDetected = this.faceDetected;
                const wasMatched = this.faceMatched;

                if (resized) {
                    faceapi.draw.drawDetections(this.$refs.overlay, resized);
                    this.faceDetected = true;
                    this.latestDetection = detection;

                    const descriptor = Array.from(detection.descriptor || []);
                    this.latestDescriptor = descriptor;
                    this.faceCaptureError = null;

                    if (!Array.isArray(this.referenceDescriptor) || this.referenceDescriptor.length === 0) {
                        this.faceMatched = false;
                        this.faceCaptured = false;
                        this.capturedDescriptorJson = '';
                        this.capturedAt = '';
                        this.faceError = 'Data wajah referensi belum tersedia untuk akun ini.';
                        this.showCameraNotice('reference-missing', this.faceError, 'error', 2600);
                        return;
                    }

                    if (this.referenceDescriptor.length !== descriptor.length) {
                        this.faceMatched = false;
                        this.faceCaptured = false;
                        this.capturedDescriptorJson = '';
                        this.capturedAt = '';
                        this.faceError = 'Dimensi data wajah tidak sesuai.';
                        this.showCameraNotice('reference-dimension', this.faceError, 'error', 2600);
                        return;
                    }

                    const distance = this.euclideanDistance(descriptor, this.referenceDescriptor);
                    this.faceMatched = distance < 0.6;
                    this.faceError = this.faceMatched ? null : 'Wajah belum sesuai. Posisikan wajah tepat di tengah lingkaran.';

                    if (!this.faceMatched) {
                        this.faceCaptured = false;
                        this.capturedDescriptorJson = '';
                        this.capturedAt = '';
                        this.faceCaptureError = 'Wajah belum valid. Ambil foto setelah status wajah sesuai.';
                        if (wasMatched) {
                            this.showCameraNotice('face-mismatch', 'Wajah tidak sesuai.', 'warning', 2200);
                        }
                    } else if (!wasMatched) {
                        this.showCameraNotice('face-match', 'Wajah sesuai.', 'success', 1600);
                    }

                    if (!wasDetected) {
                        this.showCameraNotice('face-detected', 'Wajah terdeteksi.', 'success', 1400);
                    }
                } else {
                    this.faceDetected = false;
                    this.latestDetection = null;
                    this.latestDescriptor = null;
                    this.faceMatched = false;
                    this.faceCaptured = false;
                    this.capturedDescriptorJson = '';
                    this.capturedAt = '';
                    this.faceCaptureError = null;
                    this.faceError = 'Wajah tidak terdeteksi. Pastikan pencahayaan cukup.';
                    if (wasDetected) {
                        this.showCameraNotice('face-not-detected', this.faceError, 'error', 2400);
                    }
                }
            }, 500);
        },

        showCameraNotice(key, message, type = 'error', duration = 2200) {
            if (!message) {
                return;
            }

            if (this.cameraNoticeVisible && this.lastCameraNoticeKey === key && this.cameraNoticeMessage === message) {
                return;
            }

            this.lastCameraNoticeKey = key;
            this.cameraNoticeType = type;
            this.cameraNoticeMessage = message;
            this.cameraNoticeVisible = true;

            if (this.cameraNoticeTimer) {
                clearTimeout(this.cameraNoticeTimer);
            }

            this.cameraNoticeTimer = setTimeout(() => {
                this.cameraNoticeVisible = false;
            }, duration);
        },

            captureFace() {
                if (!this.faceDetected || !this.faceMatched || !Array.isArray(this.latestDescriptor) || this.latestDescriptor.length === 0) {
                    this.faceCaptured = false;
                    this.capturedDescriptorJson = '';
                    this.capturedAt = '';
                    this.faceCaptureError = 'Wajah harus valid terlebih dahulu sebelum ambil foto.';
                    this.showCameraNotice('capture-invalid', this.faceCaptureError, 'warning', 2400);
                    return;
                }

                this.capturedDescriptorJson = JSON.stringify(this.latestDescriptor);
                this.faceCaptured = true;
                this.faceValidationLocked = true;
                this.capturedAt = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                this.faceCaptureError = null;
                this.showCameraNotice('capture-success', 'Foto wajah berhasil diambil. Validasi wajah selesai.', 'success', 2200);
            },

        euclideanDistance(a, b) {
            return Math.sqrt(a.reduce((acc, value, index) => {
                const delta = value - b[index];
                return acc + (delta * delta);
            }, 0));
        },

        canCheckIn() {
            return this.attendanceMode === 'check-in'
                && Boolean(this.lat)
                && Boolean(this.lng)
                && this.faceMatched
                && this.faceCaptured
                && this.faceValidationLocked
                && Boolean(this.capturedDescriptorJson)
                && this.checkInNote.trim().length >= 10
                && !this.gpsLoading
                && !this.faceModelLoading
                && !this.cameraLoading;
        },

        canCheckOut() {
            return this.attendanceMode === 'check-out'
                && !this.loadingCheckOut
                && this.hasActiveAttendance
                && this.checkOutNote.trim().length >= 10;
        },

        isProcessReady() {
            if (this.attendanceMode === 'check-in') {
                return this.canCheckIn();
            }

            if (this.attendanceMode === 'check-out') {
                return this.canCheckOut();
            }

            return false;
        },

        processStatusMessage() {
            if (this.attendanceMode === 'check-in') {
                return this.canCheckIn()
                    ? 'Siap untuk presensi masuk.'
                    : this.combinedIssueMessage();
            }

            if (this.attendanceMode === 'check-out') {
                if (!this.hasActiveAttendance) {
                    return 'Tidak ada sesi presensi masuk aktif untuk dipulangkan.';
                }

                if (this.checkOutNote.trim().length < 10) {
                    return 'Catatan realisasi kegiatan minimal 10 karakter.';
                }

                return 'Siap untuk presensi pulang.';
            }

            return `Di luar jam presensi. Jam sekarang ${this.currentTimeText}.`;
        },

        combinedIssueMessage() {
            if (this.attendanceMode !== 'check-in') {
                return 'Presensi masuk hanya tersedia pukul 06.00 - 08.59.';
            }

            if (!this.selectedLocationId) {
                return 'Lokasi magang belum terdeteksi. Pastikan data lokasi magang Anda sudah tersimpan.';
            }

            if (this.gpsLoading) {
                return 'Sistem masih mengambil lokasi GPS Anda.';
            }

            if (!this.gpsValid) {
                return 'Lokasi Anda di luar radius. Jika lanjut, presensi akan tercatat tidak valid.';
            }

            if (this.faceModelLoading || this.cameraLoading) {
                return 'Sistem masih mempersiapkan validasi wajah.';
            }

            if (!this.faceDetected) {
                return 'Wajah belum terdeteksi pada kamera.';
            }

            if (!this.faceMatched) {
                return 'Wajah belum sesuai dengan data profil.';
            }

            if (!this.faceCaptured) {
                return 'Klik Ambil Foto setelah wajah valid.';
            }

            if (!this.faceValidationLocked) {
                return 'Validasi wajah belum dikunci. Silakan ambil foto wajah terlebih dahulu.';
            }

            if (this.checkInNote.trim().length < 10) {
                return 'Catatan rencana kegiatan minimal 10 karakter.';
            }

            return 'Validasi belum lengkap.';
        },

        submitCheckIn() {
            if (!this.canCheckIn()) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        message: this.combinedIssueMessage(),
                        type: 'error',
                    },
                }));
                return;
            }

            if (!this.gpsValid) {
                const proceed = window.confirm('Lokasi Anda berada di luar radius lokasi magang. Lanjutkan presensi? Status akan tercatat tidak valid.');
                if (!proceed) {
                    return;
                }
            }

            this.checkInLoading = true;
            this.$refs.checkInForm.submit();
        }
    }
}
</script>
@endpush
