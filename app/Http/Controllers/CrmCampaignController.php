<?php

namespace App\Http\Controllers;

use App\Services\CrmCampaign\CrmCampaignService;
use Illuminate\Http\Request;

class CrmCampaignController extends Controller
{
    private $crmCampaignService;

    public function __construct(CrmCampaignService $crmCampaignService) {
        $this->crmCampaignService = $crmCampaignService;
    }

    public function search(Request $request){
        $result = $this->crmCampaignService->search($request);

        return $result;
    }

    public function getById($id){
        $result = $this->crmCampaignService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->crmCampaignService->create($request);

        if($result['status']) $result['message'] = "Campanha de CRM criada com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->crmCampaignService->update($request, $id);

        if($result['status']) $result['message'] = "Campanha de CRM atualizada com sucesso";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->crmCampaignService->delete($id);

        if($result['status']) $result['message'] = "Campanha de CRM deletada com sucesso";
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