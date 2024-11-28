<?php

namespace App\Http\Controllers;

use App\Services\HrCampaign\HrCampaignService;
use Illuminate\Http\Request;

class HrCampaignController extends Controller
{
    private $hrCampaignService;

    public function __construct(HrCampaignService $hrCampaignService) {
        $this->hrCampaignService = $hrCampaignService;
    }

    public function search(Request $request){
        $result = $this->hrCampaignService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->hrCampaignService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->hrCampaignService->create($request);

        if($result['status']) $result['message'] = "Campanha de RH criada com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->hrCampaignService->update($request, $id);

        if($result['status']) $result['message'] = "Campanha de RH atualizada com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->hrCampaignService->delete($id);

        if($result['status']) $result['message'] = "Campanha de RH deletada com sucesso";
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
