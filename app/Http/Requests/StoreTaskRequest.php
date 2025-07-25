<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:todo,in_progress,review,done'],
            'priority' => ['sometimes', 'in:low,medium,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date', 'after:today'],
            'estimated_hours' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'O projeto é obrigatório.',
            'project_id.exists' => 'O projeto selecionado não existe.',
            'title.required' => 'O título da tarefa é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'status.in' => 'Status inválido. Use: todo, in_progress, review ou done.',
            'priority.in' => 'Prioridade inválida. Use: low, medium, high ou urgent.',
            'assigned_to.exists' => 'O usuário selecionado não existe.',
            'due_date.date' => 'A data de vencimento deve ser uma data válida.',
            'due_date.after' => 'A data de vencimento deve ser posterior a hoje.',
            'estimated_hours.integer' => 'As horas estimadas devem ser um número inteiro.',
            'estimated_hours.min' => 'As horas estimadas devem ser no mínimo 1.',
        ];
    }
}
