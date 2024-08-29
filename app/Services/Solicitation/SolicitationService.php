<?php

namespace App\Services\Solicitation;

use App\Enums\SolicitationStatusEnum;
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
                'status' => 'required|string|max:255',
                'payment_date' => 'nullable|date',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors()];
            }

            $solicitation = Solicitation::create($validator->validated());

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

            $solicitationToUpdate->update($validator->validated());

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
