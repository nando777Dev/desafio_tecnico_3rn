<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropostaRequest;
use App\Http\Requests\UpdatePropostaRequest;
use App\Http\Resources\PropostaResource;
use App\Repositories\PropostaRepository;
use Illuminate\Http\Request;

class PropostasController extends Controller
{
    protected $repo;

    public function __construct(PropostaRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Lista todas as propostas com filtros e paginação
     */
    public function index(Request $request)
    {
        $data = $this->repo->getAll($request->only(['search', 'status']));

        return PropostaResource::collection($data)
            ->additional([
                'success' => true,
                'message' => 'Propostas carregadas com sucesso.'
            ]);
    }

    /**
     * Exibe uma proposta específica
     */
    public function show($id)
    {
        $proposta = $this->repo->find($id);

        return (new PropostaResource($proposta))
            ->additional([
                'success' => true,
                'message' => 'Proposta encontrada.'
            ]);
    }

    /**
     * Cria uma nova proposta
     */
    public function store(StorePropostaRequest $request)
    {
        $nova = $this->repo->create($request->validated());

        return (new PropostaResource($nova))
            ->additional([
                'success' => true,
                'message' => 'Proposta criada com sucesso!'
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Atualiza os dados da proposta
     */
    public function update(UpdatePropostaRequest $request, $id)
    {
        $atualizada = $this->repo->update($id, $request->validated());

        return (new PropostaResource($atualizada))
            ->additional([
                'success' => true,
                'message' => 'Proposta atualizada com sucesso!'
            ]);
    }

    /**
     * Atualiza o status da proposta
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string'
        ]);

        $nova = $this->repo->updateStatus($id, $validated['status']);

        return (new PropostaResource($nova))
            ->additional([
                'success' => true,
                'message' => 'Status atualizado com sucesso!'
            ]);
    }

    /**
     * Exclui uma proposta
     */
    public function destroy($id)
    {
        $this->repo->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Proposta removida com sucesso.'
        ]);
    }
}
