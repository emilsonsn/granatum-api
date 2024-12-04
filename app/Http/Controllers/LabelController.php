<?php

namespace App\Http\Controllers;

use App\Services\Label\LabelService;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    private $labelService;

    public function __construct(LabelService $labelService) {
        $this->labelService = $labelService;
    }

    public function search(Request $request){
        $result = $this->labelService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->labelService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->labelService->create($request);

        if($result['status']) $result['message'] = "Etiqueta criada com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->labelService->update($request, $id);

        if($result['status']) $result['message'] = "Etiqueta atualizada com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->labelService->delete($id);

        if($result['status']) $result['message'] = "Etiqueta deletada com sucesso";
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