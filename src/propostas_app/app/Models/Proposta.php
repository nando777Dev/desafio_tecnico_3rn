<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'propostas';

    protected $fillable = [
        'cliente_nome',
        'cliente_cpf',
        'cliente_salario',
        'valor_solicitado',
        'prazo_meses',
        'taxa_juros',
        'valor_parcela',
        'valor_total',
        'margem_disponivel',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'cliente_salario' => 'decimal:2',
        'valor_solicitado' => 'decimal:2',
        'taxa_juros' => 'decimal:4',
        'valor_parcela' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'margem_disponivel' => 'decimal:2',
    ];
}
