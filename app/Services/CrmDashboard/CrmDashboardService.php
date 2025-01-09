<?php

namespace App\Services\CrmDashboard;

use Exception;
use App\Models\BudgetGenerated;
use App\Models\Lead;
use Carbon\Carbon;

class CrmDashboardService
{
    public function cards($period)
    {
        try {
            $queryDate = $this->getDateQuery($period);

            $leads = Lead::where($queryDate['field'], $queryDate['value']);
            $budgets = BudgetGenerated::where($queryDate['field'], $queryDate['value']);

            $data = [
                'leads' => $leads->count(),
                'budgetGenerated' => $budgets->where('status', 'Generated')->count(),
                'budgetDelivered' => $budgets->where('status', 'Delivered')->count(),
                'budgetApproved' => $budgets->where('status', 'Approved')->count(),
                'budgetDesapproved' => $budgets->where('status', 'Desapproved')->count(),
            ];

            return [
                'status' => true,
                'data'   => $data,
            ];
        } catch (Exception $error) {
            return [
                'status' => false,
                'error' => $error->getMessage(),
                'statusCode' => 400,
            ];
        }
    }

    public function budgetGraphic($status)
    {
        try {
            $budgetGenerated = BudgetGenerated::whereYear('created_at', Carbon::now()->year)
                ->where('status', $status ?? 'Generated')
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('total', 'month');
    
            return [
                'status' => true,
                'data'   => $budgetGenerated,
            ];
        } catch (Exception $error) {
            return [
                'status' => false,
                'error' => $error->getMessage(),
                'statusCode' => 400,
            ];
        }
    }

    private function getDateQuery($period)
    {
        switch ($period) {
            case 'Monthly':
                return [
                    'field' => 'created_at',
                    'value' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
                ];
            case 'Annually':
                return [
                    'field' => 'created_at',
                    'value' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]
                ];
            case 'Daily':
            default:
                return [
                    'field' => 'created_at',
                    'value' => Carbon::now()->format('Y-m-d')
                ];
        }
    }
}
