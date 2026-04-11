<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LocationTrackingRequest;
use App\Models\LocationLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class LocationTrackingController extends Controller
{
    public function store(LocationTrackingRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user?->location_tracking_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Pelacakan lokasi belum diaktifkan.',
            ], 403);
        }

        $startHour = (int) config('internhub.tracking.start_hour', 8);
        $endHour = (int) config('internhub.tracking.end_hour', 18);
        $now = now();

        if ($now->hour < $startHour || $now->hour >= $endHour) {
            return response()->json([
                'success' => false,
                'message' => 'Pelacakan hanya aktif pada jam kerja.',
            ], 422);
        }

        $validated = $request->validated();
        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];

        if (abs($latitude) < 0.000001 && abs($longitude) < 0.000001) {
            return response()->json([
                'success' => false,
                'message' => 'Koordinat tidak valid.',
            ], 422);
        }

        $loggedAt = isset($validated['logged_at'])
            ? Carbon::parse($validated['logged_at'])
            : $now;

        LocationLog::query()->create([
            'user_id' => $user->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'logged_at' => $loggedAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil direkam.',
        ]);
    }
}
