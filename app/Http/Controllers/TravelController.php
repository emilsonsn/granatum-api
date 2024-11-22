<?php

namespace App\Http\Controllers;

use App\Services\Travel\TravelService;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    private $travelService;

    public function __construct(TravelService $travelService) {
        $this->travelService = $travelService;
    }

    public function search(Request $request) {
        $result = $this->travelService->search($request);

        return $this->response($result);
    }

    public function getById($id) {
        $result = $this->travelService->getById($id);

        return $this->response($result);
    }

    public function cards() {
        $result = $this->travelService->cards();

        return $this->response($result);
    }

    public function create(Request $request) {
        $result = $this->travelService->create($request);

        if ($result['status']) $result['message'] = "Viagem criada com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id) {
        $result = $this->travelService->update($request, $id);

        if ($result['status']) $result['message'] = "Viagem atualizada com sucesso";
        return $this->response($result);
    }

    public function deleteFile(Request $request, $id) {
        $result = $this->travelService->deleteFile($request, $id);

        if ($result['status']) $result['message'] = "Arquivo deletado com sucesso";
        return $this->response($result);
    }

    public function upRelease($id) {
        $result = $this->travelService->upRelease($id);

        if ($result['status']) $result['message'] = "Lançamento realizado com sucesso";
        return $this->response($result);
    }

    public function delete($id) {
        $result = $this->travelService->delete($id);

        if ($result['status']) $result['message'] = "Viagem deletada com sucesso";
        return $this->response($result);
    }

    private function response($result) {
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ], $result['statusCode'] ?? 200);
    }
}
