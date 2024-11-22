<?php

namespace App\Http\Controllers;

use App\Services\Profession\ProfessionService;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    private $professionService;

    public function __construct(ProfessionService $professionService) {
        $this->professionService = $professionService;
    }

    public function search(Request $request){
        $result = $this->professionService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->professionService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->professionService->create($request);

        if($result['status']) $result['message'] = "Profissão criada com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->professionService->update($request, $id);

        if($result['status']) $result['message'] = "Profissão atualizada com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->professionService->delete($id);

        if($result['status']) $result['message'] = "Profissão deletada com sucesso";
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
