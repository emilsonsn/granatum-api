<?php

namespace App\Http\Controllers;

use App\Services\BudgetDetail\BudgetDetailService;
use Illuminate\Http\Request;

class BudgetDetailController extends Controller
{
    private $budgetDetailService;

    public function __construct(BudgetDetailService $budgetDetailService) {
        $this->budgetDetailService = $budgetDetailService;
    }

    public function getById($id){
        $result = $this->budgetDetailService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->budgetDetailService->create($request);

        if($result['status']) $result['message'] = "Orçamento criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->budgetDetailService->update($request, $id);

        if($result['status']) $result['message'] = "Orçamento atualizado com sucesso";
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
