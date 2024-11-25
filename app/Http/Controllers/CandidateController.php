<?php

namespace App\Http\Controllers;

use App\Services\Candidate\CandidateService;
use App\Services\Client\ClientService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Candidatos",
 *     description="Endpoints relacionados à gestão de candidatos"
 * )
 */
class CandidateController extends Controller
{
    private $candidateService;

    public function __construct(CandidateService $candidateService) {
        $this->candidateService = $candidateService;
    }

    /**
     * @OA\Get(
     *     path="/candidate/search",
     *     summary="Listar candidatos",
     *     tags={"Candidatos"},
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
     *         description="Status ativo do candidato",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="profession_id",
     *         in="query",
     *         description="ID da profissão do candidato",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de candidatos retornada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function search(Request $request){
        $result = $this->candidateService->search($request);

        return $result;
    }

    /**
     * @OA\Post(
     *     path="/candidate/create",
     *     summary="Criar um novo candidato",
     *     tags={"Candidatos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="name", type="string", description="Nome do candidato"),
     *                 @OA\Property(property="surname", type="string", description="Sobrenome do candidato"),
     *                 @OA\Property(property="email", type="string", description="E-mail do candidato"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do candidato"),
     *                 @OA\Property(property="phone", type="string", description="Telefone do candidato"),
     *                 @OA\Property(property="is_active", type="boolean", description="Status ativo do candidato"),
     *                 @OA\Property(property="profession_id", type="integer", description="ID da profissão do candidato"),
     *                 @OA\Property(
     *                     property="attachments[]",
     *                     type="array",
     *                     description="Anexos do candidato",
     *                     @OA\Items(type="string", format="binary")
     *                 ),
     *                 @OA\Property(property="processes",example="1,2,3", type="string", description="Processos de seleção relacionados")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Candidato criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function create(Request $request){
        $result = $this->candidateService->create($request);

        if($result['status']) $result['message'] = "Candidato criado com sucesso";
        return $this->response($result);
    }

    /**
     * @OA\Post(
     *     path="/candidate/{id}?_method=PATCH",
     *     summary="Atualizar dados de um candidato",
     *     tags={"Candidatos"},
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
     *         description="ID do candidato",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="name", type="string", description="Nome do candidato"),
     *                 @OA\Property(property="surname", type="string", description="Sobrenome do candidato"),
     *                 @OA\Property(property="email", type="string", description="E-mail do candidato"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do candidato"),
     *                 @OA\Property(property="phone", type="string", description="Telefone do candidato"),
     *                 @OA\Property(property="is_active", type="boolean", description="Status ativo do candidato"),
     *                 @OA\Property(property="profession_id", type="integer", description="ID da profissão do candidato"),
     *                 @OA\Property(
     *                     property="attachments[]",
     *                     type="array",
     *                     description="Anexos do candidato",
     *                     @OA\Items(type="string", format="binary")
     *                 ),
     *          @OA\Property(property="processes",example="1,2,3", type="string", description="Processos de seleção relacionados")     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Candidato atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id){
        $result = $this->candidateService->update($request, $id);

        if($result['status']) $result['message'] = "Candidato atualizado com sucesso";
        return $this->response($result);
    }


    /**
     * @OA\Delete(
     *     path="/candidate/{id}",
     *     summary="Deletar um candidato",
     *     tags={"Candidatos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do candidato",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Candidato deletado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="string", example="Nome do candidato deletado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Candidato não encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="error", type="string", example="Candidato não encontrado")
     *         )
     *     )
     * )
     */
    public function delete($id){
        $result = $this->candidateService->delete($id);

        if($result['status']) $result['message'] = "Candidato Deletado com sucesso";
        return $this->response($result);
    }

    private function response($result){
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ], $result['statusCode'] ?? 200);
    }
}
