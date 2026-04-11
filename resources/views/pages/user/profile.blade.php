@extends('layouts.user')

@section('title', 'Profil')
@section('header', 'Pengaturan Profil')

@section('content')
<div class="space-y-6" x-data="faceSetup()" x-init="init()">
    <x-card title="Setup Profil" subtitle="Lengkapi profil dan rekam wajah Anda untuk mengaktifkan fitur presensi.">
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 text-center">
                    @php
                        $photoUrl = null;
                        if ($user?->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                            $photoUrl = \Illuminate\Support\Facades\Storage::url($user->profile_photo);
                        }
                    @endphp
                    @if ($photoUrl)
                        <img src="{{ $photoUrl }}" alt="Foto Profil" class="mx-auto h-28 w-28 rounded-full object-cover ring-4 ring-white shadow">
                    @else
                        <div class="mx-auto grid h-28 w-28 place-content-center rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 text-3xl font-bold text-white">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif

                    <p class="mt-4 text-sm font-semibold text-gray-800">{{ $user->name ?? 'Pengguna' }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email ?? '-' }}</p>

                    <div class="mt-4">
                        @if ($user?->face_registered)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Wajah sudah terdaftar</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Wajah belum terdaftar</span>
                        @endif
                    </div>

                    <button type="button" @click="openModal" class="mt-5 w-full rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-black">
                        Ambil Foto Wajah
                    </button>
                </div>
            </div>

            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('user.profile.update', $user->id) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                        <input name="name" type="text" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Jabatan</label>
                        <input name="title" type="text" value="{{ old('title', $user->title) }}" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Departemen</label>
                        <input name="department" type="text" value="{{ old('department', $user->department) }}" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Penempatan</label>
                        <input name="placement" type="text" value="{{ old('placement', $user->placement) }}" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <div class="sm:col-span-2">
                        <x-button type="submit">Simpan Perubahan Profil</x-button>
                    </div>
                </form>
            </div>
        </div>
    </x-card>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @keydown.escape.window="closeModal()">
        <div class="w-full max-w-3xl rounded-2xl bg-white p-5 shadow-2xl" @click.outside="closeModal()">
            <div class="mb-4 flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Rekam Wajah</h3>
                    <p class="text-sm text-gray-500">Posisikan wajah Anda di dalam frame.</p>
                </div>
                <button type="button" @click="closeModal()" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100">✕</button>
            </div>

            <form method="POST" action="{{ route('user.profile.face.store') }}" @submit.prevent="submitFace($event)">
                @csrf
                <input type="hidden" name="face_image" x-model="faceImage">
                <input type="hidden" name="face_descriptor" x-model="faceDescriptor">

                <div class="grid gap-4 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <div class="relative overflow-hidden rounded-xl border-2 transition-colors" :class="detectionState === 'single' ? 'border-emerald-500' : 'border-rose-500'">
                            <video x-ref="video" autoplay muted playsinline class="h-72 w-full bg-black object-cover"></video>
                            <canvas x-ref="overlay" class="pointer-events-none absolute inset-0 h-full w-full"></canvas>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Status Deteksi</p>
                            <p class="mt-1 text-sm font-semibold" :class="detectionState === 'single' ? 'text-emerald-600' : 'text-rose-600'" x-text="statusText"></p>
                        </div>

                        <template x-if="previewImage">
                            <div class="rounded-xl border border-gray-200 p-2">
                                <p class="mb-2 text-xs text-gray-500">Pratinjau Hasil</p>
                                <img :src="previewImage" alt="Hasil Rekaman Wajah" class="h-32 w-full rounded-lg object-cover">
                            </div>
                        </template>

                        <template x-if="errorMessage">
                            <p class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700" x-text="errorMessage"></p>
                        </template>

                        <template x-if="successMessage">
                            <p class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700" x-text="successMessage"></p>
                        </template>
                    </div>
                </div>

                <x-input-error :messages="$errors->get('face_image')" class="mt-3" />
                <x-input-error :messages="$errors->get('face_descriptor')" class="mt-2" />

                <div class="mt-5 flex flex-wrap justify-end gap-2">
                    <button type="button" @click="captureFace" class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-black">Ambil Foto</button>
                    <button type="button" @click="retryCapture" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Ulangi</button>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700" :disabled="!canSave" :class="!canSave ? 'opacity-50 cursor-not-allowed' : ''">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    function faceSetup() {
        return {
            showModal: false,
            modelsLoaded: false,
            loadingModels: false,
            isSubmitting: false,
            stream: null,
            detectionTimer: null,
            detectionState: 'none',
            statusText: 'Wajah tidak terdeteksi, silakan coba lagi',
            errorMessage: '',
            successMessage: '',
            faceImage: '',
            faceDescriptor: '',
            previewImage: '',
            latestDetection: null,

            init() {
                window.addEventListener('beforeunload', () => {
                    this.stopDetection();
                    this.stopCamera();
                });
            },

            async openModal() {
                this.showModal = true;
                this.errorMessage = '';
                this.successMessage = '';
                this.retryCapture();

                const modelReady = await this.loadModels();
                if (!modelReady) {
                    return;
                }

                await this.startCamera();
            },

            async closeModal() {
                this.showModal = false;
                this.stopDetection();
                this.stopCamera();
            },

            async loadModels() {
                if (this.modelsLoaded) {
                    return true;
                }

                if (this.loadingModels) {
                    return false;
                }

                if (!window.faceapi) {
                    this.errorMessage = 'Pustaka pengenalan wajah gagal dimuat. Muat ulang halaman dan coba kembali.';
                    return false;
                }

                this.loadingModels = true;
                try {
                    const sources = [
                        '/models/faceapi',
                        'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model',
                    ];

                    let loaded = false;
                    for (const source of sources) {
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

                    if (!loaded) {
                        throw new Error('Failed loading all model sources');
                    }

                    this.modelsLoaded = true;
                    this.errorMessage = '';
                    return true;
                } catch (error) {
                    this.errorMessage = 'Model wajah gagal dimuat. Periksa koneksi internet atau konfigurasi model server.';
                    return false;
                } finally {
                    this.loadingModels = false;
                }
            },

            async startCamera() {
                if (!this.modelsLoaded) {
                    return;
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    this.errorMessage = 'Perangkat atau browser tidak mendukung akses kamera.';
                    return;
                }

                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                    this.$refs.video.srcObject = this.stream;
                    await this.$refs.video.play();
                    this.startDetection();
                } catch (error) {
                    this.errorMessage = 'Akses kamera ditolak. Izinkan kamera untuk melanjutkan perekaman wajah.';
                }
            },

            stopCamera() {
                if (!this.stream) {
                    return;
                }

                this.stream.getTracks().forEach((track) => track.stop());
                this.stream = null;
            },

            startDetection() {
                this.stopDetection();

                this.detectionTimer = setInterval(async () => {
                    if (!this.showModal || !this.modelsLoaded || !this.$refs.video || this.$refs.video.readyState < 2) {
                        return;
                    }

                    const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 });
                    const detections = await faceapi
                        .detectAllFaces(this.$refs.video, options)
                        .withFaceLandmarks()
                        .withFaceDescriptors();

                    const displaySize = {
                        width: this.$refs.video.videoWidth,
                        height: this.$refs.video.videoHeight,
                    };

                    faceapi.matchDimensions(this.$refs.overlay, displaySize);
                    const resized = faceapi.resizeResults(detections, displaySize);
                    const ctx = this.$refs.overlay.getContext('2d');
                    ctx.clearRect(0, 0, this.$refs.overlay.width, this.$refs.overlay.height);
                    faceapi.draw.drawDetections(this.$refs.overlay, resized);

                    if (detections.length === 1) {
                        this.detectionState = 'single';
                        this.statusText = 'Wajah terdeteksi';
                        this.latestDetection = detections[0];
                        return;
                    }

                    this.latestDetection = null;
                    if (detections.length > 1) {
                        this.detectionState = 'multiple';
                        this.statusText = 'Terdeteksi lebih dari satu wajah';
                    } else {
                        this.detectionState = 'none';
                        this.statusText = 'Wajah tidak terdeteksi, silakan coba lagi';
                    }
                }, 350);
            },

            stopDetection() {
                if (this.detectionTimer) {
                    clearInterval(this.detectionTimer);
                    this.detectionTimer = null;
                }
            },

            captureFace() {
                this.errorMessage = '';
                this.successMessage = '';

                if (this.detectionState !== 'single' || !this.latestDetection) {
                    this.errorMessage = this.detectionState === 'multiple'
                        ? 'Terdeteksi lebih dari satu wajah. Pastikan hanya satu wajah di dalam frame.'
                        : 'Wajah tidak terdeteksi, silakan coba lagi';
                    return;
                }

                const video = this.$refs.video;
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                this.faceImage = canvas.toDataURL('image/jpeg', 0.92);
                this.previewImage = this.faceImage;
                this.faceDescriptor = JSON.stringify(Array.from(this.latestDetection.descriptor || []));
                this.successMessage = 'Wajah berhasil direkam';
            },

            retryCapture() {
                this.faceImage = '';
                this.faceDescriptor = '';
                this.previewImage = '';
                this.errorMessage = '';
                this.successMessage = '';
            },

            get canSave() {
                return this.faceImage.length > 0 && this.faceDescriptor.length > 0;
            },

            submitFace(event) {
                if (!this.canSave) {
                    this.errorMessage = 'Wajah tidak terdeteksi, silakan coba lagi';
                    return;
                }

                this.isSubmitting = true;

                event.target.submit();
            },
        };
    }
</script>
@endpush
