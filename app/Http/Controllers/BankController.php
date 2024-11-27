<?php

namespace App\Http\Controllers;

use App\Services\Bank\BankService;
use Illuminate\Http\Request;

class BankController extends Controller
{
    private $bankService;

    public function __construct(BankService $bankService) {
        $this->bankService = $bankService;
    }

    public function search(Request $request){
        $result = $this->bankService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->bankService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->bankService->create($request);

        if($result['status']) $result['message'] = "Banco criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->bankService->update($request, $id);

        if($result['status']) $result['message'] = "Banco atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->bankService->delete($id);

        if($result['status']) $result['message'] = "Banco deletado com sucesso";
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
