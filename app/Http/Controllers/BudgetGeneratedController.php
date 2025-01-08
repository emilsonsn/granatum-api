<?php

namespace App\Http\Controllers;

use App\Services\Bank\BankService;
use App\Services\BudgetGenerated\BudgetGeneratedService;
use Illuminate\Http\Request;

class BudgetGeneratedController extends Controller
{
    private $budgetGeneratedService;

    public function __construct(BudgetGeneratedService $budgetGeneratedService) {
        $this->budgetGeneratedService = $budgetGeneratedService;
    }

    public function search(Request $request){
        $result = $this->budgetGeneratedService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->budgetGeneratedService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->budgetGeneratedService->create($request);

        if($result['status']) $result['message'] = "Orçamento criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->budgetGeneratedService->update($request, $id);

        if($result['status']) $result['message'] = "Orçamento atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->budgetGeneratedService->delete($id);

        if($result['status']) $result['message'] = "Orçamento deletado com sucesso";
        return $this->response($result);
    }

    public function deleteVariable($id){
        $result = $this->budgetGeneratedService->deleteVariable($id);

        if($result['status']) $result['message'] = "Variável deletada com sucesso";
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
