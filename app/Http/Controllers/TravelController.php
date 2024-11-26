<?php

namespace App\Http\Controllers;

use App\Services\Travel\TravelService;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Travel API",
 *     version="1.0.0",
 *     description="API para gerenciamento de viagens"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api", 
 *     description="Servidor principal"
 * )
 * 
 *  * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Insira o token JWT obtido no login"
 * )
 */
class TravelController extends Controller
{
    private $travelService;

    public function __construct(TravelService $travelService) {
        $this->travelService = $travelService;
    }

    /**
     * @OA\Get(
     *     path="/travel/search",
     *     summary="Listar viagens",
     *     tags={"Viagens"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search_term",
     *         in="query",
     *         description="Termo para busca",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID do usuário",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de viagens retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function search(Request $request) {
        $result = $this->travelService->search($request);
        return $result;
    }

    /**
     * @OA\Get(
     *     path="/travel/{id}",
     *     summary="Obter detalhes da viagem",
     *     tags={"Viagens"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da viagem",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da viagem retornados com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function getById($id) {
        $result = $this->travelService->getById($id);
        return $result;
    }

    /**
     * @OA\Post(
     *     path="/travel/create",
     *     summary="Criar uma nova viagem",
     *     tags={"Viagens"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="description", type="string", description="Descrição da viagem"),
     *                 @OA\Property(property="type", type="string", description="Tipo de viagem"),
     *                 @OA\Property(property="transport", type="string", description="Meio de transporte"),
     *                 @OA\Property(property="total_value", type="number", description="Valor total da viagem"),
     *                 @OA\Property(property="observations", type="string", nullable=true, description="Observações"),
     *                 @OA\Property(property="purchase_date", type="string", format="date", description="Data de compra"),
     *                 @OA\Property(
     *                     property="attachments[]",
     *                     type="array",
     *                     description="Array de arquivos anexos",
     *                     @OA\Items(type="string", format="binary")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Viagem criada com sucesso",
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
        $result = $this->travelService->create($request);
        if ($result['status']) $result['message'] = "Viagem criada com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Post(
     *     path="/travel/{id}?_method=PATCH",
     *     summary="Atualizar uma viagem",
     *     tags={"Viagens"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="_method",
     *         in="query",
     *         description="Método HTTP override para PATCH",
     *         required=true,
     *         @OA\Schema(type="string", default="PATCH")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da viagem",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
    *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="description", type="string", description="Descrição da viagem"),
     *                 @OA\Property(property="type", type="string", description="Tipo de viagem"),
     *                 @OA\Property(property="transport", type="string", description="Meio de transporte"),
     *                 @OA\Property(property="total_value", type="number", description="Valor total da viagem"),
     *                 @OA\Property(property="observations", type="string", nullable=true, description="Observações"),
     *                 @OA\Property(property="purchase_date", type="string", format="date", description="Data de compra"),
     *                 @OA\Property(
     *                     property="attachments[]",
     *                     type="array",
     *                     description="Array de arquivos anexos",
     *                     @OA\Items(type="string", format="binary")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Viagem atualizada com sucesso",
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
        $result = $this->travelService->update($request, $id);
        if ($result['status']) $result['message'] = "Viagem atualizada com sucesso";
        return $this->response($result);
    }

    public function updateSolicitation(Request $request, $id) {
        $result = $this->travelService->updateSolicitation($request, $id);
        if ($result['status']) $result['message'] = "Viagem atualizada com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Delete(
     *     path="/travel/file/{id}",
     *     summary="Deletar arquivo de anexo de viagem",
     *     tags={"Viagens"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do anexo da viagem",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Arquivo deletado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="string", example="nome_do_arquivo.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao deletar o arquivo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Arquivo não encontrado")
     *         )
     *     )
     * )
     */
    public function deleteFile($id)
    {
        $result = $this->travelService->deleteFile($id);
        if ($result['status']) $result['message'] = "Anexo deletado com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Post(
     *     path="/travel/release/{id}",
     *     summary="Criar lançamento para uma viagem",
     *     tags={"Viagens"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da viagem",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lançamento criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lançamento criado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao criar lançamento",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Erro ao criar lançamento no granatum")
     *         )
     *     )
     * )
     */
    public function upRelease($id)
    {
        $result = $this->travelService->upRelease($id);
        if ($result['status']) $result['message'] = "Lançamento criado com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Delete(
     *     path="/travel/{id}",
     *     summary="Deletar uma viagem",
     *     tags={"Viagens"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da viagem",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Viagem deletada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="string", example="Nome da viagem deletada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao deletar viagem",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Viagem não encontrada")
     *         )
     *     )
     * )
     */
    public function delete($id)
    {
        $result = $this->travelService->delete($id);
        if ($result['status']) $result['message'] = "Viagem deletada com sucesso";
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
