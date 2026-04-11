<?php

namespace App\Http\Controllers\User;

use App\Models\Attendance;
use App\Models\Location;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AttendanceRequest;
use App\Http\Requests\User\AttendanceCheckInRequest;
use App\Http\Requests\User\AttendanceCheckOutRequest;
use App\Services\GpsValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(private readonly GpsValidationService $gpsValidationService)
    {
    }

    public function index(): View
    {
        $attendances = Attendance::query()
            ->where('user_id', Auth::id())
            ->latest('check_in_time')
            ->paginate(10);

        return view('pages.user.attendance', compact('attendances'));
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

        $distance = $this->gpsValidationService->calculateDistance(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $location->latitude,
            (float) $location->longitude,
        );

        $status = $this->gpsValidationService->resolveStatus(
            $distance,
            (float) ($validated['allowed_radius_meters'] ?? 10),
        );

        $attendanceRecord->update([
            'location_id' => $validated['location_id'],
            'check_in_time' => $validated['check_in_time'] ?? $attendanceRecord->check_in_time,
            'check_out_time' => $validated['check_out_time'] ?? $attendanceRecord->check_out_time,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'status' => $status,
        ]);

        return redirect()->route('user.attendance.index')->with('status', 'Attendance updated successfully.');
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
                return $this->respondError('No active check-in found. Please check in first.', $request->expectsJson(), 422);
            }

            $validated = $request->validated();

            $activeAttendance->update([
                'check_out_time' => $validated['check_out_time'] ?? now(),
            ]);

            return $this->respondSuccess('Check-out recorded successfully.', $request->expectsJson(), [
                'attendance_id' => $activeAttendance->id,
                'check_out_time' => $activeAttendance->check_out_time,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return $this->respondError('Failed to process check-out. Please try again.', $request->expectsJson());
        }
    }

    public function destroy(string $attendance): RedirectResponse
    {
        $attendanceRecord = Attendance::query()
            ->where('id', $attendance)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $attendanceRecord->delete();

        return redirect()->route('user.attendance.index')->with('status', 'Attendance deleted successfully.');
    }

    private function checkInFromPayload(array $validated, bool $expectsJson = false): RedirectResponse|JsonResponse
    {
        try {
            $userId = (int) Auth::id();

            $activeAttendanceExists = Attendance::query()
                ->where('user_id', $userId)
                ->whereNull('check_out_time')
                ->exists();

            if ($activeAttendanceExists) {
                return $this->respondError('You already have an active check-in. Please check out first.', $expectsJson, 422);
            }

            $location = Location::query()->findOrFail($validated['location_id']);

            $distance = $this->gpsValidationService->calculateDistance(
                (float) $validated['latitude'],
                (float) $validated['longitude'],
                (float) $location->latitude,
                (float) $location->longitude,
            );

            $radius = (float) ($validated['allowed_radius_meters'] ?? 10);
            $status = $this->gpsValidationService->resolveStatus($distance, $radius);

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
                    ? 'Check-in recorded. Location verified.'
                    : 'Check-in recorded, but your location is outside the allowed radius.',
                $expectsJson,
                [
                    'attendance_id' => $attendance->id,
                    'status' => $attendance->status,
                    'distance_meters' => round($distance, 2),
                    'allowed_radius_meters' => $radius,
                ]
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->respondError('Failed to process check-in. Please try again.', $expectsJson);
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
