<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $comment = $this->route('comment');
        
        // Apenas o autor pode editar
        return $this->user()->id === $comment->user_id;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'O conteúdo do comentário é obrigatório.',
            'content.max' => 'O comentário não pode ter mais de 5000 caracteres.',
        ];
    }
}
