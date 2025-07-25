<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');
        
        // Usuário pode atualizar se for o criador ou se a tarefa for atribuída a ele
        return $this->user()->id === $task->created_by 
            || $this->user()->id === $task->assigned_to
            || $this->user()->id === $task->project->user_id;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:todo,in_progress,review,done'],
            'priority' => ['sometimes', 'in:low,medium,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'status.in' => 'Status inválido. Use: todo, in_progress, review ou done.',
            'priority.in' => 'Prioridade inválida. Use: low, medium, high ou urgent.',
            'assigned_to.exists' => 'O usuário selecionado não existe.',
            'due_date.date' => 'A data de vencimento deve ser uma data válida.',
            'estimated_hours.integer' => 'As horas estimadas devem ser um número inteiro.',
            'estimated_hours.min' => 'As horas estimadas devem ser no mínimo 1.',
        ];
    }
}
