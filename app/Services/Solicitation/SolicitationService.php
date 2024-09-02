<?php

namespace App\Services\Solicitation;

use App\Enums\PurchaseStatusEnum;
use App\Enums\SolicitationStatusEnum;
use App\Models\Order;
use Exception;

use App\Models\Solicitation;
use Illuminate\Support\Facades\Validator;

class SolicitationService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $solicitations = Solicitation::orderBy('id', 'desc');

            $solicitations = $solicitations->paginate($perPage);

            return $solicitations;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function cards()
    {
        try {
            $solicitationPending = Solicitation::where('status', SolicitationStatusEnum::Pending->value)
                ->count();

            $solicitationReject = Solicitation::where('status', SolicitationStatusEnum::Rejected->value)
                ->count();

            $solicitationFinished= Solicitation::where('status', SolicitationStatusEnum::Finished->value)
                ->count();

            return [
                'status' => true, 
                'data' => [
                   'solicitationPending' => $solicitationPending,
                   'solicitationReject' => $solicitationReject,
                   'solicitationFinished' => $solicitationFinished,
                ]
            ];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'order_id' => 'required|integer',
                'solicitation_type' => 'required|string|max:255',
                'total_value' => 'required|numeric',
                'supplier_id' => 'required|integer',
                'user_id' => 'required|integer',
                'construction_id' => 'required|integer',
                'status' => 'nullable|string|max:255',
                'payment_date' => 'nullable|date',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors(), 'statusCode' => 400];
            }

            $data = $validator->validated();

            if(!isset($data['status']) || $data['status'] == 'null'){
                $data['status'] = SolicitationStatusEnum::Pending->value;
            }

            $solicitation = Solicitation::create($data);

            if($data['status'] === SolicitationStatusEnum::Pending->value){
                Order::find($data['order_id'])->update([
                    'purchase_status' => PurchaseStatusEnum::RequestFinance->value
                ]);
            }

            return ['status' => true, 'data' => $solicitation];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $user_id)
    {
        try {
            $rules = [
                'order_id' => 'required|integer',
                'solicitation_type' => 'required|string|max:255',
                'total_value' => 'required|numeric',
                'supplier_id' => 'required|integer',
                'user_id' => 'required|integer',
                'construction_id' => 'required|integer',
                'status' => 'required|string|max:255',
                'payment_date' => 'nullable|date',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $solicitationToUpdate = Solicitation::find($user_id);

            if(!isset($solicitationToUpdate)) throw new Exception('Solicitação não encontrada');

            $data = $validator->validated();

            $solicitationToUpdate->update($data);

            if($data['status'] === SolicitationStatusEnum::Pending->value){
                Order::find($data['order_id'])->update([
                    'purchase_status' => PurchaseStatusEnum::RequestFinance->value
                ]);
            }

            if($data['status'] === SolicitationStatusEnum::Rejected->value){
                Order::find($data['order_id'])->update([
                    'purchase_status' => PurchaseStatusEnum::Pending->value
                ]);
            }

            return ['status' => true, 'data' => $solicitationToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id){
        try{
            $solicitation = Solicitation::find($id);

            if(!$solicitation) throw new Exception('Solicitação não encontrada');

            $solicitationId = $solicitation->id;
            $solicitation->delete();

            return ['status' => true, 'data' => $solicitationId];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}