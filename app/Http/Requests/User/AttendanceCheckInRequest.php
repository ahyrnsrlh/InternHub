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
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'face_descriptor' => ['required', 'string'],
            'check_in_time' => ['nullable', 'date'],
            'allowed_radius_meters' => ['nullable', 'numeric', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required' => 'Silakan pilih lokasi sebelum presensi masuk.',
            'location_id.exists' => 'Lokasi yang dipilih tidak valid.',
            'latitude.required' => 'Latitude wajib diisi untuk validasi GPS.',
            'longitude.required' => 'Longitude wajib diisi untuk validasi GPS.',
            'face_descriptor.required' => 'Data validasi wajah wajib dilengkapi sebelum presensi masuk.',
        ];
    }
}
