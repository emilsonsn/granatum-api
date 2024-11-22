<?php

namespace App\Http\Controllers;

use App\Services\Partner\PartnerService;
use App\Services\SelectionProcess\SelectionProcessService;
use Illuminate\Http\Request;

class SelectionProcessController extends Controller
{
    private $selectionProcessService;

    public function __construct(SelectionProcessService $selectionProcessService) {
        $this->selectionProcessService = $selectionProcessService;
    }

    public function search(Request $request){
        $result = $this->selectionProcessService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->selectionProcessService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->selectionProcessService->create($request);

        if($result['status']) $result['message'] = "Processo seletivo criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->selectionProcessService->update($request, $id);

        if($result['status']) $result['message'] = "Processo seletivo atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->selectionProcessService->delete($id);

        if($result['status']) $result['message'] = "Processo seletivo deletado com sucesso";
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
