<?php

namespace App\Services\Dashboard;

use App\Enums\PurchaseStatusEnum;
use App\Enums\SolicitationStatusEnum;
use App\Models\Order;
use App\Models\Solicitation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class DashboardService
{

    public function cards()
    {
        try {
            // Compras por dia
            $orders = Order::where('purchase_status', PurchaseStatusEnum::Resolved->value)
                ->whereDate('purchase_date', Carbon::now()->format('Y-m-d'))
                ->count();

            // Compras por semana
            $ordersByWeek = Order::where('purchase_status', PurchaseStatusEnum::Resolved->value)
                ->whereRaw('WEEK(purchase_date) = ?', [Carbon::now()->weekOfYear])
                ->count();

            // Compras por mês
            $ordersByMonth = Order::where('purchase_status', PurchaseStatusEnum::Resolved->value)
                ->whereMonth('purchase_date', Carbon::now()->format('m'))
                ->count();

            // Compras por ano
            $ordersByYear = Order::where('purchase_status', PurchaseStatusEnum::Resolved->value)
                ->whereYear('purchase_date', Carbon::now()->format('Y'))
                ->count();
            
            // Pedidos pendentes
            $pendingOrders = Order::where('purchase_status', PurchaseStatusEnum::Pending->value)
                ->count();

            // Pedidos aguardando financeiro
            $awaitingFinanceOrders = Order::where('purchase_status', PurchaseStatusEnum::RequestFinance->value)
                ->count();

            // solicitaçoes pendentes
            $solicitationPendings = Solicitation::where('status', SolicitationStatusEnum::Pending->value)
                ->count();

            // solicitaçoes aprovadas
            $solicitationFinished = Solicitation::where('status', SolicitationStatusEnum::Finished->value)
                ->count();
            
            //retornar dados
            return [
               'status' => true,
                'data' => [
                    'ordersByDay' => $orders,
                    'ordersByWeek' => $ordersByWeek,
                    'ordersByMonth' => $ordersByMonth,
                    'ordersByYear' => $ordersByYear,
                    'pendingOrders' => $pendingOrders,
                    'awaitingFinanceOrders' => $awaitingFinanceOrders,
                    'solicitationPendings' => $solicitationPendings,
                    'solicitationFinished' => $solicitationFinished,
                ],
            ];
            
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function purchaseGraphic(){
        try{
            $data = Order::where('purchase_status', PurchaseStatusEnum::Resolved->value)
                ->whereYear('purchase_date', Carbon::now()->format('Y'))
                ->select(DB::raw('MONTHNAME(purchase_date) as month'), DB::raw('count(*) as total'))
                ->groupBy('month')
                ->get()
                ->toArray();
        
            return ['status' => true, 'data' => $data];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
