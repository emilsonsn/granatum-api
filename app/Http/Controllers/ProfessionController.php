<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Services\Profession\ProfessionService;

/**
 * @OA\Tag(
 *     name="Profissão",
 *     description="Endpoints relacionados à profissão da parte de RH"
 * )
 */

class ProfessionController extends Controller
{
    private $professionService;

    public function __construct(ProfessionService $professionService) {
        $this->professionService = $professionService;
    }

    /**
     * @OA\Get(
     *     path="/profession/search",
     *     summary="Listar profissões",
     *     tags={"Profissões"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search_term",
     *         in="query",
     *         description="Termo para busca",
     *         required=false,
     *         @OA\Schema(type="string")
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
     *         description="Lista de profissões retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function search(Request $request) {
        $result = $this->professionService->search($request);
        return $result;
    }

    public function cards(Request $request) {
        $result = $this->professionService->cards($request);
        return $result;
    }


    /**
     * @OA\Get(
     *     path="/profession/{id}",
     *     summary="Obter detalhes de uma profissão",
     *     tags={"Profissões"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da profissão",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da profissão retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function getById($id) {
        $result = $this->professionService->getById($id);
        return $result;
    }

    /**
     * @OA\Post(
     *     path="/profession/create",
     *     summary="Criar uma nova profissão",
     *     tags={"Profissões"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", description="Título da profissão"),
     *             @OA\Property(property="description", type="string", description="Descrição da profissão")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Profissão criada com sucesso",
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
        $result = $this->professionService->create($request);
        if ($result['status']) $result['message'] = "Profissão criada com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Patch(
     *     path="/profession/{id}",
     *     summary="Atualizar uma profissão",
     *     tags={"Profissões"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da profissão",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", description="Título da profissão"),
     *             @OA\Property(property="description", type="string", description="Descrição da profissão")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profissão atualizada com sucesso",
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
        $result = $this->professionService->update($request, $id);
        if ($result['status']) $result['message'] = "Profissão atualizada com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Delete(
     *     path="/profession/{id}",
     *     summary="Deletar uma profissão",
     *     tags={"Profissões"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da profissão",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profissão deletada com sucesso",
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
        $result = $this->professionService->delete($id);
        if ($result['status']) $result['message'] = "Profissão deletada com sucesso";
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
