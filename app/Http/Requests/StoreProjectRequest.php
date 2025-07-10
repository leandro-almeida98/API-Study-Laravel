<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,completed,archived'],
            'deadline' => ['nullable', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do projeto é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'status.in' => 'Status inválido. Use: active, completed ou archived.',
            'deadline.date' => 'A data de entrega deve ser uma data válida.',
            'deadline.after' => 'A data de entrega deve ser posterior a hoje.',
        ];
    }
}