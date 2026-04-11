<?php

namespace App\Services;

class GpsValidationService
{
    private const EARTH_RADIUS_METERS = 6371000;

    public function calculateDistance(
        float $userLatitude,
        float $userLongitude,
        float $targetLatitude,
        float $targetLongitude
    ): float {
        $userLatRad = deg2rad($userLatitude);
        $targetLatRad = deg2rad($targetLatitude);
        $deltaLatRad = deg2rad($targetLatitude - $userLatitude);
        $deltaLngRad = deg2rad($targetLongitude - $userLongitude);

        $a = sin($deltaLatRad / 2) ** 2
            + cos($userLatRad) * cos($targetLatRad) * sin($deltaLngRad / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }

    public function resolveStatus(float $distanceMeters, float $allowedRadiusMeters = 10): string
    {
        return $distanceMeters <= $allowedRadiusMeters ? 'valid' : 'invalid';
    }
}
