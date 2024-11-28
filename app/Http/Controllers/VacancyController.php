<?php

namespace App\Http\Controllers;

use App\Services\Vacancy\VacancyService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Vagas",
 *     description="Endpoints relacionados à gestão de vagas"
 * )
 */
class VacancyController extends Controller
{
    private $vacancyService;

    public function __construct(VacancyService $vacancyService) {
        $this->vacancyService = $vacancyService;
    }

    /**
     * @OA\Get(
     *     path="/vacancy/search",
     *     summary="Listar vagas",
     *     tags={"Vagas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search_term",
     *         in="query",
     *         description="Termo para busca",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Data inicial para filtrar vagas",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Data final para filtrar vagas",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="profession_id",
     *         in="query",
     *         description="Filtrar por ID da profissão",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de vagas retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function search(Request $request) {
        $result = $this->vacancyService->search($request);
        return $result;
    }


    public function cards(Request $request) {
        $result = $this->vacancyService->cards($request);
        return $result;
    }
    
    /**
     * @OA\Get(
     *     path="/vacancy/{id}",
     *     summary="Obter detalhes de uma vaga",
     *     tags={"Vagas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da vaga",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da vaga retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function getById($id) {
        $result = $this->vacancyService->getById($id);
        return $result;
    }

    /**
     * @OA\Post(
     *     path="/vacancy/create",
     *     summary="Criar uma nova vaga",
     *     tags={"Vagas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", description="Título da vaga"),
     *             @OA\Property(property="description", type="string", description="Descrição detalhada da vaga"),
     *             @OA\Property(property="profession_id", type="integer", description="ID da profissão associada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vaga criada com sucesso",
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
        $result = $this->vacancyService->create($request);
        if ($result['status']) $result['message'] = "Vaga criada com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Patch(
     *     path="/vacancy/{id}",
     *     summary="Atualizar uma vaga",
     *     tags={"Vagas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da vaga",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", description="Título da vaga"),
     *             @OA\Property(property="description", type="string", description="Descrição detalhada da vaga"),
     *             @OA\Property(property="profession_id", type="integer", description="ID da profissão associada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vaga atualizada com sucesso",
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
        $result = $this->vacancyService->update($request, $id);
        if ($result['status']) $result['message'] = "Vaga atualizada com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Delete(
     *     path="/vacancy/{id}",
     *     summary="Deletar uma vaga",
     *     tags={"Vagas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da vaga",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vaga deletada com sucesso",
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
        $result = $this->vacancyService->delete($id);
        if ($result['status']) $result['message'] = "Vaga deletada com sucesso";
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
