<?php

namespace App\Services;

use App\Http\Resources\PropostaResource;
use App\Models\Proposta;
use Illuminate\Container\Attributes\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class PropostaService
{
    const TAXA_PADRAO = 2.5; // % a.m.

    /**
     * Cria uma proposta validada e calcula parcelas/total/margem.
     *
     * @param array $data
     * @return Proposta
     * @throws ValidationException
     */
    public function create(array $data): array
    {
        try {

            $taxaJuros = $data['taxa_juros'] ?? self::TAXA_PADRAO;

            $data['margem_disponivel'] = $data['cliente_salario'] * 0.3;

            // Fórmula de juros compostos (PMT)
            $taxa = $taxaJuros / 100;
            $prazo = $data['prazo_meses'];
            $valor = $data['valor_solicitado'];

            $data['valor_parcela'] = round(
                $valor * ($taxa * pow(1 + $taxa, $prazo)) / (pow(1 + $taxa, $prazo) - 1),
                2
            );

            $data['valor_total'] = round($data['valor_parcela'] * $prazo, 2);


            if ($data['valor_parcela'] > $data['margem_disponivel']) {
                return [
                    'success' => false,
                    'message' => 'A parcela excede a margem disponível do cliente.',
                    'data' => null,
                    'status' => 422
                ];
            }

            $proposta = Proposta::create($data);

            return [
                'success' => true,
                'message' => 'Proposta criada com sucesso.',
                'data' => new PropostaResource($proposta),
                'status' => 201,
            ];
        } catch (QueryException $e) {

            return [
                'success' => false,
                'message' => 'Erro ao salvar a proposta no banco de dados.',
                'data' => null,
                'error' => $e->getMessage(),
                'status' => 500
            ];
        } catch (Throwable $e) {

            return [
                'success' => false,
                'message' => 'Erro inesperado ao criar a proposta.',
                'data' => null,
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }


    public function updateStatus(Proposta $proposta, string $novoStatus): Proposta
    {
        return DB::transaction(function () use ($proposta, $novoStatus) {
            $atual = $proposta->status;


            if (in_array($atual, ['aprovada','reprovada']) && $novoStatus === 'em_analise') {
                throw ValidationException::withMessages(['status' => 'Não é permitido voltar de aprovada/reprovada para em_analise.']);
            }

            // cancelada pode vir de qualquer status
            if ($novoStatus === 'cancelada') {
                $proposta->status = 'cancelada';
                $proposta->save();
                return $proposta;
            }

            // rascunho -> em_analise -> aprovada/reprovada
            if ($novoStatus === 'em_analise') {
                // garantir que não haja outra em_analise para o mesmo CPF
                $exists = Proposta::where('cliente_cpf', $proposta->cliente_cpf)
                    ->where('status', 'em_analise')
                    ->where('id', '!=', $proposta->id)
                    ->exists();

                if ($exists) {
                    throw ValidationException::withMessages(['status' => 'Já existe outra proposta em análise para este cliente.']);
                }
            }

            $proposta->status = $novoStatus;
            $proposta->save();

            return $proposta;
        });
    }

    // Calcula parcela com juros compostos (fórmula paga mensal)
    // r em porcentagem (ex: 2.5) => converte para 0.025
    public function calcularParcela(float $principal, float $taxaPercent, int $n): float
    {
        $r = $taxaPercent / 100;
        if ($r == 0) {
            return $principal / $n;
        }
        $parcela = $principal * ($r / (1 - pow(1 + $r, -$n)));
        return $parcela;
    }

    public function calcularMargem(float $salario): float
    {
        return round($salario * 0.30, 2);
    }

    protected function normalizeCpf(string $cpf): string
    {
        return preg_replace('/\D/', '', $cpf);
    }
}
