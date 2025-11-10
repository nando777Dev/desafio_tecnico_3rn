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
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Propostas carregadas com sucesso."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="cliente_nome", type="string", example="Maria da Silva"),
     *                     @OA\Property(property="cliente_cpf", type="string", example="12345678900"),
     *                     @OA\Property(property="cliente_salario", type="number", example=5000.00),
     *                     @OA\Property(property="valor_solicitado", type="number", example=2000.00),
     *                     @OA\Property(property="prazo_meses", type="integer", example=24),
     *                     @OA\Property(property="taxa_juros", type="number", example=2.5),
     *                     @OA\Property(property="valor_parcela", type="number", example=150.00),
     *                     @OA\Property(property="valor_total", type="number", example=3600.00),
     *                     @OA\Property(property="status", type="string", example="em_analise"),
     *                     @OA\Property(property="observacoes", type="string", example="Cliente com bom histórico de crédito."),
     *                     @OA\Property(property="created_at", type="string", example="2025-11-08 14:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2025-11-08 14:30:00")
     *                 )
     *             )
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
     *     description="Retorna os detalhes de uma proposta específica pelo ID.",
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
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Proposta encontrada com sucesso."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="cliente_nome", type="string", example="Maria da Silva"),
     *                 @OA\Property(property="cliente_cpf", type="string", example="12345678900"),
     *                 @OA\Property(property="cliente_salario", type="number", example=5000.00),
     *                 @OA\Property(property="valor_solicitado", type="number", example=2000.00),
     *                 @OA\Property(property="prazo_meses", type="integer", example=24),
     *                 @OA\Property(property="taxa_juros", type="number", example=2.5),
     *                 @OA\Property(property="valor_parcela", type="number", example=150.00),
     *                 @OA\Property(property="valor_total", type="number", example=3600.00),
     *                 @OA\Property(property="status", type="string", example="em_analise"),
     *                 @OA\Property(property="observacoes", type="string", example="Cliente com bom histórico de crédito."),
     *                 @OA\Property(property="created_at", type="string", example="2025-11-08 14:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-11-08 14:30:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Proposta não encontrada.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Proposta não encontrada.")
     *         )
     *     )
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
     *     description="Cria uma nova proposta de crédito com os dados informados.",
     *     tags={"Propostas"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados necessários para criar uma proposta",
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
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Proposta criada com sucesso."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="cliente_nome", type="string", example="Maria da Silva"),
     *                 @OA\Property(property="cliente_cpf", type="string", example="12345678900"),
     *                 @OA\Property(property="cliente_salario", type="number", example=5000.00),
     *                 @OA\Property(property="valor_solicitado", type="number", example=2000.00),
     *                 @OA\Property(property="prazo_meses", type="integer", example=24),
     *                 @OA\Property(property="taxa_juros", type="number", example=2.5),
     *                 @OA\Property(property="valor_parcela", type="number", example=150.00),
     *                 @OA\Property(property="valor_total", type="number", example=3600.00),
     *                 @OA\Property(property="status", type="string", example="em_analise"),
     *                 @OA\Property(property="observacoes", type="string", example="Cliente deseja crédito pessoal."),
     *                 @OA\Property(property="created_at", type="string", example="2025-11-08 14:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-11-08 14:30:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação nos dados enviados.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Os dados fornecidos são inválidos."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="cliente_cpf", type="array",
     *                     @OA\Items(type="string", example="O campo CPF é obrigatório.")
     *                 )
     *             )
     *         )
     *     )
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
     *     description="Atualiza os dados de uma proposta existente com base no ID informado.",
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
     *         description="Campos que podem ser atualizados na proposta",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="valor_solicitado", type="number", example=3500.00),
     *             @OA\Property(property="prazo_meses", type="integer", example=36),
     *             @OA\Property(property="observacoes", type="string", example="Alteração de prazo solicitada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proposta atualizada com sucesso.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Proposta atualizada com sucesso."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="cliente_nome", type="string", example="Maria da Silva"),
     *                 @OA\Property(property="cliente_cpf", type="string", example="12345678900"),
     *                 @OA\Property(property="cliente_salario", type="number", example=5000.00),
     *                 @OA\Property(property="valor_solicitado", type="number", example=3500.00),
     *                 @OA\Property(property="prazo_meses", type="integer", example=36),
     *                 @OA\Property(property="taxa_juros", type="number", example=2.5),
     *                 @OA\Property(property="valor_parcela", type="number", example=180.00),
     *                 @OA\Property(property="valor_total", type="number", example=6480.00),
     *                 @OA\Property(property="status", type="string", example="em_analise"),
     *                 @OA\Property(property="observacoes", type="string", example="Alteração de prazo solicitada."),
     *                 @OA\Property(property="created_at", type="string", example="2025-11-08 14:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-11-10 10:30:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Proposta não encontrada.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Proposta não encontrada.")
     *         )
     *     )
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
     *     summary="Atualizar uma proposta existente",
     *     description="Atualiza os dados de uma proposta existente com base no ID informado.",
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
     *         description="Campos que podem ser atualizados na proposta",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="valor_solicitado", type="number", example=3500.00),
     *             @OA\Property(property="prazo_meses", type="integer", example=36),
     *             @OA\Property(property="observacoes", type="string", example="Alteração de prazo solicitada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proposta atualizada com sucesso.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Proposta atualizada com sucesso."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="cliente_nome", type="string", example="Maria da Silva"),
     *                 @OA\Property(property="cliente_cpf", type="string", example="12345678900"),
     *                 @OA\Property(property="cliente_salario", type="number", example=5000.00),
     *                 @OA\Property(property="valor_solicitado", type="number", example=3500.00),
     *                 @OA\Property(property="prazo_meses", type="integer", example=36),
     *                 @OA\Property(property="taxa_juros", type="number", example=2.5),
     *                 @OA\Property(property="valor_parcela", type="number", example=180.00),
     *                 @OA\Property(property="valor_total", type="number", example=6480.00),
     *                 @OA\Property(property="status", type="string", example="em_analise"),
     *                 @OA\Property(property="observacoes", type="string", example="Alteração de prazo solicitada."),
     *                 @OA\Property(property="created_at", type="string", example="2025-11-08 14:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-11-10 10:30:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Proposta não encontrada.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Proposta não encontrada.")
     *         )
     *     )
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
