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
                'message' => 'Deskriptor wajah pengguna belum terdaftar.',
            ];
        }

        $capturedDescriptor = json_decode($capturedDescriptorJson, true);
        if (! is_array($capturedDescriptor) || empty($capturedDescriptor)) {
            return [
                'is_match' => false,
                'distance' => null,
                'threshold' => $threshold,
                'message' => 'Data deskriptor wajah yang ditangkap tidak valid.',
            ];
        }

        if (count($capturedDescriptor) !== count($storedDescriptor)) {
            return [
                'is_match' => false,
                'distance' => null,
                'threshold' => $threshold,
                'message' => 'Dimensi deskriptor wajah tidak sesuai.',
            ];
        }

        $distance = $this->calculateEuclideanDistance($capturedDescriptor, $storedDescriptor);

        return [
            'is_match' => $distance < $threshold,
            'distance' => $distance,
            'threshold' => $threshold,
            'message' => $distance < $threshold
                ? 'Wajah berhasil dicocokkan.'
                : 'Wajah tidak cocok. Silakan ulangi dengan pencahayaan dan sudut kamera yang lebih baik.',
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
