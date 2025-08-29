<?php

namespace App\Http\Requests;

use App\Enums\TeamRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('team_members')->where(function ($query) {
                    return $query->where('team_id', $this->route('team')->id);
                }),
            ],
            'role' => ['required', 'in:owner,admin,member,viewer'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'O usuário é obrigatório.',
            'user_id.exists' => 'O usuário selecionado não existe.',
            'user_id.unique' => 'Este usuário já é membro da equipe.',
            'role.required' => 'A função é obrigatória.',
            'role.in' => 'Função inválida. Use: owner, admin, member ou viewer.',
        ];
    }
}
