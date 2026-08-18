<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkoutSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // vlasništvo (owner/admin) proverava kontroler
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'session_date' => ['sometimes', 'required', 'date'],
            'notes' => ['nullable', 'string'],
            'duration_min' => ['nullable', 'integer', 'min:0', 'max:600'],
            'status' => ['sometimes', 'in:draft,completed'],
        ];
    }
}
