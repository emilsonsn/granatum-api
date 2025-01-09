<?php

namespace App\Http\Controllers;

use App\Services\CrmDashboard\CrmDashboardService;

class CrmDashboardController extends Controller
{
    private $crmDashboardService;

    public function __construct(CrmDashboardService $crmDashboardService) {
        $this->crmDashboardService = $crmDashboardService;
    }

    public function cards($period){
        $result = $this->crmDashboardService->cards($period);

        return $this->response($result);
    }

    public function budgetGraphic($status){
        $result = $this->crmDashboardService->budgetGraphic($status);

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
