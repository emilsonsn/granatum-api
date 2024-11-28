<?php

namespace App\Http\Controllers;

use App\Services\SelectionProcess\SelectionProcessService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Processos Seletivos",
 *     description="Endpoints relacionados aos processos seletivos"
 * )
 */
class SelectionProcessController extends Controller
{
    private $selectionProcessService;

    public function __construct(SelectionProcessService $selectionProcessService) {
        $this->selectionProcessService = $selectionProcessService;
    }

    /**
     * @OA\Get(
     *     path="/selection-process/search",
     *     summary="Listar processos seletivos",
     *     tags={"Processos Seletivos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search_term",
     *         in="query",
     *         description="Termo para busca",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filtrar por processos seletivos ativos",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="take",
     *         in="query",
     *         description="Quantidade de itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de processos seletivos retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function search(Request $request) {
        $result = $this->selectionProcessService->search($request);
        return $result;
    }

    /**
     * @OA\Get(
     *     path="/selection-process/{id}",
     *     summary="Obter detalhes de um processo seletivo",
     *     tags={"Processos Seletivos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do processo seletivo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do processo seletivo retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function getById($id) {
        $result = $this->selectionProcessService->getById($id);
        return $result;
    }
    
    public function cards() {
        $result = $this->selectionProcessService->cards();
        return $result;
    }
    /**
     * @OA\Post(
     *     path="/selection-process/create",
     *     summary="Criar um novo processo seletivo",
     *     tags={"Processos Seletivos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", description="Título do processo seletivo"),
     *             @OA\Property(property="total_candidates", type="integer", description="Número total de candidatos"),
     *             @OA\Property(property="available_vacancies", type="integer", nullable=true, description="Número de vagas disponíveis"),
     *             @OA\Property(property="vacancy_id", type="integer", description="ID da vaga associada"),
     *             @OA\Property(property="is_active", type="boolean", nullable=true, description="Status do processo seletivo (ativo ou inativo)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Processo seletivo criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function create(Request $request) {
        $result = $this->selectionProcessService->create($request);
        if ($result['status']) $result['message'] = "Processo seletivo criado com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Patch(
     *     path="/selection-process/{id}",
     *     summary="Atualizar um processo seletivo",
     *     tags={"Processos Seletivos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do processo seletivo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", description="Título do processo seletivo"),
     *             @OA\Property(property="total_candidates", type="integer", description="Número total de candidatos"),
     *             @OA\Property(property="available_vacancies", type="integer", nullable=true, description="Número de vagas disponíveis"),
     *             @OA\Property(property="vacancy_id", type="integer", description="ID da vaga associada"),
     *             @OA\Property(property="is_active", type="boolean", nullable=true, description="Status do processo seletivo (ativo ou inativo)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Processo seletivo atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id) {
        $result = $this->selectionProcessService->update($request, $id);
        if ($result['status']) $result['message'] = "Processo seletivo atualizado com sucesso";
        return $this->response($result);
    }

    public function updateStatus(Request $request) {
        $result = $this->selectionProcessService->updateStatus($request);
        if ($result['status']) $result['message'] = "Candidato movido com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Delete(
     *     path="/selection-process/{id}",
     *     summary="Deletar um processo seletivo",
     *     tags={"Processos Seletivos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do processo seletivo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Processo seletivo deletado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="string")
     *         )
     *     )
     * )
     */
    public function delete($id) {
        $result = $this->selectionProcessService->delete($id);
        if ($result['status']) $result['message'] = "Processo seletivo deletado com sucesso";
        return $this->response($result);
    }

    private function response($result) {
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ], $result['statusCode'] ?? 200);
    }
}
