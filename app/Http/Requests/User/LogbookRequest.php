<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'log_date' => ['required', 'date'],
            'department' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'deliverable' => ['nullable', 'string', 'max:255'],
            'hours' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'status' => ['nullable', 'in:pending,approved,revision_required'],
        ];
    }
}
