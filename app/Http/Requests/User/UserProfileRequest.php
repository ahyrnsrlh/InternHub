<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profileId = $this->route('profile');
        $profileId = is_object($profileId) ? $profileId->id : $profileId;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($profileId ?: $this->user()?->id)],
            'npm' => ['required', 'string', 'max:50'],
            'institution_name' => ['required', 'string', 'max:255'],
            'academic_supervisor' => ['required', 'string', 'max:255'],
            'field_supervisor' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive,on_leave'],
            'location_tracking_enabled' => ['nullable', 'boolean'],
        ];
    }
}
