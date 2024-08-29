<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $dashboardService;

    public function __construct(DashboardService $dashboardService) {
        $this->dashboardService = $dashboardService;
    }

    public function cards(){
        $result = $this->dashboardService->cards();

        return $this->response($result);
    }

    public function purchaseGraphic(){
        $result = $this->dashboardService->purchaseGraphic();

        return $this->response($result);
    }

    public function orderGraphic(){
        $result = $this->dashboardService->orderGraphic();

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
