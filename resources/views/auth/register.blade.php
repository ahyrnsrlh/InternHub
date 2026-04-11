@extends('layouts.app')

@section('title', 'Register | InternHub')
@section('hide_chrome', '1')
@section('body_class', 'internhub-shell text-content antialiased')
@section('content_container_class', 'min-h-screen flex items-center justify-center px-4 py-12')

@section('content')
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="mx-auto h-14 w-14 rounded-xl bg-primary text-content-inverse grid place-content-center text-xl font-black">A</div>
            <h1 class="text-2xl font-black mt-4">Join InternHub</h1>
            <p class="text-xs uppercase tracking-[0.2em] text-content-muted mt-1">Executive Experience</p>
        </div>

        <x-card class="internhub-glass internhub-shadow" title="Request Registration" subtitle="Create your executive internship account.">
            <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="registerFaceCapture()" x-init="init()" @submit.prevent="submitForm($event)">
                @csrf

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Full Name
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="Alex Carter">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </label>

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Work Email
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="name@company.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </label>

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Password
                    <input type="password" name="password" required autocomplete="new-password" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </label>

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Confirm Password
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </label>

                <input type="hidden" name="face_descriptor" x-model="faceDescriptorJson">
                <input type="hidden" name="liveness_verified" :value="livenessVerified ? '1' : '0'">

                <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-content-muted">Face Verification</p>

                    <div class="relative overflow-hidden rounded-lg border-2 transition-colors"
                         :class="faceDetected ? 'border-green-500' : 'border-red-500'">
                        <video x-ref="video" autoplay muted playsinline class="h-56 w-full bg-black object-cover"></video>
                        <canvas x-ref="overlay" class="pointer-events-none absolute inset-0 h-full w-full"></canvas>
                    </div>

                    <div class="grid gap-2 text-xs sm:grid-cols-2">
                        <div class="rounded-md border border-gray-200 px-3 py-2">
                            <p class="text-gray-500">Face status</p>
                            <p class="font-semibold" :class="faceDetected ? 'text-green-600' : 'text-red-600'" x-text="faceDetected ? 'Face detected' : 'No face detected'"></p>
                        </div>
                        <div class="rounded-md border border-gray-200 px-3 py-2">
                            <p class="text-gray-500">Liveness</p>
                            <p class="font-semibold" :class="livenessVerified ? 'text-green-600' : 'text-amber-600'" x-text="livenessVerified ? 'Verified' : 'Blink or move head'"></p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <x-button type="button" variant="secondary" @click="startCamera" x-bind:disabled="cameraLoading">
                            <span x-show="!cameraLoading">Start Camera</span>
                            <span x-show="cameraLoading">Loading...</span>
                        </x-button>
                        <x-button type="button" @click="captureFace" x-bind:disabled="!faceDetected || !livenessVerified || modelLoading">Capture Face</x-button>
                    </div>

                    <template x-if="modelLoading">
                        <p class="rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-blue-700">Loading face model...</p>
                    </template>
                    <template x-if="faceError">
                        <p class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700" x-text="faceError"></p>
                    </template>

                    <x-input-error :messages="$errors->get('face_descriptor')" class="mt-2" />
                    <x-input-error :messages="$errors->get('liveness_verified')" class="mt-2" />
                </div>

                <x-button type="submit" class="w-full" x-bind:disabled="!faceCaptured">Create Account</x-button>
            </form>

            <p class="mt-6 text-center text-sm text-content-muted">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-content hover:underline">Sign in here</a>
            </p>
        </x-card>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    function registerFaceCapture() {
        return {
            modelLoading: false,
            cameraLoading: false,
            faceDetected: false,
            faceCaptured: false,
            faceDescriptorJson: '',
            faceError: null,
            livenessVerified: false,
            blinkDetected: false,
            headMovementDetected: false,
            lowEarFrames: 0,
            noseBaseX: null,
            detectionInterval: null,
            stream: null,
            latestDescriptor: null,
            latestLandmarks: null,

            async init() {
                await this.loadModels();
                await this.startCamera();
            },

            async loadModels() {
                if (!window.faceapi) {
                    this.faceError = 'face-api library failed to load.';
                    return;
                }

                this.modelLoading = true;
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

                if (!loaded) {
                    this.faceError = 'Unable to load face models.';
                }

                this.modelLoading = false;
            },

            async startCamera() {
                this.cameraLoading = true;
                this.faceError = null;

                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                    this.$refs.video.srcObject = this.stream;
                    await this.$refs.video.play();
                    this.startDetectionLoop();
                } catch (error) {
                    this.faceError = 'Camera access is required for registration.';
                } finally {
                    this.cameraLoading = false;
                }
            },

            startDetectionLoop() {
                if (this.detectionInterval) {
                    clearInterval(this.detectionInterval);
                }

                this.detectionInterval = setInterval(async () => {
                    if (this.modelLoading || !this.$refs.video || this.$refs.video.readyState < 2) {
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
                    const ctx = this.$refs.overlay.getContext('2d');
                    ctx.clearRect(0, 0, this.$refs.overlay.width, this.$refs.overlay.height);

                    if (!detection) {
                        this.faceDetected = false;
                        this.latestDescriptor = null;
                        this.latestLandmarks = null;
                        this.faceCaptured = false;
                        this.faceDescriptorJson = '';
                        return;
                    }

                    const resized = faceapi.resizeResults(detection, displaySize);
                    faceapi.draw.drawDetections(this.$refs.overlay, resized);

                    this.faceDetected = true;
                    this.latestDescriptor = Array.from(detection.descriptor || []);
                    this.latestLandmarks = detection.landmarks;

                    this.detectBlink();
                    this.detectHeadMovement();
                    this.livenessVerified = this.blinkDetected || this.headMovementDetected;
                }, 400);
            },

            detectBlink() {
                if (!this.latestLandmarks) {
                    return;
                }

                const leftEye = this.latestLandmarks.getLeftEye();
                const rightEye = this.latestLandmarks.getRightEye();
                const leftEAR = this.eyeAspectRatio(leftEye);
                const rightEAR = this.eyeAspectRatio(rightEye);
                const ear = (leftEAR + rightEAR) / 2;

                if (ear < 0.2) {
                    this.lowEarFrames += 1;
                } else {
                    if (this.lowEarFrames >= 2) {
                        this.blinkDetected = true;
                    }
                    this.lowEarFrames = 0;
                }
            },

            detectHeadMovement() {
                if (!this.latestLandmarks) {
                    return;
                }

                const nose = this.latestLandmarks.getNose();
                const noseTip = nose && nose[3] ? nose[3] : null;
                if (!noseTip) {
                    return;
                }

                if (this.noseBaseX === null) {
                    this.noseBaseX = noseTip.x;
                    return;
                }

                if (Math.abs(noseTip.x - this.noseBaseX) > 15) {
                    this.headMovementDetected = true;
                }
            },

            eyeAspectRatio(eyePoints) {
                if (!eyePoints || eyePoints.length < 6) {
                    return 0;
                }

                const dist = (a, b) => Math.hypot(a.x - b.x, a.y - b.y);
                const p1 = eyePoints[0];
                const p2 = eyePoints[1];
                const p3 = eyePoints[2];
                const p4 = eyePoints[3];
                const p5 = eyePoints[4];
                const p6 = eyePoints[5];

                return (dist(p2, p6) + dist(p3, p5)) / (2 * dist(p1, p4));
            },

            captureFace() {
                if (!this.faceDetected || !this.latestDescriptor) {
                    this.faceError = 'Face not detected. Please face the camera.';
                    return;
                }

                if (!this.livenessVerified) {
                    this.faceError = 'Liveness not verified. Please blink or move your head.';
                    return;
                }

                this.faceDescriptorJson = JSON.stringify(this.latestDescriptor);
                this.faceCaptured = true;
                this.faceError = null;
            },

            submitForm(event) {
                if (!this.faceCaptured) {
                    this.faceError = 'Capture your face first before creating account.';
                    return;
                }

                if (!this.livenessVerified) {
                    this.faceError = 'Liveness verification is required.';
                    return;
                }

                event.target.submit();
            },
        };
    }
</script>
@endpush
