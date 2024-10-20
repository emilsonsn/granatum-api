<?php

namespace App\Http\Controllers;

use App\Services\Lead\LeadService;
use App\Services\Partner\PartnerService;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    private $partnerService;

    public function __construct(PartnerService $partnerService) {
        $this->partnerService = $partnerService;
    }

    public function search(Request $request){
        $result = $this->partnerService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->partnerService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->partnerService->create($request);

        if($result['status']) $result['message'] = "Parceiro criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->partnerService->update($request, $id);

        if($result['status']) $result['message'] = "Parceiro atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->partnerService->delete($id);

        if($result['status']) $result['message'] = "Parceiro deletado com sucesso";
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
