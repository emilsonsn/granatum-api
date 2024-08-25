<?php

namespace App\Http\Controllers;

use App\Services\Supplier\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private $supplierService;

    public function __construct(SupplierService $supplierService) {
        $this->supplierService = $supplierService;
    }

    public function search(Request $request){
        $result = $this->supplierService->search($request);

        return $this->response($result);
    }

    public function create(Request $request){
        $result = $this->supplierService->create($request);

        $result['message'] = "Fornecedor criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->supplierService->update($request, $id);
        
        $result['message'] = "Fornecedor atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->supplierService->delete($id);
        
        $result['message'] = "Fornecedor Deletado com sucesso";
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
