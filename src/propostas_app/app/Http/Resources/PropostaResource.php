<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropostaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'cliente_nome' => $this->cliente_nome,
            'cliente_cpf' => $this->cliente_cpf,
            'cliente_salario' => number_format($this->cliente_salario, 2, '.', ''),
            'valor_solicitado' => number_format($this->valor_solicitado, 2, '.', ''),
            'prazo_meses' => $this->prazo_meses,
            'taxa_juros' => number_format($this->taxa_juros, 2, '.', ''),
            'valor_parcela' => number_format($this->valor_parcela, 2, '.', ''),
            'valor_total' => number_format($this->valor_total, 2, '.', ''),
            'margem_disponivel' => number_format($this->margem_disponivel, 2, '.', ''),
            'status' => $this->status,
            'observacoes' => $this->observacoes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
