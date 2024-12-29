<?php

namespace App\Http\Controllers;

use App\Services\Lead\LeadService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    private $leadService;

    public function __construct(LeadService $leadService) {
        $this->leadService = $leadService;
    }

    public function search(Request $request){
        $result = $this->leadService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->leadService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->leadService->create($request);

        if($result['status']) $result['message'] = "Lead criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->leadService->update($request, $id);

        if($result['status']) $result['message'] = "Lead atualizado com sucesso";
        return $this->response($result);
    }

    public function leadStep(Request $request){
        $result = $this->leadService->leadStep($request);

        if($result['status']) $result['message'] = "Lead atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->leadService->delete($id);

        if($result['status']) $result['message'] = "Lead deletado com sucesso";
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
