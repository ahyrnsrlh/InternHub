<?php

namespace App\Http\Controllers\User;

use App\Models\Attendance;
use App\Models\Location;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AttendanceRequest;
use App\Http\Requests\User\AttendanceCheckInRequest;
use App\Http\Requests\User\AttendanceCheckOutRequest;
use App\Services\FaceService;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly LocationService $locationService,
        private readonly FaceService $faceService,
    ) {
    }

    public function index(): View
    {
        $attendances = Attendance::query()
            ->where('user_id', Auth::id())
            ->latest('check_in_time')
            ->paginate(10);

        $locations = Location::query()
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'latitude', 'longitude']);

        $activeAttendance = Attendance::query()
            ->where('user_id', Auth::id())
            ->whereNull('check_out_time')
            ->latest('check_in_time')
            ->first();

        return view('pages.user.attendance', compact('attendances', 'locations', 'activeAttendance'));
    }

    public function create(): View
    {
        return view('pages.user.attendance');
    }

    public function store(AttendanceRequest $request): RedirectResponse
    {
        return $this->checkInFromPayload($request->validated());
    }

    public function edit(string $attendance): View
    {
        $attendanceRecord = Attendance::query()
            ->where('id', $attendance)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pages.user.attendance', compact('attendanceRecord'));
    }

    public function update(AttendanceRequest $request, string $attendance): RedirectResponse
    {
        $validated = $request->validated();

        $attendanceRecord = Attendance::query()
            ->where('id', $attendance)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $location = Location::query()->findOrFail($validated['location_id']);

        $distance = $this->locationService->calculateDistance(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $location->latitude,
            (float) $location->longitude,
        );

        $status = $distance <= (float) ($validated['allowed_radius_meters'] ?? 100) ? 'valid' : 'invalid';

        $attendanceRecord->update([
            'location_id' => $validated['location_id'],
            'check_in_time' => $validated['check_in_time'] ?? $attendanceRecord->check_in_time,
            'check_out_time' => $validated['check_out_time'] ?? $attendanceRecord->check_out_time,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'status' => $status,
        ]);

        return redirect()->route('user.attendance.index')->with('status', 'Data kehadiran berhasil diperbarui.');
    }

    public function checkIn(AttendanceCheckInRequest $request): RedirectResponse|JsonResponse
    {
        return $this->checkInFromPayload($request->validated(), $request->expectsJson());
    }

    public function checkOut(AttendanceCheckOutRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $userId = (int) $request->user()->id;

            $activeAttendance = Attendance::query()
                ->where('user_id', $userId)
                ->whereNull('check_out_time')
                ->latest('check_in_time')
                ->first();

            if (! $activeAttendance) {
                return $this->respondError('Tidak ada presensi masuk aktif. Silakan lakukan presensi masuk terlebih dahulu.', $request->expectsJson(), 422);
            }

            $validated = $request->validated();

            $activeAttendance->update([
                'check_out_time' => $validated['check_out_time'] ?? now(),
            ]);

            return $this->respondSuccess('Presensi pulang berhasil dicatat.', $request->expectsJson(), [
                'attendance_id' => $activeAttendance->id,
                'check_out_time' => $activeAttendance->check_out_time,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return $this->respondError('Gagal memproses presensi pulang. Silakan coba lagi.', $request->expectsJson());
        }
    }

    public function destroy(string $attendance): RedirectResponse
    {
        $attendanceRecord = Attendance::query()
            ->where('id', $attendance)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $attendanceRecord->delete();

        return redirect()->route('user.attendance.index')->with('status', 'Data kehadiran berhasil dihapus.');
    }

    private function checkInFromPayload(array $validated, bool $expectsJson = false): RedirectResponse|JsonResponse
    {
        try {
            $user = Auth::user();
            $userId = (int) $user?->id;

            $activeAttendanceExists = Attendance::query()
                ->where('user_id', $userId)
                ->whereNull('check_out_time')
                ->exists();

            if ($activeAttendanceExists) {
                return $this->respondError('Anda masih memiliki presensi masuk aktif. Silakan lakukan presensi pulang terlebih dahulu.', $expectsJson, 422);
            }

            if (empty($validated['face_descriptor'])) {
                return $this->respondError('Validasi wajah wajib dilakukan sebelum presensi masuk.', $expectsJson, 422);
            }

            $location = Location::query()->findOrFail($validated['location_id']);

            $gpsValidation = $this->locationService->validateRadius(
                (float) $validated['latitude'],
                (float) $validated['longitude'],
                (float) $location->latitude,
                (float) $location->longitude,
                (float) ($validated['allowed_radius_meters'] ?? 100),
            );

            $faceValidation = $this->faceService->compareFaceDescriptor(
                (string) $validated['face_descriptor'],
                $user?->face_descriptor,
            );

            $status = ($gpsValidation['is_valid'] && $faceValidation['is_match']) ? 'valid' : 'invalid';

            $attendance = DB::transaction(function () use ($validated, $userId, $status) {
                return Attendance::query()->create([
                    'user_id' => $userId,
                    'location_id' => $validated['location_id'],
                    'check_in_time' => $validated['check_in_time'] ?? now(),
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'status' => $status,
                ]);
            });

            return $this->respondSuccess(
                $status === 'valid'
                    ? 'Presensi masuk berhasil dicatat. Verifikasi lokasi dan wajah berhasil.'
                    : 'Presensi masuk tercatat dengan status tidak valid. Validasi GPS atau wajah gagal.',
                $expectsJson,
                [
                    'attendance_id' => $attendance->id,
                    'status' => $attendance->status,
                    'gps_valid' => $gpsValidation['is_valid'],
                    'distance_meters' => round($gpsValidation['distance_meters'], 2),
                    'allowed_radius_meters' => $gpsValidation['allowed_radius_meters'],
                    'face_match' => $faceValidation['is_match'],
                    'face_distance' => $faceValidation['distance'],
                    'face_threshold' => $faceValidation['threshold'],
                    'face_message' => $faceValidation['message'],
                ]
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->respondError('Gagal memproses presensi masuk. Silakan coba lagi.', $expectsJson);
        }
    }

    private function respondSuccess(string $message, bool $expectsJson, array $payload = []): RedirectResponse|JsonResponse
    {
        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $payload,
            ]);
        }

        return redirect()->route('user.attendance.index')->with('status', $message);
    }

    private function respondError(string $message, bool $expectsJson, int $statusCode = 500): RedirectResponse|JsonResponse
    {
        if ($expectsJson) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $statusCode);
        }

        return redirect()->route('user.attendance.index')->withErrors(['attendance' => $message]);
    }
}
