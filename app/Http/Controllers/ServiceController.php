<?php

namespace App\Http\Controllers;

use App\Services\Service\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private $serviceService;

    public function __construct(ServiceService $serviceService) {
        $this->serviceService = $serviceService;
    }

    public function search(Request $request){
        $result = $this->serviceService->search($request);

        return $this->response($result);
    }

    public function create(Request $request){
        $result = $this->serviceService->create($request);

        if($result['status']) $result['message'] = "Serviço criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->serviceService->update($request, $id);
        
        if($result['status']) $result['message'] = "Serviço atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->serviceService->delete($id);
        
        if($result['status']) $result['message'] = "Serviço Deletado com sucesso";
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
