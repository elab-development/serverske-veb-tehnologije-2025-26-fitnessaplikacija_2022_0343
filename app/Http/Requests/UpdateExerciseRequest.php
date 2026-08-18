<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'muscles' => ['nullable', 'array'],
            'muscles.*' => ['string'],
            'equipment' => ['nullable', 'array'],
            'equipment.*' => ['string'],
        ];
    }
}
