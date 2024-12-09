<?php

namespace App\Http\Controllers;

use App\Services\Budget\BudgetService;
use App\Services\HrCampaign\HrCampaignService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    private $budgetService;

    public function __construct(BudgetService $budgetService) {
        $this->budgetService = $budgetService;
    }

    public function search(Request $request){
        $result = $this->budgetService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->budgetService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->budgetService->create($request);

        if($result['status']) $result['message'] = "Orçamento criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->budgetService->update($request, $id);

        if($result['status']) $result['message'] = "Orçamento atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->budgetService->delete($id);

        if($result['status']) $result['message'] = "Orçamento deletado com sucesso";
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
