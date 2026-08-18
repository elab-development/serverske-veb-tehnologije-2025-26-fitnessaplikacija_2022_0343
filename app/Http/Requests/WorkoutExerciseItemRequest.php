<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Koristi se i za dodavanje i za izmenu stavke (vežbe) unutar treninga.
class WorkoutExerciseItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->method();

        return [
            'exercise_id' => [$method === 'POST' ? 'required' : 'sometimes', 'exists:exercises,id'],
            'sets' => [$method === 'POST' ? 'required' : 'sometimes', 'integer', 'min:1', 'max:50'],
            'reps' => [$method === 'POST' ? 'required' : 'sometimes', 'integer', 'min:1', 'max:200'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
