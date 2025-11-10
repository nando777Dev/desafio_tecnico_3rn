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
        return [
            'cliente_nome' => 'sometimes|string|max:255',
            'cliente_cpf' => 'sometimes|string|max:14',
            'cliente_salario' => 'sometimes|numeric|min:0',
            'valor_solicitado' => 'sometimes|numeric|min:0',
            'prazo_meses' => 'sometimes|integer|min:1|max:48'  ,
            'taxa_juros' => 'sometimes|numeric|min:0',
            'valor_parcela' => 'sometimes|numeric|min:0',
            'valor_total' => 'sometimes|numeric|min:0',
            'margem_disponivel' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:rascunho,em_analise,aprovada,reprovada,cancelada',
            'observacoes' => 'nullable|string',
        ];
    }
}
