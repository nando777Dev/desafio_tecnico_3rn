<?php

namespace App\Repositories;

use App\Models\Proposta;
use Illuminate\Support\Facades\DB;

class PropostaRepository
{
    public function getAll($filters = [])
    {
        $query = Proposta::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('cliente_nome', 'like', "%{$filters['search']}%")
                    ->orWhere('cliente_cpf', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('created_at')->paginate(10);
    }

    public function find($id)
    {
        return Proposta::findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = $data['status'] ?? 'rascunho';
            return Proposta::create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $proposta = Proposta::findOrFail($id);
            $proposta->update($data);
            return $proposta;
        });
    }

    public function updateStatus($id, string $novoStatus)
    {
        return DB::transaction(function () use ($id, $novoStatus) {
            $proposta = Proposta::findOrFail($id);

            $statusPermitidos = ['rascunho', 'em_analise', 'aprovada', 'reprovada', 'cancelada'];

            if (!in_array($novoStatus, $statusPermitidos)) {
                abort(422, 'Status inválido.');
            }

            // Evita voltar de aprovada/reprovada para em análise
            if (in_array($proposta->status, ['aprovada', 'reprovada']) && $novoStatus === 'em_analise') {
                abort(422, 'Não é possível retornar para "em análise".');
            }

            $proposta->update(['status' => $novoStatus]);
            return $proposta;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $proposta = Proposta::findOrFail($id);
            return $proposta->delete();
        });
    }
}
