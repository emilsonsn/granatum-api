<?php

namespace App\Http\Controllers;

use App\Services\FunnelStep\FunnelStepService;
use Illuminate\Http\Request;

class FunnelStepController extends Controller
{
    private $funnelStepService;

    public function __construct(FunnelStepService $funnelStepService) {
        $this->funnelStepService = $funnelStepService;
    }

    public function search(Request $request){
        $result = $this->funnelStepService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->funnelStepService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->funnelStepService->create($request);

        if($result['status']) $result['message'] = "Etapa do funil criada com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->funnelStepService->update($request, $id);

        if($result['status']) $result['message'] = "Etapa do funil atualizada com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->funnelStepService->delete($id);

        if($result['status']) $result['message'] = "Etapa do funil deletada com sucesso";
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
