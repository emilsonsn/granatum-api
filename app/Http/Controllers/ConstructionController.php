<?php

namespace App\Http\Controllers;

use App\Services\Construction\ConstructionService;
use Illuminate\Http\Request;

class ConstructionController extends Controller
{
    private $constructionService;

    public function __construct(ConstructionService $constructionService) {
        $this->constructionService = $constructionService;
    }

    public function search(Request $request){
        $result = $this->constructionService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->constructionService->create($request);

        if($result['status']) $result['message'] = "Obra criada com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->constructionService->update($request, $id);

        if($result['status']) $result['message'] = "Obra atualizada com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->constructionService->delete($id);

        if($result['status']) $result['message'] = "Obra Deletada com sucesso";
        return $this->response($result);
    }

    private function response($result){
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ]);
    }
}
