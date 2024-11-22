<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VacancyController extends Controller
{
    private $vacancyController;

    public function __construct(VacancyController $vacancyController) {
        $this->vacancyController = $vacancyController;
    }

    public function search(Request $request){
        $result = $this->vacancyController->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->vacancyController->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->vacancyController->create($request);

        if($result['status']) $result['message'] = "Vaga criada com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->vacancyController->update($request, $id);

        if($result['status']) $result['message'] = "Vaga atualizada com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->vacancyController->delete($id);

        if($result['status']) $result['message'] = "Vaga deletada com sucesso";
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
