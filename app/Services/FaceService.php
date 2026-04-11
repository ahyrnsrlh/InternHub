<?php

namespace App\Services;

class FaceService
{
    /**
     * @param  string  $capturedDescriptorJson
     * @param  array<int, float>|null  $storedDescriptor
     * @return array{is_match: bool, distance: float|null, threshold: float, message: string}
     */
    public function compareFaceDescriptor(
        string $capturedDescriptorJson,
        ?array $storedDescriptor,
        float $threshold = 0.6
    ): array {
        if (empty($storedDescriptor)) {
            return [
                'is_match' => false,
                'distance' => null,
                'threshold' => $threshold,
                'message' => 'No registered face descriptor found for this user.',
            ];
        }

        $capturedDescriptor = json_decode($capturedDescriptorJson, true);
        if (! is_array($capturedDescriptor) || empty($capturedDescriptor)) {
            return [
                'is_match' => false,
                'distance' => null,
                'threshold' => $threshold,
                'message' => 'Invalid captured face descriptor payload.',
            ];
        }

        if (count($capturedDescriptor) !== count($storedDescriptor)) {
            return [
                'is_match' => false,
                'distance' => null,
                'threshold' => $threshold,
                'message' => 'Face descriptor dimensions do not match.',
            ];
        }

        $distance = $this->calculateEuclideanDistance($capturedDescriptor, $storedDescriptor);

        return [
            'is_match' => $distance < $threshold,
            'distance' => $distance,
            'threshold' => $threshold,
            'message' => $distance < $threshold
                ? 'Face successfully matched.'
                : 'Face mismatch. Please retry with better lighting and camera angle.',
        ];
    }

    /**
     * @param  array<int, float|int|string>  $vectorA
     * @param  array<int, float|int|string>  $vectorB
     */
    private function calculateEuclideanDistance(array $vectorA, array $vectorB): float
    {
        $sum = 0.0;

        foreach ($vectorA as $index => $value) {
            $delta = (float) $value - (float) $vectorB[$index];
            $sum += $delta ** 2;
        }

        return sqrt($sum);
    }
}
