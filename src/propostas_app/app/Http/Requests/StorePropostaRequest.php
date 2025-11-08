<?php

namespace App\Http\Requests;

use App\Rules\ValidCpf;
use Illuminate\Foundation\Http\FormRequest;

class StorePropostaRequest extends FormRequest
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
            'cliente_nome' => 'required|string|max:255',
            'cliente_cpf' => ['required',new ValidCpf],
            'cliente_salario' => 'required|numeric|min:1500',
            'valor_solicitado' => 'required|numeric|min:1000|max:50000',
            'prazo_meses' => 'required|integer|min:6|max:60',
            'observacoes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'cliente_salario.min' => 'O salário deve ser no mínimo R$ 1.500,00',
            'valor_solicitado.min' => 'Valor mínimo R$ 1.000,00',
            'valor_solicitado.max' => 'Valor máximo R$ 50.000,00',
            'prazo_meses.min' => 'Prazo mínimo 6 meses',
            'prazo_meses.max' => 'Prazo máximo 60 meses',
        ];
    }
}
