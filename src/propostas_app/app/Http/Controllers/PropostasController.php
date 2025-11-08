<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropostaRequest;
use App\Http\Requests\UpdatePropostaRequest;
use App\Http\Resources\PropostaResource;
use App\Repositories\PropostaRepository;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Propostas",
 *     description="Endpoints para gestão de propostas de crédito"
 * )
 */
class PropostasController extends Controller
{
    protected $repo;

    public function __construct(PropostaRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @OA\Get(
     *     path="/api/propostas",
     *     summary="Listar todas as propostas",
     *     description="Retorna uma lista de propostas com filtros opcionais por status e nome/CPF.",
     *     tags={"Propostas"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Buscar por nome ou CPF",
     *         required=false,
     *         @OA\Schema(type="string", example="Maria Silva")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"rascunho", "em_analise", "aprovada", "reprovada", "cancelada"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de propostas retornada com sucesso.",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Propostas carregadas com sucesso."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Proposta"))
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/propostas/{id}",
     *     summary="Exibir uma proposta específica",
     *     tags={"Propostas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da proposta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proposta encontrada com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/Proposta")
     *     ),
     *     @OA\Response(response=404, description="Proposta não encontrada.")
     * )
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
     * @OA\Post(
     *     path="/api/propostas/create",
     *     summary="Criar uma nova proposta",
     *     tags={"Propostas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cliente_nome", "cliente_cpf", "cliente_salario", "valor_solicitado", "prazo_meses"},
     *             @OA\Property(property="cliente_nome", type="string", example="Maria da Silva"),
     *             @OA\Property(property="cliente_cpf", type="string", example="12345678900"),
     *             @OA\Property(property="cliente_salario", type="number", example=5000.00),
     *             @OA\Property(property="valor_solicitado", type="number", example=2000.00),
     *             @OA\Property(property="prazo_meses", type="integer", example=24),
     *             @OA\Property(property="observacoes", type="string", example="Cliente deseja crédito pessoal.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Proposta criada com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/Proposta")
     *     ),
     *     @OA\Response(response=422, description="Dados inválidos.")
     * )
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
     * @OA\Patch(
     *     path="/api/propostas/{id}",
     *     summary="Atualizar uma proposta existente",
     *     tags={"Propostas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da proposta a ser atualizada",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="valor_solicitado", type="number", example=3500.00),
     *             @OA\Property(property="prazo_meses", type="integer", example=36),
     *             @OA\Property(property="observacoes", type="string", example="Alteração de prazo solicitada.")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Proposta atualizada com sucesso."),
     *     @OA\Response(response=404, description="Proposta não encontrada.")
     * )
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
     * @OA\Patch(
     *     path="/api/propostas/{id}/status",
     *     summary="Atualizar o status da proposta",
     *     tags={"Propostas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da proposta a ser atualizada",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="aprovada")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status atualizado com sucesso."),
     *     @OA\Response(response=422, description="Status inválido.")
     * )
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
     * @OA\Delete(
     *     path="/api/propostas/{id}",
     *     summary="Excluir uma proposta",
     *     tags={"Propostas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da proposta a ser excluída",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Proposta removida com sucesso."),
     *     @OA\Response(response=404, description="Proposta não encontrada.")
     * )
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
