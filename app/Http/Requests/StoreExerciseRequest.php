<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // autorizacija (uloga) se proverava kroz 'role:admin' middleware na ruti
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'muscles' => ['nullable', 'array'],
            'muscles.*' => ['string'],
            'equipment' => ['nullable', 'array'],
            'equipment.*' => ['string'],
        ];
    }
}
