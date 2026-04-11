<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'face_descriptor' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $decoded = json_decode((string) $value, true);

                    if (! is_array($decoded) || count($decoded) !== 128) {
                        $fail('Data validasi wajah tidak valid.');
                        return;
                    }

                    foreach ($decoded as $item) {
                        if (! is_numeric($item)) {
                            $fail('Data validasi wajah harus berupa angka.');
                            return;
                        }
                    }
                },
            ],
            'check_in_time' => ['nullable', 'date', 'before_or_equal:now'],
            'allowed_radius_meters' => ['nullable', 'numeric', 'min:1', 'max:1000'],
            'plan_note' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.exists' => 'Lokasi yang dipilih tidak valid.',
            'latitude.required' => 'Latitude wajib diisi untuk validasi GPS.',
            'longitude.required' => 'Longitude wajib diisi untuk validasi GPS.',
            'face_descriptor.required' => 'Data validasi wajah wajib dilengkapi sebelum presensi masuk.',
            'plan_note.required' => 'Rencana kegiatan untuk presensi masuk wajib diisi.',
        ];
    }
}
