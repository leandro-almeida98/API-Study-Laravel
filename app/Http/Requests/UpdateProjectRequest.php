<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('project')->user_id;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,completed,archived'],
            'deadline' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'status.in' => 'Status inválido. Use: active, completed ou archived.',
            'deadline.date' => 'A data de entrega deve ser uma data válida.',
        ];
    }
}