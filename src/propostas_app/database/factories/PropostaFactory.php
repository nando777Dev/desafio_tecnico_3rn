<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposta>
 */
class PropostaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $salario = fake()->randomFloat(2, 1500, 10000);
        $prazo = fake()->numberBetween(6, 60);
        $valor = fake()->randomFloat(2, 1000, 50000);

        return [
            'cliente_nome' => fake()->name(),
            'cliente_cpf' => fake()->unique()->numerify('###########'),
            'cliente_salario' => $salario,
            'valor_solicitado' => $valor,
            'prazo_meses' => $prazo,
            'taxa_juros' => 2.5,
            'valor_parcela' => 500,
            'valor_total' => 500 * $prazo,
            'margem_disponivel' => $salario * 0.3,
            'status' => 'rascunho',
        ];
    }
}
