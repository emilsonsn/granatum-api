<?php

namespace App\Http\Controllers;

use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(OrderService $orderService) {
        $this->orderService = $orderService;
    }

    public function search(Request $request){
        $result = $this->orderService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->orderService->create($request);

        if($result['status']) $result['message'] = "Pedido criado com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->orderService->update($request, $id);

        if($result['status']) $result['message'] = "Pedido atualizado com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->orderService->delete($id);

        if($result['status']) $result['message'] = "Pedido Deletado com sucesso";
        return $this->response($result);
    }

    public function delete_order_file($id){
        $result = $this->orderService->delete_order_file($id);

        if($result['status']) $result['message'] = "Anexo do Pedido Deletado com sucesso";
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
