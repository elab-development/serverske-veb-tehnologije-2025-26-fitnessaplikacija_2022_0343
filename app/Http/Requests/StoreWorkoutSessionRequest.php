<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // svaki ulogovan korisnik sme da napravi svoj trening
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'session_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'duration_min' => ['nullable', 'integer', 'min:0', 'max:600'],
            'status' => ['nullable', 'in:draft,completed'],
            // opciono: odmah dodati stavke prilikom kreiranja treninga
            'items' => ['nullable', 'array'],
            'items.*.exercise_id' => ['required_with:items', 'exists:exercises,id'],
            'items.*.sets' => ['required_with:items', 'integer', 'min:1', 'max:50'],
            'items.*.reps' => ['required_with:items', 'integer', 'min:1', 'max:200'],
            'items.*.weight' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }
}
