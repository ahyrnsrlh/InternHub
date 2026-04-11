<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LocationTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'logged_at' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }
}
