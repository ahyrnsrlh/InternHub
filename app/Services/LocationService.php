<?php

namespace App\Services;

class LocationService
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

    /**
     * @return array{is_valid: bool, distance_meters: float, allowed_radius_meters: float}
     */
    public function validateRadius(
        float $userLatitude,
        float $userLongitude,
        float $targetLatitude,
        float $targetLongitude,
        float $allowedRadiusMeters = 100
    ): array {
        $distance = $this->calculateDistance(
            $userLatitude,
            $userLongitude,
            $targetLatitude,
            $targetLongitude,
        );

        return [
            'is_valid' => $distance <= $allowedRadiusMeters,
            'distance_meters' => $distance,
            'allowed_radius_meters' => $allowedRadiusMeters,
        ];
    }
}
