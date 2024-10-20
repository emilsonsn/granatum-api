<?php

namespace App\Http\Controllers;

use App\Services\Funnel\FunnelService;
use App\Services\Funil\FunilService;
use Illuminate\Http\Request;

class FunnelController extends Controller
{
    private $funnelService;

    public function __construct(FunnelService $funnelService) {
        $this->funnelService = $funnelService;
    }

    public function search(Request $request){
        $result = $this->funnelService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->funnelService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->funnelService->create($request);

        if($result['status']) $result['message'] = "Funil criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->funnelService->update($request, $id);

        if($result['status']) $result['message'] = "Funil atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->funnelService->delete($id);

        if($result['status']) $result['message'] = "Funil deletado com sucesso";
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
