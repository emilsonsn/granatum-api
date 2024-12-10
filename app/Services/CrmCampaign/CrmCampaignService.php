<?php

namespace App\Services\CrmCampaign;

use Exception;
use App\Models\CrmCampaign;
use Illuminate\Support\Facades\Validator;

class CrmCampaignService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $crmCampaigns = CrmCampaign::orderBy('id', 'desc');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $crmCampaigns->where(function($query) use($search_term){
                    $query->where('title', 'LIKE', "%$search_term%")
                        ->orWhere('message', 'LIKE', "%$search_term%");
                });
            }

            if($request->filled('type')){
                $crmCampaigns->where('type', $request->type);
            }

            if($request->filled('funnel_id')){
                $crmCampaigns->where('funnel_id', $request->funnel_id);
            }

            if($request->filled('recurrence_type')){
                $crmCampaigns->where('recurrence_type', $request->recurrence_type);
            }

            $crmCampaigns = $crmCampaigns->paginate($perPage);

            return $crmCampaigns;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $crmCampaign = CrmCampaign::find($id);
            
            if(!isset($crmCampaign)) throw new Exception('Campanha de CRM não encontrada');

            return $crmCampaign;
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
                'funnel_id' => ['required', 'integer'],
                'funnel_step_id' => ['nullable', 'integer'],
                'channels' => ['required', 'string'],
                'start_date' => ['required', 'date'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $crmCampaign = CrmCampaign::create($validator->validated());            

            return ['status' => true, 'data' => $crmCampaign];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $crmCampaignToUpdate = CrmCampaign::find($id);

            if(!isset($crmCampaignToUpdate)) throw new Exception('Campanha de CRM não encontrada');

            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string'],
                'type' => ['required', 'string', 'in:Single,Recurrence'],
                'recurrence_type' => ['nullable', 'string', 'in:Monthly,Fortnightly,Weekly'],
                'funnel_id' => ['required', 'integer'],
                'funnel_step_id' => ['nullable', 'integer'],
                'channels' => ['required', 'string'],
                'start_date' => ['required', 'date'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $crmCampaignToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $crmCampaignToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    
    public function delete($id){
        try{
            $crmCampaign = CrmCampaign::find($id);

            if(!$crmCampaign) throw new Exception('Campanha de CRM não encontrada');

            $crmCampaignName = $crmCampaign->name;
            $crmCampaign->delete();

            return ['status' => true, 'data' => $crmCampaignName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
