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
            'check_in_time' => ['nullable', 'date'],
            'allowed_radius_meters' => ['nullable', 'numeric', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required' => 'Please select a location before check-in.',
            'location_id.exists' => 'Selected location is not valid.',
            'latitude.required' => 'Latitude is required for GPS validation.',
            'longitude.required' => 'Longitude is required for GPS validation.',
        ];
    }
}
