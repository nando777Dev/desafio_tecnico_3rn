<?php

/**
 * @OA\Schema(
 *     schema="Proposta",
 *     type="object",
 *     title="Proposta",
 *     required={"cliente_nome", "cliente_cpf", "valor_solicitado"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="cliente_nome", type="string", example="Maria da Silva"),
 *     @OA\Property(property="cliente_cpf", type="string", example="12345678900"),
 *     @OA\Property(property="cliente_salario", type="number", example=5000.00),
 *     @OA\Property(property="valor_solicitado", type="number", example=2000.00),
 *     @OA\Property(property="prazo_meses", type="integer", example=24),
 *     @OA\Property(property="taxa_juros", type="number", example=2.5),
 *     @OA\Property(property="valor_parcela", type="number", example=150.00),
 *     @OA\Property(property="valor_total", type="number", example=3600.00),
 *     @OA\Property(property="status", type="string", example="em_analise"),
 *     @OA\Property(property="observacoes", type="string", example="Cliente com bom histórico de crédito."),
 *     @OA\Property(property="created_at", type="string", example="2025-11-08 14:00:00"),
 *     @OA\Property(property="updated_at", type="string", example="2025-11-08 14:30:00")
 * )
 */
