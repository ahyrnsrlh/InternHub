<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'check_out_time' => ['nullable', 'date'],
        ];
    }
}
