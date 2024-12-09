<?php

namespace App\Services\HrCampaign;

use Exception;
use App\Models\HrCampaign;
use Illuminate\Support\Facades\Validator;

class HrCampaignService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $hrCampaigns = HrCampaign::with('selectionProcess', 'status')
                ->orderBy('id', 'desc');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $hrCampaigns->where(function($query) use($search_term){
                    $query->where('title', 'LIKE', "%$search_term%")
                        ->orWhere('message', 'LIKE', "%$search_term%");
                });
            }

            if($request->filled('type')){
                $hrCampaigns->where('type', $request->type);
            }

            if($request->filled('recurrence_type')){
                $hrCampaigns->where('recurrence_type', $request->recurrence_type);
            }

            if($request->filled('selection_process_id')){
                $hrCampaigns->where('selection_process_id', $request->selection_process_id);
            }

            $hrCampaigns = $hrCampaigns->paginate($perPage);

            return $hrCampaigns;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $hrCampaign = HrCampaign::find($id);
            
            if(!isset($hrCampaign)) throw new Exception('Campanha de RH não encontrada');

            return $hrCampaign;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string'],
                'type' => ['required', 'string', 'in:Single,Recurrence'],
                'recurrence_type' => ['nullable', 'string', 'in:Monthly,Fortnightly,Weekly'],
                'selection_process_id' => ['required', 'integer'],
                'status_id' => ['nullable', 'integer'],
                'channels' => ['required', 'string'],
                'start_date' => ['required', 'date'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $hrCampaign = HrCampaign::create($validator->validated());            

            return ['status' => true, 'data' => $hrCampaign];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $hrCampaignToUpdate = HrCampaign::find($id);

            if(!isset($hrCampaignToUpdate)) throw new Exception('Campanha de RH não encontrada');
            
            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string'],
                'type' => ['required', 'string', 'in:Single,Recurrence'],
                'recurrence_type' => ['nullable', 'string', 'in:Monthly,Fortnightly,Weekly'],
                'selection_process_id' => ['required', 'integer'],
                'status_id' => ['nullable', 'integer'],
                'channels' => ['required', 'string'],
                'start_date' => ['required', 'date'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $hrCampaignToUpdate = $hrCampaignToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $hrCampaignToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    public function delete($id){
        try{
            $hrCampaign = HrCampaign::find($id);

            if(!$hrCampaign) throw new Exception('Campanha de RH não encontrada');

            $hrCampaignName = $hrCampaign->name;
            $hrCampaign->delete();

            return ['status' => true, 'data' => $hrCampaignName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
