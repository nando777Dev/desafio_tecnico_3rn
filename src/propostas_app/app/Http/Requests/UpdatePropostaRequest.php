<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropostaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        // Usado para PATCH - atualizações parciais (status/observacoes)
        return [
            'status' => 'sometimes|string|in:rascunho,em_analise,aprovada,reprovada,cancelada',
            'observacoes' => 'nullable|string',
        ];
    }
}
